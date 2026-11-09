<?php

namespace App\Services\Shop;

use App\Models\Invoice;
use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Voucher;
use App\Services\InvoiceNumbering;
use App\Services\Shop\Shipping\ShippingRateProvider;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly ShippingRateProvider $shipping,
    ) {
    }

    /**
     * Voucher discount for a cart subtotal, or an error message.
     *
     * @return array{voucher: ?Voucher, discount: int, error: ?string}
     */
    public function voucherDiscount(?string $code, int $subtotal): array
    {
        $id = app()->getLocale() === 'id';

        if (blank($code)) {
            return ['voucher' => null, 'discount' => 0, 'error' => null];
        }

        $voucher = Voucher::where('code', strtoupper(trim($code)))->first();

        if (! $voucher || ! $voucher->isValid() || ! $voucher->appliesTo(Voucher::SCOPE_SHOP)) {
            return ['voucher' => null, 'discount' => 0, 'error' => $id ? 'Kode voucher tidak valid atau sudah kedaluwarsa.' : 'This voucher code is invalid or has expired.'];
        }

        if (! $voucher->meetsMinimum($subtotal)) {
            $min = Money::format($voucher->min_purchase);

            return ['voucher' => null, 'discount' => 0, 'error' => $id ? "Voucher ini berlaku untuk belanja minimal {$min}." : "This voucher needs a minimum purchase of {$min}."];
        }

        return ['voucher' => $voucher, 'discount' => (int) round($voucher->calculateDiscount($subtotal)), 'error' => null];
    }

    /** Shipping options for an address, priced from the cart's total weight. */
    public function shippingOptions(UserAddress $address, int $weightGrams, SiteSetting $settings): array
    {
        return $this->shipping->rates($address, max(1, $weightGrams), $settings);
    }

    /**
     * Turns the user's cart into an order: reserves stock, freezes prices, issues the invoice
     * and empties the cart. Everything happens in one transaction, so a failure leaves no trace.
     *
     * @param  array{channel: string, voucher_code?: ?string, phone?: ?string, notes?: ?string, address_id?: ?string, shipping_option?: ?string}  $input
     */
    public function place(User $user, array $input): ShopOrder
    {
        $settings = SiteSetting::current();
        $id = app()->getLocale() === 'id';
        $channel = $input['channel'];

        if ($channel === ShopOrder::CHANNEL_MANUAL && ! $settings->shop_manual_payment_enabled) {
            throw new ShopException($id ? 'Transfer manual sedang tidak tersedia.' : 'Manual transfer is not available right now.');
        }

        return DB::transaction(function () use ($user, $input, $settings, $channel, $id) {
            $summary = $this->cart->summarize($this->cart->items($user), $settings);

            if ($summary['lines']->isEmpty()) {
                throw new ShopException($id ? 'Keranjang Anda kosong.' : 'Your cart is empty.');
            }

            if ($summary['has_issues']) {
                throw new ShopException($id ? 'Ada produk di keranjang yang tidak tersedia. Periksa keranjang Anda.' : 'Some items in your cart are unavailable. Please review your cart.');
            }

            [$address, $shippingCost, $courierCode, $courierService] = $this->resolveShipping($user, $input, $summary, $settings);

            $voucher = $this->voucherDiscount($input['voucher_code'] ?? null, $summary['subtotal']);

            if ($voucher['error']) {
                throw new ShopException($voucher['error']);
            }

            // A pending order holds its voucher use, so max_uses cannot be exceeded by
            // placing several unpaid orders. The use is given back if the order expires or is cancelled.
            if ($voucher['voucher'] && ! Voucher::reserveUse($voucher['voucher']->code)) {
                throw new ShopException($id ? 'Kuota voucher ini sudah habis.' : 'This voucher has been fully used.');
            }

            $subtotal = $summary['subtotal'];
            $discount = min($subtotal, $voucher['discount']);
            $uniqueCode = $channel === ShopOrder::CHANNEL_MANUAL && $settings->shop_manual_unique_code ? random_int(1, 999) : 0;
            $total = $subtotal - $discount + $shippingCost + $uniqueCode;
            $taxRate = (float) $settings->shop_tax_rate;
            $tax = ShopTax::split($subtotal - $discount, $taxRate);

            foreach ($summary['lines'] as $line) {
                if (! $line->variant->reserve($line->quantity)) {
                    throw new ShopException($id
                        ? "Stok {$line->product->getTranslation('name')} baru saja habis."
                        : "{$line->product->getTranslation('name')} just sold out.");
                }
            }

            $expiryMinutes = $channel === ShopOrder::CHANNEL_MANUAL ? $settings->shop_manual_expiry_minutes : $settings->shop_order_expiry_minutes;

            $order = ShopOrder::createNumbered([
                'user_id' => $user->id,
                'payment_status' => ShopOrder::PENDING,
                'fulfillment_status' => ShopOrder::UNFULFILLED,
                'payment_channel' => $channel,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $input['phone'] ?? null,
                'shipping_address' => $address?->snapshot(),
                'courier_code' => $courierCode,
                'courier_service' => $courierService,
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'voucher_code' => $voucher['voucher']?->code,
                'unique_code' => $uniqueCode,
                'total_amount' => $total,
                'tax_rate' => $taxRate,
                'tax_base' => $tax['base'],
                'tax_amount' => $tax['tax'],
                'expires_at' => now()->addMinutes(max(15, (int) $expiryMinutes)),
                'notes' => $input['notes'] ?? null,
            ]);

            foreach ($summary['lines'] as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'product_variant_id' => $line->variant->id,
                    'product_type' => $line->product->type,
                    'product_name' => $line->product->getTranslation('name', 'id'),
                    'variant_title' => $line->variant->is_default ? null : $line->variant->getTranslation('title', 'id'),
                    'sku' => $line->variant->sku,
                    'unit_price' => $line->unit_price,
                    'quantity' => $line->quantity,
                    'subtotal' => $line->subtotal,
                    'weight_grams' => $line->variant->weight_grams,
                ]);
            }

            $order->update(['invoice_id' => $this->issueInvoice($order->load('items'))->id]);

            if ($channel === ShopOrder::CHANNEL_MANUAL) {
                $order->payments()->create([
                    'channel' => ShopOrder::CHANNEL_MANUAL,
                    'amount' => $total,
                    'status' => ShopPayment::PENDING,
                ]);
            }

            $this->cart->clear($user);

            return $order;
        });
    }

    /** @return array{0: ?UserAddress, 1: int, 2: ?string, 3: ?string} */
    private function resolveShipping(User $user, array $input, array $summary, SiteSetting $settings): array
    {
        if (! $summary['has_physical']) {
            return [null, 0, null, null];
        }

        $id = app()->getLocale() === 'id';

        if (! $settings->shop_physical_enabled) {
            throw new ShopException($id ? 'Produk fisik belum bisa dipesan.' : 'Physical products cannot be ordered yet.');
        }

        $address = $user->addresses()->whereKey($input['address_id'] ?? null)->first();

        if (! $address) {
            throw new ShopException($id ? 'Pilih alamat pengiriman.' : 'Choose a shipping address.');
        }

        $option = collect($this->shippingOptions($address, $summary['weight'], $settings))
            ->first(fn ($o) => $o['code'].':'.$o['service'] === ($input['shipping_option'] ?? null));

        if (! $option) {
            throw new ShopException($id ? 'Pilih layanan pengiriman.' : 'Choose a shipping service.');
        }

        return [$address, (int) $option['cost'], $option['code'], $option['service']];
    }

    /** Customer invoice: no PPN line (prices include it); shipping and the unique code are their own lines. */
    private function issueInvoice(ShopOrder $order): Invoice
    {
        $invoice = Invoice::create([
            'invoice_number' => InvoiceNumbering::next(),
            'client_name' => $order->customer_name,
            'client_email' => $order->customer_email,
            'client_phone' => $order->customer_phone,
            'client_address' => $order->shipping_address
                ? implode(', ', array_filter([$order->shipping_address['address_line'] ?? null, $order->shipping_address['city'] ?? null, $order->shipping_address['province'] ?? null]))
                : '-',
            'issue_date' => now(),
            'due_date' => $order->expires_at,
            'status' => 'Unpaid',
            'notes' => 'Pesanan Toko '.$order->order_number,
            'total_amount' => $order->total_amount,
            'discount' => $order->discount_amount,
            'discount_type' => 'fixed',
            'tax' => 0,
            'tax_type' => 'fixed',
        ]);

        $position = 0;

        foreach ($order->items as $item) {
            $invoice->items()->create([
                'description' => $item->displayName(),
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
                'position' => $position++,
            ]);
        }

        if ($order->shipping_cost > 0) {
            $invoice->items()->create([
                'description' => 'Ongkos kirim'.($order->courier_code ? ' ('.strtoupper($order->courier_code).' '.$order->courier_service.')' : ''),
                'quantity' => 1,
                'unit_price' => $order->shipping_cost,
                'subtotal' => $order->shipping_cost,
                'position' => $position++,
            ]);
        }

        if ($order->unique_code > 0) {
            $invoice->items()->create([
                'description' => 'Kode unik pembayaran',
                'quantity' => 1,
                'unit_price' => $order->unique_code,
                'subtotal' => $order->unique_code,
                'position' => $position,
            ]);
        }

        return $invoice;
    }
}
