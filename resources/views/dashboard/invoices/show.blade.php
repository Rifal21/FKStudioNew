@php
    $isPublicView = ($isPublic ?? false) || ! Auth::check();
    $statusMap = [
        'Paid' => ['label' => 'Lunas', 'variant' => 'success'],
        'Sent' => ['label' => 'Terkirim', 'variant' => 'info'],
        'Unpaid' => ['label' => 'Belum dibayar', 'variant' => 'warning'],
        'Draft' => ['label' => 'Draft', 'variant' => 'neutral'],
        'Cancelled' => ['label' => 'Dibatalkan', 'variant' => 'danger'],
    ];
    $st = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'variant' => 'warning'];
    $subtotal = $invoice->items->sum('subtotal');
    $discountAmount = ($invoice->discount_type === 'percent')
        ? ($subtotal * ($invoice->discount / 100))
        : $invoice->discount;
    $taxAmount = ($invoice->tax_type === 'percent')
        ? (($subtotal - $discountAmount) * ($invoice->tax / 100))
        : $invoice->tax;
    $ogImage = $settings->invoice_logo_url ?: $settings->logo_url;
    $ogDescription = 'Invoice resmi FKStudio untuk '.$invoice->client_name.'. Total: Rp '.number_format($invoice->total_amount, 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Invoice {{ $invoice->invoice_number }} | FKStudio</title>

    <!-- SEO & Social Sharing -->
    <meta name="title" content="Invoice {{ $invoice->invoice_number }} - FKStudio">
    <meta name="description" content="{{ $ogDescription }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('invoices.public.show', $invoice->id) }}">
    <meta property="og:title" content="Invoice {{ $invoice->invoice_number }} - FKStudio">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ route('invoices.public.show', $invoice->id) }}">
    <meta property="twitter:title" content="Invoice {{ $invoice->invoice_number }} - FKStudio">
    <meta property="twitter:description" content="{{ $ogDescription }}">
    <meta property="twitter:image" content="{{ $ogImage }}">

    @include('partials.head-assets')
