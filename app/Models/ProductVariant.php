<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    use HasUuid, HasTranslations, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'price' => 'integer',
        'compare_at_price' => 'integer',
        'stock' => 'integer',
        'weight_grams' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function image()
    {
        return $this->belongsTo(ProductImage::class, 'product_image_id');
    }

    public function optionValues()
    {
        return $this->belongsToMany(ProductOptionValue::class, 'product_variant_option_value');
    }

    public function files()
    {
        return $this->hasMany(ProductFile::class);
    }

    /** "Merah / L"; empty for the hidden default variant of a product without options. */
    public function label(): string
    {
        return $this->is_default ? '' : (string) $this->getTranslation('title');
    }

    public function tracksStock(): bool
    {
        return $this->stock !== null;
    }

    /** Can this many units be bought right now (ignoring what is already in the buyer's cart)? */
    public function purchasable(int $quantity = 1, ?SiteSetting $settings = null): bool
    {
        $product = $this->product;

        return ! $this->trashed()
            && $this->is_active
            && $product !== null
            && $product->isVisibleInShop($settings)
            && ($this->stock === null || $this->stock >= $quantity);
    }

    /**
     * Takes units out of stock atomically. Returns false when there is not enough left.
     * Variants without stock tracking always succeed.
     */
    public function reserve(int $quantity): bool
    {
        if ($this->stock === null) {
            return true;
        }

        $updated = DB::table($this->getTable())
            ->where('id', $this->id)
            ->whereNotNull('stock')
            ->where('stock', '>=', $quantity)
            ->update(['stock' => DB::raw('stock - '.(int) $quantity), 'updated_at' => now()]);

        if ($updated) {
            $this->stock -= $quantity;
        }

        return $updated > 0;
    }

    public function release(int $quantity): void
    {
        DB::table($this->getTable())
            ->where('id', $this->id)
            ->whereNotNull('stock')
            ->update(['stock' => DB::raw('stock + '.(int) $quantity), 'updated_at' => now()]);
    }
}
