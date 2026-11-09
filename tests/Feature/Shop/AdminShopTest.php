<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\ShopOrder;
use App\Services\Shop\OrderLifecycle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminShopTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->duitkuConfig();
        $this->fakeDuitku();
        $this->shopSettings();
        Storage::fake('local');
        Storage::fake('public');
    }

    private function variantPayload(array $overrides = []): array
    {
        return $overrides + [
            'type' => 'digital',
            'name_id' => 'UI Kit Dashboard',
            'name_en' => 'Dashboard UI Kit',
            'digital_delivery' => 'file',
            'max_downloads' => 5,
            'is_active' => '1',
            'has_variants' => '1',
            'options' => [
                ['name_id' => 'Lisensi', 'name_en' => 'License', 'values' => [['value_id' => 'Personal', 'value_en' => 'Personal'], ['value_id' => 'Komersial', 'value_en' => 'Commercial']]],
            ],
            'variants' => [
                ['key' => 'Personal', 'price' => 150000, 'sku' => 'UI-P', 'is_active' => '1'],
                ['key' => 'Komersial', 'price' => 450000, 'sku' => 'UI-K', 'is_active' => '1'],
            ],
        ];
    }

    public function test_only_admins_reach_shop_admin_pages(): void
    {
        $this->get('/dashboard/shop/orders')->assertRedirect('/login');
        $this->actingAs($this->customer())->get('/dashboard/shop/products')->assertForbidden();
    }

    public function test_admin_creates_a_product_with_variants_then_switches_it_to_a_single_price(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/dashboard/shop/products', $this->variantPayload() + [
            'images' => [UploadedFile::fake()->image('cover.jpg')],
        ])->assertRedirect();

        $product = Product::with('variants.optionValues', 'options.values')->firstOrFail();
        $this->assertTrue($product->has_variants);
        $this->assertSame(150000, $product->min_price);
        $this->assertSame(450000, $product->max_price);
        $this->assertSame(['Personal', 'Komersial'], $product->variants->pluck('title_id')->all());
        $this->assertSame('Commercial', $product->variants[1]->title_en);
        $this->assertCount(1, $product->images);

        // Edit: drop "Personal", keep "Komersial" (same row id) and add "Agensi".
        $komersial = $product->variants->firstWhere('title_id', 'Komersial');
        $payload = $this->variantPayload([
            'options' => [['name_id' => 'Lisensi', 'values' => [['value_id' => 'Komersial'], ['value_id' => 'Agensi']]]],
            'variants' => [
                ['key' => 'Komersial', 'id' => $komersial->id, 'price' => 500000, 'sku' => 'UI-K', 'is_active' => '1'],
                ['key' => 'Agensi', 'price' => 900000, 'sku' => 'UI-P', 'is_active' => '1'], // reuses the retired SKU
            ],
        ]);
        $this->actingAs($admin)->patch("/dashboard/shop/products/{$product->id}", $payload)->assertRedirect()->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertSame(['Komersial', 'Agensi'], $product->variants()->pluck('title_id')->all());
        $this->assertSame(500000, $komersial->fresh()->price);
        $this->assertSame(1, ProductVariant::onlyTrashed()->count());

        // Switch to a single price: one hidden default variant remains.
        $this->actingAs($admin)->patch("/dashboard/shop/products/{$product->id}", [
            'type' => 'digital', 'name_id' => 'UI Kit Dashboard', 'digital_delivery' => 'file', 'is_active' => '1',
            'price' => 250000, 'sku' => 'UI-K',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertFalse($product->has_variants);
        $this->assertSame(1, $product->variants()->count());
        $this->assertTrue($product->variants()->first()->is_default);
        $this->assertSame(250000, $product->min_price);
        $this->assertSame(0, $product->options()->count());
    }

    public function test_variant_page_renders_and_buys_the_chosen_variant(): void
    {
        $this->actingAs($this->admin())->post('/dashboard/shop/products', $this->variantPayload());
        $product = Product::firstOrFail();
        $komersial = $product->variants()->where('title_id', 'Komersial')->firstOrFail();
        $user = $this->customer();

        $this->get(route('shop.show', $product->slug))->assertOk()->assertSee('Lisensi')->assertSee('Komersial');

        $this->actingAs($user)->post('/cart/items', ['variant_id' => $komersial->id])->assertSessionHas('success');
        $this->actingAs($user)->get('/cart')->assertOk()->assertSee('Komersial')->assertSee('450.000');
    }

    public function test_the_create_form_accepts_digital_files_and_license_keys(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/dashboard/shop/products/create')->assertOk()->assertSee('digital_files[]', false);

        $this->actingAs($admin)->post('/dashboard/shop/products', [
            'type' => 'digital', 'name_id' => 'E-book Kopi', 'digital_delivery' => 'file', 'price' => 50000, 'is_active' => '1',
            'digital_files' => [UploadedFile::fake()->create('ebook.pdf', 100, 'application/pdf')],
        ])->assertRedirect()->assertSessionHas('success', fn ($m) => str_contains($m, '1 file diunggah'));

        $file = Product::where('name_id', 'E-book Kopi')->firstOrFail()->files()->firstOrFail();
        Storage::disk('local')->assertExists($file->path);

        $this->actingAs($admin)->post('/dashboard/shop/products', [
            'type' => 'digital', 'name_id' => 'Lisensi App', 'digital_delivery' => 'license_key', 'price' => 50000, 'is_active' => '1',
            'license_keys' => "K-1\nK-2",
        ])->assertRedirect();

        $this->assertSame(2, Product::where('name_id', 'Lisensi App')->firstOrFail()->licenseKeys()->count());
    }

    public function test_several_images_can_be_uploaded_and_any_of_them_made_the_thumbnail(): void
    {
        $admin = $this->admin();
        $base = ['type' => 'digital', 'name_id' => 'Kit Foto', 'digital_delivery' => 'file', 'price' => 10000, 'is_active' => '1'];

        $this->actingAs($admin)->post('/dashboard/shop/products', $base + [
            'images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg'), UploadedFile::fake()->image('c.jpg')],
            'thumbnail' => 'new:2',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $product = Product::firstOrFail();
        $images = $product->images()->get();
        $this->assertCount(3, $images);
        $third = $images->firstWhere('sort_order', 0);
        $this->assertStringContainsString('shop/products', $third->path);
        $this->assertSame($third->id, $product->fresh()->images->first()->id);

        // Pick an existing image, and add one more without changing the thumbnail choice.
        $target = $images->firstWhere('sort_order', 2);
        $this->actingAs($admin)->patch("/dashboard/shop/products/{$product->id}", $base + [
            'thumbnail' => $target->id,
            'images' => [UploadedFile::fake()->image('d.jpg')],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $ordered = $product->fresh()->images;
        $this->assertCount(4, $ordered);
        $this->assertSame($target->id, $ordered->first()->id);
        $this->assertSame(range(0, 3), $ordered->pluck('sort_order')->all());

        // The thumbnail is what the shop shows on the card and as the main image.
        $this->get('/shop')->assertOk()->assertSee($ordered->first()->media_url, false);

        // A thumbnail id from another product is ignored.
        $other = $this->digitalProduct();
        $foreign = $other->images()->create(['path' => 'x.jpg', 'sort_order' => 0]);
        $this->actingAs($admin)->patch("/dashboard/shop/products/{$product->id}", $base + ['thumbnail' => $foreign->id])->assertRedirect();
        $this->assertSame($target->id, $product->fresh()->images->first()->id);
    }

    public function test_admin_uploads_files_and_license_keys(): void
    {
        $admin = $this->admin();
        $product = $this->digitalProduct(['digital_delivery' => Product::DELIVERY_LICENSE]);

        $this->actingAs($admin)->post(route('dashboard.shop.products.keys.store', $product), ['keys' => "AAA-1\nBBB-2\nAAA-1"])->assertSessionHas('success');
        $this->actingAs($admin)->post(route('dashboard.shop.products.keys.store', $product), ['keys' => "BBB-2\nCCC-3"])->assertSessionHas('success');
        $this->assertSame(3, $product->licenseKeys()->count());

        $product->update(['digital_delivery' => Product::DELIVERY_FILE]);
        $this->actingAs($admin)->post(route('dashboard.shop.products.files.store', $product), ['file' => UploadedFile::fake()->create('kit.zip', 200, 'application/zip')])->assertSessionHas('success');

        $file = $product->files()->firstOrFail();
        Storage::disk('local')->assertExists($file->path);
        $this->assertStringStartsWith('shop/files/', $file->path);
    }

    public function test_all_admin_pages_render(): void
    {
        $admin = $this->admin();
        ProductCategory::create(['name_id' => 'Template', 'slug' => 'template']);
        $product = $this->digitalProduct(['digital_delivery' => Product::DELIVERY_LICENSE]);
        $order = $this->placeOrder($this->customer(), $product, ShopOrder::CHANNEL_MANUAL);
        app(OrderLifecycle::class)->markPaid($order, $order->payments()->first());

        foreach ([
            '/dashboard/shop/products', '/dashboard/shop/products/create', "/dashboard/shop/products/{$product->id}/edit",
            '/dashboard/shop/categories', '/dashboard/shop/orders', '/dashboard/shop/orders?tab=attention',
            "/dashboard/shop/orders/{$order->order_number}", '/dashboard/shop/reports', '/dashboard/shop/settings', '/dashboard',
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)->get("/dashboard/shop/orders/{$order->order_number}")->assertSee('PPN')->assertSee('DPP');
    }

    public function test_tax_report_csv_lists_paid_orders(): void
    {
        $order = $this->placeOrder($this->customer(), $this->digitalProduct(price: 111000), ShopOrder::CHANNEL_MANUAL);
        app(OrderLifecycle::class)->markPaid($order, $order->payments()->first());

        $csv = $this->actingAs($this->admin())->get('/dashboard/shop/reports/tax.csv')->assertOk()->streamedContent();

        $this->assertStringContainsString($order->order_number, $csv);
        $this->assertStringContainsString('100000', $csv);
        $this->assertStringContainsString('11000', $csv);
    }

    public function test_settings_are_saved(): void
    {
        $this->actingAs($this->admin())->post('/dashboard/shop/settings', [
            'shop_enabled' => '1', 'shop_tax_rate' => 12, 'shop_order_expiry_minutes' => 60, 'shop_manual_expiry_minutes' => 120,
            'shop_manual_unique_code' => '1',
        ])->assertSessionHas('success');

        $settings = \App\Models\SiteSetting::first();
        $this->assertTrue($settings->shop_enabled);
        $this->assertFalse($settings->shop_manual_payment_enabled);
        $this->assertTrue($settings->shop_manual_unique_code);
        $this->assertEquals(12, $settings->shop_tax_rate);
    }

    public function test_customer_pages_render(): void
    {
        $user = $this->customer();
        $product = $this->digitalProduct();
        $order = $this->placeOrder($user, $product, ShopOrder::CHANNEL_MANUAL);
        $this->actingAs($user)->post('/cart/items', ['variant_id' => $this->variantOf($product)->id]);

        foreach (['/shop', '/shop?q=Template&sort=price_asc', route('shop.show', $product->slug), '/cart', '/shop/checkout', '/my-purchases', '/my-downloads', '/my-addresses', route('shop.orders.show', $order)] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }

        $this->actingAs($user)->get('/shop/checkout')->assertDontSee('PPN');
        $this->actingAs($this->customer())->get(route('shop.orders.show', $order))->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertSee(route('shop.show', $product->slug));
    }
}
