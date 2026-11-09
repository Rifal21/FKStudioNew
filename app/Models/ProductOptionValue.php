<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    use HasUuid, HasTranslations;

    protected $guarded = [];

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
