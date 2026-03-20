<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, HasUuid, HasTranslations;

    public $incrementing = false;
    protected $keyType = "string";

    protected $guarded = [];
}
