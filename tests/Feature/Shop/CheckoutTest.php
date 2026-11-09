<?php

namespace Tests\Feature\Shop;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Models\Voucher;
use App\Services\Shop\CheckoutService;
use App\Services\Shop\OrderLifecycle;
use App\Services\Shop\ShopException;
use App\Services\Shop\ShopPaymentService;
use App\Services\Shop\ShopTax;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->duitkuConfig();
        $this->fakeDuitku();
    }

    public function test_guests_are_sent_to_login_and_the_item_is_added_when_they_return(): void
    {
        $this->shopSettings();
        $product = $this->digitalProduct();
        $variant = $this->variantOf($product);

        $this->get('/cart')->assertRedirect('/login');
        $this->get('/shop/checkout')->assertRedirect('/login');

        $this->post('/cart/items', ['variant_id' => $variant->id, 'quantity' => 2])
            ->assertRedirect('/login')
            ->assertSessionHas('pending_cart_item');

        $user = $this->customer();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('shop.show', $product->slug));

        $this->get(route('shop.show', $product->slug))->assertOk();

        $this->assertSame(2, CartItem::where('user_id', $user->id)->value('quantity'));
        $this->assertNull(session('pending_cart_item'));
    }

    public function test_manual_checkout_reserves_stock_issues_a_tax_free_invoice_and_stores_ppn_for_admin(): void
    {
        $this->shopSettings();
        $user = $this->customer();
        $product = $this->digitalProduct(stock: 3, price: 111000);

        $this->actingAs($user)->post('/cart/items', ['variant_id' => $this->variantOf($product)->id, 'quantity' => 2])->assertRedirect();

        $response = $this->actingAs($user)->post('/shop/checkout', ['payment' => 'manual', 'agree' => '1']);

        $order = ShopOrder::firstOrFail();
        $response->assertRedirect(route('shop.orders.show', $order));

        $this->assertSame(ShopOrder::CHANNEL_MANUAL, $order->payment_channel);
        $this->assertSame(222000, $order->total_amount);
        $this->assertSame(200000, $order->tax_base);
        $this->assertSame(22000, $order->tax_amount);
        $this->assertSame(1, $this->variantOf($product)->fresh()->stock);
        $this->assertSame(0, CartItem::count());
        $this->assertSame(ShopPayment::PENDING, $order->payments()->first()->status);

        $invoice = $order->invoice;
        $this->assertSame('Unpaid', $invoice->status);
        $this->assertEquals(0, $invoice->tax);
        $this->assertEquals(222000, $invoice->total_amount);

        // The customer never sees PPN: not on the order page, nor in its JSON.
        $page = $this->actingAs($user)->get(route('shop.orders.show', $order))->assertOk();
        $page->assertDontSee('PPN')->assertDontSee('Rp 22.000')->assertDontSee('DPP')->assertSee('Rp 222.000');
        $this->assertArrayNotHasKey('tax_amount', $order->toArray());
    }

    public function test_duitku_checkout_redirects_to_the_payment_page(): void
    {
        $this->shopSettings();
        $user = $this->customer();
        $product = $this->digitalProduct();
        $this->actingAs($user)->post('/cart/items', ['variant_id' => $this->variantOf($product)->id]);

        $this->actingAs($user)->post('/shop/checkout', ['payment' => 'duitku:VC', 'agree' => '1'])
            ->assertRedirect('https://sandbox.duitku.com/pay/abc');

        $payment = ShopPayment::firstOrFail();
        $this->assertSame('VC', $payment->method_code);
        $this->assertSame('REF123', $payment->reference);
    }

    public function test_the_last_unit_can_only_be_sold_once(): void
    {
        $this->shopSettings();
        $product = $this->digitalProduct(stock: 1);
        $first = $this->customer();
        $second = $this->customer();

        app(\App\Services\Shop\CartService::class)->add($first, $this->variantOf($product));
        app(\App\Services\Shop\CartService::class)->add($second, $this->variantOf($product));

        app(CheckoutService::class)->place($first, ['channel' => ShopOrder::CHANNEL_DUITKU]);

        $this->expectException(ShopException::class);
        app(CheckoutService::class)->place($second, ['channel' => ShopOrder::CHANNEL_DUITKU]);
    }

    public function test_expired_orders_return_stock_but_orders_under_review_are_kept(): void
    {
        $this->shopSettings();
        $product = $this->digitalProduct(stock: 5);
        $expiring = $this->placeOrder($this->customer(), $product, quantity: 2);
        $underReview = $this->placeOrder($this->customer(), $product, ShopOrder::CHANNEL_MANUAL, 1);
        $underReview->update(['payment_status' => ShopOrder::AWAITING_VERIFICATION]);

        $this->assertSame(2, $this->variantOf($product)->fresh()->stock);
        $this->travel(2)->days();

        $this->artisan('shop:expire-orders')->assertSuccessful();

        $this->assertSame(ShopOrder::EXPIRED, $expiring->fresh()->payment_status);
        $this->assertSame('Cancelled', $expiring->invoice->fresh()->status);
        $this->assertSame(ShopOrder::AWAITING_VERIFICATION, $underReview->fresh()->payment_status);
        $this->assertSame(4, $this->variantOf($product)->fresh()->stock);
    }

    public function test_a_late_payment_after_expiry_is_flagged_when_stock_is_gone(): void
    {
        $this->shopSettings();
        $product = $this->digitalProduct(stock: 1);
        $order = $this->placeOrder($this->customer(), $product);
        app(ShopPaymentService::class)->startDuitku($order);
        app(OrderLifecycle::class)->cancel($order);

        // Someone else buys the unit that came back.
        $this->placeOrder($this->customer(), $product);

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($order->order_number.'-1', $order->total_amount))->assertOk();

        $order->refresh();
        $this->assertSame(ShopOrder::PAID, $order->payment_status);
        $this->assertTrue($order->needs_attention);
    }

    public function test_vouchers_respect_scope_and_limit_and_pending_orders_hold_their_use(): void
    {
        $this->shopSettings();
        Voucher::create(['code' => 'PAKET', 'type' => 'percent', 'value' => 10, 'is_active' => true, 'scope' => 'package', 'used_count' => 0]);
        $shopVoucher = Voucher::create(['code' => 'TOKO', 'type' => 'fixed', 'value' => 11000, 'is_active' => true, 'scope' => 'shop', 'used_count' => 0, 'max_uses' => 1]);
        $product = $this->digitalProduct(price: 111000);

        try {
            $this->placeOrder($this->customer(), $product, input: ['voucher_code' => 'PAKET']);
            $this->fail('Package voucher should not work in the shop.');
        } catch (ShopException) {
        }

        $first = $this->placeOrder($this->customer(), $product, input: ['voucher_code' => 'toko']);

        $this->assertSame(11000, $first->discount_amount);
        $this->assertSame(100000, $first->total_amount);
        $this->assertSame(ShopTax::split(100000, 11)['tax'], $first->tax_amount);
        // The unpaid order already holds the only use.
        $this->assertSame(1, $shopVoucher->fresh()->used_count);

        try {
            $this->placeOrder($this->customer(), $product, input: ['voucher_code' => 'TOKO']);
            $this->fail('A max_uses=1 voucher must not be usable by a second pending order.');
        } catch (ShopException) {
        }

        // Paying does not count it twice.
        app(ShopPaymentService::class)->startDuitku($first);
        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($first->order_number.'-1', 100000))->assertOk();
        $this->assertSame(1, $shopVoucher->fresh()->used_count);
    }

    public function test_cancelled_or_expired_orders_give_the_voucher_use_back(): void
    {
        $this->shopSettings();
        $voucher = Voucher::create(['code' => 'SEKALI', 'type' => 'fixed', 'value' => 1000, 'is_active' => true, 'scope' => 'all', 'used_count' => 0, 'max_uses' => 1]);
        $product = $this->digitalProduct();

        $cancelled = $this->placeOrder($this->customer(), $product, input: ['voucher_code' => 'SEKALI']);
        app(OrderLifecycle::class)->cancel($cancelled);
        $this->assertSame(0, $voucher->fresh()->used_count);

        $expiring = $this->placeOrder($this->customer(), $product, input: ['voucher_code' => 'SEKALI']);
        app(ShopPaymentService::class)->startDuitku($expiring);
        $this->travel(2)->days();
        $this->artisan('shop:expire-orders');
        $this->assertSame(0, $voucher->fresh()->used_count);

        // Someone else takes the freed use; the late payment on the expired order is flagged.
        $this->placeOrder($this->customer(), $product, input: ['voucher_code' => 'SEKALI']);
        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($expiring->order_number.'-1', $expiring->total_amount))->assertOk();

        $this->assertTrue($expiring->fresh()->needs_attention);
        $this->assertStringContainsString('kuota voucher', $expiring->fresh()->attention_reason);
    }

    public function test_physical_products_stay_hidden_until_enabled(): void
    {
        $settings = $this->shopSettings();
        $product = $this->digitalProduct(['type' => Product::PHYSICAL, 'digital_delivery' => null, 'name_id' => 'Kaos', 'name_en' => 'Shirt']);

        $this->get(route('shop.show', $product->slug))->assertNotFound();
        $this->get('/shop')->assertOk()->assertDontSee('Kaos');

        $settings->update(['shop_physical_enabled' => true]);

        $this->get(route('shop.show', $product->slug))->assertOk()->assertSee('Kaos');
    }

    public function test_shop_is_hidden_from_visitors_until_switched_on(): void
    {
        $this->shopSettings(['shop_enabled' => false]);
        $product = $this->digitalProduct();

        $this->get('/shop')->assertNotFound();
        $this->actingAs($this->admin())->get('/shop')->assertOk()->assertSee($product->name_id);
    }

    public function test_ppn_split_is_tax_inclusive(): void
    {
        $this->assertSame(['base' => 100000, 'tax' => 11000], ShopTax::split(111000, 11));
        $this->assertSame(['base' => 50000, 'tax' => 0], ShopTax::split(50000, 0));
    }
}
