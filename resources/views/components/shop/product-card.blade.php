@props(['product'])
@php
    $id = app()->getLocale() === 'id';
    $cover = $product->coverImage();
    $variant = $product->defaultVariant();
    $compare = ! $product->hasPriceRange() && $variant?->compare_at_price > $variant?->price ? $variant->compare_at_price : null;
    $soldOut = ! $product->inStock();
@endphp
<article class="group relative flex h-full flex-col overflow-hidden rounded-xl border-2 border-edge bg-surface shadow-hard-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-hard">
    <div class="relative aspect-[4/3] overflow-hidden border-b-2 border-edge bg-sun-100 dark:bg-surface-2">
        @if ($cover)
            <img src="{{ $cover->media_url }}" alt="{{ $cover->altText() }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
        @else
            <div class="flex h-full w-full items-center justify-center text-5xl text-navy/30 dark:text-ink/30" aria-hidden="true">
                <i class="fa-solid {{ $product->isDigital() ? 'fa-file-zipper' : 'fa-box-open' }}"></i>
            </div>
        @endif
        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
            <span class="rounded-full border-2 border-edge bg-surface px-2.5 py-0.5 text-caption font-bold text-ink">
                <i class="fa-solid {{ $product->isDigital() ? 'fa-bolt text-brand-600' : 'fa-truck-fast text-sun-700' }}" aria-hidden="true"></i>
                {{ $product->isDigital() ? ($id ? 'Digital' : 'Digital') : ($id ? 'Fisik' : 'Physical') }}
            </span>
            @if ($compare)
                <span class="rounded-full border-2 border-edge bg-sun-400 px-2.5 py-0.5 text-caption font-extrabold text-navy">-{{ round(100 - ($variant->price / $compare * 100)) }}%</span>
            @endif
        </div>
        @if ($soldOut)
            <span class="absolute inset-x-0 bottom-0 bg-navy/85 py-1.5 text-center text-caption font-bold uppercase tracking-wider text-white">{{ $id ? 'Stok habis' : 'Sold out' }}</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4">
        @if ($product->category)
            <p class="text-caption font-bold uppercase tracking-wider text-brand-text">{{ $product->category->getTranslation('name') }}</p>
        @endif
        <h3 class="mt-1 text-lg font-bold leading-snug">
            <a href="{{ route('shop.show', $product->slug) }}" class="after:absolute after:inset-0 focus:outline-none focus-visible:underline">{{ $product->getTranslation('name') }}</a>
        </h3>
        @if ($product->getTranslation('short_description'))
            <p class="mt-1.5 line-clamp-2 text-small text-muted">{{ $product->getTranslation('short_description') }}</p>
        @endif

        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            <p class="leading-tight">
                @if ($product->hasPriceRange())
                    <span class="block text-caption text-muted">{{ $id ? 'Mulai dari' : 'From' }}</span>
                @elseif ($compare)
                    <span class="block text-caption text-muted line-through">{{ \App\Services\Shop\Money::format($compare) }}</span>
                @endif
                <span class="text-xl font-extrabold">{{ \App\Services\Shop\Money::format($product->min_price) }}</span>
            </p>
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-edge bg-sun-400 text-navy transition group-hover:translate-x-0.5" aria-hidden="true">
                <i class="fa-solid fa-arrow-right"></i>
            </span>
        </div>
    </div>
</article>
