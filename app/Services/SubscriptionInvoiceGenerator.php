<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class SubscriptionInvoiceGenerator
{
    /** Today as a plain date in the business timezone (WIB), independent of the app timezone. */
    public static function today(): Carbon
    {
        return Carbon::parse(Carbon::now('Asia/Jakarta')->toDateString());
    }

    /**
     * Creates the invoice for the subscription's current run and moves it to the next month.
     * Returns null when that month already has an invoice (safe to call twice).
     */
    public function generate(Subscription $subscription, ?Carbon $today = null): ?Invoice
    {
        $today ??= self::today();
        $run = ($subscription->next_run_date ?? $subscription->firstRunOnOrAfter($today))->copy();
        $period = $run->copy()->startOfMonth();

        try {
            return DB::transaction(function () use ($subscription, $today, $run, $period) {
                $subscription->loadMissing('items');

                $totals = InvoiceTotals::compute(
                    $subscription->subtotal(),
                    (float) $subscription->discount,
                    $subscription->discount_type,
                    (float) $subscription->tax,
                    $subscription->tax_type,
                );

                $invoice = Invoice::create([
                    'subscription_id' => $subscription->id,
                    'billing_period' => $period,
                    'invoice_number' => InvoiceNumbering::next($today),
                    'client_name' => $subscription->client_name,
                    'client_email' => $subscription->client_email,
                    'client_phone' => $subscription->client_phone,
                    'client_address' => $subscription->client_address,
                    'issue_date' => $today,
                    'due_date' => $today->copy()->addDays($subscription->due_days),
                    'discount' => $subscription->discount,
                    'discount_type' => $subscription->discount_type,
                    'tax' => $subscription->tax,
                    'tax_type' => $subscription->tax_type,
                    'total_amount' => $totals['total'],
                    'status' => 'Draft',
                    'notes' => $this->fill($subscription->notes, $period),
                ]);

                foreach ($subscription->items as $position => $item) {
                    $invoice->items()->create([
                        'description' => $this->fill($item->description, $period),
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->quantity * $item->unit_price,
                        'position' => $position,
                    ]);
                }

                $this->advance($subscription, $run);

                return $invoice;
            });
        } catch (UniqueConstraintViolationException) {
            // Someone (or an earlier run) already billed this month: just move on.
            $this->advance($subscription, $run);

            return null;
        }
    }

    /** Replaces {bulan} and {tahun} with the billed month, e.g. "November" and "2026". */
    public function fill(?string $text, Carbon $period): ?string
    {
        if ($text === null) {
            return null;
        }

        $month = $period->copy()->locale('id');

        return strtr($text, [
            '{bulan}' => $month->translatedFormat('F'),
            '{tahun}' => $month->translatedFormat('Y'),
        ]);
    }

    private function advance(Subscription $subscription, Carbon $from): void
    {
        $next = $subscription->billingDateIn($from->copy()->startOfMonth()->addMonth());

        if ($subscription->end_date !== null && $next->gt($subscription->end_date)) {
            $subscription->update(['next_run_date' => null, 'status' => Subscription::ENDED]);

            return;
        }

        $subscription->update(['next_run_date' => $next]);
    }
}
