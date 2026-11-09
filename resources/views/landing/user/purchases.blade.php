@php
    $id = app()->getLocale() === 'id';
    $money = fn ($v) => \App\Services\Shop\Money::format($v);
@endphp
<x-layouts.public :noindex="true" :title="$id ? 'Pembelian Toko' : 'Shop Purchases'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="container-page max-w-5xl">
            <x-ui.page-header :title="$id ? 'Pembelian toko' : 'Shop purchases'" :description="$id ? 'Status pembayaran, invoice, dan pengiriman pesanan dari toko.' : 'Payment status, invoices and shipping for your shop orders.'">
                <x-slot:actions>
                    <x-ui.button :href="route('user.downloads')" variant="secondary" icon="fa-download">{{ $id ? 'Produk Digital Saya' : 'My Digital Products' }}</x-ui.button>
                </x-slot:actions>
            </x-ui.page-header>

            @include('landing.user._flash')

            @if ($orders->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state icon="fa-bag-shopping" :title="$id ? 'Belum ada pembelian' : 'No purchases yet'" :text="$id ? 'Pesanan dari toko akan muncul di sini.' : 'Your shop orders will show up here.'">
                        <a href="{{ route('shop.index') }}" class="btn-primary">{{ $id ? 'Buka toko' : 'Go to shop' }}</a>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <ul class="space-y-4">
                    @foreach ($orders as $order)
                        <li>
                            <a href="{{ route('shop.orders.show', $order) }}" class="flex flex-col gap-3 rounded-xl border-2 border-edge bg-surface p-5 shadow-hard-sm transition hover:-translate-y-0.5 hover:shadow-hard sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-mono font-bold">{{ $order->order_number }}</span>
                                        <x-ui.badge :variant="$order->paymentStatusVariant()" :dot="true">{{ $order->paymentStatusLabel() }}</x-ui.badge>
                                        @if ($order->isPaid() && $order->hasPhysicalItems())
                                            <x-ui.badge variant="info">{{ $order->fulfillmentStatusLabel() }}</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="mt-1 truncate text-small text-muted">{{ $order->items->map(fn ($i) => $i->displayName())->implode(', ') }}</p>
                                    <p class="text-caption text-muted">{{ $order->created_at->timezone('Asia/Jakarta')->locale(app()->getLocale())->translatedFormat('d M Y, H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-4 sm:text-right">
                                    <span class="text-lg font-extrabold">{{ $money($order->total_amount) }}</span>
                                    <i class="fa-solid fa-chevron-right text-muted" aria-hidden="true"></i>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-8">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
