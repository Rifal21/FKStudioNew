<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;

class InvoicePdf
{
    public static function make(Invoice $invoice): DomPdf
    {
        $settings = SiteSetting::first();
        $invoice->loadMissing('items');

        return Pdf::loadView('dashboard.invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);
    }

    public static function filename(Invoice $invoice): string
    {
        return $invoice->invoice_number.'-'.$invoice->client_name.'.pdf';
    }
}
