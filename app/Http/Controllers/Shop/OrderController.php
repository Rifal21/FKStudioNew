<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\SiteSetting;
use App\Services\DuitkuService;
use App\Services\InvoicePdf;
use App\Services\Shop\OrderLifecycle;
use App\Services\Shop\ShopException;
use App\Services\Shop\ShopPaymentService;
use Illuminate\Http\Request;

/** The customer's own shop orders. */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = ShopOrder::where('user_id', $request->user()->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('landing.user.purchases', compact('orders'));
    }

    public function show(Request $request, ShopOrder $shopOrder, DuitkuService $duitku)
    {
        $order = $this->own($request, $shopOrder)->load(['items.product.images', 'payments', 'invoice']);
        $settings = SiteSetting::current();
        $latestPayment = $order->payments->first();

        return view('landing.shop.order', [
            'order' => $order,
            'settings' => $settings,
            'latestPayment' => $latestPayment,
            'manualPayment' => $order->payments->firstWhere('channel', ShopOrder::CHANNEL_MANUAL),
            'duitkuMethods' => $order->canPay() ? $duitku->getPaymentMethods($order->total_amount) : [],
            'returnedFromGateway' => $request->has('resultCode') || $request->has('merchantOrderId'),
        ]);
    }

    /** Polled by the order page right after returning from Duitku. Status comes from the callback only. */
    public function status(Request $request, ShopOrder $shopOrder)
    {
        $order = $this->own($request, $shopOrder);

        return response()->json([
            'payment_status' => $order->payment_status,
            'label' => $order->paymentStatusLabel(),
            'paid' => $order->isPaid(),
        ]);
    }

    public function pay(Request $request, ShopOrder $shopOrder, ShopPaymentService $payments)
    {
        $order = $this->own($request, $shopOrder);
        $data = $request->validate(['payment' => ['required', 'string', 'regex:/^(manual|duitku(:[A-Za-z0-9]{1,10})?)$/']]);
        [$channel, $method] = array_pad(explode(':', $data['payment'], 2), 2, null);

        try {
            if ($channel === 'manual') {
                $payments->switchToManual($order);

                return back()->with('success', app()->getLocale() === 'id' ? 'Silakan transfer lalu unggah bukti pembayaran.' : 'Please transfer and upload your payment proof.');
            }

            return redirect()->away($payments->startDuitku($order, $method));
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function proof(Request $request, ShopOrder $shopOrder, ShopPaymentService $payments)
    {
        $order = $this->own($request, $shopOrder);
        $id = app()->getLocale() === 'id';

        $data = $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            'sender_name' => 'required|string|max:100',
            'sender_bank' => 'required|string|max:100',
            'method_name' => 'nullable|string|max:150',
            'customer_note' => 'nullable|string|max:500',
        ], [
            'proof.required' => $id ? 'Unggah foto atau PDF bukti transfer.' : 'Upload a photo or PDF of your transfer receipt.',
            'proof.mimes' => $id ? 'Format bukti harus JPG, PNG, WEBP, atau PDF.' : 'The proof must be a JPG, PNG, WEBP or PDF file.',
            'proof.max' => $id ? 'Ukuran bukti maksimal 2 MB.' : 'The proof may not be larger than 2 MB.',
        ]);

        try {
            $payments->submitProof($order, $request->file('proof'), $data);
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $id ? 'Bukti transfer terkirim. Kami akan memverifikasinya secepatnya.' : 'Proof sent. We will verify it shortly.');
    }

    public function cancel(Request $request, ShopOrder $shopOrder, OrderLifecycle $lifecycle)
    {
        $order = $this->own($request, $shopOrder);
        $id = app()->getLocale() === 'id';

        if ($order->payment_status !== ShopOrder::PENDING || ! $lifecycle->cancel($order)) {
            return back()->with('error', $id ? 'Pesanan ini tidak bisa dibatalkan.' : 'This order cannot be cancelled.');
        }

        return back()->with('success', $id ? 'Pesanan dibatalkan.' : 'Order cancelled.');
    }

    public function invoice(Request $request, ShopOrder $shopOrder)
    {
        $order = $this->own($request, $shopOrder);
        abort_unless($order->invoice, 404);

        return InvoicePdf::make($order->invoice)->stream(InvoicePdf::filename($order->invoice));
    }

    private function own(Request $request, ShopOrder $order): ShopOrder
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return $order;
    }
}
