@php
    $id = app()->getLocale() === 'id';
    $sorts = [
        'featured' => $id ? 'Unggulan' : 'Featured',
        'newest' => $id ? 'Terbaru' : 'Newest',
        'price_asc' => $id ? 'Harga terendah' : 'Lowest price',
        'price_desc' => $id ? 'Harga tertinggi' : 'Highest price',
    ];
    $hasFilter = $filters['q'] !== '' || $filters['category'] !== '' || $filters['type'] !== '';
    $activeCategory = $categories->firstWhere('slug', $filters['category']);
@endphp
<x-layouts.public
    :title="$activeCategory ? $activeCategory->getTranslation('name').' — '.($id ? 'Toko' : 'Shop') : ($id ? 'Toko Produk Digital' : 'Digital Product Shop')"
    :description="$id ? 'Template, source code, aset desain, dan produk digital siap pakai dari FKStudio. Bayar online, langsung unduh.' : 'Templates, source code, design assets and ready-to-use digital products from FKStudio. Pay online, download instantly.'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <x-seo.schema :data="['@graph' => [
        \App\Support\Seo::breadcrumbs([[$id ? 'Beranda' : 'Home', route('home')], [$id ? 'Toko' : 'Shop', route('shop.index')]]),
        ['@type' => 'ItemList', 'name' => $id ? 'Produk FKStudio' : 'FKStudio products', 'itemListElement' => $products->getCollection()->values()->map(fn ($p, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => route('shop.show', $p->slug), 'name' => $p->getTranslation('name')])->all()],
    ]]" />

    <x-site.page-head
        :title="$id ? 'Toko' : 'Shop'"
        :lead="$id ? 'Produk digital siap pakai dari tim yang membangun website dan aplikasi setiap hari. Bayar sekali, unduh dari akun Anda kapan saja.' : 'Ready-to-use digital products from the team that builds websites and apps every day. Pay once, download from your account any time.'">
        <x-slot:aside>
            <ul class="space-y-2.5 text-small font-semibold">
                <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-edge bg-sun-400 text-navy"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>{{ $id ? 'Akses langsung setelah bayar' : 'Instant access after payment' }}</li>
                <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-edge bg-surface text-brand-600"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span>{{ $id ? 'VA, QRIS, e-wallet, atau transfer' : 'VA, QRIS, e-wallet or bank transfer' }}</li>
                <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-edge bg-surface text-mint-600"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span>{{ $id ? 'Invoice resmi tiap pembelian' : 'An invoice with every purchase' }}</li>
            </ul>
        </x-slot:aside>
    </x-site.page-head>

    <section class="container-page py-10 md:py-14">
        @if (session('success') || session('error'))
            <div @class(['mb-6 flex items-start gap-3 rounded-lg border-2 border-edge p-4 text-small font-semibold', 'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300' => session('success'), 'bg-danger-50 text-danger-700 dark:bg-danger-500/15 dark:text-red-300' => session('error')]) role="status">
                <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} mt-0.5" aria-hidden="true"></i>
                <span>{{ session('success') ?? session('error') }}</span>
            </div>
        @endif

        <form method="GET" action="{{ route('shop.index') }}" class="flex flex-col gap-3 md:flex-row md:items-center">
            @if ($filters['category'] !== '')<input type="hidden" name="category" value="{{ $filters['category'] }}">@endif
            @if ($filters['type'] !== '')<input type="hidden" name="type" value="{{ $filters['type'] }}">@endif
            <label class="relative min-w-0 flex-1">
                <span class="sr-only">{{ $id ? 'Cari produk' : 'Search products' }}</span>
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted" aria-hidden="true"></i>
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ $id ? 'Cari template, e-book, aset…' : 'Search templates, e-books, assets…' }}" class="input pl-10">
            </label>
            <div class="flex gap-3">
                <label class="min-w-0 flex-1 md:w-52 md:flex-none">
                    <span class="sr-only">{{ $id ? 'Urutkan' : 'Sort' }}</span>
                    <select name="sort" class="select" onchange="this.form.submit()">
                        @foreach ($sorts as $value => $label)
                            <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="btn-primary shrink-0">{{ $id ? 'Cari' : 'Search' }}</button>
            </div>
        </form>

        @if ($categories->isNotEmpty() || $settings->shop_physical_enabled)
            <nav class="mt-5 flex gap-2 overflow-x-auto pb-1" aria-label="{{ $id ? 'Kategori' : 'Categories' }}">
                @php $chip = fn ($active) => $active ? 'border-edge bg-navy text-white dark:bg-sun-400 dark:text-navy' : 'border-line bg-surface hover:border-edge'; @endphp
                <a href="{{ route('shop.index', array_filter(['q' => $filters['q'], 'sort' => $filters['sort'] !== 'featured' ? $filters['sort'] : null, 'type' => $filters['type'] ?: null])) }}"
                    class="shrink-0 rounded-full border-2 px-4 py-1.5 text-small font-bold transition {{ $chip($filters['category'] === '') }}" @if ($filters['category'] === '') aria-current="page" @endif>{{ $id ? 'Semua' : 'All' }}</a>
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', array_filter(['category' => $category->slug, 'q' => $filters['q'], 'sort' => $filters['sort'] !== 'featured' ? $filters['sort'] : null, 'type' => $filters['type'] ?: null])) }}"
                        class="shrink-0 rounded-full border-2 px-4 py-1.5 text-small font-bold transition {{ $chip($filters['category'] === $category->slug) }}" @if ($filters['category'] === $category->slug) aria-current="page" @endif>{{ $category->getTranslation('name') }}</a>
                @endforeach
                @if ($settings->shop_physical_enabled)
                    <span class="mx-1 w-px shrink-0 bg-line" aria-hidden="true"></span>
                    @foreach (['digital' => $id ? 'Digital' : 'Digital', 'physical' => $id ? 'Fisik' : 'Physical'] as $type => $label)
                        <a href="{{ route('shop.index', array_filter(['type' => $filters['type'] === $type ? null : $type, 'category' => $filters['category'] ?: null, 'q' => $filters['q']])) }}"
                            class="shrink-0 rounded-full border-2 px-4 py-1.5 text-small font-bold transition {{ $chip($filters['type'] === $type) }}">{{ $label }}</a>
                    @endforeach
                @endif
            </nav>
        @endif

        <div class="mt-8">
            @if ($products->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state icon="fa-store"
                        :title="$hasFilter ? ($id ? 'Tidak ada produk yang cocok' : 'No matching products') : ($id ? 'Produk segera hadir' : 'Products coming soon')"
                        :text="$hasFilter ? ($id ? 'Coba kata kunci lain atau hapus filter.' : 'Try another keyword or clear the filters.') : ($id ? 'Kami sedang menyiapkan produk pertama. Kembali lagi sebentar lagi.' : 'We are preparing our first products. Check back soon.')">
                        @if ($hasFilter)
                            <a href="{{ route('shop.index') }}" class="btn-secondary">{{ $id ? 'Hapus filter' : 'Clear filters' }}</a>
                        @endif
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <p class="mb-4 text-small text-muted">{{ $id ? $products->total().' produk' : $products->total().' products' }}</p>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <x-shop.product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </div>
    </section>

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
