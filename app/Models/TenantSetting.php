<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasMediaUrl;

class TenantSetting extends Model
{
    use HasFactory, HasMediaUrl;

    protected $guarded = [];

    protected $casts = [
        'is_onboarded' => 'boolean',
        'sections_data' => 'array',
    ];
}
