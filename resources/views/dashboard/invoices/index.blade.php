<x-app-layout>
    <x-slot name="header">Invoice</x-slot>

    @php
        $statusMap = [
            'Paid' => ['label' => 'Lunas', 'variant' => 'success'],
            'Sent' => ['label' => 'Terkirim', 'variant' => 'info'],
            'Unpaid' => ['label' => 'Belum dibayar', 'variant' => 'warning'],
            'Draft' => ['label' => 'Draft', 'variant' => 'neutral'],
            'Cancelled' => ['label' => 'Dibatalkan', 'variant' => 'danger'],
        ];
        $invoiceGroups = [
            'auto' => $invoices->filter(fn ($i) => $i->packageOrder !== null),
            'manual' => $invoices->filter(fn ($i) => $i->packageOrder === null),
        ];
        $paidCount = $invoices->where('status', 'Paid')->count();
        $openCount = $invoices->count() - $paidCount;
    @endphp

    <div class="mx-auto max-w-7xl space-y-6" x-data="{ tab: 'auto' }">
        <x-ui.page-header title="Invoice" description="Kelola invoice otomatis dari pesanan paket dan invoice manual untuk klien Anda.">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.invoices.create')" icon="fa-plus">Buat invoice</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-ui.stat-card label="Total invoice" :value="$invoices->count()" icon="fa-file-invoice" tone="brand" />
            <x-ui.stat-card label="Sudah lunas" :value="$paidCount" icon="fa-circle-check" tone="mint" />
            <x-ui.stat-card label="Belum lunas" :value="$openCount" icon="fa-hourglass-half" tone="sun" />
        </div>

        {{-- Tabs --}}
        <div class="inline-flex w-full gap-1 rounded-lg border border-line bg-surface-2 p-1 sm:w-auto" role="tablist" aria-label="Jenis invoice">
            <button type="button" role="tab" @click="tab = 'auto'" :aria-selected="tab === 'auto'"
                :class="tab === 'auto' ? 'bg-surface text-brand-text shadow-soft' : 'text-muted hover:text-ink'"
                class="flex min-h-[44px] flex-1 items-center justify-center gap-2 rounded-md px-4 text-small font-semibold transition sm:flex-none">
                <i class="fa-solid fa-robot" aria-hidden="true"></i> Otomatis
                <span class="badge-neutral">{{ $invoiceGroups['auto']->count() }}</span>
            </button>
            <button type="button" role="tab" @click="tab = 'manual'" :aria-selected="tab === 'manual'"
                :class="tab === 'manual' ? 'bg-surface text-brand-text shadow-soft' : 'text-muted hover:text-ink'"
                class="flex min-h-[44px] flex-1 items-center justify-center gap-2 rounded-md px-4 text-small font-semibold transition sm:flex-none">
                <i class="fa-solid fa-user-pen" aria-hidden="true"></i> Manual
                <span class="badge-neutral">{{ $invoiceGroups['manual']->count() }}</span>
            </button>
        </div>

        @foreach (['auto', 'manual'] as $type)
            <section x-show="tab === '{{ $type }}'" @if ($type !== 'auto') x-cloak @endif role="tabpanel" aria-label="Invoice {{ $type === 'auto' ? 'otomatis' : 'manual' }}">
                @if ($invoiceGroups[$type]->isEmpty())
                    <div class="card">
                        <x-ui.empty-state icon="fa-file-circle-minus" :title="$type === 'auto' ? 'Belum ada invoice otomatis' : 'Belum ada invoice manual'"
                            :text="$type === 'auto' ? 'Invoice otomatis muncul saat pelanggan membuat pesanan paket.' : 'Buat invoice manual untuk klien di luar pesanan paket.'">
                            @if ($type === 'manual')
                                <x-ui.button :href="route('dashboard.invoices.create')" icon="fa-plus">Buat invoice</x-ui.button>
                            @endif
                        </x-ui.empty-state>
                    </div>
                @else
                    {{-- Desktop table --}}
                    <div class="table-wrap hidden lg:block">
                        <table class="table-base">
                            <thead>
                                <tr>
                                    <th scope="col">Nomor</th>
                                    <th scope="col">Klien</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoiceGroups[$type] as $invoice)
                                    @php $st = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'variant' => 'neutral']; @endphp
                                    <tr>
                                        <td class="whitespace-nowrap font-bold">{{ $invoice->invoice_number }}@if ($invoice->subscription_id) <span class="badge-brand ml-1 align-middle" title="Dibuat dari langganan"><i class="fa-solid fa-rotate" aria-hidden="true"></i> Langganan</span>@endif</td>
                                        <td>
                                            <p class="font-semibold">{{ $invoice->client_name }}</p>
                                            @if ($invoice->client_email)<p class="text-caption text-muted">{{ $invoice->client_email }}</p>@endif
                                        </td>
                                        <td class="whitespace-nowrap text-muted">{{ $invoice->issue_date->locale('id')->translatedFormat('d M Y') }}</td>
                                        <td class="whitespace-nowrap font-bold text-brand-text">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                                        <td><x-ui.badge :variant="$st['variant']" dot>{{ $st['label'] }}</x-ui.badge></td>
                                        <td>
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('dashboard.invoices.show', $invoice->id) }}" target="_blank" rel="noopener" class="btn-icon" title="Pratinjau" aria-label="Pratinjau invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                                <a href="{{ route('dashboard.invoices.download', $invoice->id) }}" class="btn-icon" title="Unduh PDF" aria-label="Unduh PDF invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-download" aria-hidden="true"></i></a>
                                                <button type="button" class="btn-icon" title="Salin tautan" aria-label="Salin tautan publik invoice {{ $invoice->invoice_number }}"
                                                    @click="navigator.clipboard.writeText(@js(route('invoices.public.show', $invoice->id))).then(() => window.toast('success', 'Tautan invoice disalin'), () => window.toast('error', 'Gagal menyalin tautan'))"><i class="fa-solid fa-share-nodes" aria-hidden="true"></i></button>
                                                @include('dashboard.invoices._whatsapp-button', ['invoice' => $invoice])
                                                @if ($type === 'manual')
                                                    <a href="{{ route('dashboard.invoices.edit', $invoice->id) }}" class="btn-icon" title="Ubah" aria-label="Ubah invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-pencil" aria-hidden="true"></i></a>
                                                @endif
                                                <form action="{{ route('dashboard.invoices.destroy', $invoice->id) }}" method="POST"
                                                    data-confirm="Invoice {{ $invoice->invoice_number }} akan dihapus permanen." data-confirm-title="Hapus invoice?" data-confirm-ok="Ya, hapus">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700" title="Hapus" aria-label="Hapus invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile / tablet cards --}}
                    <div class="space-y-3 lg:hidden">
                        @foreach ($invoiceGroups[$type] as $invoice)
                            @php $st = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'variant' => 'neutral']; @endphp
                            <article class="card card-pad">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="break-all text-small font-bold">{{ $invoice->invoice_number }}@if ($invoice->subscription_id) <span class="badge-brand ml-1 align-middle"><i class="fa-solid fa-rotate" aria-hidden="true"></i> Langganan</span>@endif</p>
                                        <p class="mt-0.5 font-semibold">{{ $invoice->client_name }}</p>
                                        @if ($invoice->client_email)<p class="break-all text-caption text-muted">{{ $invoice->client_email }}</p>@endif
                                    </div>
                                    <x-ui.badge :variant="$st['variant']" dot class="shrink-0">{{ $st['label'] }}</x-ui.badge>
                                </div>
                                <dl class="mt-4 flex items-end justify-between gap-3 border-t border-line pt-3">
                                    <div>
                                        <dt class="text-caption text-muted">Tanggal</dt>
                                        <dd class="text-small">{{ $invoice->issue_date->locale('id')->translatedFormat('d M Y') }}</dd>
                                    </div>
                                    <div class="text-right">
                                        <dt class="text-caption text-muted">Total</dt>
                                        <dd class="text-lg font-extrabold text-brand-text">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</dd>
                                    </div>
                                </dl>
                                <div class="mt-3 flex flex-wrap items-center gap-1 border-t border-line pt-3">
                                    <a href="{{ route('dashboard.invoices.show', $invoice->id) }}" target="_blank" rel="noopener" class="btn-icon" aria-label="Pratinjau invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('dashboard.invoices.download', $invoice->id) }}" class="btn-icon" aria-label="Unduh PDF invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-download" aria-hidden="true"></i></a>
                                    <button type="button" class="btn-icon" aria-label="Salin tautan publik invoice {{ $invoice->invoice_number }}"
                                        @click="navigator.clipboard.writeText(@js(route('invoices.public.show', $invoice->id))).then(() => window.toast('success', 'Tautan invoice disalin'), () => window.toast('error', 'Gagal menyalin tautan'))"><i class="fa-solid fa-share-nodes" aria-hidden="true"></i></button>
                                    @include('dashboard.invoices._whatsapp-button', ['invoice' => $invoice])
                                    @if ($type === 'manual')
                                        <a href="{{ route('dashboard.invoices.edit', $invoice->id) }}" class="btn-icon" aria-label="Ubah invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-pencil" aria-hidden="true"></i></a>
                                    @endif
                                    <form action="{{ route('dashboard.invoices.destroy', $invoice->id) }}" method="POST" class="ml-auto"
                                        data-confirm="Invoice {{ $invoice->invoice_number }} akan dihapus permanen." data-confirm-title="Hapus invoice?" data-confirm-ok="Ya, hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700" aria-label="Hapus invoice {{ $invoice->invoice_number }}"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</x-app-layout>
