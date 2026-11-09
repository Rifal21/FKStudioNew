<?php

namespace Tests\Feature\Shop;

use App\Models\DigitalEntitlement;
use App\Models\Product;
use App\Models\ProductLicenseKey;
use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Services\Shop\OrderLifecycle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FulfillmentTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->duitkuConfig();
        $this->fakeDuitku();
        $this->shopSettings();
        Storage::fake('local');
    }

    public function test_manual_transfer_flow_proof_reject_and_approve(): void
    {
        $user = $this->customer();
        $admin = $this->admin();
        $order = $this->placeOrder($user, $this->digitalProduct(), ShopOrder::CHANNEL_MANUAL);

        $this->actingAs($user)->post(route('shop.orders.proof', $order), [
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
            'sender_name' => 'Budi',
            'sender_bank' => 'BCA',
        ])->assertRedirect();

        $order->refresh();
        $payment = $order->payments()->first();
        $this->assertSame(ShopOrder::AWAITING_VERIFICATION, $order->payment_status);
        $this->assertSame(ShopPayment::SUBMITTED, $payment->status);
        Storage::disk('local')->assertExists($payment->proof_path);

        // Only admins can see the proof.
        $this->actingAs($user)->get(route('dashboard.shop.orders.proof', [$order, $payment]))->assertForbidden();
        $this->actingAs($admin)->get(route('dashboard.shop.orders.proof', [$order, $payment]))->assertOk();

        $this->actingAs($admin)->post(route('dashboard.shop.orders.reject', [$order, $payment]), ['reason' => 'Nominal kurang'])->assertRedirect();
        $this->assertSame(ShopOrder::PENDING, $order->fresh()->payment_status);
        $this->actingAs($user)->get(route('shop.orders.show', $order))->assertSee('Nominal kurang');

        $this->actingAs($user)->post(route('shop.orders.proof', $order), [
            'proof' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
            'sender_name' => 'Budi',
            'sender_bank' => 'BCA',
        ])->assertRedirect();

        $second = $order->payments()->where('status', ShopPayment::SUBMITTED)->firstOrFail();
        $this->assertNotSame($payment->id, $second->id);

        $this->actingAs($admin)->post(route('dashboard.shop.orders.approve', [$order, $second]))->assertRedirect();

        $order->refresh();
        $this->assertSame(ShopOrder::PAID, $order->payment_status);
        $this->assertSame($admin->id, $second->fresh()->verified_by);
        $this->assertSame(1, DigitalEntitlement::where('user_id', $user->id)->count());

        // Approving twice does nothing more.
        $this->actingAs($admin)->post(route('dashboard.shop.orders.approve', [$order, $second]))->assertSessionHas('error');
        $this->assertSame(1, DigitalEntitlement::count());
    }

    public function test_downloads_are_owner_only_counted_and_limited(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(['max_downloads' => 2]);
        Storage::disk('local')->put('shop/files/x/template.zip', 'zip-bytes');
        $file = $product->files()->create(['disk' => 'local', 'path' => 'shop/files/x/template.zip', 'original_name' => 'template.zip', 'size' => 9]);

        $order = $this->placeOrder($user, $product, ShopOrder::CHANNEL_MANUAL);
        $payment = $order->payments()->first();
        app(OrderLifecycle::class)->markPaid($order, $payment);
        $entitlement = DigitalEntitlement::firstOrFail();

        $this->actingAs($this->customer())->get(route('user.downloads.get', [$entitlement, $file]))->assertNotFound();

        $redirect = $this->actingAs($user)->get(route('user.downloads.get', [$entitlement, $file]));
        $redirect->assertRedirect();
        $this->assertStringContainsString('signature=', $redirect->headers->get('Location'));

        $this->actingAs($user)->get($redirect->headers->get('Location'))->assertOk()->assertDownload('template.zip');

        // The signed link only works with a valid signature.
        $this->actingAs($user)->get(route('user.downloads.file', [$entitlement, $file]))->assertForbidden();

        $this->actingAs($user)->get(route('user.downloads.get', [$entitlement, $file]))->assertRedirect();
        $this->actingAs($user)->get(route('user.downloads.get', [$entitlement, $file]))->assertSessionHas('error');
        $this->assertSame(2, $entitlement->fresh()->download_count);

        $this->actingAs($user)->get(route('user.downloads'))->assertOk()->assertSee('template.zip');
    }

    public function test_each_unit_gets_its_own_license_key_and_missing_keys_flag_the_order(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(['digital_delivery' => Product::DELIVERY_LICENSE]);
        $product->licenseKeys()->create(['key' => 'KEY-ONE']);
        $product->licenseKeys()->create(['key' => 'KEY-TWO']);

        $order = $this->placeOrder($user, $product, ShopOrder::CHANNEL_MANUAL, quantity: 3);
        app(OrderLifecycle::class)->markPaid($order, $order->payments()->first());

        $order->refresh();
        $keys = DigitalEntitlement::with('licenseKey')->get()->pluck('licenseKey.key')->filter()->sort()->values()->all();
        $this->assertSame(['KEY-ONE', 'KEY-TWO'], $keys);
        $this->assertSame(3, DigitalEntitlement::count());
        $this->assertTrue($order->needs_attention);
        $this->assertStringContainsString('Kunci lisensi habis', $order->attention_reason);

        // Admin adds a key and re-sends access: the gap is filled.
        $product->licenseKeys()->create(['key' => 'KEY-THREE']);
        $this->actingAs($this->admin())->post(route('dashboard.shop.orders.resend', $order))->assertSessionHas('success');

        $this->assertSame(3, DigitalEntitlement::whereNotNull('product_license_key_id')->count());
        $this->assertSame(0, ProductLicenseKey::whereNull('assigned_at')->count());
        $this->assertFalse($order->fresh()->needs_attention);

        $this->actingAs($user)->get(route('user.downloads'))->assertSee('KEY-THREE');
    }

    public function test_refund_revokes_access_and_can_restock(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct(stock: 1);
        $order = $this->placeOrder($user, $product, ShopOrder::CHANNEL_MANUAL);
        app(OrderLifecycle::class)->markPaid($order, $order->payments()->first());

        $this->actingAs($this->admin())->post(route('dashboard.shop.orders.refund', $order), ['confirm' => '1', 'restock' => '1'])->assertSessionHas('success');

        $this->assertSame(ShopOrder::REFUNDED, $order->fresh()->payment_status);
        $this->assertFalse(DigitalEntitlement::first()->isActive());
        $this->assertSame(1, $this->variantOf($product)->fresh()->stock);
    }
}
