<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductFile;
use App\Models\ProductLicenseKey;
use App\Services\Shop\ProductAssets;
use App\Services\Shop\ProductInput;
use App\Services\Shop\ProductSaver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Admin API for shop products: create and update products (with variants), upload
 * downloadable files and add license keys. Requires a Sanctum token with "shop:manage"
 * held by a super admin. Accepts JSON, or multipart/form-data when files are attached.
 */
class ShopProductApiController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'images', 'options.values', 'variants.optionValues', 'files'])
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($w) => $w->where('name_id', 'like', '%'.$request->search.'%')->orWhere('name_en', 'like', '%'.$request->search.'%')))
            ->when(in_array($request->type, [Product::DIGITAL, Product::PHYSICAL], true), fn ($q) => $q->where('type', $request->type))
            ->when($request->has('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy('sort_order')->latest()
            ->paginate(min(max((int) $request->get('per_page', 20), 1), 100));

        return ShopProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return new ShopProductResource($product);
    }

    public function store(Request $request, ProductSaver $saver, ProductAssets $assets): JsonResponse
    {
        $data = $request->validate(ProductInput::rules($request), ProductInput::messages());

        $product = $saver->save(new Product(), ProductInput::normalize($request, $data));
        $attached = $assets->attachFromRequest($product, $request);

        return (new ShopProductResource($product->fresh()))
            ->additional(['meta' => ['message' => 'Produk dibuat.', 'attached' => $attached]])
            ->response()
            ->setStatusCode(201);
    }

    /** Partial update: fields that are not sent keep their current value. Sending "variants" replaces all variants. */
    public function update(Request $request, Product $product, ProductSaver $saver, ProductAssets $assets)
    {
        $request->merge(array_replace(ProductInput::fromProduct($product), $request->except(['images', 'digital_files'])));

        $data = $request->validate(ProductInput::rules($request, $product), ProductInput::messages());

        $saver->save($product, ProductInput::normalize($request, $data));
        $attached = $assets->attachFromRequest($product->fresh(), $request);

        return (new ShopProductResource($product->fresh()))
            ->additional(['meta' => ['message' => 'Produk diperbarui.', 'attached' => $attached]]);
    }

    public function destroy(Product $product): JsonResponse
    {
        // Soft delete: past orders and buyers' downloads stay intact.
        $product->update(['is_active' => false]);
        $product->delete();

        return response()->json(['message' => 'Produk dihapus dari toko.']);
    }

    public function storeFile(Request $request, Product $product, ProductAssets $assets): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:512000|mimes:'.ProductAssets::FILE_EXTENSIONS,
            'variant_id' => ['nullable', Rule::exists('product_variants', 'id')->where('product_id', $product->id)->whereNull('deleted_at')],
            'version_label' => 'nullable|string|max:50',
        ], [
            'file.max' => 'Ukuran file maksimal 500 MB (cek juga upload_max_filesize dan post_max_size di server).',
            'file.mimes' => 'Tipe file tidak diizinkan.',
        ]);

        abort_unless($product->isDigital(), 422, 'File unduhan hanya untuk produk digital.');

        $file = $assets->storeFile($product, $request->file('file'), $request->variant_id, $request->version_label);

        return response()->json([
            'message' => 'File diunggah.',
            'data' => ['id' => $file->id, 'name' => $file->original_name, 'size' => $file->size, 'mime' => $file->mime, 'variant_id' => $file->product_variant_id, 'version_label' => $file->version_label],
        ], 201);
    }

    public function destroyFile(Product $product, ProductFile $file, ProductAssets $assets): JsonResponse
    {
        abort_unless($file->product_id === $product->id, 404);

        $assets->deleteFile($file);

        return response()->json(['message' => 'File dihapus.']);
    }

    public function storeKeys(Request $request, Product $product, ProductAssets $assets): JsonResponse
    {
        $request->validate([
            'keys' => 'required|array|min:1|max:5000',
            'keys.*' => 'required|string|max:500',
            'variant_id' => ['nullable', Rule::exists('product_variants', 'id')->where('product_id', $product->id)->whereNull('deleted_at')],
        ]);

        abort_unless($product->isDigital() && $product->digital_delivery === Product::DELIVERY_LICENSE, 422, 'Produk ini tidak memakai kunci lisensi.');

        $result = $assets->addKeys($product, implode("\n", array_map(fn ($k) => str_replace(["\r", "\n"], '', $k), $request->keys)), $request->variant_id);

        return response()->json([
            'message' => "{$result['added']} kunci lisensi ditambahkan.",
            'data' => $result + ['available' => $product->licenseKeys()->whereNull('assigned_at')->count()],
        ], 201);
    }

    public function destroyKey(Product $product, ProductLicenseKey $key): JsonResponse
    {
        abort_unless($key->product_id === $product->id, 404);
        abort_if($key->assigned_at !== null, 422, 'Kunci yang sudah dipakai pembeli tidak bisa dihapus.');

        $key->delete();

        return response()->json(['message' => 'Kunci lisensi dihapus.']);
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => ProductCategory::orderBy('sort_order')->orderBy('name_id')->get()
                ->map(fn ($c) => ['id' => $c->id, 'slug' => $c->slug, 'name' => ['id' => $c->name_id, 'en' => $c->name_en], 'is_active' => $c->is_active]),
        ]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: ($request->input('name_en') ?: (string) $request->input('name_id')))]);

        $data = $request->validate([
            'name_id' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'slug' => 'required|string|max:120|unique:product_categories,slug',
            'description_id' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:100000',
            'is_active' => 'nullable|boolean',
        ]);

        $category = ProductCategory::create(['is_active' => $request->boolean('is_active', true), 'sort_order' => (int) ($data['sort_order'] ?? 0)] + $data);

        return response()->json(['message' => 'Kategori dibuat.', 'data' => ['id' => $category->id, 'slug' => $category->slug]], 201);
    }
}
