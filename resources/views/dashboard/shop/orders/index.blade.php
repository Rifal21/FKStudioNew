@php
    $money = fn ($v) => \App\Services\Shop\Money::format($v);
    $tabs = \App\Http\Controllers\Dashboard\Shop\OrderController::TABS;
@endphp
<x-app-layout>
    <x-slot name="header">Pesanan Toko</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Pesanan toko" description="Pembayaran Duitku tercatat otomatis. Transfer manual perlu diverifikasi di tab Menunggu verifikasi.">
            <x-slot:actions>
                <form method="GET" class="flex gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="search" name="q" value="{{ $q }}" placeholder="No. pesanan / nama / email" class="input w-64" aria-label="Cari pesanan">
                    <button type="submit" class="btn-secondary">Cari</button>
                </form>
            </x-slot:actions>
        </x-ui.page-header>

        <nav class="flex gap-2 overflow-x-auto pb-1" aria-label="Filter status">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('dashboard.shop.orders.index', array_filter(['tab' => $key === 'all' ? null : $key, 'q' => $q ?: null])) }}"
                    @class(['flex shrink-0 items-center gap-2 rounded-full border px-4 py-1.5 text-small font-semibold transition', 'border-brand-600 bg-brand-soft text-brand-text' => $tab === $key, 'border-line bg-surface text-muted hover:text-ink' => $tab !== $key])
                    @if ($tab === $key) aria-current="page" @endif>
                    {{ $label }}
                    @if ($counts[$key])
                        <span @class(['rounded-full px-1.5 text-caption font-bold', 'bg-pink-500 text-white' => in_array($key, ['verify', 'attention', 'ship']), 'bg-surface-2 text-muted' => ! in_array($key, ['verify', 'attention', 'ship'])])>{{ $counts[$key] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        @if ($orders->isEmpty())
            <div class="card"><x-ui.empty-state icon="fa-bag-shopping" title="Tidak ada pesanan" text="Pesanan yang cocok dengan filter ini akan muncul di sini." /></div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr><th scope="col">Pesanan</th><th scope="col">Pelanggan</th><th scope="col">Total</th><th scope="col">PPN</th><th scope="col">Status</th><th scope="col" class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.shop.orders.show', $order) }}" class="font-mono font-bold hover:underline">{{ $order->order_number }}</a>
                                    <p class="text-caption text-muted">{{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} · {{ $order->items->sum('quantity') }} item</p>
                                    @if ($order->needs_attention)<p class="text-caption font-semibold text-danger-700 dark:text-red-300"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Perlu tindakan</p>@endif
                                </td>
                                <td><span class="font-semibold">{{ $order->customer_name }}</span><p class="text-caption text-muted">{{ $order->customer_email }}</p></td>
                                <td class="whitespace-nowrap font-semibold">{{ $money($order->total_amount) }}<p class="text-caption font-normal text-muted">{{ match ($order->payment_channel) { 'manual' => 'Transfer manual', 'free' => 'Gratis (Rp 0)', default => 'Duitku' } }}</p></td>
                                <td class="whitespace-nowrap text-small">{{ $money($order->tax_amount) }}</td>
                                <td>
                                    <x-ui.badge :variant="$order->paymentStatusVariant()" dot>{{ $order->paymentStatusLabel('id') }}</x-ui.badge>
                                    @if ($order->isPaid())<p class="mt-1 text-caption text-muted">{{ $order->fulfillmentStatusLabel('id') }}</p>@endif
                                </td>
                                <td class="text-right"><a href="{{ route('dashboard.shop.orders.show', $order) }}" class="btn-icon" aria-label="Detail {{ $order->order_number }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        @endif
    </div>
</x-app-layout>
