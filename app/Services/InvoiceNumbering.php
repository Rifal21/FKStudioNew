<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\CarbonInterface;

class InvoiceNumbering
{
    /** INV-YYYYMMDD-FKS-NNNN, where NNNN keeps counting across days. */
    public static function next(?CarbonInterface $date = null): string
    {
        $date ??= now();

        $last = Invoice::where('invoice_number', 'LIKE', 'INV-%')
            ->pluck('invoice_number')
            ->map(fn ($number) => (int) last(explode('-', $number)))
            ->max() ?? 0;

        return 'INV-'.$date->format('Ymd').'-FKS-'.str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }
}
