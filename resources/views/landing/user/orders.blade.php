@php
    $statusBadge = ['pending' => 'warning', 'paid' => 'success', 'completed' => 'info', 'cancelled' => 'danger'];
    $statusLabel = ['pending' => 'Menunggu pembayaran', 'paid' => 'Dibayar', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $domBadge = ['pending' => 'warning', 'registered' => 'success', 'failed' => 'danger'];
    $domLabel = ['pending' => 'Proses registrasi', 'registered' => 'Aktif / terdaftar', 'failed' => 'Registrasi tertunda'];
    $steps = [
        ['label' => 'Menunggu', 'icon' => 'fa-clock'],
        ['label' => 'Pengerjaan', 'icon' => 'fa-laptop-code'],
        ['label' => 'Revisi', 'icon' => 'fa-rotate-left'],
        ['label' => 'Selesai', 'icon' => 'fa-circle-check'],
    ];
@endphp
<x-layouts.public :noindex="true" title="Pesanan Saya" description="Pantau status pembayaran dan pengerjaan website Anda.">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="relative pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_30%_at_50%_0%,rgb(30_155_229/0.12),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative max-w-5xl">
            <x-ui.page-header title="Pesanan saya" description="Pantau pembayaran, invoice, dan progres pengerjaan website Anda.">
                <x-slot:actions>
                    <x-ui.button :href="route('package.index')" variant="secondary" icon="fa-plus">Pesan paket baru</x-ui.button>
                </x-slot:actions>
            </x-ui.page-header>

            <div class="space-y-6">
                @forelse ($orders as $order)
                    @php
                        $paidish = in_array($order->status, ['paid', 'completed']);
                        $needsFinal = $order->status === 'paid' && $order->final_invoice_id && $order->finalInvoice && $order->finalInvoice->status === 'Unpaid';
                        $taxBase = $order->subtotal_amount - $order->discount_amount;
                        $taxRate = $taxBase > 0 ? round(($order->tax_amount / $taxBase) * 100) : 11;
                        $currentStep = $order->workStatusStep();
                    @endphp
                    <article class="card overflow-hidden">
                        {{-- Header --}}
                        <div class="flex flex-col gap-5 p-5 md:flex-row md:items-start md:justify-between md:p-6">
                            <div class="flex min-w-0 gap-4">
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-2xl text-brand-700 dark:bg-brand-500/20 dark:text-brand-300"><i class="fa-solid fa-box" aria-hidden="true"></i></span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-xl">{{ $order->package_name }}</h2>
                                        <x-ui.badge :variant="$statusBadge[$order->status] ?? 'neutral'" :dot="true">{{ $statusLabel[$order->status] ?? $order->status }}</x-ui.badge>
                                    </div>
                                    <p class="mt-1 text-small text-muted">ID <span class="font-mono text-ink">#{{ substr($order->id, 0, 8) }}</span> &bull; {{ $order->created_at->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 md:justify-end">
                                @if ($order->payment_scheme === 'dp' && $order->status === 'pending')
                                    <x-ui.button :href="$order->payment_url ?: route('checkout.success', $order->id)">Bayar DP (50%)</x-ui.button>
                                @elseif ($order->payment_scheme !== 'dp' && $order->status === 'pending')
                                    <x-ui.button :href="$order->payment_url ?: route('checkout.success', $order->id)">Bayar sekarang</x-ui.button>
                                @elseif ($needsFinal)
                                    <x-ui.button :href="route('checkout.pelunasan', $order->id)" variant="sun" icon="fa-bolt">Bayar pelunasan (50%)</x-ui.button>
                                @endif
                                @if ($order->invoice_id)
                                    <x-ui.button :href="route('invoices.public.show', $order->invoice_id)" target="_blank" variant="secondary" size="sm" icon="fa-file-invoice">Invoice{{ $order->payment_scheme === 'dp' ? ' DP' : '' }}</x-ui.button>
                                @endif
                                @if ($order->final_invoice_id)
                                    <x-ui.button :href="route('invoices.public.show', $order->final_invoice_id)" target="_blank" variant="secondary" size="sm" icon="fa-file-invoice">Invoice pelunasan</x-ui.button>
                                @endif
                                @if ($order->tickets->count() > 0)
                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                                        <button type="button" @click="open = !open" class="btn-secondary btn-sm" :aria-expanded="open">
                                            Tiket ({{ $order->tickets->count() }}) <i class="fa-solid fa-chevron-down text-caption transition" :class="open && 'rotate-180'" aria-hidden="true"></i>
                                        </button>
                                        <div x-show="open" x-cloak x-transition class="absolute right-0 z-20 mt-2 w-72 space-y-3 rounded-lg border border-line bg-surface p-4 shadow-pop">
                                            @foreach ($order->tickets as $ticket)
                                                <div @class(['border-l-4 pl-3', 'border-mint-400' => $ticket->status === 'resolved', 'border-brand-500' => $ticket->status !== 'resolved'])>
                                                    <p class="text-caption text-muted">{{ $ticket->created_at->translatedFormat('d M Y H:i') }}</p>
                                                    <p class="text-small font-semibold">{{ $ticket->subject }}</p>
                                                    <p class="text-caption font-semibold capitalize text-muted">{{ str_replace('_', ' ', $ticket->status) }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Figures --}}
                        <dl class="grid grid-cols-2 gap-4 border-t border-line bg-surface-2/50 px-5 py-4 md:grid-cols-4 md:px-6">
                            <div><dt class="text-caption text-muted">Total proyek</dt><dd class="font-extrabold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd></div>
                            <div><dt class="text-caption text-muted">Skema</dt><dd class="font-semibold">{{ $order->payment_scheme === 'dp' ? 'DP 50%' : 'Lunas 100%' }}</dd></div>
                            @if ($order->payment_scheme === 'dp')
                                <div>
                                    <dt class="text-caption text-muted">DP (50%)</dt>
                                    <dd class="font-semibold">Rp {{ number_format($order->dp_amount, 0, ',', '.') }} <x-ui.badge :variant="$paidish ? 'success' : 'warning'" class="ml-1">{{ $paidish ? 'Lunas' : 'Belum dibayar' }}</x-ui.badge></dd>
                                </div>
                                <div>
                                    <dt class="text-caption text-muted">Sisa pelunasan</dt>
                                    <dd class="font-semibold">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}
                                        @if ($order->status === 'completed')<x-ui.badge variant="success" class="ml-1">Lunas</x-ui.badge>
                                        @elseif ($order->final_invoice_id)<x-ui.badge variant="warning" class="ml-1">Ditagih</x-ui.badge>
                                        @else<x-ui.badge class="ml-1">Belum ditagih</x-ui.badge>@endif
                                    </dd>
                                </div>
                            @endif
                            <div><dt class="text-caption text-muted">Metode bayar</dt><dd class="truncate font-semibold">{{ str_replace('Duitku|', '', $order->payment_method ?: 'Belum memilih') }}</dd></div>
                        </dl>

                        {{-- Details + Progress --}}
                        <div class="grid grid-cols-1 gap-6 p-5 md:p-6 lg:grid-cols-3">
                            <div class="min-w-0 space-y-4">
                                <h3 class="text-small font-bold uppercase tracking-wider text-brand-text">Detail website</h3>
                                <dl class="space-y-3 text-small">
                                    <div><dt class="text-caption text-muted">Nama brand</dt><dd class="font-semibold">{{ $order->website_name }}</dd></div>
                                    <div><dt class="text-caption text-muted">Subdomain diinginkan</dt><dd class="break-all font-semibold">{{ $order->website_url ?: '-' }}</dd></div>
                                    <div><dt class="text-caption text-muted">Jenis bisnis</dt><dd class="font-semibold">{{ $order->business_type }}</dd></div>
                                    @if ($order->client_notes)
                                        <div><dt class="text-caption text-muted">Catatan / brief</dt><dd class="text-muted">{{ $order->client_notes }}</dd></div>
                                    @endif
                                    @if ($order->buy_domain && $order->domain_name)
                                        <div class="rounded-lg bg-surface-2 p-3">
                                            <dt class="text-caption font-semibold text-muted"><i class="fa-solid fa-server" aria-hidden="true"></i> Domain</dt>
                                            <dd class="break-all font-mono font-semibold">{{ $order->domain_name }}</dd>
                                            <dd class="mt-1"><x-ui.badge :variant="$domBadge[$order->domain_status] ?? 'neutral'">{{ $domLabel[$order->domain_status] ?? $order->domain_status }}</x-ui.badge></dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            <div class="min-w-0 lg:col-span-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h3 class="text-small font-bold uppercase tracking-wider text-brand-text">Progres pengerjaan</h3>
                                    <p class="text-small text-muted">Estimasi selesai: <strong class="text-ink">{{ $order->delivery_date ? $order->delivery_date->translatedFormat('d M Y') : 'Menunggu antrean' }}</strong></p>
                                </div>

                                @if ($order->work_status === 'cancelled')
                                    <div class="mt-4 flex items-start gap-4 rounded-lg bg-danger-50 p-5 dark:bg-danger-500/10">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-red-300"><i class="fa-solid fa-ban" aria-hidden="true"></i></span>
                                        <div><p class="font-bold">Pengerjaan dibatalkan</p><p class="text-small text-muted">Silakan hubungi tim kami untuk informasi lebih lanjut terkait pembatalan ini.</p></div>
                                    </div>
                                @else
                                    <ol class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-4">
                                        @foreach ($steps as $index => $step)
                                            @php($done = $index < $currentStep)
                                            @php($active = $index === $currentStep)
                                            <li class="relative flex items-center gap-3 sm:flex-col sm:text-center" @if ($active) aria-current="step" @endif>
                                                @unless ($loop->last)
                                                    <span class="absolute left-6 top-12 h-4 w-0.5 sm:left-[calc(50%+1.75rem)] sm:top-6 sm:h-0.5 sm:w-[calc(100%-3.5rem)] {{ $done ? 'bg-brand-600' : 'bg-line' }}" aria-hidden="true"></span>
                                                @endunless
                                                <span @class([
                                                    'relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 text-lg transition',
                                                    'border-transparent bg-brand-600 text-white shadow-glow' => $done,
                                                    'border-brand-600 bg-surface text-brand-text ring-4 ring-brand-600/15' => $active,
                                                    'border-line bg-surface text-muted' => ! $done && ! $active,
                                                ])>
                                                    <i class="fa-solid {{ $done ? 'fa-check' : $step['icon'] }}" aria-hidden="true"></i>
                                                </span>
                                                <span>
                                                    <span @class(['block text-small font-bold', 'text-brand-text' => $active, 'text-ink' => $done, 'text-muted' => ! $done && ! $active])>{{ $step['label'] }}</span>
                                                    <span class="block text-caption text-muted">{{ $active ? 'Tahap aktif' : ($done ? 'Selesai' : 'Menunggu') }}</span>
                                                </span>
                                            </li>
                                        @endforeach
                                    </ol>
                                @endif

                                <details class="mt-6 rounded-lg border border-line">
                                    <summary class="cursor-pointer px-4 py-3 text-small font-semibold">Rincian harga proyek</summary>
                                    <dl class="space-y-2 border-t border-line px-4 py-4 text-small">
                                        <div class="flex justify-between gap-4"><dt class="text-muted">Harga paket &amp; layanan</dt><dd class="font-semibold">Rp {{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 0, ',', '.') }}</dd></div>
                                        @if ($order->discount_amount > 0)
                                            <div class="flex justify-between gap-4 text-mint-800 dark:text-mint-300"><dt>Voucher <span class="font-mono">{{ $order->voucher_code }}</span></dt><dd class="font-semibold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</dd></div>
                                        @endif
                                        <div class="flex justify-between gap-4"><dt class="text-muted">PPN ({{ $taxRate }}%)</dt><dd class="font-semibold">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</dd></div>
                                        <div class="flex justify-between gap-4 border-t border-line pt-2"><dt class="font-bold">Grand total</dt><dd class="font-extrabold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd></div>
                                    </dl>
                                </details>
                            </div>
                        </div>
                    </article>
                @empty
                    <x-ui.card>
                        <x-ui.empty-state icon="fa-box-open" title="Belum ada pesanan" text="Anda belum memesan paket apa pun. Jelajahi layanan kami dan mulai proyek pertama Anda!">
                            <x-ui.button :href="route('package.index')" icon="fa-arrow-right" :icon-right="true">Lihat paket layanan</x-ui.button>
                        </x-ui.empty-state>
                    </x-ui.card>
                @endforelse
            </div>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
