<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasTranslations;
use App\Traits\HasMediaUrl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory, HasUuid, HasTranslations, HasMediaUrl;

    public function getLogoUrlAttribute() { return $this->getUrl($this->site_logo); }
    public function getFaviconUrlAttribute() { return $this->getUrl($this->site_favicon); }
    public function getInvoiceLogoUrlAttribute() { return $this->getUrl($this->invoice_logo); }
    public function getInvoiceSignatureUrlAttribute() { return $this->getUrl($this->invoice_signature); }
    public function getInvoiceQrisUrlAttribute() { return $this->getUrl($this->invoice_qris); }
    public function getOgImageUrlAttribute() { return $this->getUrl($this->og_image); }


    public $incrementing = false;
    protected $keyType = "string";

    protected $guarded = [];

    protected $casts = [
        'social_links' => 'array',
        'payment_methods' => 'array',
        'waha_enabled' => 'boolean',
        'waha_api_key' => 'encrypted',
        'shop_enabled' => 'boolean',
        'shop_physical_enabled' => 'boolean',
        'shop_manual_payment_enabled' => 'boolean',
        'shop_manual_unique_code' => 'boolean',
        'shop_tax_rate' => 'decimal:2',
        'shop_order_expiry_minutes' => 'integer',
        'shop_manual_expiry_minutes' => 'integer',
        'shop_flat_shipping_cost' => 'integer',
    ];

    /** The single settings row, or an unsaved instance with column defaults when none exists yet. */
    public static function current(): self
    {
        return static::first() ?? new static([
            'shop_enabled' => false,
            'shop_physical_enabled' => false,
            'shop_tax_rate' => 11,
            'shop_order_expiry_minutes' => 1440,
            'shop_manual_expiry_minutes' => 1440,
            'shop_manual_payment_enabled' => true,
            'shop_manual_unique_code' => false,
        ]);
    }

}
