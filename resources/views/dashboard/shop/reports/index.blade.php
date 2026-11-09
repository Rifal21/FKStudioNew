@php $money = fn ($v) => \App\Services\Shop\Money::format($v); @endphp
<x-app-layout>
    <x-slot name="header">Laporan Toko</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Laporan & PPN" :description="'Pesanan lunas bulan '.$month->locale('id')->translatedFormat('F Y').'. Pesanan yang di-refund tidak dihitung.'">
            <x-slot:actions>
                <form method="GET" class="flex gap-2">
                    <input type="month" name="month" value="{{ $month->format('Y-m') }}" class="input" aria-label="Bulan">
                    <button type="submit" class="btn-secondary">Tampilkan</button>
                </form>
                <x-ui.button :href="route('dashboard.shop.reports.tax', ['month' => $month->format('Y-m')])" icon="fa-file-csv">Ekspor PPN (CSV)</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.stat-card label="Pesanan lunas" :value="$totals['orders']" icon="fa-bag-shopping" tone="brand" />
            <x-ui.stat-card label="Total diterima" :value="$money($totals['gross'])" icon="fa-coins" tone="sun" />
            <x-ui.stat-card label="DPP" :value="$money($totals['tax_base'])" icon="fa-receipt" tone="mint" />
            <x-ui.stat-card label="PPN" :value="$money($totals['tax'])" icon="fa-landmark" tone="sky" />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="min-w-0 lg:col-span-2">
                <h2 class="mb-3 text-lg font-bold">Rincian PPN per pesanan</h2>
                @if ($orders->isEmpty())
                    <div class="card"><x-ui.empty-state icon="fa-chart-column" title="Belum ada pesanan lunas" text="Pilih bulan lain atau tunggu pesanan pertama." /></div>
                @else
                    <div class="table-wrap">
                        <table class="table-base">
                            <thead><tr><th>Lunas</th><th>Pesanan</th><th class="text-right">Barang</th><th class="text-right">DPP</th><th class="text-right">PPN</th><th class="text-right">Total</th></tr></thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="whitespace-nowrap text-small">{{ $order->paid_at?->timezone('Asia/Jakarta')->format('d/m H:i') }}</td>
                                        <td><a href="{{ route('dashboard.shop.orders.show', $order) }}" class="font-mono text-small font-bold hover:underline">{{ $order->order_number }}</a></td>
                                        <td class="whitespace-nowrap text-right text-small">{{ $money($order->goodsAmount()) }}</td>
                                        <td class="whitespace-nowrap text-right text-small">{{ $money($order->tax_base) }}</td>
                                        <td class="whitespace-nowrap text-right text-small font-semibold">{{ $money($order->tax_amount) }}</td>
                                        <td class="whitespace-nowrap text-right text-small">{{ $money($order->total_amount) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-bold">
                                    <td colspan="2">Total</td>
                                    <td class="whitespace-nowrap text-right">{{ $money($totals['goods']) }}</td>
                                    <td class="whitespace-nowrap text-right">{{ $money($totals['tax_base']) }}</td>
                                    <td class="whitespace-nowrap text-right">{{ $money($totals['tax']) }}</td>
                                    <td class="whitespace-nowrap text-right">{{ $money($totals['gross']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2 text-caption text-muted">PPN dihitung dari nilai barang setelah diskon (harga sudah termasuk PPN). Ongkir {{ $money($totals['shipping']) }} dan kode unik {{ $money($totals['unique']) }} tidak dikenai PPN.</p>
                @endif
            </div>

            <div>
                <h2 class="mb-3 text-lg font-bold">Produk terlaris</h2>
                <x-ui.card :padded="false">
                    @forelse ($topProducts as $row)
                        <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-3 text-small last:border-0">
                            <span class="min-w-0"><span class="block truncate font-semibold">{{ $row->product_name }}</span><span class="text-muted">{{ $row->qty }} terjual</span></span>
                            <span class="shrink-0 font-bold">{{ $money($row->revenue) }}</span>
                        </div>
                    @empty
                        <p class="p-4 text-small text-muted">Belum ada data.</p>
                    @endforelse
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
