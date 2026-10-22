@php
    $rp = function ($n) {
        $n = (float) $n;
        if ($n >= 1_000_000_000) return 'Rp ' . rtrim(rtrim(number_format($n / 1_000_000_000, 1, ',', '.'), '0'), ',') . ' M';
        if ($n >= 1_000_000) return 'Rp ' . rtrim(rtrim(number_format($n / 1_000_000, 1, ',', '.'), '0'), ',') . ' jt';
        return 'Rp ' . number_format($n, 0, ',', '.');
    };
    $axis = function ($n) {
        if ($n >= 1_000_000_000) return round($n / 1_000_000_000, 1) . ' M';
        if ($n >= 1_000_000) return round($n / 1_000_000, 1) . ' jt';
        if ($n >= 1_000) return round($n / 1_000) . ' rb';
        return (string) round($n);
    };

    // Round the chart ceiling up to a "nice" number (1, 2 or 5 x 10^k) so ticks read cleanly.
    $maxValue = max($monthlyRevenue->max('value'), 1);
    $pow = 10 ** floor(log10($maxValue));
    $niceMax = collect([1, 2, 5, 10])->map(fn ($m) => $m * $pow)->first(fn ($v) => $v >= $maxValue);
    $ticks = collect(range(4, 0))->map(fn ($i) => $niceMax * $i / 4);

    $statusMeta = [
        'pending' => ['Menunggu pembayaran', 'warning', 'bg-sun-400'],
        'paid' => ['Dibayar', 'success', 'bg-mint-400'],
        'completed' => ['Selesai', 'info', 'bg-sky-400'],
        'cancelled' => ['Dibatalkan', 'danger', 'bg-danger-500'],
    ];
    $orderTotal = max($orderCounts->sum(), 1);
    $ticketBadge = ['open' => 'warning', 'in_progress' => 'info', 'resolved' => 'success', 'closed' => 'neutral'];
@endphp

