<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Admin API view of a shop product. License key values are never included, only counts. */
class ShopProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;
        $product->loadMissing(['category', 'images', 'options.values', 'variants.optionValues', 'files']);

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'type' => $product->type,
            'name' => ['id' => $product->name_id, 'en' => $product->name_en],
            'short_description' => ['id' => $product->short_description_id, 'en' => $product->short_description_en],
            'description' => ['id' => $product->description_id, 'en' => $product->description_en],
            'license_terms' => ['id' => $product->license_terms_id, 'en' => $product->license_terms_en],
            'category' => $product->category ? ['id' => $product->category->id, 'slug' => $product->category->slug, 'name' => $product->category->name_id] : null,
            'is_active' => $product->is_active,
            'is_featured' => $product->is_featured,
            'sort_order' => $product->sort_order,
            'price' => ['min' => $product->min_price, 'max' => $product->max_price],
            'digital' => $product->isDigital() ? [
                'delivery' => $product->digital_delivery,
                'max_downloads' => $product->max_downloads,
                'download_expiry_days' => $product->download_expiry_days,
                'has_access_url' => filled($product->access_url),
                'license_keys' => $product->digital_delivery === Product::DELIVERY_LICENSE ? [
                    'available' => $product->licenseKeys()->whereNull('assigned_at')->count(),
                    'assigned' => $product->licenseKeys()->whereNotNull('assigned_at')->count(),
                ] : null,
            ] : null,
            'has_variants' => $product->has_variants,
            'options' => $product->options->map(fn ($o) => [
                'name' => ['id' => $o->name_id, 'en' => $o->name_en],
                'values' => $o->values->map(fn ($v) => ['id' => $v->value_id, 'en' => $v->value_en])->all(),
            ])->all(),
            'variants' => $product->variants->map(fn ($v) => [
                'id' => $v->id,
                'key' => $product->options->map(fn ($o) => $v->optionValues->firstWhere('product_option_id', $o->id)?->value_id)->implode('|') ?: null,
                'title' => ['id' => $v->title_id, 'en' => $v->title_en],
                'sku' => $v->sku,
                'price' => $v->price,
                'compare_at_price' => $v->compare_at_price,
                'stock' => $v->stock,
                'weight_grams' => $v->weight_grams,
                'is_default' => $v->is_default,
                'is_active' => $v->is_active,
                'image_id' => $v->product_image_id,
            ])->all(),
            'thumbnail_id' => $product->images->first()?->id,
            'images' => $product->images->map(fn ($i) => ['id' => $i->id, 'url' => $i->media_url, 'alt' => ['id' => $i->alt_id, 'en' => $i->alt_en]])->all(),
            'files' => $product->files->map(fn ($f) => [
                'id' => $f->id,
                'name' => $f->original_name,
                'size' => $f->size,
                'mime' => $f->mime,
                'variant_id' => $f->product_variant_id,
                'version_label' => $f->version_label,
                'uploaded_at' => $f->created_at?->toIso8601String(),
            ])->all(),
            'url' => route('shop.show', $product->slug),
            'meta' => ['title' => $product->meta_title, 'description' => $product->meta_description],
            'created_at' => $product->created_at?->toIso8601String(),
            'updated_at' => $product->updated_at?->toIso8601String(),
        ];
    }
}
