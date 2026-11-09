<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Frozen copy stored on an order so later edits do not change past shipments. */
    public function snapshot(): array
    {
        return $this->only(['label', 'recipient_name', 'phone', 'province', 'city', 'district', 'postal_code', 'address_line', 'shipping_area_id']);
    }
}
