<?php

namespace App\Models;

use App\Services\InvoiceTotals;
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
        // MySQL hands integers back as strings, and Carbon rejects a string day or day count.
        'billing_day' => 'integer',
        'due_days' => 'integer',
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

    /**
     * Next billing date from today (or the start date, if later), skipping months that already
     * have an invoice. Used after create, edit and resume so the schedule never bills twice.
     */
    public function scheduleNext(CarbonInterface $today): void
    {
        $from = $this->start_date->gt($today) ? $this->start_date : $today;
        $next = $this->firstRunOnOrAfter($from);

        while ($this->invoices()->whereDate('billing_period', $next->copy()->startOfMonth())->exists()) {
            $next = $this->billingDateIn($next->copy()->startOfMonth()->addMonth());
        }

        if ($this->end_date !== null && $next->gt($this->end_date)) {
            $this->update(['next_run_date' => null, 'status' => self::ENDED]);

            return;
        }

        $this->update(['next_run_date' => $next, 'status' => $this->status === self::ENDED ? self::ACTIVE : $this->status]);
    }

    public function subtotal(): float
    {
        return (float) $this->items->sum(fn ($i) => $i->quantity * $i->unit_price);
    }

    public function monthlyTotal(): float
    {
        return InvoiceTotals::compute($this->subtotal(), (float) $this->discount, $this->discount_type, (float) $this->tax, $this->tax_type)['total'];
    }

    /** The exact moment (WIB) the next invoice goes out: next_run_date at billing_time. */
    public function runAt(): ?Carbon
    {
        if ($this->next_run_date === null) {
            return null;
        }

        return Carbon::parse($this->next_run_date->toDateString().' '.substr($this->billing_time ?: '06:00', 0, 5), 'Asia/Jakarta');
    }

    public function isDue(CarbonInterface $now): bool
    {
        $runAt = $this->runAt();

        return $this->status === self::ACTIVE
            && $runAt !== null
            && $runAt->lte($now)
            && ($this->end_date === null || $this->next_run_date->lte($this->end_date));
    }
}
