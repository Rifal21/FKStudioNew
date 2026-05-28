<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasTranslations;
use App\Traits\HasMediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory, HasUuid, HasTranslations, HasMediaUrl;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views'        => 'integer',
    ];

    /**
     * Relationship with the author (User)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
