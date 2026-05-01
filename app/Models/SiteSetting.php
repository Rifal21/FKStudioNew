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


    public $incrementing = false;
    protected $keyType = "string";

    protected $guarded = [];

    protected $casts = [
        'social_links' => 'array',
        'payment_methods' => 'array',
    ];

}
