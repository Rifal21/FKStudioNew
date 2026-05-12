<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory, HasUuid, HasTranslations;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($package) {
            if (empty($package->slug) && !empty($package->name_en)) {
                $package->slug = \Illuminate\Support\Str::slug($package->name_en);
            }
        });
    }

    public $incrementing = false;
    protected $keyType = "string";

    protected $guarded = [];

    protected $casts = [
        'features_id' => 'array',
        'features_en' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(PackageOrder::class);
    }
}
