<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Models\DigitalEntitlement;
use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Services\Shop\OrderLifecycle;
use App\Services\Shop\ShopException;
use App\Services\Shop\ShopNotifier;
use App\Services\Shop\ShopPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public const TABS = [
        'all' => 'Semua',
        'pending' => 'Menunggu pembayaran',
        'verify' => 'Menunggu verifikasi',
        'paid' => 'Lunas',
        'ship' => 'Perlu dikirim',
        'attention' => 'Perlu tindakan',
        'closed' => 'Batal / kedaluwarsa',
    ];

    public function index(Request $request)
    {
        $tab = array_key_exists($request->query('tab'), self::TABS) ? $request->query('tab') : 'all';
        $q = trim((string) $request->query('q', ''));

        $orders = $this->scopeTab(ShopOrder::query(), $tab)
            ->with(['items', 'user'])
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('order_number', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%")->orWhere('customer_email', 'like', "%{$q}%")))
            ->latest()
            ->paginate(25)->withQueryString();

        $counts = collect(array_keys(self::TABS))->mapWithKeys(fn ($key) => [$key => $key === 'all' ? null : $this->scopeTab(ShopOrder::query(), $key)->count()]);

        return view('dashboard.shop.orders.index', compact('orders', 'tab', 'q', 'counts'));
    }

    public function show(ShopOrder $order)
    {
        $order->load(['items.product', 'items.entitlements.licenseKey', 'payments.verifier', 'user', 'invoice']);

        return view('dashboard.shop.orders.show', compact('order'));
    }

    public function proof(ShopOrder $order, ShopPayment $payment)
    {
        abort_unless($payment->shop_order_id === $order->id && $payment->hasProof(), 404);
        $disk = Storage::disk($payment->proof_disk);
        abort_unless($disk->exists($payment->proof_path), 404);

        return $disk->response($payment->proof_path);
    }

    public function approve(Request $request, ShopOrder $order, ShopPayment $payment, ShopPaymentService $payments)
    {
        abort_unless($payment->shop_order_id === $order->id, 404);

        try {
            $payments->approve($payment, $request->user());
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembayaran disetujui. Pesanan lunas dan akses produk digital sudah dikirim.');
    }

    public function reject(Request $request, ShopOrder $order, ShopPayment $payment, ShopPaymentService $payments)
    {
        abort_unless($payment->shop_order_id === $order->id, 404);
        $data = $request->validate(['reason' => 'required|string|max:500'], ['reason.required' => 'Tulis alasan penolakan agar pelanggan tahu apa yang harus diperbaiki.']);

        try {
            $payments->reject($payment, $request->user(), $data['reason']);
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bukti transfer ditolak. Pelanggan diberi tahu dan bisa mengunggah ulang.');
    }

    public function cancel(ShopOrder $order, OrderLifecycle $lifecycle)
    {
        return $lifecycle->cancel($order)
            ? back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.')
            : back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
    }

    public function refund(Request $request, ShopOrder $order, OrderLifecycle $lifecycle)
    {
        $request->validate(['confirm' => 'accepted'], ['confirm.accepted' => 'Centang konfirmasi bahwa dana sudah dikembalikan.']);

        try {
            $lifecycle->refund($order, $request->boolean('restock'), $request->user());
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Refund dicatat. Akses produk digital dicabut.');
    }

    public function ship(Request $request, ShopOrder $order, ShopNotifier $notifier)
    {
        abort_unless($order->isPaid() && $order->hasPhysicalItems(), 422);

        $data = $request->validate([
            'courier_code' => 'required|string|max:30',
            'courier_service' => 'nullable|string|max:50',
            'tracking_number' => 'required|string|max:100',
        ]);

        $order->update($data + ['fulfillment_status' => ShopOrder::SHIPPED, 'shipped_at' => now()]);
        $notifier->orderShipped($order);

        return back()->with('success', 'Pesanan ditandai dikirim. Pelanggan menerima nomor resi.');
    }

    public function deliver(ShopOrder $order)
    {
        abort_unless($order->fulfillment_status === ShopOrder::SHIPPED, 422);

        $order->update(['fulfillment_status' => ShopOrder::DELIVERED, 'delivered_at' => now()]);

        return back()->with('success', 'Pesanan ditandai diterima.');
    }

    public function resendAccess(ShopOrder $order, OrderLifecycle $lifecycle, ShopNotifier $notifier)
    {
        if (! $order->isPaid()) {
            return back()->with('error', 'Pesanan belum lunas.');
        }

        $complete = $lifecycle->regrantDigital($order);
        $notifier->accessResent($order);

        if ($complete && $order->needs_attention) {
            $lifecycle->resolveAttention($order);
        }

        return back()->with($complete ? 'success' : 'error', $complete
            ? 'Akses produk digital dikirim ulang ke pelanggan.'
            : 'Akses dikirim ulang, tetapi masih ada kunci lisensi yang kurang. Tambahkan kunci di halaman produk.');
    }

    public function resolve(ShopOrder $order, OrderLifecycle $lifecycle)
    {
        $lifecycle->resolveAttention($order);

        return back()->with('success', 'Tanda "perlu tindakan" dihapus.');
    }

    public function notes(Request $request, ShopOrder $order)
    {
        $order->update($request->validate(['admin_notes' => 'nullable|string|max:5000']));

        return back()->with('success', 'Catatan disimpan.');
    }

    public function resetDownloads(ShopOrder $order, DigitalEntitlement $entitlement)
    {
        abort_unless($order->items()->whereKey($entitlement->shop_order_item_id)->exists(), 404);
        $entitlement->update(['download_count' => 0]);

        return back()->with('success', 'Jumlah unduhan direset.');
    }

    public function toggleAccess(ShopOrder $order, DigitalEntitlement $entitlement)
    {
        abort_unless($order->items()->whereKey($entitlement->shop_order_item_id)->exists(), 404);
        $entitlement->update(['revoked_at' => $entitlement->revoked_at ? null : now()]);

        return back()->with('success', $entitlement->revoked_at ? 'Akses dicabut.' : 'Akses dipulihkan.');
    }

    private function scopeTab($query, string $tab)
    {
        return match ($tab) {
            'pending' => $query->where('payment_status', ShopOrder::PENDING),
            'verify' => $query->where('payment_status', ShopOrder::AWAITING_VERIFICATION),
            'paid' => $query->where('payment_status', ShopOrder::PAID),
            'ship' => $query->where('payment_status', ShopOrder::PAID)->where('fulfillment_status', ShopOrder::PROCESSING),
            'attention' => $query->where('needs_attention', true),
            'closed' => $query->whereIn('payment_status', [ShopOrder::EXPIRED, ShopOrder::CANCELLED, ShopOrder::REFUNDED]),
            default => $query,
        };
    }
}
