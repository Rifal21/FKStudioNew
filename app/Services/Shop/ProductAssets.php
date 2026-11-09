<?php

namespace App\Services\Shop;

use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Traits\NextcloudStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Product images, downloadable files and license keys. Shared by the admin form and the admin API. */
class ProductAssets
{
    use NextcloudStorage;

    /** Downloadable files live on the private disk and are only streamed through signed links. */
    public const FILE_DISK = 'local';

    public const FILE_EXTENSIONS = 'zip,rar,7z,pdf,epub,mobi,psd,ai,fig,sketch,xd,png,jpg,jpeg,mp4,mp3,wav,txt,csv,xlsx,docx,pptx,json';

    /**
     * Attaches what came with a product create/update request: new images, downloadable
     * files (available to every variant) and pasted license keys.
     *
     * @return array{files: int, keys_added: int, keys_skipped: int}
     */
    public function attachFromRequest(Product $product, \Illuminate\Http\Request $request): array
    {
        $newImages = $this->storeImages($product, array_values(array_filter((array) $request->file('images', []))));
        $this->setThumbnail($product, $request->input('thumbnail'), $newImages);

        $files = 0;
        $keys = ['added' => 0, 'skipped' => 0];

        if (! $product->isDigital()) {
            return ['files' => 0, 'keys_added' => 0, 'keys_skipped' => 0];
        }

        foreach (array_filter((array) $request->file('digital_files', [])) as $upload) {
            $this->storeFile($product, $upload);
            $files++;
        }

        $keys = filled($request->input('license_keys'))
            ? $this->addKeys($product, (string) $request->input('license_keys'))
            : ['added' => 0, 'skipped' => 0];

        return ['files' => $files, 'keys_added' => $keys['added'], 'keys_skipped' => $keys['skipped']];
    }

    public function storeFile(Product $product, UploadedFile $upload, ?string $variantId = null, ?string $versionLabel = null): ProductFile
    {
        $name = Str::random(12).'-'.(Str::slug(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file');
        $extension = strtolower($upload->getClientOriginalExtension() ?: ($upload->extension() ?: 'bin'));
        $path = $upload->storeAs('shop/files/'.$product->id, $name.'.'.$extension, self::FILE_DISK);

        if ($path === false) {
            throw new ShopException('File gagal disimpan. Periksa ruang disk dan izin folder storage.');
        }

        return $product->files()->create([
            'product_variant_id' => $variantId ?: null,
            'disk' => self::FILE_DISK,
            'path' => $path,
            'original_name' => mb_substr($upload->getClientOriginalName(), 0, 255),
            'mime' => $upload->getMimeType(),
            'size' => $upload->getSize(),
            'version_label' => $versionLabel,
        ]);
    }

    public function deleteFile(ProductFile $file): void
    {
        Storage::disk($file->disk)->delete($file->path);
        $file->delete();
    }

    /**
     * One key per line; for CSV text only the first column is used. Keys already stored
     * for the product are skipped.
     *
     * @return array{added: int, skipped: int}
     */
    public function addKeys(Product $product, string $raw, ?string $variantId = null): array
    {
        $keys = collect(preg_split('/\r\n|\r|\n/', $raw))
            ->map(fn ($line) => trim(str_getcsv($line)[0] ?? ''))
            ->filter()
            ->unique()
            ->values();

        $existing = $product->licenseKeys()->get()->map(fn ($k) => $k->key)->flip();
        $added = 0;

        foreach ($keys as $key) {
            if ($existing->has($key)) {
                continue;
            }

            $product->licenseKeys()->create(['key' => $key, 'product_variant_id' => $variantId ?: null]);
            $existing->put($key, true);
            $added++;
        }

        return ['added' => $added, 'skipped' => $keys->count() - $added];
    }

    /**
     * @param  list<UploadedFile>  $files
     * @return list<ProductImage> the new images, in upload order
     */
    public function storeImages(Product $product, array $files): array
    {
        $next = (int) $product->images()->max('sort_order') + 1;
        $created = [];

        foreach ($files as $file) {
            $created[] = $product->images()->create([
                'path' => $this->storeMedia($file, 'shop/products'),
                'alt_id' => $product->name_id,
                'alt_en' => $product->name_en,
                'sort_order' => $next++,
            ]);
        }

        return $created;
    }

    /**
     * The thumbnail is simply the first image in the gallery. $choice is an existing image id
     * or "new:<n>" pointing into $newImages; the chosen image moves to the front, the rest keep
     * their order. Unknown choices are ignored.
     *
     * @param  list<ProductImage>  $newImages
     */
    public function setThumbnail(Product $product, ?string $choice, array $newImages = []): void
    {
        if (blank($choice)) {
            return;
        }

        $chosen = str_starts_with($choice, 'new:')
            ? ($newImages[(int) substr($choice, 4)] ?? null)
            : $product->images()->whereKey($choice)->first();

        if (! $chosen || $chosen->product_id !== $product->id) {
            return;
        }

        $position = 1;
        foreach ($product->images()->get() as $image) {
            $image->update(['sort_order' => $image->id === $chosen->id ? 0 : $position++]);
        }
    }

    /** Deletes, re-orders and re-labels existing images. */
    public function updateImages(Product $product, array $deleteIds, array $order, array $altId, array $altEn): void
    {
        foreach ($product->images()->whereIn('id', array_filter($deleteIds))->get() as $image) {
            ProductVariant::where('product_image_id', $image->id)->update(['product_image_id' => null]);
            $image->delete();

            // A duplicated product shares image files with its original.
            if (! ProductImage::where('path', $image->path)->exists()) {
                $this->deleteMedia($image->path);
            }
        }

        $order = array_values(array_filter($order));

        foreach ($product->images()->get() as $image) {
            $position = array_search($image->id, $order, true);
            $image->update([
                'alt_id' => $altId[$image->id] ?? $image->alt_id,
                'alt_en' => $altEn[$image->id] ?? $image->alt_en,
                'sort_order' => $position === false ? $image->sort_order : $position,
            ]);
        }
    }

    /** Same storage as the rest of the site's media: Nextcloud when configured, otherwise the public disk. */
    private function storeMedia(UploadedFile $file, string $folder): string
    {
        if (filled(config('services.nextcloud.base_url'))) {
            return $this->uploadToNextcloud($file, $folder);
        }

        return $file->store($folder, 'public');
    }

    private function deleteMedia(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        } elseif (filled(config('services.nextcloud.base_url'))) {
            $this->deleteFromNextcloud($path);
        }
    }
}
