<x-app-layout>
    <x-slot name="header">Langganan</x-slot>

    @php
        $statusMap = ['active' => ['Aktif', 'success'], 'paused' => ['Dijeda', 'warning'], 'ended' => ['Selesai', 'neutral']];
        [$statusLabel, $statusVariant] = $statusMap[$subscription->status] ?? [$subscription->status, 'neutral'];
        $invStatus = ['Paid' => ['Lunas', 'success'], 'Sent' => ['Terkirim', 'info'], 'Unpaid' => ['Belum dibayar', 'warning'], 'Draft' => ['Draft', 'neutral'], 'Cancelled' => ['Dibatalkan', 'danger']];
        $fmt = fn ($d, $f = 'd F Y') => $d?->locale('id')->translatedFormat($f) ?? '-';
        $upcoming = $subscription->next_run_date;
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header :title="$subscription->client_name" :description="$subscription->title">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.subscriptions.index')" variant="secondary" icon="fa-arrow-left">Kembali</x-ui.button>
                <x-ui.button :href="route('dashboard.subscriptions.edit', $subscription)" variant="secondary" icon="fa-pencil">Ubah</x-ui.button>
                @if ($subscription->status === 'active')
                    <form action="{{ route('dashboard.subscriptions.pause', $subscription) }}" method="POST">@csrf<x-ui.button type="submit" variant="secondary" icon="fa-pause">Jeda</x-ui.button></form>
                    <form action="{{ route('dashboard.subscriptions.generate', $subscription) }}" method="POST"
                        data-confirm="Invoice untuk {{ $upcoming ? $fmt($upcoming, 'F Y') : 'periode berikutnya' }} dibuat sekarang{{ $subscription->auto_send ? ' dan langsung dikirim lewat WhatsApp' : '' }}. Jadwal maju ke bulan setelahnya." data-confirm-title="Buat invoice sekarang?" data-confirm-ok="Ya, buat">
                        @csrf<x-ui.button type="submit" icon="fa-bolt">Buat invoice sekarang</x-ui.button>
                    </form>
                @elseif ($subscription->status === 'paused')
                    <form action="{{ route('dashboard.subscriptions.resume', $subscription) }}" method="POST">@csrf<x-ui.button type="submit" icon="fa-play">Lanjutkan</x-ui.button></form>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5 lg:items-start">
            <div class="space-y-6 lg:col-span-3">
                {{-- Jadwal --}}
                <x-ui.card>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-bold">Jadwal</h2>
                        <x-ui.badge :variant="$statusVariant" dot>{{ $statusLabel }}</x-ui.badge>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-4 text-small sm:grid-cols-3">
                        <div><dt class="text-caption text-muted">Terbit berikutnya</dt><dd class="font-bold">{{ $upcoming ? $fmt($upcoming) : '-' }}</dd></div>
                        <div><dt class="text-caption text-muted">Tiap tanggal</dt><dd class="font-bold">{{ $subscription->billing_day }}, pukul {{ substr($subscription->billing_time, 0, 5) }} WIB</dd></div>
                        <div><dt class="text-caption text-muted">Jatuh tempo</dt><dd class="font-bold">{{ $subscription->due_days }} hari</dd></div>
                        <div><dt class="text-caption text-muted">Mulai</dt><dd class="font-bold">{{ $fmt($subscription->start_date) }}</dd></div>
                        <div><dt class="text-caption text-muted">Berakhir</dt><dd class="font-bold">{{ $subscription->end_date ? $fmt($subscription->end_date) : 'Tanpa batas' }}</dd></div>
                        <div>
                            <dt class="text-caption text-muted">Pengiriman</dt>
                            <dd class="font-bold">{{ $subscription->auto_send ? 'Otomatis' : 'Manual (Draft)' }}</dd>
                        </div>
                    </dl>
                    @if ($subscription->auto_send && ! $whatsapp->enabled())
                        <p class="mt-4 rounded-lg border border-danger-500/40 bg-danger-50 p-3 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300">
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Kirim otomatis menyala, tetapi WhatsApp belum aktif. <a href="{{ route('dashboard.whatsapp.edit') }}" class="font-semibold underline">Atur WhatsApp</a>
                        </p>
                    @endif
                </x-ui.card>

                {{-- Item --}}
                <x-ui.card>
                    <h2 class="text-lg font-bold">Item tiap bulan</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left text-small">
                            <thead>
                                <tr class="border-b-2 border-edge text-caption">
                                    <th scope="col" class="w-10 pb-2 font-bold">No</th>
                                    <th scope="col" class="pb-2 font-bold">Deskripsi</th>
                                    <th scope="col" class="w-14 pb-2 text-center font-bold">Jml</th>
                                    <th scope="col" class="w-32 pb-2 text-right font-bold">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscription->items as $item)
                                    <tr class="border-b border-line">
                                        <td class="py-3 text-muted">{{ $loop->iteration }}</td>
                                        <td class="py-3 font-semibold">{{ $item->description }}</td>
                                        <td class="py-3 text-center">{{ $item->quantity }}</td>
                                        <td class="py-3 text-right font-bold">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <dl class="ml-auto mt-4 max-w-xs space-y-1.5 text-small">
                        <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="font-semibold">Rp {{ number_format($totals['subtotal'], 0, ',', '.') }}</dd></div>
                        @if ($totals['discount'] > 0)<div class="flex justify-between"><dt class="text-muted">Diskon</dt><dd class="font-semibold">- Rp {{ number_format($totals['discount'], 0, ',', '.') }}</dd></div>@endif
                        @if ($totals['tax'] > 0)<div class="flex justify-between"><dt class="text-muted">Pajak</dt><dd class="font-semibold">+ Rp {{ number_format($totals['tax'], 0, ',', '.') }}</dd></div>@endif
                    </dl>
                    <div class="ml-auto mt-3 max-w-xs rounded-lg border-2 border-edge bg-navy px-4 py-3">
                        <p class="text-caption text-brand-200">Tagihan tiap bulan</p>
                        <p class="font-display text-2xl font-extrabold text-sun-400 [font-stretch:85%]">Rp {{ number_format($totals['total'], 0, ',', '.') }}</p>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-lg font-bold">Klien</h2>
                    <dl class="mt-3 space-y-1.5 text-small">
                        <div><dt class="sr-only">WhatsApp</dt><dd><i class="fa-brands fa-whatsapp mr-1.5 text-brand-text" aria-hidden="true"></i>{{ $subscription->client_phone ?: 'Belum diisi' }}</dd></div>
                        @if ($subscription->client_email)<div><dt class="sr-only">Email</dt><dd><i class="fa-solid fa-envelope mr-1.5 text-brand-text" aria-hidden="true"></i>{{ $subscription->client_email }}</dd></div>@endif
                        @if ($subscription->client_address)<div class="text-muted">{!! nl2br(e($subscription->client_address)) !!}</div>@endif
                    </dl>
                </x-ui.card>

                <form action="{{ route('dashboard.subscriptions.destroy', $subscription) }}" method="POST"
                    data-confirm="Langganan ini dihapus dan tidak akan menerbitkan invoice lagi. Invoice yang sudah terbit tetap ada." data-confirm-title="Hapus langganan?" data-confirm-ok="Ya, hapus">
                    @csrf @method('DELETE')
                    <x-ui.button type="submit" variant="danger-soft" icon="fa-trash-can">Hapus langganan</x-ui.button>
                </form>
            </div>

            {{-- Riwayat --}}
            <x-ui.card class="lg:col-span-2">
                <h2 class="text-lg font-bold">Riwayat invoice</h2>
                @if ($subscription->invoices->isEmpty())
                    <p class="mt-3 text-small text-muted">Belum ada invoice. Yang pertama terbit {{ $upcoming ? $fmt($upcoming) : 'saat langganan aktif' }}.</p>
                @else
                    <ol class="mt-4 space-y-4">
                        @foreach ($subscription->invoices as $invoice)
                            @php [$iLabel, $iVariant] = $invStatus[$invoice->status] ?? [$invoice->status, 'neutral']; @endphp
                            <li class="rounded-lg border border-line bg-surface-2 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-bold">{{ $fmt($invoice->billing_period, 'F Y') }}</p>
                                        <a href="{{ route('dashboard.invoices.show', $invoice->id) }}" class="text-caption text-brand-text hover:underline">{{ $invoice->invoice_number }}</a>
                                    </div>
                                    <x-ui.badge :variant="$iVariant" dot>{{ $iLabel }}</x-ui.badge>
                                </div>
                                <p class="mt-2 text-small font-semibold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                                <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                                    @if ($invoice->wa_sent_at)
                                        <p class="text-caption text-muted"><i class="fa-brands fa-whatsapp text-brand-text" aria-hidden="true"></i> Terkirim {{ $fmt($invoice->wa_sent_at, 'd M, H:i') }}</p>
                                    @elseif ($invoice->wa_error)
                                        <p class="text-caption text-danger-700 dark:text-red-300"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> {{ $invoice->wa_error }}</p>
                                    @else
                                        <p class="text-caption text-muted">Belum dikirim</p>
                                    @endif
                                    <form action="{{ route('dashboard.invoices.whatsapp', $invoice->id) }}" method="POST">
                                        @csrf
                                        <x-ui.button type="submit" variant="secondary" size="sm" icon="fa-brands fa-whatsapp">{{ $invoice->wa_sent_at ? 'Kirim ulang' : 'Kirim' }}</x-ui.button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
