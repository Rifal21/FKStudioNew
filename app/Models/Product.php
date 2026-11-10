<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUuid, HasTranslations, SoftDeletes;

    public const DIGITAL = 'digital';
    public const PHYSICAL = 'physical';

    public const DELIVERY_FILE = 'file';
    public const DELIVERY_LICENSE = 'license_key';
    public const DELIVERY_LINK = 'link';

    protected $guarded = [];

    protected $casts = [
        'has_variants' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'access_url' => 'encrypted',
        'min_price' => 'integer',
        'max_price' => 'integer',
        'max_downloads' => 'integer',
        'download_expiry_days' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (blank($product->slug)) {
                $product->slug = static::uniqueSlug($product->name_en ?: $product->name_id, $product->id);
            }
        });
    }

    public static function uniqueSlug(string $source, ?string $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'produk';
        $slug = $base;
        $i = 2;

        while (static::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class)->orderBy('position');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('position');
    }

    public function activeVariants()
    {
        return $this->variants()->where('is_active', true);
    }

    public function files()
    {
        return $this->hasMany(ProductFile::class);
    }

    public function licenseKeys()
    {
        return $this->hasMany(ProductLicenseKey::class);
    }

    public function isDigital(): bool
    {
        return $this->type === self::DIGITAL;
    }

    public function isPhysical(): bool
    {
        return $this->type === self::PHYSICAL;
    }

    /** Active products a visitor may see; physical ones only once the physical shop is switched on. */
    public function scopeVisibleInShop(Builder $query, ?SiteSetting $settings = null): Builder
    {
        $settings ??= SiteSetting::first();

        return $query->where('is_active', true)
            ->when(! $settings?->shop_physical_enabled, fn ($q) => $q->where('type', self::DIGITAL))
            ->whereHas('variants', fn ($q) => $q->where('is_active', true));
    }

    public function isVisibleInShop(?SiteSetting $settings = null): bool
    {
        $settings ??= SiteSetting::first();

        return $this->is_active && ! $this->trashed() && ($this->isDigital() || (bool) $settings?->shop_physical_enabled);
    }

    /** Options and variants as the storefront's product page script expects them (needs options.values and variants.optionValues/image loaded). */
    public function storefrontData(): array
    {
        return [
            'options' => $this->has_variants ? $this->options->map(fn ($o) => [
                'id' => $o->id,
                'name' => $o->getTranslation('name'),
                'values' => $o->values->map(fn ($v) => ['id' => $v->id, 'label' => $v->getTranslation('value')])->values(),
            ])->values() : [],
            'variants' => $this->variants->map(fn (ProductVariant $v) => [
                'id' => $v->id,
                'values' => $v->optionValues->pluck('id')->values(),
                'title' => $v->label(),
                'price' => $v->price,
                'compare' => $v->compare_at_price && $v->compare_at_price > $v->price ? $v->compare_at_price : null,
                'stock' => $v->stock,
                'image' => $v->image?->media_url,
            ])->values(),
        ];
    }

    public function defaultVariant(): ?ProductVariant
    {
        return $this->variants->firstWhere('is_default', true) ?? $this->variants->first();
    }

    public function coverImage(): ?ProductImage
    {
        return $this->images->first();
    }

    public function syncPriceRange(): void
    {
        $prices = $this->variants()->where('is_active', true)->pluck('price');

        $this->forceFill([
            'min_price' => (int) ($prices->min() ?? 0),
            'max_price' => (int) ($prices->max() ?? 0),
        ])->saveQuietly();
    }

    public function hasPriceRange(): bool
    {
        return $this->min_price !== $this->max_price;
    }

    public function inStock(): bool
    {
        return $this->variants->where('is_active', true)->contains(fn (ProductVariant $v) => $v->stock === null || $v->stock > 0);
    }

    public function deliveryLabel(string $locale = 'id'): string
    {
        $labels = [
            'id' => [self::DELIVERY_FILE => 'Unduhan file', self::DELIVERY_LICENSE => 'Kunci lisensi', self::DELIVERY_LINK => 'Akses link'],
            'en' => [self::DELIVERY_FILE => 'File download', self::DELIVERY_LICENSE => 'License key', self::DELIVERY_LINK => 'Access link'],
        ];

        return $labels[$locale][$this->digital_delivery] ?? '';
    }
}
