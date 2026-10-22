<?php

namespace App\Models;

use App\Traits\HasUuid;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasUuid;

    public const ACTIVE = 'active';
    public const PAUSED = 'paused';
    public const ENDED = 'ended';

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'auto_send' => 'boolean',
        'discount' => 'float',
        'tax' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class)->orderBy('position');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->orderByDesc('billing_period');
    }

    /** Tanggal tagih di bulan $month, dipotong ke hari terakhir bulan itu. */
    public function billingDateIn(CarbonInterface $month): Carbon
    {
        $first = Carbon::parse($month->format('Y-m-01'))->startOfDay();

        return $first->copy()->day(min($this->billing_day, $first->daysInMonth));
    }

    /** Tanggal tagih pertama pada atau setelah $from. */
    public function firstRunOnOrAfter(CarbonInterface $from): Carbon
    {
        $candidate = $this->billingDateIn($from);

        return $candidate->lt($from->copy()->startOfDay())
            ? $this->billingDateIn($from->copy()->startOfMonth()->addMonth())
            : $candidate;
    }

    public function subtotal(): float
    {
        return (float) $this->items->sum(fn ($i) => $i->quantity * $i->unit_price);
    }

    public function isDue(CarbonInterface $today): bool
    {
        return $this->status === self::ACTIVE
            && $this->next_run_date !== null
            && $this->next_run_date->lte($today)
            && ($this->end_date === null || $this->next_run_date->lte($this->end_date));
    }
}
