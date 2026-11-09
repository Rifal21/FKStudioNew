<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShopOrder;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Shop\CartService;
use App\Services\Shop\CheckoutService;
use Illuminate\Support\Facades\Http;

trait BuildsShop
{
    protected function shopSettings(array $overrides = []): SiteSetting
    {
        return SiteSetting::create($overrides + [
            'site_name' => 'FKStudio',
            'shop_enabled' => true,
            'shop_physical_enabled' => false,
            'shop_tax_rate' => 11,
            'shop_manual_payment_enabled' => true,
            'payment_methods' => [['bank' => 'BCA', 'number' => '1234567890', 'name' => 'FKStudio']],
        ]);
    }

    protected function duitkuConfig(): void
    {
        config(['services.duitku' => ['merchant_code' => 'DTEST', 'api_key' => 'secret-key', 'sandbox' => true]]);
    }

    /** What the faked Duitku status API answers; tests may change it after fakeDuitku(). */
    protected string $duitkuStatus = '00';

    /** Fakes Duitku: inquiry succeeds, payment-method list is empty, status API answers $duitkuStatus. */
    protected function fakeDuitku(): void
    {
        Http::fake([
            '*/v2/inquiry' => Http::response(['statusCode' => '00', 'paymentUrl' => 'https://sandbox.duitku.com/pay/abc', 'reference' => 'REF123']),
            '*/getpaymentmethod' => Http::response(['paymentFee' => []]),
            '*/transactionStatus' => fn () => Http::response(['statusCode' => $this->duitkuStatus, 'amount' => '0']),
        ]);
    }

    protected function digitalProduct(array $attributes = [], int $price = 111000, ?int $stock = null): Product
    {
        $product = Product::create($attributes + [
            'type' => Product::DIGITAL,
            'name_id' => 'Template Toko Online',
            'name_en' => 'Online Store Template',
            'digital_delivery' => Product::DELIVERY_FILE,
            'max_downloads' => 5,
            'is_active' => true,
        ]);

        $product->variants()->create(['price' => $price, 'stock' => $stock, 'is_default' => true, 'is_active' => true]);
        $product->syncPriceRange();

        return $product->fresh('variants');
    }

    protected function variantOf(Product $product): ProductVariant
    {
        return $product->variants()->first();
    }

    protected function customer(): User
    {
        return User::factory()->create(['role' => 'user']);
    }

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    protected function placeOrder(User $user, Product $product, string $channel = ShopOrder::CHANNEL_DUITKU, int $quantity = 1, array $input = []): ShopOrder
    {
        app(CartService::class)->add($user, $this->variantOf($product), $quantity);

        return app(CheckoutService::class)->place($user, $input + ['channel' => $channel]);
    }

    protected function signedCallback(string $merchantOrderId, int $amount, string $resultCode = '00'): array
    {
        return [
            'merchantCode' => 'DTEST',
            'amount' => (string) $amount,
            'merchantOrderId' => $merchantOrderId,
            'resultCode' => $resultCode,
            'reference' => 'REF123',
            'paymentCode' => 'VC',
            'signature' => md5('DTEST'.$amount.$merchantOrderId.'secret-key'),
        ];
    }
}
