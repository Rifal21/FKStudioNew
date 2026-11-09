<?php

namespace Tests\Feature\Shop;

use App\Models\DigitalEntitlement;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Models\User;
use App\Services\Shop\ShopPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuitkuCallbackTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->duitkuConfig();
        $this->fakeDuitku();
        $this->shopSettings();
    }

    private function packageOrder(array $attributes = []): PackageOrder
    {
        $user = User::factory()->create();
        $package = Package::create(['name_id' => 'Starter', 'name_en' => 'Starter', 'price' => 'Rp 1.000.000', 'slug' => 'starter', 'is_active' => true]);
        $invoice = Invoice::create(['invoice_number' => 'INV-1', 'client_name' => 'A', 'issue_date' => now(), 'status' => 'Unpaid', 'total_amount' => 1110000, 'discount' => 0, 'tax' => 0]);

        return PackageOrder::create($attributes + [
            'user_id' => $user->id, 'package_id' => $package->id, 'package_name' => 'Starter', 'package_price' => 'Rp 1.000.000',
            'website_name' => 'Toko', 'business_type' => 'Lainnya', 'payment_method' => 'Duitku', 'payment_scheme' => 'full',
            'dp_amount' => 0, 'remaining_balance' => 0, 'total_amount' => 1110000, 'status' => 'pending', 'work_status' => 'pending',
            'invoice_id' => $invoice->id,
        ]);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $payload = $this->signedCallback('whatever', 1000);
        $payload['signature'] = 'forged';

        $this->postJson('/api/payment/duitku/callback', $payload)->assertStatus(400);
    }

    public function test_package_order_is_paid_once_and_amount_is_checked(): void
    {
        $order = $this->packageOrder();

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($order->id, 999))->assertStatus(400);
        $this->assertSame('pending', $order->fresh()->status);

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($order->id, 1110000))->assertOk();
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('Paid', $order->invoice->fresh()->status);

        // A repeated callback changes nothing and still answers OK.
        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($order->id, 1110000))->assertOk();
        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_package_final_payment_completes_the_order(): void
    {
        $final = Invoice::create(['invoice_number' => 'INV-2', 'client_name' => 'A', 'issue_date' => now(), 'status' => 'Unpaid', 'total_amount' => 555000, 'discount' => 0, 'tax' => 0]);
        $order = $this->packageOrder(['payment_scheme' => 'dp', 'dp_amount' => 555000, 'remaining_balance' => 555000, 'status' => 'paid', 'final_invoice_id' => $final->id]);

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($order->id.'-final', 555000))->assertOk();

        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame('Paid', $final->fresh()->status);
    }

    public function test_shop_order_callback_settles_the_order_exactly_once(): void
    {
        $user = $this->customer();
        $order = $this->placeOrder($user, $this->digitalProduct());
        app(ShopPaymentService::class)->startDuitku($order);
        $payment = $order->payments()->first();

        $this->assertSame($order->order_number.'-1', $payment->merchant_order_id);

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($payment->merchant_order_id, $order->total_amount))->assertOk();
        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($payment->merchant_order_id, $order->total_amount))->assertOk();

        $order->refresh();
        $this->assertSame(ShopOrder::PAID, $order->payment_status);
        $this->assertSame(ShopOrder::FULFILLED, $order->fulfillment_status);
        $this->assertSame(ShopPayment::SUCCESS, $payment->fresh()->status);
        $this->assertSame('Paid', $order->invoice->status);
        $this->assertSame(1, DigitalEntitlement::where('user_id', $user->id)->count());
        $this->assertFalse($order->needs_attention);
    }

    public function test_shop_callback_with_wrong_amount_is_rejected(): void
    {
        $order = $this->placeOrder($this->customer(), $this->digitalProduct());
        app(ShopPaymentService::class)->startDuitku($order);
        $payment = $order->payments()->first();

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($payment->merchant_order_id, 1000))->assertStatus(400);

        $this->assertSame(ShopOrder::PENDING, $order->fresh()->payment_status);
    }

    public function test_shop_callback_is_refused_when_duitku_status_api_disagrees(): void
    {
        $this->duitkuStatus = '01';
        $order = $this->placeOrder($this->customer(), $this->digitalProduct());
        app(ShopPaymentService::class)->startDuitku($order);
        $payment = $order->payments()->first();

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($payment->merchant_order_id, $order->total_amount))->assertStatus(400);

        $this->assertSame(ShopOrder::PENDING, $order->fresh()->payment_status);
    }

    public function test_failed_result_marks_the_attempt_failed_without_paying(): void
    {
        $order = $this->placeOrder($this->customer(), $this->digitalProduct());
        app(ShopPaymentService::class)->startDuitku($order);
        $payment = $order->payments()->first();

        $this->postJson('/api/payment/duitku/callback', $this->signedCallback($payment->merchant_order_id, $order->total_amount, '01'))->assertOk();

        $this->assertSame(ShopPayment::FAILED, $payment->fresh()->status);
        $this->assertSame(ShopOrder::PENDING, $order->fresh()->payment_status);
    }
}
