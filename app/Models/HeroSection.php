<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasTranslations;
use App\Traits\HasMediaUrl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    use HasFactory, HasUuid, HasTranslations, HasMediaUrl;


    public $incrementing = false;
    protected $keyType = "string";

    protected $guarded = [];
}
