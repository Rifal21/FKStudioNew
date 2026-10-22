@php($sent = $invoice->wa_sent_at)
@if ($invoice->client_phone)
    <form action="{{ route('dashboard.invoices.whatsapp', $invoice->id) }}" method="POST"
        data-confirm="PDF dan tautan invoice {{ $invoice->invoice_number }} dikirim ke {{ $invoice->client_phone }}.{{ $sent ? ' Sudah pernah terkirim '.$sent->locale('id')->translatedFormat('d M, H:i').'.' : '' }}"
        data-confirm-title="{{ $sent ? 'Kirim ulang lewat WhatsApp?' : 'Kirim lewat WhatsApp?' }}" data-confirm-ok="Ya, kirim">
        @csrf
        <button type="submit" class="btn-icon {{ $sent ? 'text-brand-text' : '' }}" title="{{ $sent ? 'Kirim ulang WhatsApp' : 'Kirim WhatsApp' }}"
            aria-label="{{ $sent ? 'Kirim ulang' : 'Kirim' }} invoice {{ $invoice->invoice_number }} lewat WhatsApp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></button>
    </form>
@elseif ($invoice->packageOrder === null)
    <a href="{{ route('dashboard.invoices.edit', $invoice->id) }}" class="btn-icon opacity-50" title="Isi nomor WhatsApp klien dulu"
        aria-label="Isi nomor WhatsApp klien untuk invoice {{ $invoice->invoice_number }}"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
@endif
