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
<body class="grid-paper min-h-screen font-sans text-ink antialiased [-webkit-print-color-adjust:exact] [print-color-adjust:exact] print:bg-white print:[background-image:none]" x-data>
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
                <form action="{{ route('dashboard.invoices.whatsapp', $invoice->id) }}" method="POST"
                    data-confirm="PDF dan tautan invoice dikirim ke {{ $invoice->client_phone ?: 'nomor klien (belum diisi)' }}." data-confirm-title="Kirim lewat WhatsApp?" data-confirm-ok="Ya, kirim">
                    @csrf
                    <button type="submit" class="btn-secondary" aria-label="Kirim lewat WhatsApp" @disabled(! $invoice->client_phone)>
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span class="hidden md:inline">{{ $invoice->wa_sent_at ? 'Kirim ulang' : 'WhatsApp' }}</span>
                    </button>
                </form>
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
    <main class="px-4 py-8 print:p-0">
        <article class="relative mx-auto max-w-3xl overflow-hidden rounded-xl border-2 border-edge bg-surface shadow-hard-lg print:max-w-none print:rounded-none print:border-0 print:shadow-none">
            {{-- Masthead --}}
            <header class="border-b-[5px] border-navy bg-sun-400 px-5 py-7 text-navy sm:px-9 sm:py-9">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        @if ($settings->invoice_logo)
                            <img src="{{ $settings->invoice_logo_url }}" alt="Logo FKStudio" class="h-14 object-contain object-left">
                        @else
                            <p class="font-display text-3xl font-extrabold tracking-tight [font-stretch:80%]">FK<span class="text-brand-700">Studio</span></p>
                        @endif
                        <p class="mt-4 text-small font-bold">{{ $settings->invoice_company_name ?: 'FKStudio Agency' }}</p>
                        <p class="mt-1 max-w-xs text-caption leading-relaxed text-navy/75">{{ $settings->invoice_company_address ?: 'Jl. Raya Digital No. 21, Jakarta' }}</p>
                    </div>

                    <div class="sm:text-right">
                        <h1 class="font-display text-5xl text-navy font-extrabold leading-none tracking-tight [font-stretch:80%] sm:text-6xl">Invoice</h1>
                        <p class="mt-3 break-all text-small font-bold sm:text-base">{{ $invoice->invoice_number }}</p>
                    </div>
                </div>
            </header>

            <div class="p-5 sm:p-9">
                {{-- Billed to + facts --}}
                <div class="grid gap-6 sm:grid-cols-[1.4fr_1fr]">
                    <div class="min-w-0 border-l-[5px] border-navy bg-brand-soft px-5 py-4 dark:border-edge">
                        <p class="text-caption font-semibold text-brand-text">Ditagihkan kepada</p>
                        <h2 class="mt-1 font-display text-2xl font-bold [font-stretch:85%]">{{ $invoice->client_name }}</h2>
                        @if ($invoice->client_email)
                            <p class="mt-1 break-all text-small font-semibold text-brand-text"><i class="fa-solid fa-envelope mr-1.5" aria-hidden="true"></i>{{ $invoice->client_email }}</p>
                        @endif
                        @if ($invoice->client_address)
                            <p class="mt-1 max-w-sm text-small leading-relaxed text-muted">{!! nl2br(e($invoice->client_address)) !!}</p>
                        @endif
                    </div>

                    <dl class="grid grid-cols-2 content-start gap-x-4 gap-y-4 text-small sm:grid-cols-1 sm:gap-y-3">
                        <div>
                            <dt class="text-caption text-muted">Tanggal terbit</dt>
                            <dd class="font-bold">{{ $invoice->issue_date->locale('id')->translatedFormat('d F Y') }}</dd>
                        </div>
                        @if ($invoice->due_date)
                            <div>
                                <dt class="text-caption text-muted">Jatuh tempo</dt>
                                <dd class="font-bold">{{ $invoice->due_date->locale('id')->translatedFormat('d F Y') }}</dd>
                            </div>
                        @endif
                        <div class="col-span-2 sm:col-span-1">
                            <dt class="mb-1 text-caption text-muted">Status</dt>
                            <dd>
                                <span @class([
                                    'inline-flex items-center gap-2 rounded-full border-2 border-navy px-3.5 py-1 text-small font-bold text-navy',
                                    'bg-emerald-200' => $invoice->status === 'Paid',
                                    'bg-brand-200' => $invoice->status === 'Sent',
                                    'bg-sun-300' => ! in_array($invoice->status, ['Paid', 'Sent', 'Draft', 'Cancelled']),
                                    'bg-slate-200' => $invoice->status === 'Draft',
                                    'bg-red-200' => $invoice->status === 'Cancelled',
                                ])>{{ $st['label'] }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Items --}}
                <div class="mt-9">
                    <table class="w-full text-left text-small">
                        <thead>
                            <tr class="border-b-2 border-navy text-caption dark:border-edge">
                                <th scope="col" class="w-10 pb-2 pr-2 font-bold">No</th>
                                <th scope="col" class="pb-2 pr-2 font-bold">Deskripsi</th>
                                <th scope="col" class="w-14 pb-2 text-center font-bold">Jml</th>
                                <th scope="col" class="hidden w-32 pb-2 text-right font-bold sm:table-cell">Harga</th>
                                <th scope="col" class="w-28 pb-2 pl-2 text-right font-bold sm:w-32">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr class="border-b border-line">
                                    <td class="py-3.5 pr-2 align-top">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full border border-navy bg-sun-300 text-[11px] font-bold text-navy">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="py-3.5 pr-2 align-top">
                                        <p class="font-bold">{{ $item->description }}</p>
                                        <p class="mt-0.5 text-caption text-muted sm:hidden">Rp {{ number_format($item->unit_price, 0, ',', '.') }} / item</p>
                                    </td>
                                    <td class="py-3.5 text-center align-top">{{ $item->quantity }}</td>
                                    <td class="hidden py-3.5 text-right align-top text-muted sm:table-cell">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 pl-2 text-right align-top font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Payment + totals --}}
                <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-[1.25fr_1fr] print:grid-cols-[1.25fr_1fr]">
                    <div class="space-y-5">
                        @if ($invoice->status !== 'Paid' && ($settings->payment_methods || $settings->invoice_qris))
                            <div class="flex flex-wrap items-start gap-5 rounded-lg border-2 border-edge p-4">
                                <div class="min-w-0 flex-1">
                                    <p class="mb-2 text-caption font-semibold text-brand-text">Cara membayar</p>
                                    @if ($settings->payment_methods)
                                        <ul class="space-y-3">
                                            @foreach ($settings->payment_methods as $payment)
                                                <li>
                                                    <p class="text-small font-bold">{{ $payment['bank'] }}</p>
                                                    <p class="text-base font-bold tracking-wide text-brand-text">{{ $payment['number'] }}</p>
                                                    <p class="text-caption text-muted">a.n. {{ $payment['name'] }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                @if ($settings->invoice_qris)
                                    <figure class="shrink-0 text-center">
                                        <div class="rounded-md border border-line bg-white p-2">
                                            <img src="{{ $settings->invoice_qris_url }}" alt="Kode QRIS pembayaran" class="h-28 w-28 object-contain">
                                        </div>
                                        <figcaption class="mt-1.5 text-caption text-muted">Pindai untuk bayar</figcaption>
                                    </figure>
                                @endif
                            </div>
                        @endif

                        @if ($invoice->notes)
                            <div class="border-l-4 border-sun-400 bg-sun-100 px-4 py-3 text-navy dark:bg-surface-2 dark:text-ink">
                                <p class="mb-1 text-caption font-bold">Catatan</p>
                                <p class="text-small leading-relaxed">{!! nl2br(e($invoice->notes)) !!}</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <dl class="space-y-2 text-small">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted">Subtotal</dt>
                                <dd class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                            </div>
                            @if ($invoice->discount > 0)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-muted">Diskon</dt>
                                    <dd class="font-semibold">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</dd>
                                </div>
                            @endif
                            @if ($invoice->tax > 0)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-muted">Pajak</dt>
                                    <dd class="font-semibold">+ Rp {{ number_format($taxAmount, 0, ',', '.') }}</dd>
                                </div>
                            @endif
                        </dl>

                        <div class="mt-4 rounded-lg border-2 border-edge bg-navy px-5 py-4 shadow-hard-sm print:shadow-none">
                            <p class="text-caption text-brand-200">Total tagihan</p>
                            <p class="mt-0.5 font-display text-3xl font-extrabold text-sun-400 [font-stretch:85%]">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>

                        <div class="mt-8 text-center">
                            @if ($settings->invoice_signature)
                                <img src="{{ $settings->invoice_signature_url }}" alt="Tanda tangan" class="mx-auto mb-1 h-12 object-contain">
                            @else
                                <div class="mx-auto mb-1 flex h-12 w-32 items-end justify-center border-b-2 border-dashed border-edge/60 pb-1">
                                    <span class="text-caption text-muted">Tanda tangan</span>
                                </div>
                            @endif
                            <p class="text-small font-bold">{{ $settings->invoice_signer_name ?: 'Owner of FKStudio' }}</p>
                            <p class="text-caption text-muted">{{ $settings->invoice_signer_title ?: 'Authorized Representative' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="flex flex-wrap items-center justify-between gap-2 bg-navy px-5 py-3 text-caption text-white sm:px-9">
                <span>Terima kasih atas kepercayaan Anda.</span>
                <span class="font-semibold">{{ $settings->invoice_company_name ?: 'FKStudio IT Solution' }}</span>
            </footer>
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
