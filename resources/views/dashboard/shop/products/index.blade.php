@php $money = fn ($v) => \App\Services\Shop\Money::format($v); @endphp
<x-app-layout>
    <x-slot name="header">Produk Toko</x-slot>

    <div class="mx-auto max-w-7xl space-y-6"
        x-data="{
            open: false, loading: false, failed: false, html: '', url: null,
            async show(url) {
                this.open = true; this.url = url; this.loading = true; this.failed = false; this.html = '';
                this.$nextTick(() => this.$refs.close.focus());
                try {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } });
                    if (this.url !== url) return;
                    if (!res.ok) throw new Error(res.status);
                    this.html = await res.text();
                } catch (e) { if (this.url === url) this.failed = true; }
                if (this.url === url) this.loading = false;
            },
            close() { this.open = false; this.url = null; },
        }"
        x-effect="document.body.classList.toggle('overflow-hidden', open)"
        @keydown.escape.window="if (open) close()">
        <x-ui.page-header title="Produk" description="Produk digital dan fisik yang dijual di toko. Harga sudah termasuk PPN.">
            <x-slot:actions>
                @if (! $settings->shop_enabled)
                    <x-ui.button :href="route('dashboard.shop.settings.edit')" variant="secondary" icon="fa-eye-slash">Toko belum aktif</x-ui.button>
                @endif
                <x-ui.button :href="route('dashboard.shop.products.create')" icon="fa-plus">Produk baru</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari nama produk" class="input sm:max-w-xs" aria-label="Cari produk">
            <select name="type" class="select sm:w-40" aria-label="Tipe">
                <option value="">Semua tipe</option>
                <option value="digital" @selected($filters['type'] === 'digital')>Digital</option>
                <option value="physical" @selected($filters['type'] === 'physical')>Fisik</option>
            </select>
            <select name="status" class="select sm:w-40" aria-label="Status">
                <option value="">Semua status</option>
                <option value="active" @selected($filters['status'] === 'active')>Aktif</option>
                <option value="inactive" @selected($filters['status'] === 'inactive')>Nonaktif</option>
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>

        @if ($products->isEmpty())
            <div class="card">
                <x-ui.empty-state icon="fa-boxes-stacked" title="Belum ada produk" text="Tambahkan produk digital pertama Anda: template, e-book, source code, atau lisensi.">
                    <x-ui.button :href="route('dashboard.shop.products.create')" icon="fa-plus">Produk baru</x-ui.button>
                </x-ui.empty-state>
            </div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Stok / pengiriman</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            @php
                                $cover = $product->images->first();
                                $active = $product->variants->where('is_active', true);
                                $tracked = $active->whereNotNull('stock');
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="h-12 w-12 shrink-0 overflow-hidden rounded-md border border-line bg-surface-2">
                                            @if ($cover)<img src="{{ $cover->media_url }}" alt="" class="h-full w-full object-cover">@endif
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('dashboard.shop.products.edit', $product) }}" class="font-bold hover:underline">{{ $product->name_id }}</a>
                                            <p class="text-caption text-muted">
                                                {{ $product->isDigital() ? 'Digital' : 'Fisik' }}{{ $product->category ? ' · '.$product->category->name_id : '' }}{{ $product->has_variants ? ' · '.$active->count().' varian' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap font-semibold">{{ $money($product->min_price) }}{{ $product->hasPriceRange() ? ' – '.$money($product->max_price) : '' }}</td>
                                <td class="text-small">
                                    @if ($product->isDigital())
                                        {{ $product->deliveryLabel() }}
                                        @if ($product->digital_delivery === 'license_key')
                                            <span @class(['block text-caption', 'text-danger-700 dark:text-red-300 font-semibold' => $product->available_keys_count < 5, 'text-muted' => $product->available_keys_count >= 5])>{{ $product->available_keys_count }} kunci tersedia</span>
                                        @endif
                                    @endif
                                    @if ($tracked->isNotEmpty())
                                        <span @class(['block', 'font-semibold text-danger-700 dark:text-red-300' => $tracked->sum('stock') <= 5])>Stok {{ $tracked->sum('stock') }}</span>
                                    @elseif ($product->isPhysical())
                                        <span class="block text-muted">Stok tak terbatas</span>
                                    @endif
                                </td>
                                <td>
                                    <x-ui.badge :variant="$product->is_active ? 'success' : 'neutral'" dot>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                                    @if ($product->is_featured)<x-ui.badge variant="warning">Unggulan</x-ui.badge>@endif
                                    @if ($product->isPhysical() && ! $settings->shop_physical_enabled)
                                        <p class="mt-1 text-caption text-muted">Belum tampil (produk fisik nonaktif)</p>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('dashboard.shop.products.show', $product) }}" target="_blank" rel="noopener" class="btn-icon" title="Pratinjau isi produk" aria-label="Pratinjau isi {{ $product->name_id }}" @click.prevent="show('{{ route('dashboard.shop.products.preview', $product) }}')"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                        @if ($product->is_active)
                                            <a href="{{ route('shop.show', $product->slug) }}" target="_blank" rel="noopener" class="btn-icon" title="Lihat di toko" aria-label="Lihat {{ $product->name_id }} di toko"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                                        @endif
                                        <a href="{{ route('dashboard.shop.products.edit', $product) }}" class="btn-icon" title="Ubah" aria-label="Ubah {{ $product->name_id }}"><i class="fa-solid fa-pencil" aria-hidden="true"></i></a>
                                        <form method="POST" action="{{ route('dashboard.shop.products.duplicate', $product) }}">@csrf<button type="submit" class="btn-icon" title="Duplikasi" aria-label="Duplikasi {{ $product->name_id }}"><i class="fa-regular fa-copy" aria-hidden="true"></i></button></form>
                                        <form method="POST" action="{{ route('dashboard.shop.products.destroy', $product) }}" x-data @submit="if (!confirm('Hapus produk ini dari toko? Pembeli lama tetap bisa mengunduh.')) $event.preventDefault()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon text-danger-700 dark:text-red-300" title="Hapus" aria-label="Hapus {{ $product->name_id }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $products->links() }}
        @endif
        {{-- Product preview: a slide-over, so the list stays where it is --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-labelledby="preview-title">
            <div class="absolute inset-0 bg-navy/50" @click="close()" x-show="open" x-transition.opacity></div>
            <aside class="absolute inset-y-0 right-0 flex w-full max-w-2xl flex-col border-l-2 border-edge bg-surface shadow-hard"
                x-show="open" x-transition:enter="transition duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-3">
                    <h2 id="preview-title" class="text-lg font-bold">Pratinjau produk</h2>
                    <button type="button" x-ref="close" class="btn-icon" @click="close()" aria-label="Tutup pratinjau"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto p-5" aria-live="polite" :aria-busy="loading">
                    <p x-show="loading" class="text-small text-muted"><i class="fa-solid fa-spinner animate-spin" aria-hidden="true"></i> Memuat isi produk…</p>
                    <div x-show="failed" x-cloak class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                        <p class="font-semibold"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Pratinjau gagal dimuat.</p>
                        <button type="button" class="mt-2 underline" @click="show(url)">Coba lagi</button>
                    </div>
                    <div x-html="html"></div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
