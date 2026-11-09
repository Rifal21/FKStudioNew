<?php

namespace App\Services\Shop;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Validation rules and normalisation for product input, shared by the admin form
 * and the admin API so both accept exactly the same payload.
 */
class ProductInput
{
    public static function rules(Request $request, ?Product $product = null): array
    {
        $hasVariants = $request->boolean('has_variants');
        $variantIds = $product ? $product->variants()->pluck('id')->all() : [];
        $physical = $request->input('type') === Product::PHYSICAL;

        // SKUs of this product's own variants are re-assigned on save (old ones are freed first),
        // so only other products count; duplicates within the payload are caught by "distinct".
        $sku = ['nullable', 'string', 'max:100', Rule::unique('product_variants', 'sku')->when($product, fn ($rule) => $rule->where(fn ($q) => $q->where('product_id', '!=', $product->id)))];

        $rules = [
            'type' => ['required', Rule::in([Product::DIGITAL, Product::PHYSICAL])],
            'product_category_id' => 'nullable|exists:product_categories,id',
            'name_id' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash',
            'short_description_id' => 'nullable|string|max:500',
            'short_description_en' => 'nullable|string|max:500',
            'description_id' => 'nullable|string|max:100000',
            'description_en' => 'nullable|string|max:100000',
            'digital_delivery' => ['nullable', Rule::requiredIf(! $physical), Rule::in([Product::DELIVERY_FILE, Product::DELIVERY_LICENSE, Product::DELIVERY_LINK])],
            'access_url' => ['nullable', 'url:http,https', 'max:2000', Rule::requiredIf(! $physical && $request->input('digital_delivery') === Product::DELIVERY_LINK && ! $product?->access_url)],
            'max_downloads' => 'nullable|integer|min:1|max:1000',
            'unlimited_downloads' => 'nullable|boolean',
            'download_expiry_days' => 'nullable|integer|min:1|max:3650',
            'license_terms_id' => 'nullable|string|max:5000',
            'license_terms_en' => 'nullable|string|max:5000',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'has_variants' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:100000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            // Thumbnail: an existing image id, or "new:<n>" for the n-th image uploaded in this request.
            'thumbnail' => ['nullable', 'string', 'max:64', 'regex:/^(new:\d{1,2}|[0-9a-f-]{36})$/'],
            'digital_files' => 'nullable|array|max:10',
            'digital_files.*' => 'file|max:512000|mimes:'.ProductAssets::FILE_EXTENSIONS,
            'license_keys' => 'nullable|string|max:200000',
        ];

        if ($hasVariants) {
            $rules += [
                'options' => 'required|array|min:1|max:'.ProductSaver::MAX_OPTIONS,
                'options.*.name_id' => 'required|string|max:50',
                'options.*.name_en' => 'nullable|string|max:50',
                'options.*.values' => 'required|array|min:1|max:30',
                'options.*.values.*.value_id' => 'required|string|max:50',
                'options.*.values.*.value_en' => 'nullable|string|max:50',
                'variants' => 'required|array|min:1|max:200',
                'variants.*.key' => 'required|string|max:200',
                'variants.*.id' => ['nullable', Rule::in($variantIds)],
                'variants.*.price' => 'required|integer|min:0|max:1000000000000',
                'variants.*.compare_at_price' => 'nullable|integer|min:0|max:1000000000000',
                'variants.*.stock' => 'nullable|integer|min:0|max:10000000',
                'variants.*.weight_grams' => [$physical ? 'required' : 'nullable', 'integer', 'min:1', 'max:1000000'],
                'variants.*.is_active' => 'nullable|boolean',
                'variants.*.image_id' => 'nullable|string',
                'variants.*.sku' => array_merge($sku, ['distinct']),
            ];
        } else {
            $rules += [
                'price' => 'required|integer|min:0|max:1000000000000',
                'compare_at_price' => 'nullable|integer|min:0|max:1000000000000',
                'stock' => 'nullable|integer|min:0|max:10000000',
                'weight_grams' => [$physical ? 'required' : 'nullable', 'integer', 'min:1', 'max:1000000'],
                'sku' => $sku,
            ];
        }

        return $rules;
    }

    public static function messages(): array
    {
        return [
            'digital_files.*.max' => 'Ukuran file produk maksimal 500 MB (cek juga upload_max_filesize di server).',
            'digital_files.*.mimes' => 'Tipe file produk tidak diizinkan.',
        ];
    }

    /** Turns validated input into what ProductSaver expects. */
    public static function normalize(Request $request, array $data): array
    {
        $data['has_variants'] = $request->boolean('has_variants');
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['max_downloads'] = $request->boolean('unlimited_downloads') ? null : ($data['max_downloads'] ?? 5);

        // Keep the stored link when the field is left empty on edit.
        if (blank($data['access_url'] ?? null)) {
            unset($data['access_url']);
        }

        if ($data['has_variants']) {
            $data['variants'] = collect($data['variants'])
                // Missing = active (API clients); the admin form always sends 0 or 1.
                ->map(fn ($row) => ['is_active' => filter_var($row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN)] + $row)
                ->all();
        }

        unset($data['images'], $data['digital_files'], $data['license_keys'], $data['unlimited_downloads'], $data['thumbnail']);

        return $data;
    }

    /**
     * The product's current values in input form. The API merges a PATCH payload over this,
     * so clients can send only the fields they want to change.
     */
    public static function fromProduct(Product $product): array
    {
        $product->loadMissing('options.values', 'variants.optionValues');
        $default = $product->variants->firstWhere('is_default', true) ?? $product->variants->first();

        $input = [
            'type' => $product->type,
            'product_category_id' => $product->product_category_id,
            'name_id' => $product->name_id,
            'name_en' => $product->name_en,
            'slug' => $product->slug,
            'short_description_id' => $product->short_description_id,
            'short_description_en' => $product->short_description_en,
            'description_id' => $product->description_id,
            'description_en' => $product->description_en,
            'digital_delivery' => $product->digital_delivery,
            'max_downloads' => $product->max_downloads,
            'unlimited_downloads' => $product->isDigital() && $product->max_downloads === null,
            'download_expiry_days' => $product->download_expiry_days,
            'license_terms_id' => $product->license_terms_id,
            'license_terms_en' => $product->license_terms_en,
            'is_active' => $product->is_active,
            'is_featured' => $product->is_featured,
            'has_variants' => $product->has_variants,
            'sort_order' => $product->sort_order,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
        ];

        if ($product->has_variants) {
            $input['options'] = $product->options->map(fn ($o) => [
                'name_id' => $o->name_id,
                'name_en' => $o->name_en,
                'values' => $o->values->map(fn ($v) => ['value_id' => $v->value_id, 'value_en' => $v->value_en])->all(),
            ])->all();
            $input['variants'] = $product->variants->map(fn ($v) => [
                'id' => $v->id,
                'key' => $product->options->map(fn ($o) => $v->optionValues->firstWhere('product_option_id', $o->id)?->value_id)->implode('|'),
                'price' => $v->price,
                'compare_at_price' => $v->compare_at_price,
                'sku' => $v->sku,
                'stock' => $v->stock,
                'weight_grams' => $v->weight_grams,
                'is_active' => $v->is_active,
                'image_id' => $v->product_image_id,
            ])->all();
        } elseif ($default) {
            $input += [
                'price' => $default->price,
                'compare_at_price' => $default->compare_at_price,
                'sku' => $default->sku,
                'stock' => $default->stock,
                'weight_grams' => $default->weight_grams,
            ];
        }

        return $input;
    }
}
