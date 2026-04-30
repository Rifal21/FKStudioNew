<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasTranslations;
use App\Traits\HasMediaUrl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, HasUuid, HasTranslations, HasMediaUrl;

    protected $mediaColumn = 'avatar';


    public $incrementing = false;
    protected $keyType = "string";

    protected $guarded = [];
}
