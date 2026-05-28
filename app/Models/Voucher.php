<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
        'value'      => 'decimal:2',
    ];

    /**
     * Check if the voucher is valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount.
     */
    public function calculateDiscount(float $total): float
    {
        if (!$this->isValid()) {
            return 0.0;
        }

        if ($this->type === 'percent') {
            return round(($this->value / 100) * $total, 2);
        }

        // 'fixed' type
        return min((float) $this->value, $total);
    }
}
