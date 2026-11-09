<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ProductLicenseKey extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $hidden = ['key'];

    protected $casts = [
        'key' => 'encrypted',
        'assigned_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('assigned_at');
    }

    /** Unassigned keys usable for a variant: keys for that variant first, then keys for any variant. */
    public function scopeUsableFor($query, ProductVariant $variant)
    {
        return $query->available()
            ->where('product_id', $variant->product_id)
            ->where(fn ($q) => $q->where('product_variant_id', $variant->id)->orWhereNull('product_variant_id'))
            ->orderByRaw('CASE WHEN product_variant_id IS NULL THEN 1 ELSE 0 END')
            ->orderBy('created_at');
    }

    public function masked(): string
    {
        $key = (string) $this->key;

        return strlen($key) <= 6 ? str_repeat('•', strlen($key)) : substr($key, 0, 3).str_repeat('•', max(3, strlen($key) - 6)).substr($key, -3);
    }
}