</head>
<body class="min-h-screen bg-bg font-sans text-ink antialiased [-webkit-print-color-adjust:exact] [print-color-adjust:exact] print:bg-surface" x-data>
    <x-ui.toaster />

    {{-- Toolbar --}}
    <header class="sticky top-0 z-40 border-b border-line bg-bg/85 backdrop-blur-xl print:hidden">
        <div class="mx-auto flex h-16 max-w-3xl items-center gap-3 px-4">
            @if ($isPublicView)
                <a href="{{ route('home') }}" class="btn-icon -ml-2 shrink-0" aria-label="Ke beranda FKStudio"><i class="fa-solid fa-house" aria-hidden="true"></i></a>
            @else
                <a href="{{ route('dashboard.invoices.index') }}" class="btn-icon -ml-2 shrink-0" aria-label="Kembali ke daftar invoice"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-caption text-muted">Invoice</p>
                <p class="truncate text-small font-bold">{{ $invoice->invoice_number }}</p>
            </div>
            @auth
                <button type="button" class="btn-secondary" aria-label="Salin tautan publik"
                    @click="navigator.clipboard.writeText(@js(route('invoices.public.show', $invoice->id))).then(() => window.toast('success', 'Tautan invoice disalin'), () => window.toast('error', 'Gagal menyalin tautan'))">
                    <i class="fa-solid fa-share-nodes" aria-hidden="true"></i><span class="hidden md:inline">Bagikan</span>
                </button>
            @endauth
            <a href="{{ route('dashboard.invoices.download', $invoice->id) }}" class="btn-secondary" aria-label="Unduh PDF">
                <i class="fa-solid fa-download" aria-hidden="true"></i><span class="hidden md:inline">Unduh PDF</span>
            </a>
            <button type="button" class="btn-primary" onclick="window.print()" aria-label="Cetak invoice">
                <i class="fa-solid fa-print" aria-hidden="true"></i><span class="hidden md:inline">Cetak</span>
            </button>
        </div>
    </header>

    {{-- Invoice sheet --}}
    <main class="px-4 py-6 print:p-0">
        <article class="mx-auto max-w-3xl rounded-lg border border-line bg-surface p-5 shadow-soft sm:p-8 print:max-w-none print:rounded-none print:border-0 print:p-0 print:shadow-none">
            {{-- Header --}}
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    @if ($settings->invoice_logo)
                        <img src="{{ $settings->invoice_logo_url }}" alt="Logo FKStudio" class="h-14 object-contain object-left">
                    @else
                        <p class="text-3xl font-extrabold tracking-tight">FK<span class="text-brand-text">Studio</span></p>
                    @endif
                    <p class="mt-4 text-small font-bold">{{ $settings->invoice_company_name ?: 'FKStudio Agency' }}</p>
                    <p class="mt-1 max-w-xs text-caption leading-relaxed text-muted">{{ $settings->invoice_company_address ?: 'Jl. Raya Digital No. 21, Jakarta' }}</p>
                </div>

                <div class="sm:text-right">
                    <span class="inline-block rounded-md bg-brand-600 px-3 py-1 text-small font-bold text-white">Invoice</span>
                    <h1 class="mt-3 break-all text-xl font-extrabold tracking-tight sm:text-2xl">{{ $invoice->invoice_number }}</h1>
                    <dl class="mt-1 space-y-0.5 text-small">
                        <div class="flex gap-2 sm:justify-end">
                            <dt class="text-muted">Tanggal</dt>
                            <dd class="font-semibold">{{ $invoice->issue_date->locale('id')->translatedFormat('d F Y') }}</dd>
                        </div>
                        @if ($invoice->due_date)
                            <div class="flex gap-2 sm:justify-end">
                                <dt class="text-muted">Jatuh tempo</dt>
                                <dd class="font-semibold">{{ $invoice->due_date->locale('id')->translatedFormat('d F Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Billed to + status --}}
            <div class="mt-8 flex flex-col gap-4 border-y border-line py-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <p class="text-caption font-semibold text-brand-text">Ditagihkan kepada</p>
                    <h2 class="mt-1 text-lg font-bold">{{ $invoice->client_name }}</h2>
                    @if ($invoice->client_email)
                        <p class="mt-0.5 break-all text-small font-medium text-brand-text"><i class="fa-solid fa-envelope mr-1.5" aria-hidden="true"></i>{{ $invoice->client_email }}</p>
                    @endif
                    @if ($invoice->client_address)
                        <p class="mt-1 max-w-sm text-small leading-relaxed text-muted">{!! nl2br(e($invoice->client_address)) !!}</p>
                    @endif
                </div>
                <div class="sm:text-right">
                    <p class="mb-1.5 text-caption font-semibold text-muted">Status invoice</p>
                    <x-ui.badge :variant="$st['variant']" dot class="px-3 py-1.5 text-small">{{ $st['label'] }}</x-ui.badge>
                </div>
            </div>

            {{-- Items --}}
            <div class="mt-6">
                <table class="w-full text-left text-small">
                    <thead>
                        <tr class="border-b border-line text-caption text-muted">
                            <th scope="col" class="py-2 pr-2 font-semibold">Deskripsi</th>
                            <th scope="col" class="w-16 py-2 text-center font-semibold">Jml</th>
                            <th scope="col" class="hidden w-32 py-2 text-right font-semibold sm:table-cell">Harga</th>
                            <th scope="col" class="w-28 py-2 pl-2 text-right font-semibold sm:w-32">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr class="border-b border-line last:border-b-0">
                                <td class="py-3 pr-2 align-top">
                                    <p class="font-semibold">{{ $item->description }}</p>
                                    <p class="mt-0.5 text-caption text-muted sm:hidden">Rp {{ number_format($item->unit_price, 0, ',', '.') }} / item</p>
                                </td>
                                <td class="py-3 text-center align-top">{{ $item->quantity }}</td>
                                <td class="hidden py-3 text-right align-top text-muted sm:table-cell">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 pl-2 text-right align-top font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="mt-6 grid grid-cols-1 gap-8 border-t border-line pt-6 md:grid-cols-2 print:grid-cols-2">
                {{-- Payment info + notes --}}
                <div class="space-y-5">
                    @if ($invoice->status !== 'Paid')
                        <div class="flex flex-wrap items-start gap-5">
                            <div class="min-w-0 flex-1">
                                <p class="mb-2 text-caption font-semibold text-brand-text">Metode pembayaran</p>
                                @if ($settings->payment_methods)
                                    <ul class="space-y-3">
                                        @foreach ($settings->payment_methods as $payment)
                                            <li>
                                                <p class="text-small font-bold">{{ $payment['bank'] }}</p>
                                                <p class="text-small font-semibold tracking-wide">{{ $payment['number'] }}</p>
                                                <p class="text-caption text-muted">a.n. {{ $payment['name'] }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            @if ($settings->invoice_qris)
                                <figure class="shrink-0 text-center">
                                    <div class="rounded-md border border-line bg-surface p-2">
                                        <img src="{{ $settings->invoice_qris_url }}" alt="Kode QRIS pembayaran" class="h-28 w-28 object-contain">
                                    </div>
                                    <figcaption class="mt-1.5 text-caption text-muted">Pindai untuk bayar</figcaption>
                                </figure>
                            @endif
                        </div>
                    @endif

                    @if ($invoice->notes)
                        <div class="rounded-lg border border-line bg-surface-2 p-4">
                            <p class="mb-1.5 text-caption font-semibold text-muted"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i> Catatan / ketentuan</p>
                            <p class="text-small leading-relaxed">{!! nl2br(e($invoice->notes)) !!}</p>
                        </div>
                    @endif
                </div>

                {{-- Totals + signature --}}
                <div class="space-y-6">
                    <div class="space-y-2 rounded-lg bg-surface-2 p-4 text-small">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if ($invoice->discount > 0)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-muted">Diskon</span>
                                <span class="font-semibold">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($invoice->tax > 0)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-muted">Pajak</span>
                                <span class="font-semibold">+ Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                            <span class="font-bold">Total</span>
                            <span class="text-xl font-extrabold text-brand-text">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="text-center">
                        @if ($settings->invoice_signature)
                            <img src="{{ $settings->invoice_signature_url }}" alt="Tanda tangan" class="mx-auto mb-1 h-12 object-contain">
                        @else
                            <div class="mx-auto mb-1 flex h-12 w-28 items-end justify-center border-b-2 border-dashed border-line pb-1">
                                <span class="text-caption text-muted">Tanda tangan</span>
                            </div>
                        @endif
                        <p class="text-small font-bold">{{ $settings->invoice_signer_name ?: 'Owner of FKStudio' }}</p>
                        <p class="text-caption text-muted">{{ $settings->invoice_signer_title ?: 'Authorized Representative' }}</p>
                    </div>
                </div>
            </div>

            <p class="mt-8 border-t border-line pt-5 text-center text-small font-medium text-muted">Terima kasih atas kepercayaan Anda.</p>
        </article>
    </main>

    <script>
        // Always print on a light sheet, even when the dark theme is active.
        (function () {
            var wasDark = false;
            window.addEventListener('beforeprint', function () {
                wasDark = document.documentElement.classList.contains('dark');
                document.documentElement.classList.remove('dark');
            });
            window.addEventListener('afterprint', function () {
                if (wasDark) document.documentElement.classList.add('dark');
            });
        })();
    </script>
</body>
</html>
