<?php

namespace Tests\Feature\Shop;

use App\Jobs\SendShopNotification;
use App\Models\CartItem;
use App\Models\DigitalEntitlement;
use App\Models\Product;
use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Models\SiteSetting;
use App\Models\Voucher;
use App\Services\Shop\ShopNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/** An order that comes to Rp 0 (100% voucher or a price of 0) is paid the moment it is placed. */
class FreeOrderTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->duitkuConfig();
        $this->fakeDuitku();
        Queue::fake();
        $this->shopSettings();
    }

    /** Changes the one settings row made in setUp (a second row would make SiteSetting::first() a coin flip). */
    private function configure(array $attributes): void
    {
        SiteSetting::firstOrFail()->update($attributes);
    }

    private function voucher(string $code, float $percent = 100): Voucher
    {
        return Voucher::create(['code' => $code, 'type' => 'percent', 'value' => $percent, 'is_active' => true, 'scope' => 'shop']);
    }

    private function addToCart($user, Product $product, int $quantity = 1): void
    {
        $this->actingAs($user)->post('/cart/items', ['variant_id' => $this->variantOf($product)->id, 'quantity' => $quantity])->assertRedirect();
    }

    private function checkout($user, array $data = [])
    {
        return $this->actingAs($user)->post('/shop/checkout', $data + ['agree' => '1']);
    }

    public function test_a_full_discount_voucher_settles_the_order_and_grants_the_product_at_once(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(stock: 5, price: 150000);
        $this->voucher('GRATIS100');
        $this->addToCart($user, $product);

        $response = $this->checkout($user, ['voucher_code' => 'GRATIS100']);

        $order = ShopOrder::firstOrFail();
        $response->assertRedirect(route('shop.orders.show', $order))->assertSessionHas('success');

        $this->assertSame(0, $order->total_amount);
        $this->assertSame(150000, $order->discount_amount);
        $this->assertSame(ShopOrder::PAID, $order->payment_status);
        $this->assertSame(ShopOrder::CHANNEL_FREE, $order->payment_channel);
        $this->assertSame(ShopOrder::FULFILLED, $order->fulfillment_status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(0, $order->unique_code);

        $payment = $order->payments()->firstOrFail();
        $this->assertSame([ShopOrder::CHANNEL_FREE, 0, ShopPayment::SUCCESS], [$payment->channel, $payment->amount, $payment->status]);
        $this->assertSame('Paid', $order->invoice->status);

        // Access, stock, voucher and cart behave as for any paid order.
        $this->assertSame(1, DigitalEntitlement::where('user_id', $user->id)->count());
        $this->assertSame(4, $this->variantOf($product)->fresh()->stock);
        $this->assertSame(1, Voucher::where('code', 'GRATIS100')->value('used_count'));
        $this->assertSame(0, CartItem::count());
        Http::assertNothingSent();

        $this->actingAs($user)->get(route('shop.orders.show', $order))->assertOk()->assertSee('Pesanan berhasil')->assertDontSee('Pembayaran diterima');
        $this->actingAs($user)->get(route('user.downloads'))->assertOk();
    }

    public function test_a_product_priced_at_zero_needs_no_voucher_and_no_payment_method(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(['name_id' => 'Template Gratis'], price: 0);
        $this->addToCart($user, $product);

        $this->checkout($user)->assertSessionHasNoErrors()->assertSessionMissing('error');

        $order = ShopOrder::firstOrFail();
        $this->assertSame([0, ShopOrder::PAID, ShopOrder::CHANNEL_FREE], [$order->total_amount, $order->payment_status, $order->payment_channel]);
        $this->assertSame(1, DigitalEntitlement::count());
        $this->assertSame(0, $order->tax_amount);
        Http::assertNothingSent();
    }

    public function test_a_chosen_payment_method_is_ignored_and_no_unique_code_is_added_to_a_free_order(): void
    {
        $this->configure(['shop_manual_unique_code' => true]);
        $user = $this->customer();
        $product = $this->digitalProduct(price: 50000);
        $this->voucher('GRATIS100');
        $this->addToCart($user, $product);

        $this->checkout($user, ['payment' => 'manual', 'voucher_code' => 'GRATIS100']);

        $order = ShopOrder::firstOrFail();
        $this->assertSame([0, 0, ShopOrder::PAID, ShopOrder::CHANNEL_FREE], [$order->total_amount, $order->unique_code, $order->payment_status, $order->payment_channel]);
        $this->assertSame(1, $order->payments()->count(), 'no manual-transfer payment is created');
    }

    public function test_a_license_key_product_gets_its_key_immediately(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(['digital_delivery' => Product::DELIVERY_LICENSE], price: 0);
        $product->licenseKeys()->create(['key' => 'FREE-KEY-1']);
        $this->addToCart($user, $product);

        $this->checkout($user);

        $this->assertSame('FREE-KEY-1', DigitalEntitlement::with('licenseKey')->firstOrFail()->licenseKey->key);
        $this->assertFalse(ShopOrder::firstOrFail()->needs_attention);
    }

    public function test_running_out_of_license_keys_still_settles_the_order_but_flags_it_for_the_admin(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(['digital_delivery' => Product::DELIVERY_LICENSE], price: 0);
        $this->addToCart($user, $product);

        $this->checkout($user);

        $order = ShopOrder::firstOrFail();
        $this->assertSame(ShopOrder::PAID, $order->payment_status);
        $this->assertTrue($order->needs_attention);
        $this->assertStringContainsString('Kunci lisensi habis', $order->attention_reason);
    }

    public function test_a_free_order_sends_the_paid_message_not_the_please_pay_one(): void
    {
        $user = $this->customer();
        $this->addToCart($user, $this->digitalProduct(price: 0));

        $this->checkout($user);

        Queue::assertPushed(SendShopNotification::class, fn ($job) => $job->event === ShopNotifier::PAID);
        Queue::assertNotPushed(SendShopNotification::class, fn ($job) => $job->event === ShopNotifier::PLACED);

        $order = ShopOrder::firstOrFail();
        $job = new SendShopNotification($order->id, ShopNotifier::PAID);
        $messages = (new \ReflectionMethod($job, 'messages'));
        $messages->setAccessible(true);
        [$customer, $admin] = $messages->invoke($job, $order->load('items'), SiteSetting::current());

        $this->assertSame("Pesanan {$order->order_number} berhasil", $customer['subject']);
        $this->assertStringContainsString('Tidak ada pembayaran', $customer['lines'][0]);
        $this->assertStringNotContainsString('Rp 0', $customer['lines'][0]);
        $this->assertStringContainsString('Pesanan gratis baru', $admin);
    }

    public function test_a_partial_voucher_still_needs_a_payment_method_and_nothing_is_left_behind(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(stock: 5, price: 100000);
        $this->voucher('HEMAT50', 50);
        $this->addToCart($user, $product);

        $this->checkout($user, ['voucher_code' => 'HEMAT50'])->assertSessionHas('error', 'Pilih metode pembayaran.');

        $this->assertSame(0, ShopOrder::count());
        $this->assertSame(5, $this->variantOf($product)->fresh()->stock, 'stock reservation rolled back');
        $this->assertSame(0, Voucher::where('code', 'HEMAT50')->value('used_count'), 'voucher use rolled back');
        $this->assertSame(1, CartItem::count(), 'the cart is kept');
    }

    public function test_a_paid_order_without_a_payment_method_is_still_refused(): void
    {
        $user = $this->customer();
        $this->addToCart($user, $this->digitalProduct(price: 25000));

        $this->checkout($user)->assertSessionHas('error', 'Pilih metode pembayaran.');

        $this->assertSame(0, ShopOrder::count());
    }

    public function test_a_shipping_fee_keeps_an_otherwise_free_physical_order_payable(): void
    {
        $this->configure(['shop_physical_enabled' => true, 'shop_flat_shipping_cost' => 20000]);
        $user = $this->customer();
        $address = $user->addresses()->create(['recipient_name' => 'Budi', 'phone' => '0812', 'province' => 'Jawa Barat', 'city' => 'Bandung', 'address_line' => 'Jl. Contoh 1', 'is_default' => true]);
        $product = $this->digitalProduct(['type' => Product::PHYSICAL, 'digital_delivery' => null], price: 0);
        $this->addToCart($user, $product);

        $this->checkout($user, ['address_id' => $address->id, 'shipping_option' => 'flat:standard'])->assertSessionHas('error', 'Pilih metode pembayaran.');
        $this->checkout($user, ['address_id' => $address->id, 'shipping_option' => 'flat:standard', 'payment' => 'manual']);

        $order = ShopOrder::firstOrFail();
        $this->assertSame(20000, $order->total_amount);
        $this->assertSame(ShopOrder::PENDING, $order->payment_status);
        $this->assertSame(ShopOrder::CHANNEL_MANUAL, $order->payment_channel);
    }

    public function test_a_free_physical_order_goes_to_processing_for_the_admin_to_ship(): void
    {
        $this->configure(['shop_physical_enabled' => true, 'shop_flat_shipping_cost' => 0]);
        $user = $this->customer();
        $address = $user->addresses()->create(['recipient_name' => 'Budi', 'phone' => '0812', 'province' => 'Jawa Barat', 'city' => 'Bandung', 'address_line' => 'Jl. Contoh 1', 'is_default' => true]);
        $product = $this->digitalProduct(['type' => Product::PHYSICAL, 'digital_delivery' => null], price: 0);
        $this->addToCart($user, $product);

        $this->checkout($user, ['address_id' => $address->id, 'shipping_option' => 'flat:standard']);

        $order = ShopOrder::firstOrFail();
        $this->assertSame([0, ShopOrder::PAID, ShopOrder::PROCESSING], [$order->total_amount, $order->payment_status, $order->fulfillment_status]);
    }

    public function test_the_checkout_page_hides_payment_for_a_free_cart_and_skips_the_payment_gateway(): void
    {
        $user = $this->customer();
        $this->addToCart($user, $this->digitalProduct(price: 0));

        $this->actingAs($user)->get('/shop/checkout')->assertOk()->assertSee('Tidak perlu membayar')->assertSee('Dapatkan sekarang');

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'getpaymentmethod'));
    }

    public function test_admin_sees_the_order_as_free(): void
    {
        $user = $this->customer();
        $this->addToCart($user, $this->digitalProduct(price: 0));
        $this->checkout($user);
        $order = ShopOrder::firstOrFail();

        $admin = $this->admin();
        $this->actingAs($admin)->get('/dashboard/shop/orders')->assertOk()->assertSee('Gratis (Rp 0)');
        $this->actingAs($admin)->get(route('dashboard.shop.orders.show', $order))->assertOk()->assertSee('Gratis (Rp 0)');
    }
}
