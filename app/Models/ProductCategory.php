<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory, HasUuid, HasTranslations;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProductCategory $category) {
            if (blank($category->slug)) {
                $category->slug = Str::slug($category->name_en ?: $category->name_id);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
