<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ShopPayment extends Model
{
    use HasUuid;

    public const PENDING = 'pending';
    public const SUBMITTED = 'submitted';
    public const SUCCESS = 'success';
    public const REJECTED = 'rejected';
    public const FAILED = 'failed';
    public const EXPIRED = 'expired';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'raw_callback' => 'array',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected $hidden = ['raw_callback', 'proof_path', 'proof_disk'];

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'shop_order_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isManual(): bool
    {
        return $this->channel === ShopOrder::CHANNEL_MANUAL;
    }

    public function hasProof(): bool
    {
        return filled($this->proof_path);
    }
}
