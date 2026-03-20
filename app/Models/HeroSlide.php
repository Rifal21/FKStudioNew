<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasUuid;

class HeroSlide extends Model
{
    use HasUuid;

    protected $fillable = ['image', 'order'];
}
