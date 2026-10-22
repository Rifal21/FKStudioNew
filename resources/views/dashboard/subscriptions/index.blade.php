<x-app-layout>
    <x-slot name="header">Langganan</x-slot>

    @php
        $statusMap = [
            'active' => ['Aktif', 'success'],
            'paused' => ['Dijeda', 'warning'],
            'ended' => ['Selesai', 'neutral'],
        ];
        $active = $subscriptions->where('status', 'active');
        $fmt = fn ($d) => $d?->locale('id')->translatedFormat('d M Y') ?? '-';
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Langganan" description="Invoice berulang yang terbit otomatis tiap bulan pada tanggal yang Anda atur.">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.whatsapp.edit')" variant="secondary" icon="fa-brands fa-whatsapp">Pengaturan WhatsApp</x-ui.button>
                <x-ui.button :href="route('dashboard.subscriptions.create')" icon="fa-plus">Langganan baru</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-ui.stat-card label="Langganan aktif" :value="$active->count()" icon="fa-rotate" tone="brand" />
            <x-ui.stat-card label="Tagihan per bulan" :value="'Rp '.number_format($active->sum(fn ($s) => $s->monthlyTotal()), 0, ',', '.')" icon="fa-coins" tone="sun" />
            <x-ui.stat-card label="Invoice belum terkirim" :value="$pending->count()" icon="fa-paper-plane" tone="mint" />
        </div>

        @if ($pending->isNotEmpty())
            <section class="rounded-lg border-2 border-edge bg-sun-100 p-5 text-navy shadow-hard-sm dark:bg-surface-2 dark:text-ink" aria-labelledby="pending-title">
                <h2 id="pending-title" class="text-lg font-bold">Menunggu dikirim</h2>
                <p class="text-small opacity-80">Invoice dari langganan yang masih Draft dan belum sampai ke klien.</p>
                <ul class="mt-4 divide-y divide-edge/30">
                    @foreach ($pending as $invoice)
                        <li class="flex flex-wrap items-center gap-3 py-3">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('dashboard.invoices.show', $invoice->id) }}" class="font-bold hover:underline">{{ $invoice->invoice_number }}</a>
                                <span class="text-small"> &middot; {{ $invoice->client_name }} &middot; Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                @if ($invoice->wa_error)
                                    <p class="text-caption text-danger-700 dark:text-red-300"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> {{ $invoice->wa_error }}</p>
                                @endif
                            </div>
                            <form action="{{ route('dashboard.invoices.whatsapp', $invoice->id) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" size="sm" icon="fa-brands fa-whatsapp">Kirim WhatsApp</x-ui.button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($subscriptions->isEmpty())
            <div class="card">
                <x-ui.empty-state icon="fa-rotate" title="Belum ada langganan" text="Buat langganan untuk klien hosting, maintenance, atau retainer. Invoice bulanannya terbit sendiri.">
                    <x-ui.button :href="route('dashboard.subscriptions.create')" icon="fa-plus">Langganan baru</x-ui.button>
                </x-ui.empty-state>
            </div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th scope="col">Klien</th>
                            <th scope="col">Per bulan</th>
                            <th scope="col">Terbit berikutnya</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions as $subscription)
                            @php [$label, $variant] = $statusMap[$subscription->status] ?? [$subscription->status, 'neutral']; @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.subscriptions.show', $subscription) }}" class="font-bold hover:underline">{{ $subscription->client_name }}</a>
                                    <p class="text-caption text-muted">{{ $subscription->title }}</p>
                                </td>
                                <td class="whitespace-nowrap font-semibold">Rp {{ number_format($subscription->monthlyTotal(), 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap">
                                    @if ($subscription->next_run_date)
                                        {{ $fmt($subscription->next_run_date) }}
                                        <p class="text-caption text-muted">tiap tgl {{ $subscription->billing_day }}, {{ substr($subscription->billing_time, 0, 5) }} WIB</p>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <x-ui.badge :variant="$variant" dot>{{ $label }}</x-ui.badge>
                                    @if ($subscription->auto_send)
                                        <span class="ml-1 text-caption text-muted" title="Dikirim otomatis lewat WhatsApp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> otomatis</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('dashboard.subscriptions.show', $subscription) }}" class="btn-icon" title="Lihat" aria-label="Lihat langganan {{ $subscription->client_name }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                        <a href="{{ route('dashboard.subscriptions.edit', $subscription) }}" class="btn-icon" title="Ubah" aria-label="Ubah langganan {{ $subscription->client_name }}"><i class="fa-solid fa-pencil" aria-hidden="true"></i></a>
                                        @if ($subscription->status === 'active')
                                            <form action="{{ route('dashboard.subscriptions.pause', $subscription) }}" method="POST">@csrf<button type="submit" class="btn-icon" title="Jeda" aria-label="Jeda langganan {{ $subscription->client_name }}"><i class="fa-solid fa-pause" aria-hidden="true"></i></button></form>
                                        @elseif ($subscription->status === 'paused')
                                            <form action="{{ route('dashboard.subscriptions.resume', $subscription) }}" method="POST">@csrf<button type="submit" class="btn-icon" title="Lanjutkan" aria-label="Lanjutkan langganan {{ $subscription->client_name }}"><i class="fa-solid fa-play" aria-hidden="true"></i></button></form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
