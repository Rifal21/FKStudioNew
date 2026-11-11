<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductFile;
use App\Models\ProductLicenseKey;
use App\Models\SiteSetting;
use App\Services\Shop\ProductAssets;
use App\Services\Shop\ProductInput;
use App\Services\Shop\ProductSaver;
use App\Services\Shop\ShopException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'type' => (string) $request->query('type', ''),
            'status' => (string) $request->query('status', ''),
        ];

        $products = Product::with(['images', 'category', 'variants'])
            ->withCount(['licenseKeys as available_keys_count' => fn ($q) => $q->whereNull('assigned_at')])
            ->when($filters['q'] !== '', fn ($q) => $q->where(fn ($w) => $w->where('name_id', 'like', '%'.$filters['q'].'%')->orWhere('name_en', 'like', '%'.$filters['q'].'%')))
            ->when(in_array($filters['type'], [Product::DIGITAL, Product::PHYSICAL], true), fn ($q) => $q->where('type', $filters['type']))
            ->when($filters['status'] === 'active', fn ($q) => $q->where('is_active', true))
            ->when($filters['status'] === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('sort_order')->latest()
            ->paginate(20)->withQueryString();

        return view('dashboard.shop.products.index', [
            'products' => $products,
            'filters' => $filters,
            'settings' => SiteSetting::current(),
        ]);
    }

    public function create()
    {
        return $this->form(new Product([
            'type' => Product::DIGITAL,
            'digital_delivery' => Product::DELIVERY_FILE,
            'max_downloads' => 5,
            'is_active' => true,
        ]));
    }

    public function store(Request $request, ProductSaver $saver, ProductAssets $assets)
    {
        $data = $request->validate(ProductInput::rules($request), ProductInput::messages());

        try {
            $product = $saver->save(new Product(), ProductInput::normalize($request, $data));
            $attached = $assets->attachFromRequest($product, $request);
        } catch (ShopException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard.shop.products.edit', $product)
            ->with('success', 'Produk dibuat.'.$this->assetSummary($product, $attached));
    }

    /** Storefront page as buyers see it, available for inactive products too. */
    public function show(Product $product)
    {
        $product->load(['images', 'category', 'options.values']);

        $variants = fn () => $product->variants()->with('optionValues', 'image');
        $product->setRelation('variants', ($variants()->where('is_active', true)->get()->whenEmpty(fn () => $variants()->get())));

        $related = Product::visibleInShop(SiteSetting::current())
            ->whereKeyNot($product->id)
            ->when($product->product_category_id, fn ($q) => $q->where('product_category_id', $product->product_category_id))
            ->with(['images', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('is_featured')->latest()
            ->limit(4)
            ->get();

        return view('landing.shop.show', [
            'settings' => SiteSetting::current(),
            'product' => $product,
            'related' => $related,
            'variantData' => $product->storefrontData(),
            'preview' => true,
        ]);
    }

    /**
     * Everything stored for a product, as an HTML fragment for the slide-over in the product list.
     * Unlike show(), which is the storefront page, this is an admin summary: variants with stock,
     * digital files, key counts and SEO, and why the product might not be visible to buyers.
     */
    public function preview(Product $product)
    {
        $product->load(['images', 'category', 'options.values', 'variants.optionValues', 'files.variant']);

        $settings = SiteSetting::current();
        $hidden = array_values(array_filter([
            ! $settings->shop_enabled ? 'Toko belum diaktifkan, jadi semua produk disembunyikan dari pengunjung.' : null,
            ! $product->is_active ? 'Produk nonaktif.' : null,
            $product->isPhysical() && ! $settings->shop_physical_enabled ? 'Produk fisik belum diaktifkan di pengaturan toko.' : null,
            $product->variants->where('is_active', true)->isEmpty() ? 'Tidak ada varian aktif.' : null,
        ]));

        return view('dashboard.shop.products._preview', [
            'product' => $product,
            'hidden' => $hidden,
            'keys' => [
                'available' => $product->licenseKeys()->whereNull('assigned_at')->count(),
                'total' => $product->licenseKeys()->count(),
            ],
        ]);
    }

    public function edit(Product $product)
    {
        return $this->form($product);
    }

    public function update(Request $request, Product $product, ProductSaver $saver, ProductAssets $assets)
    {
        // TEMP-DEBUG (hapus setelah masalah "simpan tidak terjadi apa-apa" selesai)
        \Log::info('[product-update-debug] request diterima', ['product' => $product->id, 'fields' => count($request->all()), 'has_variants' => $request->boolean('has_variants')]);

        try {
            $data = $request->validate(ProductInput::rules($request, $product), ProductInput::messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::info('[product-update-debug] validasi gagal', $e->errors());
            throw $e;
        }

        $assets->updateImages(
            $product,
            (array) $request->input('delete_images', []),
            explode(',', (string) $request->input('image_order')),
            (array) $request->input('image_alt_id', []),
            (array) $request->input('image_alt_en', []),
        );

        try {
            $saver->save($product, ProductInput::normalize($request, $data));
            $attached = $assets->attachFromRequest($product->fresh(), $request);
        } catch (ShopException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard.shop.products.edit', $product)->with('success', 'Produk diperbarui.'.$this->assetSummary($product->fresh(), $attached));
    }

    public function destroy(Product $product)
    {
        // Soft delete: past orders keep their product link and buyers keep their downloads.
        $product->update(['is_active' => false]);
        $product->delete();

        return redirect()->route('dashboard.shop.products.index')->with('success', 'Produk dihapus dari toko. Riwayat pesanan dan akses pembeli tetap ada.');
    }

    public function duplicate(Product $product)
    {
        $copy = DB::transaction(function () use ($product) {
            $product->load('variants.optionValues', 'options.values', 'images');

            $copy = $product->replicate(['slug', 'min_price', 'max_price']);
            $copy->name_id = $product->name_id.' (salinan)';
            $copy->name_en = $product->name_en ? $product->name_en.' (copy)' : null;
            $copy->slug = Product::uniqueSlug(($product->name_en ?: $product->name_id).' copy');
            $copy->is_active = false;
            $copy->save();

            $valueMap = [];
            foreach ($product->options as $option) {
                $newOption = $copy->options()->create($option->only(['name_id', 'name_en', 'position']));
                foreach ($option->values as $value) {
                    $valueMap[$value->id] = $newOption->values()->create($value->only(['value_id', 'value_en', 'position']))->id;
                }
            }

            foreach ($product->images as $image) {
                $copy->images()->create($image->only(['path', 'alt_id', 'alt_en', 'sort_order']));
            }

            foreach ($product->variants as $variant) {
                $newVariant = $copy->variants()->create($variant->only(['title_id', 'title_en', 'price', 'compare_at_price', 'stock', 'weight_grams', 'is_default', 'is_active', 'position']));
                $newVariant->optionValues()->sync($variant->optionValues->pluck('id')->map(fn ($id) => $valueMap[$id])->all());
            }

            $copy->syncPriceRange();

            return $copy;
        });

        return redirect()->route('dashboard.shop.products.edit', $copy)->with('success', 'Produk diduplikasi (nonaktif). File digital dan kunci lisensi tidak ikut disalin.');
    }

    public function storeFile(Request $request, Product $product, ProductAssets $assets)
    {
        $request->validate([
            'file' => 'required|file|max:512000|mimes:'.ProductAssets::FILE_EXTENSIONS,
            'product_variant_id' => ['nullable', Rule::exists('product_variants', 'id')->where('product_id', $product->id)],
            'version_label' => 'nullable|string|max:50',
        ], [
            'file.max' => 'Ukuran file maksimal 500 MB (cek juga upload_max_filesize di server).',
            'file.mimes' => 'Tipe file tidak diizinkan.',
        ]);

        try {
            $assets->storeFile($product, $request->file('file'), $request->product_variant_id, $request->version_label);
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'File diunggah.');
    }

    /**
     * Lets an admin open a product file to check what buyers get. It is not a purchase: nothing is counted
     * and no entitlement is involved. PDFs, images, text, audio and video open in the browser; every other
     * type (and anything whose real content type is not on the list) is sent as a download.
     */
    public function showFile(Request $request, Product $product, ProductFile $file)
    {
        abort_unless($file->product_id === $product->id, 404);

        $disk = Storage::disk($file->disk);
        abort_unless($disk->exists($file->path), 404, 'File tidak ditemukan di penyimpanan.');

        $mime = $disk->mimeType($file->path) ?: null;
        $headers = ['X-Content-Type-Options' => 'nosniff'];

        if ($request->boolean('download') || ! in_array($mime, ProductFile::INLINE_MIMES, true)) {
            return $disk->download($file->path, $file->original_name, $headers);
        }

        return $disk->response($file->path, $file->original_name, $headers + ['Content-Type' => $mime], 'inline');
    }

    public function destroyFile(Product $product, ProductFile $file, ProductAssets $assets)
    {
        abort_unless($file->product_id === $product->id, 404);

        $assets->deleteFile($file);

        return back()->with('success', 'File dihapus.');
    }

    /** One key per line (pasted or from a .txt/.csv file); duplicates within the product are skipped. */
    public function storeKeys(Request $request, Product $product, ProductAssets $assets)
    {
        $request->validate([
            'keys' => 'nullable|string|max:200000',
            'keys_file' => 'nullable|file|mimes:txt,csv|max:2048',
            'product_variant_id' => ['nullable', Rule::exists('product_variants', 'id')->where('product_id', $product->id)],
        ]);

        $raw = (string) $request->input('keys');
        if ($request->hasFile('keys_file')) {
            $raw .= "\n".file_get_contents($request->file('keys_file')->getRealPath());
        }

        $result = $assets->addKeys($product, $raw, $request->product_variant_id);

        if ($result['added'] + $result['skipped'] === 0) {
            return back()->with('error', 'Tidak ada kunci lisensi yang terbaca.');
        }

        return back()->with('success', "{$result['added']} kunci lisensi ditambahkan.".($result['skipped'] ? " {$result['skipped']} dilewati karena sudah ada." : ''));
    }

    public function destroyKey(Product $product, ProductLicenseKey $key)
    {
        abort_unless($key->product_id === $product->id, 404);

        if ($key->assigned_at) {
            return back()->with('error', 'Kunci yang sudah dipakai pembeli tidak bisa dihapus.');
        }

        $key->delete();

        return back()->with('success', 'Kunci lisensi dihapus.');
    }

    private function form(Product $product)
    {
        $product->loadMissing(['images', 'options.values', 'variants.optionValues', 'files.variant']);

        $keys = $product->exists
            ? $product->licenseKeys()->with('variant')->latest()->limit(200)->get()
            : collect();

        return view('dashboard.shop.products.form', [
            'product' => $product,
            'categories' => ProductCategory::orderBy('sort_order')->orderBy('name_id')->get(),
            'keys' => $keys,
            'keyStats' => $product->exists ? [
                'available' => $product->licenseKeys()->whereNull('assigned_at')->count(),
                'assigned' => $product->licenseKeys()->whereNotNull('assigned_at')->count(),
            ] : ['available' => 0, 'assigned' => 0],
            'settings' => SiteSetting::current(),
        ]);
    }

    private function assetSummary(Product $product, array $attached): string
    {
        $parts = [];
        if ($attached['files']) {
            $parts[] = $attached['files'].' file diunggah';
        }
        if ($attached['keys_added'] || $attached['keys_skipped']) {
            $parts[] = $attached['keys_added'].' kunci lisensi ditambahkan'.($attached['keys_skipped'] ? ' ('.$attached['keys_skipped'].' duplikat dilewati)' : '');
        }

        $summary = $parts ? ' '.ucfirst(implode(', ', $parts)).'.' : '';

        if ($product->isDigital() && $product->digital_delivery === Product::DELIVERY_FILE && ! $product->files()->exists()) {
            $summary .= ' Produk ini belum punya file unduhan.';
        }

        return $summary;
    }
}
