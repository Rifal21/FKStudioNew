<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPostView extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = ['viewed_on' => 'date', 'created_at' => 'datetime'];
}
