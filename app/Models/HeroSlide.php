<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasUuid;
use App\Traits\HasMediaUrl;


class HeroSlide extends Model
{
    use HasUuid, HasMediaUrl;


    protected $fillable = ['image', 'order'];
}
