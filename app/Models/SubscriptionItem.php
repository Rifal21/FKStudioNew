<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SubscriptionItem extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'float',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
