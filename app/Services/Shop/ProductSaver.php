<?php

namespace App\Services\Shop;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Saves a product with its options and variants from the admin form.
 *
 * Every product keeps at least one variant: without options it has a single hidden
 * default variant, so cart lines, stock and order items always point at a variant.
 */
class ProductSaver
{
    public const MAX_OPTIONS = 3;

    /**
     * @param  array  $data  validated form data (see Dashboard\Shop\ProductController::rules)
     */
    public function save(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->fill(Arr::only($data, [
                'product_category_id', 'type', 'name_id', 'name_en', 'short_description_id', 'short_description_en',
                'description_id', 'description_en', 'digital_delivery', 'max_downloads', 'download_expiry_days',
                'license_terms_id', 'license_terms_en', 'is_active', 'is_featured', 'sort_order', 'meta_title', 'meta_description',
            ]));

            $product->slug = filled($data['slug'] ?? null)
                ? Product::uniqueSlug($data['slug'], $product->id)
                : ($product->slug ?: Product::uniqueSlug(($data['name_en'] ?? null) ?: $data['name_id'], $product->id));

            if (array_key_exists('access_url', $data)) {
                $product->access_url = $data['access_url'] ?: null;
            }

            if ($product->type === Product::PHYSICAL) {
                $product->digital_delivery = null;
            }

            $product->has_variants = (bool) ($data['has_variants'] ?? false);
            $product->save();

            $product->has_variants
                ? $this->syncVariants($product, $data['options'] ?? [], $data['variants'] ?? [])
                : $this->syncSingle($product, $data);

            $product->syncPriceRange();

            return $product->fresh(['variants', 'options.values']);
        });
    }

    private function syncSingle(Product $product, array $data): void
    {
        $product->options()->delete();

        $variant = $product->variants()->withTrashed()->where('is_default', true)->first() ?? new ProductVariant(['product_id' => $product->id]);

        // Retire the others first so their SKUs are free for the default variant.
        $this->retire($product->variants()->when($variant->exists, fn ($q) => $q->whereKeyNot($variant->id))->get());

        if ($variant->trashed()) {
            $variant->restore();
        }

        $variant->fill($this->variantFields($data) + ['is_default' => true, 'is_active' => true, 'position' => 0, 'title_id' => null, 'title_en' => null, 'product_image_id' => null]);
        $variant->product_id = $product->id;
        $variant->save();
        $variant->optionValues()->detach();
    }

    /**
     * @param  list<array{name_id: string, name_en?: ?string, values: list<array{value_id: string, value_en?: ?string}>}>  $options
     * @param  list<array{key: string, id?: ?string}>  $rows  key = value_id labels joined with "|" in option order
     */
    private function syncVariants(Product $product, array $options, array $rows): void
    {
        $product->options()->delete(); // values and the variant pivot go with it

        $lookup = [];

        foreach (array_values($options) as $position => $option) {
            $model = $product->options()->create([
                'name_id' => $option['name_id'],
                'name_en' => $option['name_en'] ?? null,
                'position' => $position,
            ]);

            foreach (array_values($option['values']) as $valuePosition => $value) {
                $lookup[$position][mb_strtolower(trim($value['value_id']))] = $model->values()->create([
                    'value_id' => trim($value['value_id']),
                    'value_en' => filled($value['value_en'] ?? null) ? trim($value['value_en']) : null,
                    'position' => $valuePosition,
                ]);
            }
        }

        $referenced = collect($rows)->pluck('id')->filter()->all();
        $this->retire($product->variants()->whereNotIn('id', $referenced)->get());

        $existing = $product->variants()->get()->keyBy('id');
        // Free the kept variants' SKUs so two variants can swap SKUs without a unique clash.
        $product->variants()->update(['sku' => null]);
        $kept = [];

        foreach (array_values($rows) as $position => $row) {
            $labels = explode('|', $row['key']);
            $values = [];

            foreach ($labels as $i => $label) {
                $values[] = $lookup[$i][mb_strtolower(trim($label))] ?? throw new ShopException('Kombinasi varian "'.$row['key'].'" tidak cocok dengan opsi yang diisi.');
            }

            if (count($values) !== count($lookup)) {
                throw new ShopException('Kombinasi varian "'.$row['key'].'" tidak lengkap.');
            }

            $variant = (filled($row['id'] ?? null) ? $existing->get($row['id']) : null) ?? new ProductVariant();
            $variant->fill($this->variantFields($row) + [
                'title_id' => collect($values)->pluck('value_id')->implode(' / '),
                'title_en' => collect($values)->map(fn ($v) => $v->value_en ?: $v->value_id)->implode(' / '),
                'is_default' => false,
                'is_active' => (bool) ($row['is_active'] ?? true),
                'position' => $position,
                'product_image_id' => filled($row['image_id'] ?? null) && $product->images()->whereKey($row['image_id'])->exists() ? $row['image_id'] : null,
            ]);
            $variant->product_id = $product->id;
            $variant->save();
            $variant->optionValues()->sync(collect($values)->pluck('id'));

            $kept[] = $variant->id;
        }

        if ($kept === []) {
            throw new ShopException('Produk bervarian butuh minimal satu varian.');
        }
    }

    private function variantFields(array $row): array
    {
        return [
            'price' => (int) $row['price'],
            'compare_at_price' => filled($row['compare_at_price'] ?? null) ? (int) $row['compare_at_price'] : null,
            'sku' => filled($row['sku'] ?? null) ? trim($row['sku']) : null,
            'stock' => filled($row['stock'] ?? null) ? (int) $row['stock'] : null,
            'weight_grams' => filled($row['weight_grams'] ?? null) ? (int) $row['weight_grams'] : null,
        ];
    }

    /** Old variants stay (soft-deleted) for order history; their SKU is freed for reuse. */
    private function retire($variants): void
    {
        foreach ($variants as $variant) {
            $variant->forceFill(['sku' => null, 'is_active' => false])->save();
            $variant->delete();
        }
    }
}
