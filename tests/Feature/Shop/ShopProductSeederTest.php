<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductFile;
use App\Models\ProductLicenseKey;
use App\Models\ShopOrder;
use App\Services\Shop\OrderLifecycle;
use Database\Seeders\ShopProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopProductSeederTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    public function test_it_seeds_every_delivery_type_and_can_run_twice(): void
    {
        Storage::fake('local');
        $this->shopSettings();

        $this->seed(ShopProductSeeder::class);
        $this->seed(ShopProductSeeder::class);

        $this->assertSame(7, Product::count());
        $this->assertSame(4, ProductCategory::count());
        $this->assertSame(10, ProductLicenseKey::count());
        $this->assertSame(['file', 'license_key', 'link'], Product::distinct()->orderBy('digital_delivery')->pluck('digital_delivery')->all());

        $store = Product::where('slug', 'template-toko-online-umkm')->firstOrFail();
        $this->assertTrue($store->has_variants);
        $this->assertSame([499000, 899000], [$store->min_price, $store->max_price]);
        $this->assertSame(1, ProductFile::where('product_id', $store->id)->whereNotNull('product_variant_id')->count());

        ProductFile::all()->each(fn ($file) => Storage::disk('local')->assertExists($file->path));

        $this->assertNull(Product::where('slug', 'icon-pack-umkm')->value('max_downloads'));
        $this->assertSame('https://example.com/kelas-laravel-pemula', Product::where('slug', 'kelas-online-laravel-untuk-pemula')->first()->access_url);

        $this->get('/shop')->assertOk()->assertSee('Template Toko Online UMKM');
    }

    public function test_seeded_license_product_can_be_bought(): void
    {
        Storage::fake('local');
        $this->shopSettings();
        $this->seed(ShopProductSeeder::class);

        $user = $this->customer();
        $order = $this->placeOrder($user, Product::where('slug', 'plugin-invoice-whatsapp-laravel')->firstOrFail(), ShopOrder::CHANNEL_MANUAL);
        app(OrderLifecycle::class)->markPaid($order, $order->payments()->first());

        $this->actingAs($user)->get(route('user.downloads'))->assertOk()->assertSee('DEMO-');
    }
}
