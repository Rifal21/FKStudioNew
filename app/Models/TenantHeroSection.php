<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasMediaUrl;

class TenantHeroSection extends Model
{
    use HasFactory, HasMediaUrl;

    protected $guarded = [];
}
