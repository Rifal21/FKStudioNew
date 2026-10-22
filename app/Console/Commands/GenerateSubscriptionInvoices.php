<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\InvoiceWhatsApp;
use App\Services\SubscriptionInvoiceGenerator;
use Illuminate\Console\Command;

class GenerateSubscriptionInvoices extends Command
{
    protected $signature = 'subscriptions:generate {--dry-run : Only list what would be generated}';

    protected $description = 'Generate invoices for subscriptions that are due today and send them over WhatsApp when auto-send is on';

    public function handle(SubscriptionInvoiceGenerator $generator, InvoiceWhatsApp $whatsApp): int
    {
        $today = SubscriptionInvoiceGenerator::today();

        $due = Subscription::with('items')
            ->where('status', Subscription::ACTIVE)
            ->whereNotNull('next_run_date')
            ->whereDate('next_run_date', '<=', $today)
            ->get()
            ->filter(fn (Subscription $s) => $s->isDue($today));

        $this->info("Tanggal {$today->toDateString()}: {$due->count()} langganan jatuh tempo.");

        foreach ($due as $subscription) {
            if ($this->option('dry-run')) {
                $this->line("  - {$subscription->client_name} / {$subscription->title}");

                continue;
            }

            try {
                $invoice = $generator->generate($subscription, $today);
            } catch (\Throwable $e) {
                report($e);
                $this->error("  x {$subscription->client_name}: {$e->getMessage()}");

                continue;
            }

            if ($invoice === null) {
                $this->warn("  = {$subscription->client_name}: invoice bulan ini sudah ada, dilewati.");

                continue;
            }

            $this->info("  + {$invoice->invoice_number} untuk {$subscription->client_name}");

            if ($subscription->auto_send) {
                $this->reportSend($invoice, $whatsApp);
            }
        }

        if (! $this->option('dry-run')) {
            $this->retryFailedSends($whatsApp, $today);
        }

        return self::SUCCESS;
    }

    /** Auto-send invoices that failed in the last 3 days get another try (server down, session expired). */
    private function retryFailedSends(InvoiceWhatsApp $whatsApp, $today): void
    {
        Invoice::whereNull('wa_sent_at')
            ->whereNotNull('wa_error')
            ->where('created_at', '>=', $today->copy()->subDays(3))
            ->whereHas('subscription', fn ($q) => $q->where('auto_send', true))
            ->get()
            ->each(function (Invoice $invoice) use ($whatsApp) {
                $this->line("  ~ mengirim ulang {$invoice->invoice_number}");
                $this->reportSend($invoice, $whatsApp);
            });
    }

    private function reportSend(Invoice $invoice, InvoiceWhatsApp $whatsApp): void
    {
        if ($whatsApp->send($invoice)) {
            $this->info("    terkirim ke {$invoice->client_phone}");
        } else {
            $this->warn('    gagal kirim: '.$invoice->fresh()->wa_error);
        }
    }
}
