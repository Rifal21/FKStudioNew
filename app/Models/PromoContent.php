<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PromoContent extends Model
{
    use HasUuid;

    public const DRAFT = 'draft';

    public const FINAL = 'final';

    protected $guarded = [];

    protected $casts = [
        'formats' => 'array',
        'content' => 'array',
        'warnings' => 'array',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
