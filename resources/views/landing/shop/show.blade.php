@php
    $id = app()->getLocale() === 'id';
    $name = $product->getTranslation('name');
    $images = $product->images->map(fn ($img) => ['id' => $img->id, 'url' => $img->media_url, 'alt' => $img->altText()])->values();
    $description = $product->getTranslation('description');
    $license = $product->getTranslation('license_terms');
    $cfg = $variantData + [
        'images' => $images,
        'locale' => app()->getLocale(),
        'oldQty' => (int) old('quantity', 1),
    ];
    $deliveryInfo = match ($product->digital_delivery) {
        'file' => [$id ? 'File diunduh dari akun Anda' : 'Download the files from your account', 'fa-download'],
        'license_key' => [$id ? 'Kunci lisensi muncul di akun Anda' : 'License key shown in your account', 'fa-key'],
        'link' => [$id ? 'Link akses muncul di akun Anda' : 'Access link shown in your account', 'fa-link'],
        default => [$id ? 'Dikirim ke alamat Anda' : 'Shipped to your address', 'fa-truck-fast'],
    };
@endphp
<x-layouts.public :title="$product->meta_title ?: $name"
    :description="$product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->getTranslation('short_description') ?: $description), 160)"
    :image="$images->first()['url'] ?? null"
    :image-alt="$images->first()['alt'] ?? $name"
    type="product">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <x-slot:head>
        <meta property="product:price:amount" content="{{ $product->min_price }}">
        <meta property="product:price:currency" content="IDR">
        <meta property="product:availability" content="{{ $product->inStock() ? 'in stock' : 'out of stock' }}">
    </x-slot:head>

    <x-seo.schema :data="['@graph' => [
        \App\Support\Seo::shopProduct($product),
        \App\Support\Seo::breadcrumbs([[$id ? 'Beranda' : 'Home', route('home')], [$id ? 'Toko' : 'Shop', route('shop.index')], [$name, route('shop.show', $product->slug)]]),
    ]]" />

    @if ($preview ?? false)
        <div class="fixed inset-x-0 bottom-0 z-[60] flex flex-wrap items-center justify-center gap-x-4 gap-y-1 border-t-2 border-edge bg-sun-400 px-4 py-2 text-center text-small font-bold text-navy">
            <span><i class="fa-solid fa-eye" aria-hidden="true"></i> Mode pratinjau admin · {{ $product->is_active ? 'Produk aktif di toko' : 'Produk NONAKTIF, belum tampil di toko' }}</span>
            <a href="{{ route('dashboard.shop.products.edit', $product) }}" class="underline">Ubah produk</a>
        </div>
    @endif

    <div class="pb-16 pt-28 md:pb-24 md:pt-36" x-data="productPage(@js($cfg))">
        <div class="container-page">
            <nav class="flex flex-wrap items-center gap-2 text-small text-muted" aria-label="Breadcrumb">
                <a href="{{ route('shop.index') }}" class="font-semibold hover:text-ink">{{ $id ? 'Toko' : 'Shop' }}</a>
                @if ($product->category)
                    <i class="fa-solid fa-chevron-right text-[10px]" aria-hidden="true"></i>
                    <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="font-semibold hover:text-ink">{{ $product->category->getTranslation('name') }}</a>
                @endif
            </nav>

            @if (session('success') || session('error'))
                <div @class(['mt-6 flex items-start gap-3 rounded-lg border-2 border-edge p-4 text-small font-semibold', 'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300' => session('success'), 'bg-danger-50 text-danger-700 dark:bg-danger-500/15 dark:text-red-300' => session('error')]) role="status">
                    <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} mt-0.5" aria-hidden="true"></i>
                    <span class="flex-1">{{ session('success') ?? session('error') }}</span>
                    @if (session('success'))
                        <a href="{{ route('cart.index') }}" class="shrink-0 underline">{{ $id ? 'Lihat keranjang' : 'View cart' }}</a>
                    @endif
                </div>
            @endif

            <div class="mt-6 grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
                {{-- Gallery --}}
                <div class="min-w-0 lg:col-span-7">
                    <div class="overflow-hidden rounded-xl border-2 border-edge bg-sun-100 shadow-hard dark:bg-surface-2">
                        <div class="aspect-[4/3]">
                            <template x-if="currentImage">
                                <img :src="currentImage.url" :alt="currentImage.alt" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!currentImage">
                                <div class="flex h-full items-center justify-center text-7xl text-navy/25 dark:text-ink/25" aria-hidden="true"><i class="fa-solid {{ $product->isDigital() ? 'fa-file-zipper' : 'fa-box-open' }}"></i></div>
                            </template>
                        </div>
                    </div>
                    @if ($images->count() > 1)
                        <div class="mt-4 flex gap-3 overflow-x-auto pb-1" role="group" aria-label="{{ $id ? 'Gambar produk' : 'Product images' }}">
                            <template x-for="(image, index) in images" :key="image.id">
                                <button type="button" @click="imageId = image.id"
                                    class="h-20 w-24 shrink-0 overflow-hidden rounded-lg border-2 transition"
                                    :class="currentImage && currentImage.id === image.id ? 'border-edge ring-2 ring-sun-400' : 'border-line opacity-80 hover:opacity-100'"
                                    :aria-label="'{{ $id ? 'Gambar' : 'Image' }} ' + (index + 1)" :aria-pressed="currentImage && currentImage.id === image.id">
                                    <img :src="image.url" alt="" class="h-full w-full object-cover">
                                </button>
                            </template>
                        </div>
                    @endif
                </div>

                {{-- Buy box --}}
                <div class="min-w-0 lg:col-span-5">
                    <div class="lg:sticky lg:top-28">
                        <span class="inline-flex items-center gap-1.5 rounded-full border-2 border-edge bg-surface px-3 py-1 text-caption font-bold">
                            <i class="fa-solid {{ $product->isDigital() ? 'fa-bolt text-brand-600' : 'fa-truck-fast text-sun-700' }}" aria-hidden="true"></i>
                            {{ $product->isDigital() ? ($id ? 'Produk digital' : 'Digital product') : ($id ? 'Produk fisik' : 'Physical product') }}
                        </span>
                        <h1 class="mt-4 font-display text-4xl font-extrabold leading-[1.05] tracking-tight md:text-5xl">{{ $name }}</h1>
                        @if ($product->getTranslation('short_description'))
                            <p class="mt-4 text-base text-muted md:text-lg">{{ $product->getTranslation('short_description') }}</p>
                        @endif

                        <div class="mt-6 flex flex-wrap items-end gap-3" aria-live="polite">
                            <span class="text-4xl font-extrabold" x-text="money(variant ? variant.price : {{ $product->min_price }})">{{ \App\Services\Shop\Money::format($product->min_price) }}</span>
                            <span class="pb-1 text-lg text-muted line-through" x-show="variant && variant.compare" x-cloak x-text="variant ? money(variant.compare) : ''"></span>
                        </div>

                        <form method="POST" action="{{ route('cart.store') }}" class="mt-6 space-y-5" @if ($preview ?? false) @submit.prevent @endif>
                            @csrf
                            <input type="hidden" name="variant_id" :value="variant ? variant.id : ''">

                            <template x-for="option in options" :key="option.id">
                                <fieldset>
                                    <legend class="label"><span x-text="option.name"></span>: <span class="font-normal text-muted" x-text="selectedLabel(option)"></span></legend>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="value in option.values" :key="value.id">
                                            <button type="button" @click="select(option.id, value.id)"
                                                class="min-h-[44px] rounded-lg border-2 px-4 text-small font-bold transition"
                                                :class="selected[option.id] === value.id ? 'border-edge bg-sun-400 text-navy shadow-hard-sm' : (isAvailable(option.id, value.id) ? 'border-line bg-surface hover:border-edge' : 'border-dashed border-line bg-surface text-muted line-through')"
                                                :aria-pressed="selected[option.id] === value.id"
                                                x-text="value.label"></button>
                                        </template>
                                    </div>
                                </fieldset>
                            </template>

                            <div class="flex flex-wrap items-center gap-3 text-small">
                                <template x-if="variant && variant.stock === 0">
                                    <span class="badge-danger"><i class="fa-solid fa-circle-xmark" aria-hidden="true"></i> {{ $id ? 'Stok habis' : 'Sold out' }}</span>
                                </template>
                                <template x-if="variant && variant.stock !== null && variant.stock > 0 && variant.stock <= 10">
                                    <span class="badge-warning" x-text="'{{ $id ? 'Sisa' : 'Only' }} ' + variant.stock + '{{ $id ? ' lagi' : ' left' }}'"></span>
                                </template>
                                <template x-if="!variant">
                                    <span class="badge-neutral">{{ $id ? 'Kombinasi ini tidak tersedia' : 'This combination is not available' }}</span>
                                </template>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <div class="flex h-12 items-center overflow-hidden rounded-lg border-2 border-edge bg-surface">
                                    <button type="button" class="h-full w-11 text-lg font-bold hover:bg-surface-2" @click="qty = Math.max(1, qty - 1)" aria-label="{{ $id ? 'Kurangi' : 'Decrease' }}">−</button>
                                    <label class="sr-only" for="quantity">{{ $id ? 'Jumlah' : 'Quantity' }}</label>
                                    <input id="quantity" name="quantity" type="number" min="1" :max="maxQty" x-model.number="qty" class="h-full w-14 border-0 bg-transparent text-center font-bold focus:ring-0">
                                    <button type="button" class="h-full w-11 text-lg font-bold hover:bg-surface-2" @click="qty = Math.min(maxQty, qty + 1)" aria-label="{{ $id ? 'Tambah' : 'Increase' }}">+</button>
                                </div>
                                <button type="submit" class="btn-primary btn-lg min-w-0 flex-1" :disabled="!canBuy">
                                    <i class="fa-solid fa-cart-plus" aria-hidden="true"></i> {{ $id ? 'Tambah ke keranjang' : 'Add to cart' }}
                                </button>
                            </div>
                            @auth
                                <button type="submit" name="buy_now" value="1" class="btn-sun btn-lg w-full" :disabled="!canBuy">{{ $id ? 'Beli sekarang' : 'Buy now' }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                            @else
                                <p class="text-caption text-muted"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> {{ $id ? 'Anda akan diminta masuk atau daftar dulu. Pilihan Anda tetap tersimpan.' : 'You will be asked to log in or sign up first. Your choice is kept.' }}</p>
                            @endauth
                        </form>

                        <ul class="mt-8 divide-y divide-line rounded-xl border-2 border-edge bg-surface text-small">
                            <li class="flex items-center gap-3 p-4"><i class="fa-solid {{ $deliveryInfo[1] }} w-5 text-brand-600" aria-hidden="true"></i><span>{{ $deliveryInfo[0] }}</span></li>
                            @if ($product->isDigital() && $product->digital_delivery === 'file')
                                <li class="flex items-center gap-3 p-4"><i class="fa-solid fa-rotate w-5 text-brand-600" aria-hidden="true"></i><span>{{ $product->max_downloads ? ($id ? "Bisa diunduh hingga {$product->max_downloads} kali" : "Up to {$product->max_downloads} downloads") : ($id ? 'Unduh tanpa batas' : 'Unlimited downloads') }}{{ $product->download_expiry_days ? ($id ? ", berlaku {$product->download_expiry_days} hari" : ", valid for {$product->download_expiry_days} days") : '' }}</span></li>
                            @endif
                            <li class="flex items-center gap-3 p-4"><i class="fa-solid fa-shield-halved w-5 text-mint-600" aria-hidden="true"></i><span>{{ $id ? 'Pembayaran aman lewat Duitku atau transfer bank' : 'Secure payment via Duitku or bank transfer' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            @if ($description || $license)
                <div class="mt-16 grid grid-cols-1 gap-10 lg:grid-cols-12">
                    @if ($description)
                        <section class="min-w-0 lg:col-span-8" aria-labelledby="desc-title">
                            <h2 id="desc-title" class="text-2xl font-extrabold">{{ $id ? 'Tentang produk ini' : 'About this product' }}</h2>
                            <div class="rich-text mt-6">{!! $description !!}</div>
                        </section>
                    @endif
                    @if ($license)
                        <aside class="lg:col-span-4">
                            <div class="rounded-xl border-2 border-edge bg-brand-100 p-6 dark:bg-surface-2">
                                <h2 class="flex items-center gap-2 text-lg font-bold"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i> {{ $id ? 'Ketentuan lisensi' : 'License terms' }}</h2>
                                <p class="mt-3 whitespace-pre-line text-small">{{ $license }}</p>
                            </div>
                        </aside>
                    @endif
                </div>
            @endif

            @if ($related->isNotEmpty())
                <section class="mt-20" aria-labelledby="related-title">
                    <h2 id="related-title" class="text-2xl font-extrabold">{{ $id ? 'Produk lain' : 'More products' }}</h2>
                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($related as $item)
                            <x-shop.product-card :product="$item" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
    <x-slot:scripts>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('productPage', (cfg) => ({
                    options: cfg.options,
                    variants: cfg.variants,
                    images: cfg.images,
                    selected: {},
                    imageId: cfg.images.length ? cfg.images[0].id : null,
                    qty: Math.max(1, cfg.oldQty || 1),
                    valueOption: {},
                    init() {
                        this.options.forEach(o => o.values.forEach(v => { this.valueOption[v.id] = o.id; }));
                        const start = this.variants.find(v => v.stock === null || v.stock > 0) || this.variants[0];
                        if (start) { start.values.forEach(valueId => { this.selected[this.valueOption[valueId]] = valueId; }); }
                        this.$watch('variant', (v) => { if (v && v.image) this.imageId = null; this.qty = Math.min(this.qty, this.maxQty); });
                    },
                    get variant() {
                        if (!this.options.length) return this.variants[0] || null;
                        return this.variants.find(v => this.options.every(o => v.values.includes(this.selected[o.id]))) || null;
                    },
                    get currentImage() {
                        if (this.imageId) return this.images.find(i => i.id === this.imageId) || null;
                        if (this.variant && this.variant.image) return { id: 'variant', url: this.variant.image, alt: this.variant.title };
                        return this.images[0] || null;
                    },
                    get maxQty() { return this.variant && this.variant.stock !== null ? Math.max(1, this.variant.stock) : 99; },
                    get canBuy() { return !!this.variant && (this.variant.stock === null || this.variant.stock > 0); },
                    select(optionId, valueId) { this.selected[optionId] = valueId; },
                    selectedLabel(option) { const v = option.values.find(x => x.id === this.selected[option.id]); return v ? v.label : ''; },
                    isAvailable(optionId, valueId) {
                        return this.variants.some(v => v.values.includes(valueId)
                            && (v.stock === null || v.stock > 0)
                            && this.options.every(o => o.id === optionId || !this.selected[o.id] || v.values.includes(this.selected[o.id])));
                    },
                    money(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0); },
                }));
            });
        </script>
    </x-slot:scripts>
</x-layouts.public>
