<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DigitalEntitlement extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'download_count' => 'integer',
        'max_downloads' => 'integer',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(ShopOrderItem::class, 'shop_order_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id')->withTrashed();
    }

    public function licenseKey()
    {
        return $this->belongsTo(ProductLicenseKey::class, 'product_license_key_id');
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function remainingDownloads(): ?int
    {
        return $this->max_downloads === null ? null : max(0, $this->max_downloads - $this->download_count);
    }

    public function canDownload(): bool
    {
        return $this->isActive() && ($this->max_downloads === null || $this->download_count < $this->max_downloads);
    }

    /** Files for the bought variant: product-wide files plus files tied to that variant. */
    public function files(): Collection
    {
        if (! $this->product_id) {
            return collect();
        }

        return ProductFile::where('product_id', $this->product_id)
            ->where(fn ($q) => $q->whereNull('product_variant_id')->orWhere('product_variant_id', $this->product_variant_id))
            ->orderBy('created_at')
            ->get();
    }
}
