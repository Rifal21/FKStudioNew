<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use App\Models\ProductFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopApiTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopSettings();
        Storage::fake('local');
        Storage::fake('public');
    }

    private function token(?User $user = null): string
    {
        $user ??= $this->admin();

        // Earlier API calls in the same test switch the default guard to sanctum; a real request starts fresh.
        $this->app['auth']->forgetGuards();
        $this->app['auth']->shouldUse('web');

        return $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->json('token');
    }

    private function api(string $token): self
    {
        return $this->withHeader('Authorization', 'Bearer '.$token);
    }

    public function test_only_super_admin_tokens_with_shop_ability_can_use_the_api(): void
    {
        $this->getJson('/api/admin/shop/products')->assertUnauthorized();

        $customer = $this->customer();
        $this->postJson('/api/auth/login', ['email' => $customer->email, 'password' => 'password'])->assertForbidden();

        $blogOnly = $this->admin()->createToken('old-blog-token', ['blog:manage'])->plainTextToken;
        $this->api($blogOnly)->getJson('/api/admin/shop/products')->assertForbidden();

        // A token stops working for the shop once its owner is no longer an admin.
        $admin = $this->admin();
        $token = $this->token($admin);
        $this->api($token)->getJson('/api/admin/shop/products')->assertOk();
        $admin->update(['role' => 'user']);
        $this->app['auth']->forgetGuards();
        $this->api($token)->getJson('/api/admin/shop/products')->assertForbidden();
    }

    public function test_create_a_digital_product_with_its_files_in_one_multipart_request(): void
    {
        $response = $this->api($this->token())->post('/api/admin/shop/products', [
            'type' => 'digital',
            'name_id' => 'Template Landing Page',
            'name_en' => 'Landing Page Template',
            'short_description_id' => 'Landing page siap pakai.',
            'digital_delivery' => 'file',
            'price' => 199000,
            'is_active' => '1',
            'images' => [UploadedFile::fake()->image('cover.jpg'), UploadedFile::fake()->image('second.jpg')],
            'thumbnail' => 'new:1',
            'digital_files' => [UploadedFile::fake()->create('template.zip', 300, 'application/zip'), UploadedFile::fake()->create('panduan.pdf', 50, 'application/pdf')],
        ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('data.name.id', 'Template Landing Page')
            ->assertJsonPath('data.price.min', 199000)
            ->assertJsonPath('data.digital.delivery', 'file')
            ->assertJsonCount(2, 'data.files')
            ->assertJsonCount(2, 'data.images')
            ->assertJsonPath('meta.attached.files', 2);

        $product = Product::firstOrFail();
        $this->assertTrue($product->is_active);
        $this->assertSame($response->json('data.thumbnail_id'), $response->json('data.images.0.id'));
        $this->assertSame(1, $product->images()->where('sort_order', '>', 0)->count());
        ProductFile::all()->each(function ($file) {
            Storage::disk('local')->assertExists($file->path);
            $this->assertStringStartsWith('shop/files/', $file->path);
        });
    }

    public function test_create_with_variants_as_json_then_patch_only_what_changes(): void
    {
        $token = $this->token();

        $id = $this->api($token)->postJson('/api/admin/shop/products', [
            'type' => 'digital',
            'name_id' => 'Plugin Kasir',
            'digital_delivery' => 'license_key',
            'has_variants' => true,
            'is_active' => true,
            'options' => [['name_id' => 'Lisensi', 'values' => [['value_id' => '1 Domain'], ['value_id' => '5 Domain']]]],
            'variants' => [
                ['key' => '1 Domain', 'price' => 250000, 'sku' => 'KSR-1'],
                ['key' => '5 Domain', 'price' => 900000, 'sku' => 'KSR-5'],
            ],
            'license_keys' => "KSR-AAA\nKSR-BBB\nKSR-AAA",
        ])->assertCreated()
            ->assertJsonPath('data.price.max', 900000)
            ->assertJsonPath('data.digital.license_keys.available', 2)
            ->assertJsonPath('meta.attached.keys_added', 2)
            ->assertJsonMissingPath('data.license_keys.0.key')
            ->json('data.id');

        $product = Product::findOrFail($id);
        $variantIds = $product->variants()->pluck('id')->all();

        $this->api($token)->patchJson("/api/admin/shop/products/{$id}", ['name_en' => 'POS Plugin', 'is_featured' => true])
            ->assertOk()
            ->assertJsonPath('data.name.en', 'POS Plugin')
            ->assertJsonPath('data.name.id', 'Plugin Kasir')
            ->assertJsonPath('data.is_featured', true)
            ->assertJsonPath('data.price.min', 250000);

        // Variants (and their ids) survive a partial update.
        $this->assertSame($variantIds, $product->variants()->pluck('id')->all());

        $this->api($token)->postJson("/api/admin/shop/products/{$id}/keys", ['keys' => ['KSR-CCC', 'KSR-BBB']])
            ->assertCreated()
            ->assertJsonPath('data.added', 1)
            ->assertJsonPath('data.available', 3);
    }

    public function test_upload_a_file_for_one_variant_and_delete_it(): void
    {
        $token = $this->token();
        $product = $this->digitalProduct();

        $fileId = $this->api($token)->post("/api/admin/shop/products/{$product->id}/files", [
            'file' => UploadedFile::fake()->create('bonus.zip', 10, 'application/zip'),
            'version_label' => 'v2',
        ], ['Accept' => 'application/json'])->assertCreated()->assertJsonPath('data.version_label', 'v2')->json('data.id');

        $path = ProductFile::findOrFail($fileId)->path;
        Storage::disk('local')->assertExists($path);

        $this->api($token)->deleteJson("/api/admin/shop/products/{$product->id}/files/{$fileId}")->assertOk();
        Storage::disk('local')->assertMissing($path);

        // Updating with files: multipart POST with _method=PATCH, other fields untouched.
        $this->api($token)->post("/api/admin/shop/products/{$product->id}", [
            '_method' => 'PATCH',
            'compare_at_price' => 150000,
            'digital_files' => [UploadedFile::fake()->create('v3.zip', 10, 'application/zip')],
        ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('meta.attached.files', 1)
            ->assertJsonPath('data.price.min', 111000)
            ->assertJsonPath('data.variants.0.compare_at_price', 150000);
    }

    public function test_validation_and_rule_errors_come_back_as_json(): void
    {
        $token = $this->token();

        $this->api($token)->post('/api/admin/shop/products', ['type' => 'digital'])
            ->assertStatus(422)->assertJsonValidationErrors(['name_id', 'price', 'digital_delivery']);

        $this->api($token)->post('/api/admin/shop/products', [
            'type' => 'digital', 'name_id' => 'X', 'digital_delivery' => 'file', 'price' => 1000,
            'digital_files' => [UploadedFile::fake()->create('evil.php', 1, 'application/x-php')],
        ])->assertStatus(422)->assertJsonValidationErrors(['digital_files.0']);

        $this->api($token)->postJson('/api/admin/shop/products', [
            'type' => 'digital', 'name_id' => 'X', 'digital_delivery' => 'link', 'access_url' => 'javascript:alert(1)', 'price' => 1000,
        ])->assertStatus(422)->assertJsonValidationErrors(['access_url']);

        $this->api($token)->postJson('/api/admin/shop/products', [
            'type' => 'digital', 'name_id' => 'X', 'digital_delivery' => 'file', 'has_variants' => true,
            'options' => [['name_id' => 'Ukuran', 'values' => [['value_id' => 'S']]]],
            'variants' => [['key' => 'XL', 'price' => 1000]],
        ])->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'tidak cocok'));
    }

    public function test_categories_can_be_listed_and_created(): void
    {
        $token = $this->token();

        $this->api($token)->postJson('/api/admin/shop/categories', ['name_id' => 'Aset Desain', 'name_en' => 'Design Assets'])
            ->assertCreated()->assertJsonPath('data.slug', 'design-assets');

        $this->api($token)->getJson('/api/admin/shop/categories')->assertOk()->assertJsonPath('data.0.name.id', 'Aset Desain');
    }
}
