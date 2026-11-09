<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ShopOrderItem extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'integer',
        'quantity' => 'integer',
        'subtotal' => 'integer',
        'weight_grams' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'shop_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id')->withTrashed();
    }

    public function entitlements()
    {
        return $this->hasMany(DigitalEntitlement::class);
    }

    public function licenseKeys()
    {
        return $this->hasMany(ProductLicenseKey::class);
    }

    public function displayName(): string
    {
        return $this->product_name.($this->variant_title ? ' — '.$this->variant_title : '');
    }
}
