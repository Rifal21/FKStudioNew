<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GenerateSubscriptionInvoices extends Command
{
    protected $signature = 'invoices:generate-subscriptions';
    protected $description = 'Generate automatic invoices for server subscriptions';

    public function handle()
    {
        $today = Carbon::today();
        $dayOfMonth = $today->day;

        $clients = Client::where('is_server_subscribed', true)
            ->where('billing_date', $dayOfMonth)
            ->get();

        if ($clients->isEmpty()) {
            $this->info('No subscriptions to process for today (Day ' . $dayOfMonth . ').');
            return;
        }

        foreach ($clients as $client) {
            // Check if invoice for this month already exists for this client subscription
            $exists = Invoice::where('client_name', $client->name)
                ->whereYear('issue_date', $today->year)
                ->whereMonth('issue_date', $today->month)
                ->where('notes', 'LIKE', '%Server Subscription%')
                ->exists();

            if ($exists) {
                $this->info("Invoice for {$client->name} already exists for this month. Skipping.");
                continue;
            }

            // Generate Invoice Number following the pattern: INV-YYYYMMDD-COUNT
            $year = $today->format('Y');
            $month = $today->format('m');
            $day = $today->format('d');
            
            $lastInvoice = Invoice::where('invoice_number', 'LIKE', 'INV-%')
                ->latest()
                ->first();

            if ($lastInvoice) {
                $parts = explode('-', $lastInvoice->invoice_number);
                $lastNumber = (int) end($parts);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $invoiceNumber = "INV-$year$month$day-FKS-$nextNumber";
            
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'client_name'    => $client->name,
                'client_email'   => '', // Can be updated manually if needed
                'client_address' => '', 
                'issue_date'     => $today,
                'due_date'       => $today->copy()->addDays(7),
                'total_amount'   => $client->subscription_price,
                'discount'       => 0,
                'tax'            => 0,
                'discount_type'  => 'percent',
                'tax_type'       => 'percent',
                'status'         => 'Draft',
                'notes'          => 'Server Subscription for ' . $today->format('F Y'),
            ]);

            InvoiceItem::create([
                'invoice_id'   => $invoice->id,
                'description'  => 'Monthly Server Subscription - ' . $today->format('F Y'),
                'quantity'     => 1,
                'unit_price'   => $client->subscription_price,
                'subtotal'     => $client->subscription_price,
            ]);

            $this->info("Generated invoice {$invoiceNumber} for {$client->name}.");
        }

        $this->info('All subscriptions processed.');
    }
}