<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-extrabold md:text-3xl">Halo, <span class="gradient-text">{{ Str::before(Auth::user()->name, ' ') }}</span></h2>
            <p class="text-muted">Ringkasan bisnis dan aktivitas terbaru FKStudio.</p>
        </div>

        {{-- KPI --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('dashboard.orders.index') }}" class="block rounded-lg focus-visible:ring-2">
                <x-ui.stat-card label="Pendapatan bulan ini" :value="$rp($revenueThisMonth)" icon="fa-wallet" tone="brand"
                    :trend="$revenueTrend !== null ? abs($revenueTrend) . '% vs bulan lalu' : null" :trend-up="($revenueTrend ?? 0) >= 0" class="h-full transition hover:border-brand-300" />
            </a>
            <a href="{{ route('dashboard.orders.index') }}" class="block rounded-lg">
                <x-ui.stat-card label="Menunggu pembayaran" :value="$orderCounts['pending'] ?? 0" icon="fa-hourglass-half" tone="sun" class="h-full transition hover:border-brand-300" />
            </a>
            <a href="{{ route('dashboard.orders.index') }}" class="block rounded-lg">
                <x-ui.stat-card label="Sedang dikerjakan" :value="$inProgressCount" icon="fa-code" tone="sky" class="h-full transition hover:border-brand-300" />
            </a>
            <a href="{{ route('dashboard.tickets.index') }}" class="block rounded-lg">
                <x-ui.stat-card label="Tiket terbuka" :value="$openTickets" icon="fa-headset" tone="pink" class="h-full transition hover:border-brand-300" />
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Revenue chart --}}
            <x-ui.card class="lg:col-span-2" x-data="{ tip: null }">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <h3 class="text-lg">Pendapatan 6 bulan terakhir</h3>
                        <p class="text-small text-muted">Dihitung dari pesanan yang sudah dibayar, per bulan pemesanan.</p>
                    </div>
                    <p class="text-right"><span class="block text-caption text-muted">Bulan ini</span><span class="text-xl font-extrabold">{{ $rp($revenueThisMonth) }}</span></p>
                </div>

                <figure class="mt-6" aria-label="Grafik pendapatan per bulan">
                    <div class="flex gap-3">
                        <div class="flex h-56 w-12 shrink-0 flex-col justify-between text-right text-caption text-muted" aria-hidden="true">
                            @foreach ($ticks as $t)<span class="leading-none">{{ $axis($t) }}</span>@endforeach
                        </div>
                        <div class="relative h-56 flex-1">
                            <div class="absolute inset-0 flex flex-col justify-between" aria-hidden="true">
                                @foreach ($ticks as $t)<span class="h-px w-full bg-line"></span>@endforeach
                            </div>
                            <div class="relative flex h-full items-end justify-between gap-2" @mouseleave="tip = null">
                                @foreach ($monthlyRevenue as $i => $m)
                                    @php($pct = $niceMax > 0 ? ($m['value'] / $niceMax) * 100 : 0)
                                    <div class="relative flex h-full flex-1 flex-col items-center justify-end"
                                        tabindex="0" role="img" aria-label="{{ $m['full'] }}: {{ $rp($m['value']) }}"
                                        @mouseenter="tip = {{ $i }}" @focus="tip = {{ $i }}" @blur="tip = null">
                                        @if ($loop->last && $m['value'] > 0)
                                            <span class="mb-1 text-caption font-bold">{{ $rp($m['value']) }}</span>
                                        @endif
                                        <span class="w-full max-w-6 rounded-t-[4px] transition"
                                            :class="tip === {{ $i }} ? 'bg-brand-500' : 'bg-brand-600'"
                                            style="height: {{ max($pct, $m['value'] > 0 ? 2 : 0.5) }}%"></span>

                                        <div x-show="tip === {{ $i }}" x-cloak x-transition.opacity
                                            class="pointer-events-none absolute bottom-full z-10 mb-2 w-max -translate-y-1 rounded-md border border-line bg-surface px-3 py-2 text-center shadow-pop"
                                            style="bottom: calc({{ min($pct, 100) }}% + 1.75rem)">
                                            <p class="text-small font-extrabold">{{ $rp($m['value']) }}</p>
                                            <p class="text-caption text-muted">{{ $m['full'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="ml-[3.75rem] mt-2 flex justify-between gap-2 text-center text-caption font-medium text-muted" aria-hidden="true">
                        @foreach ($monthlyRevenue as $m)<span class="flex-1 capitalize">{{ $m['label'] }}</span>@endforeach
                    </div>
                    <details class="mt-4 text-small">
                        <summary class="cursor-pointer font-semibold text-brand-text">Lihat sebagai tabel</summary>
                        <div class="table-wrap mt-3"><table class="table-base">
                            <thead><tr><th>Bulan</th><th class="text-right">Pendapatan</th></tr></thead>
                            <tbody>@foreach ($monthlyRevenue as $m)<tr><td>{{ $m['full'] }}</td><td class="text-right font-semibold">Rp {{ number_format($m['value'], 0, ',', '.') }}</td></tr>@endforeach</tbody>
                        </table></div>
                    </details>
                </figure>
            </x-ui.card>

            {{-- Order status --}}
            <x-ui.card>
                <h3 class="text-lg">Status pesanan</h3>
                <p class="text-small text-muted">{{ $orderCounts->sum() }} pesanan secara keseluruhan.</p>

                <div class="mt-5 flex h-3 w-full gap-0.5 overflow-hidden rounded-full bg-surface-2" role="img"
                    aria-label="Komposisi status pesanan">
                    @foreach ($statusMeta as $key => [$label, $variant, $bar])
                        @if (($orderCounts[$key] ?? 0) > 0)
                            <span class="{{ $bar }} first:rounded-l-full last:rounded-r-full" style="width: {{ (($orderCounts[$key] ?? 0) / $orderTotal) * 100 }}%"></span>
                        @endif
                    @endforeach
                </div>

                <ul class="mt-5 space-y-3">
                    @foreach ($statusMeta as $key => [$label, $variant, $bar])
                        @php($count = $orderCounts[$key] ?? 0)
                        <li class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-3"><span class="h-3 w-3 rounded-sm {{ $bar }}" aria-hidden="true"></span><span class="text-small font-medium">{{ $label }}</span></span>
                            <span class="text-small"><strong>{{ $count }}</strong> <span class="text-muted">({{ round(($count / $orderTotal) * 100) }}%)</span></span>
                        </li>
                    @endforeach
                </ul>

                @if ($unpaidInvoices > 0)
                    <a href="{{ route('dashboard.invoices.index') }}" class="mt-6 flex items-center gap-3 rounded-lg bg-sun-50 p-3 text-small text-sun-700 transition hover:bg-sun-100 dark:bg-sun-400/10 dark:text-sun-300">
                        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                        <span><strong>{{ $unpaidInvoices }}</strong> invoice belum dibayar</span>
                        <i class="fa-solid fa-chevron-right ml-auto text-caption" aria-hidden="true"></i>
                    </a>
                @endif
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Recent orders --}}
            <x-ui.card :padded="false" class="lg:col-span-2">
                <div class="flex items-center justify-between p-5 pb-3 md:p-6 md:pb-3">
                    <h3 class="text-lg">Pesanan terbaru</h3>
                    <a href="{{ route('dashboard.orders.index') }}" class="text-small font-semibold text-brand-text hover:underline">Lihat semua</a>
                </div>
                @if ($recentOrders->isEmpty())
                    <x-ui.empty-state icon="fa-cart-shopping" title="Belum ada pesanan" text="Pesanan pelanggan akan muncul di sini." />
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead><tr><th>Pelanggan</th><th>Paket</th><th>Status</th><th class="text-right">Total</th></tr></thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td><p class="font-semibold">{{ $order->user?->name ?? '—' }}</p><p class="text-caption text-muted">{{ $order->created_at->translatedFormat('d M Y') }}</p></td>
                                        <td>{{ $order->package_name }}</td>
                                        <td><x-ui.badge :variant="$statusMeta[$order->status][1] ?? 'neutral'" :dot="true">{{ $statusMeta[$order->status][0] ?? $order->status }}</x-ui.badge></td>
                                        <td class="whitespace-nowrap text-right font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>

            {{-- Tickets + quick actions --}}
            <div class="space-y-6">
                <x-ui.card>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg">Tiket terbaru</h3>
                        <a href="{{ route('dashboard.tickets.index') }}" class="text-small font-semibold text-brand-text hover:underline">Semua</a>
                    </div>
                    @forelse ($recentTickets as $ticket)
                        <div class="mt-4 flex items-start gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ in_array($ticket->status, ['open', 'in_progress']) ? 'bg-pink-500' : 'bg-mint-400' }}" aria-hidden="true"></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-small font-semibold">{{ $ticket->subject }}</p>
                                <p class="text-caption text-muted">{{ $ticket->user?->name ?? '—' }} &bull; {{ $ticket->created_at->diffForHumans() }}</p>
                            </div>
                            <x-ui.badge :variant="$ticketBadge[$ticket->status] ?? 'neutral'">{{ str_replace('_', ' ', $ticket->status) }}</x-ui.badge>
                        </div>
                    @empty
                        <p class="mt-4 text-small text-muted">Belum ada tiket bantuan.</p>
                    @endforelse
                </x-ui.card>

                <x-ui.card>
                    <h3 class="text-lg">Aksi cepat</h3>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        @foreach ([
                            ['dashboard.invoices.create', 'fa-file-invoice-dollar', 'Buat invoice'],
                            ['dashboard.packages.index', 'fa-tags', 'Kelola paket'],
                            ['dashboard.blogs.index', 'fa-pen-nib', 'Tulis blog'],
                            ['dashboard.hero.edit', 'fa-wand-magic-sparkles', 'Edit hero'],
                        ] as [$route, $icon, $label])
                            <a href="{{ route($route) }}" class="flex flex-col items-center gap-2 rounded-lg border border-line bg-surface-2 p-4 text-center text-small font-semibold transition hover:border-brand-300 hover:bg-brand-soft hover:text-brand-text">
                                <i class="fa-solid {{ $icon }} text-xl text-brand-500" aria-hidden="true"></i>{{ $label }}
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>
            </div>
        </div>

        {{-- Content summary --}}
        <x-ui.card>
            <h3 class="text-lg">Ringkasan konten situs</h3>
            <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-7">
                @foreach ([['Layanan', $servicesCount, 'dashboard.services.index'], ['Proyek', $projectsCount, 'dashboard.projects.index'], ['Paket', $packagesCount, 'dashboard.packages.index'], ['Blog', $blogsCount, 'dashboard.blogs.index'], ['Testimoni', $testimonialsCount, 'dashboard.testimonials.index'], ['Tim', $ownersCount, 'dashboard.owners.index'], ['Invoice', $invoicesCount, 'dashboard.invoices.index']] as [$label, $count, $route])
                    <a href="{{ route($route) }}" class="rounded-lg bg-surface-2 p-4 transition hover:bg-brand-soft">
                        <dd class="text-2xl font-extrabold">{{ $count }}</dd>
                        <dt class="text-small text-muted">{{ $label }}</dt>
                    </a>
                @endforeach
            </dl>
        </x-ui.card>
    </div>
</x-app-layout>
