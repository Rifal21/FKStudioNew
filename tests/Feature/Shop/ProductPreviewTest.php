<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** The admin slide-over that shows a product's stored content without leaving the product list. */
class ProductPreviewTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shopSettings();
    }

    private function product(array $attributes = [], int $price = 150000): Product
    {
        return $this->digitalProduct($attributes + [
            'name_id' => 'UI Kit Dashboard',
            'name_en' => 'Dashboard UI Kit',
            'short_description_id' => 'Komponen siap pakai.',
            'description_id' => '<p>Isi <strong>lengkap</strong> produk.</p>',
            'description_en' => '<p>Full product content.</p>',
            'meta_title' => 'UI Kit Terbaik',
        ], $price);
    }

    public function test_only_admins_can_open_the_preview(): void
    {
        $product = $this->product();

        $this->get(route('dashboard.shop.products.preview', $product))->assertRedirect('/login');
        $this->actingAs($this->customer())->get(route('dashboard.shop.products.preview', $product))->assertForbidden();
    }

    public function test_preview_shows_the_stored_content_variants_files_and_seo(): void
    {
        $product = $this->product();
        $product->variants()->update(['compare_at_price' => 200000, 'sku' => 'UI-001', 'stock' => 7]);
        $product->files()->create(['disk' => 'local', 'path' => 'shop/x', 'original_name' => 'ui-kit.zip', 'size' => 5 * 1024 * 1024, 'version_label' => 'v1.2']);

        $html = $this->actingAs($this->admin())->get(route('dashboard.shop.products.preview', $product))->assertOk()->getContent();

        $this->assertStringContainsString('UI Kit Dashboard', $html);
        $this->assertStringContainsString('Dashboard UI Kit', $html);
        $this->assertStringContainsString('/shop/'.$product->slug, $html);
        $this->assertStringContainsString('Komponen siap pakai.', $html);
        $this->assertStringContainsString('<strong>lengkap</strong>', $html);
        $this->assertStringContainsString('Full product content.', $html);
        $this->assertStringContainsString('Rp 150.000', $html);
        $this->assertStringContainsString('Rp 200.000', $html, 'the crossed-out price');
        $this->assertStringContainsString('UI-001', $html);
        $this->assertStringContainsString('ui-kit.zip', $html);
        $this->assertStringContainsString('5,0 MB', $html);
        $this->assertStringContainsString('v1.2', $html);
        $this->assertStringContainsString('UI Kit Terbaik', $html);
        $this->assertStringContainsString(route('dashboard.shop.products.edit', $product), $html);
        $this->assertStringContainsString(route('dashboard.shop.products.show', $product), $html);
        $this->assertStringNotContainsString('<html', $html, 'a fragment, not a page');
        $this->assertStringNotContainsString('Belum tampil di toko', $html);
    }

    public function test_preview_explains_why_a_product_is_not_visible_to_buyers(): void
    {
        $product = $this->product(['is_active' => false]);
        SiteSetting::firstOrFail()->update(['shop_enabled' => false]);

        $html = $this->actingAs($this->admin())->get(route('dashboard.shop.products.preview', $product))->assertOk()->getContent();

        $this->assertStringContainsString('Belum tampil di toko', $html);
        $this->assertStringContainsString('Produk nonaktif.', $html);
        $this->assertStringContainsString('Toko belum diaktifkan', $html);
        $this->assertStringContainsString('Nonaktif', $html);
    }

    public function test_preview_reports_license_key_stock_and_never_prints_a_key_or_access_url(): void
    {
        $product = $this->product(['digital_delivery' => Product::DELIVERY_LICENSE, 'access_url' => 'https://secret.example/access']);
        $product->licenseKeys()->create(['key' => 'SECRET-KEY-ONE']);
        $product->licenseKeys()->create(['key' => 'SECRET-KEY-TWO', 'assigned_at' => now()]);

        $html = $this->actingAs($this->admin())->get(route('dashboard.shop.products.preview', $product))->assertOk()->getContent();

        $this->assertStringContainsString('1 tersedia dari 2', $html);
        $this->assertStringNotContainsString('SECRET-KEY', $html);
        $this->assertStringNotContainsString('secret.example', $html);
    }

    public function test_preview_lists_every_variant_of_a_product_with_options(): void
    {
        $product = $this->product();
        $product->update(['has_variants' => true]);
        $option = $product->options()->create(['name_id' => 'Lisensi', 'position' => 0]);
        $option->values()->create(['value_id' => 'Personal', 'position' => 0]);
        $option->values()->create(['value_id' => 'Komersial', 'position' => 1]);
        $product->variants()->update(['title_id' => 'Personal']);
        $product->variants()->create(['title_id' => 'Komersial', 'price' => 450000, 'is_active' => false, 'position' => 1]);

        $html = $this->actingAs($this->admin())->get(route('dashboard.shop.products.preview', $product))->assertOk()->getContent();

        $this->assertStringContainsString('Harga &amp; varian', $html);
        $this->assertStringContainsString('Personal, Komersial', $html);
        $this->assertStringContainsString('Rp 450.000', $html);
        $this->assertStringContainsString('Nonaktif', $html, 'an inactive variant is shown but marked');
    }

    public function test_the_list_opens_the_slide_over_instead_of_navigating(): void
    {
        $product = $this->product();

        $page = $this->actingAs($this->admin())->get(route('dashboard.shop.products.index'))->assertOk()->getContent();

        $this->assertStringContainsString("show('".route('dashboard.shop.products.preview', $product)."')", $page);
        $this->assertStringContainsString('Pratinjau produk', $page);
        $this->assertStringContainsString('href="'.route('dashboard.shop.products.show', $product).'"', $page, 'the storefront link stays as the no-JS fallback');
    }

    private function storedFile(Product $product, string $name, string $contents)
    {
        Storage::fake('local');
        Storage::disk('local')->put('shop/'.$name, $contents);

        return $product->files()->create(['disk' => 'local', 'path' => 'shop/'.$name, 'original_name' => $name, 'size' => strlen($contents)]);
    }

    public function test_admin_opens_a_pdf_in_the_browser_and_can_still_force_a_download(): void
    {
        $product = $this->product();
        $file = $this->storedFile($product, 'panduan.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF");
        $url = route('dashboard.shop.products.files.show', [$product, $file]);

        $inline = $this->actingAs($this->admin())->get($url)->assertOk();
        $this->assertSame('application/pdf', $inline->headers->get('Content-Type'));
        $this->assertStringStartsWith('inline;', $inline->headers->get('Content-Disposition'));
        $this->assertStringContainsString('panduan.pdf', $inline->headers->get('Content-Disposition'));
        $this->assertSame('nosniff', $inline->headers->get('X-Content-Type-Options'));

        $download = $this->actingAs($this->admin())->get($url.'?download=1')->assertOk();
        $this->assertStringStartsWith('attachment;', $download->headers->get('Content-Disposition'));
    }

    public function test_files_that_could_run_in_the_admin_origin_are_only_ever_downloaded(): void
    {
        $product = $this->product();
        $admin = $this->admin();

        $cases = [
            'archive.zip' => "PK\x03\x04".str_repeat("\0", 40),
            'page.html' => '<html><script>alert(document.cookie)</script></html>',
            'disguised.pdf' => '<!DOCTYPE html><html><body><script>alert(1)</script></body></html>',
            'logo.svg' => '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>',
        ];

        foreach ($cases as $name => $contents) {
            $file = $this->storedFile($product, $name, $contents);
            $res = $this->actingAs($admin)->get(route('dashboard.shop.products.files.show', [$product, $file]))->assertOk();

            $this->assertStringStartsWith('attachment;', $res->headers->get('Content-Disposition'), "{$name} must not open inline");
        }
    }

    public function test_file_route_is_admin_only_and_scoped_to_the_products_own_files(): void
    {
        $product = $this->product();
        $other = $this->product(['name_id' => 'Produk Lain']);
        $file = $this->storedFile($product, 'panduan.pdf', '%PDF-1.4');
        $url = route('dashboard.shop.products.files.show', [$product, $file]);

        $this->get($url)->assertRedirect('/login');
        $this->actingAs($this->customer())->get($url)->assertForbidden();
        $this->actingAs($this->admin())->get(route('dashboard.shop.products.files.show', [$other, $file]))->assertNotFound();

        Storage::disk('local')->delete('shop/panduan.pdf');
        $this->actingAs($this->admin())->get($url)->assertNotFound();
    }

    public function test_preview_links_each_file_and_offers_open_only_for_viewable_types(): void
    {
        $product = $this->product();
        $pdf = $this->storedFile($product, 'panduan.pdf', '%PDF-1.4');
        $zip = $this->storedFile($product, 'source.zip', 'PK');
        $pdfUrl = route('dashboard.shop.products.files.show', [$product, $pdf]);
        $zipUrl = route('dashboard.shop.products.files.show', [$product, $zip]);

        $html = $this->actingAs($this->admin())->get(route('dashboard.shop.products.preview', $product))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.$pdfUrl.'"', $html);
        $this->assertStringContainsString('href="'.$pdfUrl.'?download=1"', $html);
        $this->assertStringContainsString('Buka panduan.pdf di tab baru', $html);
        $this->assertStringContainsString('href="'.$zipUrl.'?download=1"', $html);
        $this->assertStringNotContainsString('Buka source.zip di tab baru', $html, 'a zip is download-only');

        $edit = $this->actingAs($this->admin())->get(route('dashboard.shop.products.edit', $product))->assertOk()->getContent();
        $this->assertStringContainsString('href="'.$pdfUrl.'"', $edit);
    }
}
