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
        'min_purchase' => 'integer',
    ];

    public const SCOPE_ALL = 'all';
    public const SCOPE_PACKAGE = 'package';
    public const SCOPE_SHOP = 'shop';

    /** Can this voucher be used for a package order ('package') or a shop order ('shop')? */
    public function appliesTo(string $context): bool
    {
        $scope = $this->scope ?: self::SCOPE_ALL;

        return $scope === self::SCOPE_ALL || $scope === $context;
    }

    /**
     * Takes one use atomically, only while the voucher is still under its limit.
     * Returns false when the last use was taken in the meantime.
     */
    public static function reserveUse(string $code): bool
    {
        return static::where('code', $code)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses'))
            ->increment('used_count') > 0;
    }

    public static function releaseUse(string $code): void
    {
        static::where('code', $code)->where('used_count', '>', 0)->decrement('used_count');
    }

    public function meetsMinimum(float $amount): bool
    {
        return $this->min_purchase === null || $amount >= $this->min_purchase;
    }

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
