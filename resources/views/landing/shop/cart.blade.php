@php
    $id = app()->getLocale() === 'id';
    $money = fn ($v) => \App\Services\Shop\Money::format($v);
@endphp
<x-layouts.public :noindex="true" :title="$id ? 'Keranjang' : 'Cart'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="pb-28 pt-28 md:pb-24 md:pt-36">
        <div class="container-page max-w-5xl">
            <x-ui.page-header :title="$id ? 'Keranjang' : 'Your cart'" :description="$id ? 'Periksa produk dan jumlahnya sebelum checkout.' : 'Check your products and quantities before checking out.'">
                <x-slot:actions>
                    <x-ui.button :href="route('shop.index')" variant="secondary" icon="fa-arrow-left">{{ $id ? 'Lanjut belanja' : 'Keep shopping' }}</x-ui.button>
                </x-slot:actions>
            </x-ui.page-header>

            @if (session('success') || session('error'))
                <div @class(['mb-6 flex items-start gap-3 rounded-lg border-2 border-edge p-4 text-small font-semibold', 'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300' => session('success'), 'bg-danger-50 text-danger-700 dark:bg-danger-500/15 dark:text-red-300' => session('error')]) role="status">
                    <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} mt-0.5" aria-hidden="true"></i>
                    <span>{{ session('success') ?? session('error') }}</span>
                </div>
            @endif

            @if ($summary['lines']->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state icon="fa-cart-shopping" :title="$id ? 'Keranjang masih kosong' : 'Your cart is empty'" :text="$id ? 'Lihat produk di toko dan tambahkan yang Anda butuhkan.' : 'Browse the shop and add what you need.'">
                        <a href="{{ route('shop.index') }}" class="btn-primary">{{ $id ? 'Buka toko' : 'Go to shop' }}</a>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <ul class="space-y-4 lg:col-span-2">
                        @foreach ($summary['lines'] as $line)
                            @php
                                $product = $line->product;
                                $cover = $line->variant?->image ?? $product?->coverImage();
                            @endphp
                            <li @class(['flex gap-4 rounded-xl border-2 bg-surface p-4', 'border-edge' => ! $line->issue, 'border-danger-500' => $line->issue])>
                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg border-2 border-edge bg-sun-100 dark:bg-surface-2 sm:h-24 sm:w-24">
                                    @if ($cover)
                                        <img src="{{ $cover->media_url }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-2xl text-navy/30 dark:text-ink/30" aria-hidden="true"><i class="fa-solid fa-box"></i></div>
                                    @endif
                                </div>
                                <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        @if ($product)
                                            <a href="{{ route('shop.show', $product->slug) }}" class="font-bold hover:underline">{{ $product->getTranslation('name') }}</a>
                                        @endif
                                        @if ($line->variant?->label())
                                            <p class="text-small text-muted">{{ $line->variant->label() }}</p>
                                        @endif
                                        <p class="mt-1 text-small font-semibold">{{ $money($line->unit_price) }}</p>
                                        @if ($line->issue)
                                            <p class="mt-1 text-small font-semibold text-danger-700 dark:text-red-300"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> {{ $line->issue }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                                        <form method="POST" action="{{ route('cart.update', $line->item) }}" class="flex items-center gap-2">
                                            @csrf @method('PATCH')
                                            <label class="sr-only" for="qty-{{ $line->item->id }}">{{ $id ? 'Jumlah' : 'Quantity' }}</label>
                                            <input id="qty-{{ $line->item->id }}" type="number" name="quantity" min="1" max="99" value="{{ $line->quantity }}" class="input w-20 text-center" onchange="this.form.submit()">
                                            <noscript><button type="submit" class="btn-secondary btn-sm">OK</button></noscript>
                                        </form>
                                        <p class="font-extrabold sm:mt-1">{{ $money($line->subtotal) }}</p>
                                        <form method="POST" action="{{ route('cart.destroy', $line->item) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon text-danger-700 dark:text-red-300" aria-label="{{ $id ? 'Hapus' : 'Remove' }} {{ $product?->getTranslation('name') }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <aside>
                        <div class="rounded-xl border-2 border-edge bg-surface p-5 shadow-hard lg:sticky lg:top-28">
                            <h2 class="text-lg font-bold">{{ $id ? 'Ringkasan' : 'Summary' }}</h2>
                            <dl class="mt-4 space-y-2 text-small">
                                <div class="flex justify-between gap-4"><dt class="text-muted">{{ $id ? 'Subtotal' : 'Subtotal' }} ({{ $summary['lines']->whereNull('issue')->sum('quantity') }} item)</dt><dd class="font-semibold">{{ $money($summary['subtotal']) }}</dd></div>
                                @if ($summary['has_physical'])
                                    <div class="flex justify-between gap-4"><dt class="text-muted">{{ $id ? 'Ongkir' : 'Shipping' }}</dt><dd class="text-muted">{{ $id ? 'dihitung saat checkout' : 'calculated at checkout' }}</dd></div>
                                @endif
                            </dl>
                            <div class="divider my-4"></div>
                            <div class="flex items-end justify-between gap-4">
                                <span class="text-small font-semibold">{{ $id ? 'Total' : 'Total' }}</span>
                                <span class="text-2xl font-extrabold">{{ $money($summary['subtotal']) }}</span>
                            </div>
                            @if ($summary['has_issues'])
                                <p class="mt-4 rounded-lg bg-danger-50 p-3 text-caption font-semibold text-danger-700 dark:bg-danger-500/15 dark:text-red-300">{{ $id ? 'Hapus atau ubah produk yang bermasalah sebelum checkout.' : 'Remove or fix the flagged items before checking out.' }}</p>
                            @endif
                            <a href="{{ route('shop.checkout') }}" @class(['btn-primary btn-lg mt-5 w-full', 'pointer-events-none opacity-50' => $summary['has_issues']]) @if ($summary['has_issues']) aria-disabled="true" tabindex="-1" @endif>
                                {{ $id ? 'Lanjut ke checkout' : 'Proceed to checkout' }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
