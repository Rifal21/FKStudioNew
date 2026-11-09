<?php

namespace App\Models;

use App\Traits\HasMediaUrl;
use App\Traits\HasTranslations;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasUuid, HasMediaUrl, HasTranslations;

    protected $guarded = [];

    protected string $mediaColumn = 'path';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function altText(): string
    {
        return (string) ($this->getTranslation('alt') ?: $this->product?->getTranslation('name'));
    }
}
