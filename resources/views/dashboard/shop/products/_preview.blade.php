@php
    $money = fn ($v) => \App\Services\Shop\Money::format($v);
    $variants = $product->variants;
    $activeCount = $variants->where('is_active', true)->count();
    $isSingle = ! $product->has_variants;
@endphp
<article class="space-y-6 text-small">
    <header>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge :variant="$product->is_active ? 'success' : 'neutral'" dot>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
            <x-ui.badge variant="info">{{ $product->isDigital() ? 'Digital' : 'Fisik' }}</x-ui.badge>
            @if ($product->is_featured)<x-ui.badge variant="warning">Unggulan</x-ui.badge>@endif
            @if ($product->category)<x-ui.badge variant="neutral">{{ $product->category->name_id }}</x-ui.badge>@endif
        </div>
        <h2 class="mt-3 text-2xl font-extrabold">{{ $product->name_id }}</h2>
        @if ($product->name_en && $product->name_en !== $product->name_id)<p class="text-muted">{{ $product->name_en }}</p>@endif
        <p class="mt-1 font-mono text-caption text-muted">/shop/{{ $product->slug }}</p>
    </header>

    @if ($hidden)
        <div class="rounded-lg border border-warning-500/40 bg-warning-50 p-3 text-warning-700 dark:bg-warning-500/15 dark:text-yellow-200" role="note">
            <p class="font-semibold"><i class="fa-solid fa-eye-slash" aria-hidden="true"></i> Belum tampil di toko</p>
            <ul class="mt-1 list-inside list-disc">@foreach ($hidden as $reason)<li>{{ $reason }}</li>@endforeach</ul>
        </div>
    @endif

    @if ($product->images->isNotEmpty())
        <section aria-label="Gambar produk">
            <div class="grid grid-cols-3 gap-2">
                @foreach ($product->images as $image)
                    <a href="{{ $image->media_url }}" target="_blank" rel="noopener" @class(['block overflow-hidden rounded-lg border border-line bg-surface-2', 'col-span-3' => $loop->first && $product->images->count() > 1])>
                        <img src="{{ $image->media_url }}" alt="{{ $image->altText() }}" loading="lazy" @class(['w-full object-cover', 'max-h-72' => $loop->first, 'aspect-square' => ! $loop->first])>
                    </a>
                @endforeach
            </div>
        </section>
    @else
        <p class="rounded-lg border border-dashed border-line p-4 text-center text-muted"><i class="fa-regular fa-image" aria-hidden="true"></i> Belum ada gambar produk.</p>
    @endif

    <section>
        <h3 class="text-base font-bold">{{ $isSingle ? 'Harga' : 'Harga & varian' }}</h3>
        @if (! $isSingle && $product->options->isNotEmpty())
            <dl class="mt-2 space-y-1 text-muted">
                @foreach ($product->options as $option)
                    <div><dt class="inline font-semibold text-ink">{{ $option->name_id }}:</dt> <dd class="inline">{{ $option->values->pluck('value_id')->implode(', ') }}</dd></div>
                @endforeach
            </dl>
        @endif
        <div class="table-wrap mt-3">
            <table class="table-base">
                <thead>
                    <tr>
                        @unless ($isSingle)<th scope="col">Varian</th>@endunless
                        <th scope="col">SKU</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Stok</th>
                        @unless ($isSingle)<th scope="col">Status</th>@endunless
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variants as $variant)
                        <tr @class(['opacity-60' => ! $variant->is_active])>
                            @unless ($isSingle)<td class="font-semibold">{{ $variant->getTranslation('title', 'id') ?: '—' }}</td>@endunless
                            <td class="font-mono text-caption">{{ $variant->sku ?: '—' }}</td>
                            <td class="whitespace-nowrap font-semibold">
                                {{ $money($variant->price) }}
                                @if ($variant->compare_at_price && $variant->compare_at_price > $variant->price)
                                    <span class="block text-caption font-normal text-muted line-through">{{ $money($variant->compare_at_price) }}</span>
                                @endif
                            </td>
                            <td>{{ $variant->stock === null ? 'Tak terbatas' : $variant->stock }}@if ($product->isPhysical() && $variant->weight_grams)<span class="block text-caption text-muted">{{ number_format($variant->weight_grams) }} g</span>@endif</td>
                            @unless ($isSingle)<td><x-ui.badge :variant="$variant->is_active ? 'success' : 'neutral'">{{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>@endunless
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($product->hasPriceRange())<p class="mt-2 text-caption text-muted">Tampil di toko: {{ $money($product->min_price) }} – {{ $money($product->max_price) }}. {{ $activeCount }} varian aktif.</p>@endif
    </section>

    @if (filled($product->short_description_id) || filled($product->description_id))
        <section>
            <h3 class="text-base font-bold">Deskripsi</h3>
            @if (filled($product->short_description_id))<p class="mt-2 font-semibold">{{ $product->short_description_id }}</p>@endif
            @if (filled($product->description_id))<div class="rich-text mt-3">{!! $product->description_id !!}</div>@endif
        </section>
    @endif

    @if (filled($product->short_description_en) || filled($product->description_en))
        <details class="rounded-lg border border-line p-3">
            <summary class="cursor-pointer font-semibold">Deskripsi (English)</summary>
            @if (filled($product->short_description_en))<p class="mt-3 font-semibold">{{ $product->short_description_en }}</p>@endif
            @if (filled($product->description_en))<div class="rich-text mt-3">{!! $product->description_en !!}</div>@endif
        </details>
    @endif

    @if ($product->isDigital())
        <section>
            <h3 class="text-base font-bold">Pengiriman digital</h3>
            <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1">
                <dt class="text-muted">Cara akses</dt><dd class="font-semibold">{{ $product->deliveryLabel() ?: '—' }}</dd>
                @if ($product->digital_delivery === \App\Models\Product::DELIVERY_FILE)
                    <dt class="text-muted">Batas unduhan</dt><dd>{{ $product->max_downloads ? $product->max_downloads.' kali' : 'Tak terbatas' }}</dd>
                    <dt class="text-muted">Masa berlaku</dt><dd>{{ $product->download_expiry_days ? $product->download_expiry_days.' hari' : 'Tanpa batas' }}</dd>
                @endif
                @if ($product->digital_delivery === \App\Models\Product::DELIVERY_LICENSE)
                    <dt class="text-muted">Kunci lisensi</dt>
                    <dd @class(['font-semibold', 'text-danger-700 dark:text-red-300' => $keys['available'] < 5])>{{ $keys['available'] }} tersedia dari {{ $keys['total'] }}</dd>
                @endif
                @if ($product->digital_delivery === \App\Models\Product::DELIVERY_LINK)
                    <dt class="text-muted">Link akses</dt><dd>{{ filled($product->access_url) ? 'Sudah diisi' : 'Belum diisi' }}</dd>
                @endif
            </dl>

            @if ($product->digital_delivery === \App\Models\Product::DELIVERY_FILE)
                @if ($product->files->isEmpty())
                    <p class="mt-3 rounded-lg border border-dashed border-line p-3 text-center text-muted">Belum ada file diunggah.</p>
                @else
                    <ul class="mt-3 divide-y divide-line rounded-lg border border-line">
                        @foreach ($product->files as $file)
                            @php $open = route('dashboard.shop.products.files.show', [$product, $file]); @endphp
                            <li class="flex items-center justify-between gap-3 px-3 py-2">
                                <a href="{{ $open }}" target="_blank" rel="noopener" class="group min-w-0 flex-1" title="{{ $file->looksViewable() ? 'Buka file di tab baru' : 'Unduh file' }}">
                                    <span class="block truncate font-semibold group-hover:underline"><i class="fa-regular fa-file mr-1 text-muted" aria-hidden="true"></i>{{ $file->original_name }}</span>
                                    <span class="block text-caption text-muted">{{ $file->humanSize() }}{{ $file->version_label ? ' · '.$file->version_label : '' }}{{ $file->variant ? ' · '.$file->variant->getTranslation('title', 'id') : ' · semua varian' }}</span>
                                </a>
                                <span class="flex shrink-0 items-center gap-1">
                                    @if ($file->looksViewable())
                                        <a href="{{ $open }}" target="_blank" rel="noopener" class="btn-icon" title="Buka di tab baru" aria-label="Buka {{ $file->original_name }} di tab baru"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                                    @endif
                                    <a href="{{ $open }}?download=1" class="btn-icon" title="Unduh" aria-label="Unduh {{ $file->original_name }}"><i class="fa-solid fa-download" aria-hidden="true"></i></a>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </section>
    @endif

    @if (filled($product->license_terms_id))
        <details class="rounded-lg border border-line p-3">
            <summary class="cursor-pointer font-semibold">Ketentuan lisensi</summary>
            <div class="rich-text mt-3">{!! nl2br(e($product->license_terms_id)) !!}</div>
        </details>
    @endif

    <section>
        <h3 class="text-base font-bold">SEO</h3>
        <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1">
            <dt class="text-muted">Judul meta</dt><dd>{{ $product->meta_title ?: 'Otomatis dari nama produk' }}</dd>
            <dt class="text-muted">Deskripsi meta</dt><dd>{{ $product->meta_description ?: 'Otomatis dari deskripsi singkat' }}</dd>
        </dl>
    </section>

    <footer class="flex flex-wrap gap-2 border-t border-line pt-4">
        <a href="{{ route('dashboard.shop.products.edit', $product) }}" class="btn-primary btn-sm"><i class="fa-solid fa-pencil" aria-hidden="true"></i> Ubah produk</a>
        <a href="{{ route('dashboard.shop.products.show', $product) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> Lihat tampilan toko</a>
        <a href="{{ route('dashboard.shop.promos.create', ['product' => $product->id]) }}" class="btn-secondary btn-sm"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i> Buat konten promosi</a>
    </footer>
</article>
