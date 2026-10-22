<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Services\Waha\WahaClient;
use App\Services\Waha\WahaException;

class InvoiceWhatsApp
{
    public const DEFAULT_TEMPLATE = <<<'TXT'
Halo {nama},

Invoice *{nomor}* dari {perusahaan} sudah terbit.
Total tagihan: *{total}*
Jatuh tempo: {jatuh_tempo}

Lihat dan unduh invoice di sini:
{link}

Dokumen PDF terlampir. Terima kasih atas kepercayaan Anda.
TXT;

    public function __construct(private readonly ?SiteSetting $settings = null)
    {
    }

    public function enabled(): bool
    {
        $settings = $this->settings ?? SiteSetting::first();

        return (bool) ($settings?->waha_enabled && filled($settings->waha_api_key));
    }

    public function message(Invoice $invoice): string
    {
        $settings = $this->settings ?? SiteSetting::first();
        $template = filled($settings?->waha_invoice_template) ? $settings->waha_invoice_template : self::DEFAULT_TEMPLATE;

        return strtr($template, [
            '{nama}' => $invoice->client_name,
            '{nomor}' => $invoice->invoice_number,
            '{perusahaan}' => $settings?->invoice_company_name ?: config('app.name'),
            '{total}' => 'Rp '.number_format((float) $invoice->total_amount, 0, ',', '.'),
            '{jatuh_tempo}' => $invoice->due_date?->locale('id')->translatedFormat('d F Y') ?? '-',
            '{tanggal}' => $invoice->issue_date->locale('id')->translatedFormat('d F Y'),
            '{link}' => route('invoices.public.show', $invoice->id),
        ]);
    }

    /**
     * Sends the invoice text + PDF and records the outcome on the invoice.
     * Returns true when WhatsApp accepted the message. Never throws.
     */
    public function send(Invoice $invoice): bool
    {
        try {
            if (! $this->enabled()) {
                throw new WahaException('Pengiriman WhatsApp belum diaktifkan di Pengaturan > WhatsApp.');
            }

            if (WahaClient::normalizePhone($invoice->client_phone) === null) {
                throw new WahaException('Nomor WhatsApp klien kosong atau tidak valid.');
            }

            $client = WahaClient::fromSettings($this->settings);
            $client->ensureWorking();

            $client->sendText($invoice->client_phone, $this->message($invoice));
            $client->sendFile(
                $invoice->client_phone,
                InvoicePdf::make($invoice)->output(),
                InvoicePdf::filename($invoice),
                'application/pdf',
            );
        } catch (WahaException $e) {
            $invoice->update(['wa_error' => $e->getMessage(), 'wa_attempts' => $invoice->wa_attempts + 1]);

            return false;
        } catch (\Throwable $e) {
            report($e);
            $invoice->update(['wa_error' => 'Gagal mengirim: '.$e->getMessage(), 'wa_attempts' => $invoice->wa_attempts + 1]);

            return false;
        }

        $invoice->update([
            'wa_sent_at' => now(),
            'wa_error' => null,
            // A draft that has gone out to the client is no longer a draft.
            'status' => $invoice->status === 'Draft' ? 'Sent' : $invoice->status,
        ]);

        return true;
    }
}
