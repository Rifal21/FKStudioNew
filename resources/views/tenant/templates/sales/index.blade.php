<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $sections = $setting->sections_data ?? [];
        $footer = $sections['footer'] ?? null;
        $design = $sections['design'] ?? [];
        $theme = $design['theme_color'] ?? '#10b981';
        $btnShape = $design['button_style'] ?? 'rounded-xl';
        $productCta = $design['product_cta_text'] ?? 'Beli Sekarang';
    @endphp
    <title>{{ $brandingName }} - Toko Online</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #050505; color: #ffffff; }
    </style>
</head>
<body class="antialiased min-h-screen" x-data="{ cartCount: {{ count(Session::get('cart', [])) }} }">
    <style>
        :root {
            @php
                $themeHex = $theme;
                if (!str_starts_with($themeHex, '#')) {
                    $themeHex = match($themeHex) {
                        'blue' => '#3b82f6',
                        'emerald' => '#10b981',
                        'rose' => '#f43f5e',
                        'amber' => '#f59e0b',
                        'violet' => '#8b5cf6',
                        'indigo' => '#6366f1',
                        'teal' => '#14b8a6',
                        'orange' => '#f97316',
                        default => '#3b82f6'
                    };
                }
            @endphp
            --theme-color: {{ $themeHex }};
            --theme-soft: {{ $themeHex }}15;
            --theme-hover: {{ $themeHex }}30;
            --theme-strong: {{ $themeHex }}e6;
            --theme-shadow: {{ $themeHex }}40;
            --theme-gradient: linear-gradient(135deg, {{ $themeHex }}, {{ $themeHex }}cc);
        }
        .theme-text { color: var(--theme-color); }
        .theme-bg { background-color: var(--theme-color); }
        .theme-bg-soft { background-color: var(--theme-soft); }
        .theme-shadow { box-shadow: 0 10px 25px -5px var(--theme-shadow); }
        .theme-gradient { background: var(--theme-gradient); }
        .theme-text-gradient { background: var(--theme-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .theme-border { border-color: var(--theme-shadow); }
        
        .hover-theme-text:hover { color: var(--theme-color); }
        .hover-theme-bg:hover { background-color: var(--theme-strong); }
        .hover-theme-border:hover { border-color: var(--theme-shadow); }
        
        .group:hover .group-hover-theme-bg { background-color: var(--theme-color); }
        .group:hover .group-hover-theme-bg-soft { background-color: var(--theme-soft); }
    </style>
    
    <!-- Top Banner -->
    <div class="theme-gradient text-white text-center py-2 text-xs font-bold uppercase tracking-widest">
        🚀 Penawaran Terbatas - Belanja Sekarang & Dapatkan Harga Spesial!
    </div>

    <!-- Navbar -->
    <nav class="w-full border-b border-white/10 bg-black/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('tenant.home') }}" class="text-xl font-black text-white tracking-widest uppercase">
                {{ $brandingName }}
            </a>
            
            <div class="flex items-center space-x-6">
                <a href="#products" class="text-xs font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest">Produk</a>
                <a href="{{ route('tenant.cart') }}" class="relative w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white hover:bg-white/10 transition-all">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <template x-if="cartCount > 0">
                        <span x-text="cartCount" class="absolute -top-1 -right-1 w-5 h-5 theme-bg text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-[#050505]"></span>
                    </template>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Banner Style) -->
    <div class="relative min-h-[70vh] flex items-center justify-center pt-10 pb-20 overflow-hidden bg-[#080808]">
        @if($hero->background_image)
            <div class="absolute inset-0 z-0">
                <img src="{{ $hero->getUrl($hero->background_image) }}" class="w-full h-full object-cover opacity-40">
                <div class="absolute inset-0 bg-gradient-to-t from-[#050505] via-transparent to-transparent"></div>
            </div>
        @else
            <div class="absolute top-0 right-0 w-[500px] h-[500px] theme-bg-soft rounded-full blur-[120px] pointer-events-none opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] theme-bg-soft rounded-full blur-[120px] pointer-events-none opacity-20"></div>
        @endif

        <div class="relative z-10 text-center px-6 max-w-4xl mx-auto">
            <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full theme-border border theme-bg-soft theme-text text-[10px] font-black uppercase tracking-widest mb-8">
                <span>{{ $brandingName }} Official Store</span>
            </div>
            
            <h1 class="text-5xl md:text-8xl font-black tracking-tighter mb-6 leading-[0.9] uppercase italic">
                {{ $hero->headline ?? 'Koleksi Terbaik Untuk Gaya Hidup Anda' }}
            </h1>
            
            <p class="text-base md:text-lg font-medium mb-10 text-gray-400 max-w-2xl mx-auto leading-relaxed">
                {{ $hero->subheadline ?? 'Temukan berbagai produk berkualitas tinggi dengan harga yang kompetitif. Belanja mudah, aman, dan cepat.' }}
            </p>
            
            <a href="#products" class="group relative inline-flex items-center px-10 py-5 theme-bg text-white {{ $btnShape }} font-black uppercase tracking-widest text-sm hover:-translate-y-1 transition-all theme-shadow">
                Mulai Belanja <i class="fa-solid fa-arrow-down ml-3 group-hover:translate-y-1 transition-transform"></i>
            </a>
        </div>
    </div>

    <!-- Products Grid -->
    <section class="py-24 bg-[#050505]" id="products">
        <div class="max-w-5xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4">
                <div>
                    <h2 class="text-3xl md:text-5xl font-black text-white tracking-tighter mb-4 uppercase">Produk Kami</h2>
                    <div class="w-20 h-1 theme-gradient rounded-full"></div>
                </div>
                <p class="text-gray-500 text-sm font-medium">Menampilkan {{ $products->count() }} produk pilihan</p>
            </div>
            
            @php
                $cardStyle = $setting->sections_data['design']['card_style'] ?? 'grid';
            @endphp

            @if($cardStyle === 'grid')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($products as $product)
                <div class="bg-[#111] border border-white/5 rounded-2xl overflow-hidden group hover:border-white/20 transition-all flex flex-col">
                    <div class="aspect-square bg-black overflow-hidden relative">
                        @if($product->image)
                            <img src="{{ $product->getUrl($product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-800">
                                <i class="fa-solid fa-image text-4xl"></i>
                            </div>
                        @endif
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button 
                                @click="
                                    fetch('{{ route('tenant.cart.add', $product) }}', { 
                                        method: 'POST', 
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
                                    })
                                    .then(res => res.json())
                                    .then(data => { 
                                        if(data.success) { 
                                            cartCount = data.cart_count; 
                                            alert(data.message);
                                        } 
                                    })
                                "
                                class="px-6 py-3 theme-bg text-white font-black uppercase tracking-widest text-[10px] {{ $btnShape }} transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                <i class="fa-solid fa-cart-plus mr-2"></i> {{ $productCta }}
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-white mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-500 text-xs mb-4 flex-1 line-clamp-2">{{ $product->description }}</p>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-xl font-black theme-text-gradient italic">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Stok: {{ $product->stock }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center glass rounded-3xl border-dashed border-2 border-white/10">
                    <i class="fa-solid fa-box-open text-5xl text-gray-800 mb-6 block"></i>
                    <p class="text-gray-500 font-bold italic">Belum ada produk yang tersedia saat ini.</p>
                </div>
                @endforelse
            </div>
            @else
            <div class="space-y-6">
                @forelse($products as $product)
                <div class="bg-[#111] border border-white/5 rounded-2xl overflow-hidden group hover:border-white/20 transition-all flex flex-col md:flex-row h-auto md:h-64">
                    <div class="w-full md:w-64 bg-black overflow-hidden relative shrink-0">
                        @if($product->image)
                            <img src="{{ $product->getUrl($product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-800">
                                <i class="fa-solid fa-image text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-8 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-2xl font-bold text-white">{{ $product->name }}</h3>
                                <span class="text-2xl font-black theme-text-gradient italic">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-gray-500 text-sm mb-6 line-clamp-3">{{ $product->description }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Stok: {{ $product->stock }}</span>
                            <button 
                                @click="
                                    fetch('{{ route('tenant.cart.add', $product) }}', { 
                                        method: 'POST', 
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
                                    })
                                    .then(res => res.json())
                                    .then(data => { 
                                        if(data.success) { 
                                            cartCount = data.cart_count; 
                                            alert(data.message);
                                        } 
                                    })
                                "
                                class="px-8 py-4 theme-bg text-white font-black uppercase tracking-widest text-[10px] {{ $btnShape }} transition-all">
                                <i class="fa-solid fa-cart-plus mr-2"></i> {{ $productCta }}
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center glass rounded-3xl border-dashed border-2 border-white/10">
                    <i class="fa-solid fa-box-open text-5xl text-gray-800 mb-6 block"></i>
                    <p class="text-gray-500 font-bold italic">Belum ada produk yang tersedia saat ini.</p>
                </div>
                @endforelse
            </div>
            @endif
        </div>
    </section>

    <!-- Simple Footer -->
    <footer class="bg-[#020202] pt-20 pb-10 border-t border-white/5" id="contact">
        <div class="max-w-5xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-12 border-b border-white/10 pb-12 mb-12">
            <div>
                <a href="#" class="text-2xl font-black text-white tracking-widest uppercase mb-4 block text-center md:text-left">
                    {{ $brandingName }}
                </a>
                <div class="flex items-center justify-center md:justify-start space-x-6">
                    @if($footer && !empty($footer['whatsapp']))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $footer['whatsapp']) }}" class="text-gray-500 hover:text-white transition-colors text-xl"><i class="fa-brands fa-whatsapp"></i></a>
                    @endif
                    @if($footer && !empty($footer['instagram']))
                        <a href="#" class="text-gray-500 hover:text-white transition-colors text-xl"><i class="fa-brands fa-instagram"></i></a>
                    @endif
                    @if($footer && !empty($footer['email']))
                        <a href="mailto:{{ $footer['email'] }}" class="text-gray-500 hover:text-white transition-colors text-xl"><i class="fa-solid fa-envelope"></i></a>
                    @endif
                </div>
            </div>
            
            <div class="text-center md:text-right space-y-2">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Metode Pembayaran</p>
                <div class="flex items-center space-x-4 text-2xl text-gray-800">
                    <i class="fa-brands fa-cc-visa"></i>
                    <i class="fa-brands fa-cc-mastercard"></i>
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>
        <div class="text-center text-gray-700 text-[10px] font-black tracking-[0.2em] uppercase">
            &copy; {{ date('Y') }} {{ $brandingName }}. Powered by FKStudio.
        </div>
    </footer>
</body>
</html>
