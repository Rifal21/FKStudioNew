<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\SiteSetting;
use App\Services\DuitkuService;
use App\Services\Shop\CartService;
use App\Services\Shop\CheckoutService;
use App\Services\Shop\ShopException;
use App\Services\Shop\ShopNotifier;
use App\Services\Shop\ShopPaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
    ) {
    }

    public function show(Request $request, DuitkuService $duitku)
    {
        $settings = SiteSetting::current();
        $summary = $this->cart->summarize($this->cart->items($request->user()), $settings);

        if ($summary['lines']->isEmpty()) {
            return redirect()->route('cart.index');
        }

        if ($summary['has_issues']) {
            return redirect()->route('cart.index')->with('error', app()->getLocale() === 'id'
                ? 'Ada produk yang tidak tersedia. Hapus atau ubah jumlahnya dulu.'
                : 'Some items are unavailable. Remove them or change the quantity first.');
        }

        $addresses = $summary['has_physical'] ? $request->user()->addresses()->orderByDesc('is_default')->get() : collect();

        return view('landing.shop.checkout', [
            'settings' => $settings,
            'summary' => $summary,
            'addresses' => $addresses,
            'duitkuMethods' => $duitku->getPaymentMethods(max(1, $summary['subtotal'])),
        ]);
    }

    public function voucher(Request $request)
    {
        $request->validate(['voucher_code' => 'required|string|max:100']);

        $summary = $this->cart->summarize($this->cart->items($request->user()));
        $result = $this->checkout->voucherDiscount($request->voucher_code, $summary['subtotal']);

        if ($result['error']) {
            return response()->json(['success' => false, 'message' => $result['error']]);
        }

        return response()->json([
            'success' => true,
            'code' => $result['voucher']->code,
            'discount' => $result['discount'],
            'message' => app()->getLocale() === 'id' ? 'Voucher dipakai.' : 'Voucher applied.',
        ]);
    }

    public function rates(Request $request)
    {
        $request->validate(['address_id' => 'required|string']);

        $settings = SiteSetting::current();
        $address = $request->user()->addresses()->findOrFail($request->address_id);
        $summary = $this->cart->summarize($this->cart->items($request->user()), $settings);

        return response()->json([
            'options' => $this->checkout->shippingOptions($address, $summary['weight'], $settings),
        ]);
    }

    public function process(Request $request, ShopPaymentService $payments, ShopNotifier $notifier)
    {
        $id = app()->getLocale() === 'id';

        $data = $request->validate([
            'payment' => ['required', 'string', 'regex:/^(manual|duitku(:[A-Za-z0-9]{1,10})?)$/'],
            'voucher_code' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:1000',
            'address_id' => 'nullable|string',
            'shipping_option' => 'nullable|string|max:100',
            'agree' => 'accepted',
        ], [
            'payment.required' => $id ? 'Pilih metode pembayaran.' : 'Choose a payment method.',
            'agree.accepted' => $id ? 'Setujui syarat & ketentuan untuk melanjutkan.' : 'Accept the terms to continue.',
        ]);

        [$channel, $method] = array_pad(explode(':', $data['payment'], 2), 2, null);

        try {
            $order = $this->checkout->place($request->user(), [
                'channel' => $channel === 'manual' ? ShopOrder::CHANNEL_MANUAL : ShopOrder::CHANNEL_DUITKU,
                'voucher_code' => $data['voucher_code'] ?? null,
                'phone' => $data['customer_phone'] ?? null,
                'notes' => $data['notes'] ?? null,
                'address_id' => $data['address_id'] ?? null,
                'shipping_option' => $data['shipping_option'] ?? null,
            ]);
        } catch (ShopException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $notifier->orderPlaced($order);

        if ($order->payment_channel === ShopOrder::CHANNEL_MANUAL) {
            return redirect()->route('shop.orders.show', $order)
                ->with('success', $id ? 'Pesanan dibuat. Silakan transfer lalu unggah bukti pembayaran.' : 'Order placed. Please transfer and upload your payment proof.');
        }

        try {
            return redirect()->away($payments->startDuitku($order, $method));
        } catch (ShopException $e) {
            return redirect()->route('shop.orders.show', $order)
                ->with('error', ($id ? 'Pesanan dibuat, tetapi halaman pembayaran gagal dibuka: ' : 'Order placed, but the payment page could not be opened: ').$e->getMessage());
        }
    }
}
