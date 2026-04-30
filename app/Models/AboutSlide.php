<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasMediaUrl;


class AboutSlide extends Model
{
    use HasFactory, HasMediaUrl;


    protected $fillable = ['image'];
}
