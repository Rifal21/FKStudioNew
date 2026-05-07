<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $sections = $setting->sections_data ?? [];
        $about = $sections['about'] ?? null;
        $features = $sections['features'] ?? [];
        $products = $sections['products'] ?? [];
        $footer = $sections['footer'] ?? null;
        $design = $sections['design'] ?? [];
        $theme = $design['theme_color'] ?? '#3b82f6';
        $btnShape = $design['button_style'] ?? 'rounded-full';
        $productCta = $design['product_cta_text'] ?? 'Learn More';
    @endphp
    <title>{{ $brandingName }} | {{ $hero->headline ?? 'Premium Profile' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #020617; color: #f8fafc; overflow-x: hidden; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-gradient { @apply bg-clip-text text-transparent bg-gradient-to-r from-white to-white/40; }
        .section-title { font-size: clamp(2rem, 5vw, 4rem); line-height: 1; letter-spacing: -0.05em; font-weight: 900; text-transform: uppercase; }
    </style>
</head>
<body class="antialiased min-h-screen">
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
            --theme-soft: {{ $themeHex }}20;
            --theme-glow: {{ $themeHex }}40;
        }
        .theme-text { color: var(--theme-color); }
        .theme-bg { background-color: var(--theme-color); }
        .theme-bg-soft { background-color: var(--theme-soft); }
        .theme-border { border-color: var(--theme-color); }
        .glow-theme { box-shadow: 0 0 50px -10px var(--theme-glow); }
        .btn-premium { @apply relative overflow-hidden transition-all duration-500 hover:scale-105 active:scale-95; }
    </style>
    
    <!-- Navigation -->
    <nav class="fixed w-full z-50 px-6 py-8">
        <div class="max-w-7xl mx-auto glass rounded-3xl px-8 h-20 flex items-center justify-between border-white/5">
            <a href="#" class="text-2xl font-black text-white tracking-tighter uppercase italic">
                {{ $brandingName }}<span class="theme-text">.</span>
            </a>
            
            <div class="hidden lg:flex space-x-10 text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                <a href="#" class="hover:text-white transition-colors">Home</a>
                <a href="#about" class="hover:text-white transition-colors">Identity</a>
                <a href="#services" class="hover:text-white transition-colors">Expertise</a>
                <a href="#portfolio" class="hover:text-white transition-colors">Gallery</a>
            </div>
            
            <a href="{{ route('tenant.login') }}" class="px-6 py-2.5 bg-white text-black {{ $btnShape }} text-[10px] font-black uppercase tracking-widest hover:bg-white/90 transition-all">
                Login
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center pt-20">
        <!-- Abstract Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-1/4 -right-1/4 w-[800px] h-[800px] bg-blue-600/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute -bottom-1/4 -left-1/4 w-[600px] h-[600px] theme-bg-soft rounded-full blur-[100px] animate-bounce" style="animation-duration: 10s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-20" style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');"></div>
        </div>

        <div class="relative z-10 text-center px-6 max-w-5xl mx-auto">
            @if($hero->background_image)
                <div class="absolute inset-0 -z-10 scale-110 blur-sm opacity-30">
                    <img src="{{ $hero->getUrl($hero->background_image) }}" class="w-full h-full object-cover rounded-[5rem]">
                </div>
            @endif

            <span class="inline-block px-4 py-1.5 rounded-full glass text-[10px] font-black uppercase tracking-[0.3em] theme-text mb-8 animate-fade-in">
                Professional Showcase
            </span>
            
            <h1 class="section-title text-white mb-8 leading-[0.9] text-gradient">
                {{ $hero->headline ?? 'Elevating Your Digital Presence' }}
            </h1>
            
            <p class="text-lg md:text-xl font-medium mb-12 text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ $hero->subheadline ?? 'We craft exceptional experiences and deliver innovative solutions tailored to your unique brand identity.' }}
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
                <a href="{{ $hero->cta_link ?? '#' }}" class="btn-premium px-10 py-5 theme-bg text-white {{ $btnShape }} font-black uppercase tracking-widest text-xs glow-theme">
                    {{ $hero->cta_text ?? 'Start Project' }}
                </a>
                <a href="#about" class="btn-premium px-10 py-5 glass text-white {{ $btnShape }} font-black uppercase tracking-widest text-xs border-white/10 hover:bg-white/5">
                    Our Story <i class="fa-solid fa-arrow-down ml-2 animate-bounce"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    @if($about && !empty($about['title']))
    <section class="py-32 relative" id="about">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-24 items-center">
            <div class="relative group">
                <div class="absolute -inset-4 theme-bg rounded-[3rem] blur-2xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                @if(!empty($about['image']))
                    <img src="{{ $setting->getUrl($about['image']) }}" alt="About" class="relative rounded-[3rem] w-full h-[600px] object-cover shadow-2xl grayscale hover:grayscale-0 transition-all duration-700">
                @else
                    <div class="relative rounded-[3rem] w-full h-[600px] bg-slate-900 border border-white/5 flex items-center justify-center text-slate-700">
                        <i class="fa-solid fa-cube text-9xl"></i>
                    </div>
                @endif
                <div class="absolute -bottom-10 -right-10 glass p-8 rounded-3xl hidden md:block">
                    <p class="text-4xl font-black theme-text italic">10+</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Years Experience</p>
                </div>
            </div>

            <div class="space-y-10">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] theme-text mb-4 block">Who we are</span>
                    <h2 class="section-title text-white mb-6 leading-tight">{{ $about['title'] }}</h2>
                    <div class="w-20 h-1.5 theme-bg rounded-full"></div>
                </div>
                <p class="text-slate-400 text-lg md:text-xl leading-relaxed font-medium">
                    {{ $about['description'] }}
                </p>
                <div class="grid grid-cols-2 gap-8 pt-8 border-t border-white/5">
                    <div>
                        <p class="text-white font-black uppercase text-xs mb-2">Innovation</p>
                        <p class="text-slate-500 text-xs">Pioneering new standards in digital excellence.</p>
                    </div>
                    <div>
                        <p class="text-white font-black uppercase text-xs mb-2">Precision</p>
                        <p class="text-slate-500 text-xs">Meticulous attention to every single pixel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Services Section -->
    @if(count($features) > 0)
    <section class="py-32 bg-white/5" id="services">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-24">
                <span class="text-[10px] font-black uppercase tracking-[0.4em] theme-text mb-4 block">Core Expertise</span>
                <h2 class="section-title text-white">Service Ecosystem</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                @foreach($features as $feature)
                <div class="glass p-10 rounded-[3rem] group hover:bg-white/5 transition-all duration-500 border-white/5">
                    <div class="w-16 h-16 theme-bg-soft theme-text rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-4 uppercase tracking-tighter">{{ $feature['title'] }}</h3>
                    <p class="text-slate-400 leading-relaxed font-medium mb-8 text-sm">{{ $feature['description'] }}</p>
                    <div class="h-1 w-0 group-hover:w-full theme-bg transition-all duration-700 rounded-full"></div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Gallery / Portfolio -->
    @if(count($products) > 0)
    <section class="py-32" id="portfolio">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-24 gap-8">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] theme-text mb-4 block">Our Work</span>
                    <h2 class="section-title text-white leading-none">Creative Archive</h2>
                </div>
                <div class="w-32 h-px bg-white/10 hidden md:block"></div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                @foreach($products as $product)
                <div class="group relative aspect-[4/5] rounded-[3rem] overflow-hidden glass border-white/10">
                    @if(!empty($product['image']))
                        <img src="{{ $setting->getUrl($product['image']) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-slate-900 flex items-center justify-center text-slate-700">
                             <i class="fa-solid fa-image text-5xl"></i>
                        </div>
                    @endif
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="absolute bottom-0 left-0 p-10 w-full translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-tighter">{{ $product['name'] }}</h3>
                        <p class="text-[10px] font-bold theme-text uppercase tracking-widest mb-6">{{ $product['price'] }}</p>
                        <p class="text-slate-400 text-xs mb-8 opacity-0 group-hover:opacity-100 transition-opacity line-clamp-2">
                            {{ $product['description'] }}
                        </p>
                        <a href="{{ $hero->cta_link ?? '#' }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-white group-hover:theme-text transition-colors">
                            View Case Study <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="bg-slate-950 pt-32 pb-16 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-4 gap-20 mb-24">
                <div class="lg:col-span-2 space-y-10">
                    <a href="#" class="text-4xl font-black text-white tracking-tighter uppercase italic">
                        {{ $brandingName }}<span class="theme-text">.</span>
                    </a>
                    <p class="text-slate-400 text-lg font-medium leading-relaxed max-w-md">
                        We are a design-led studio creating iconic digital experiences for forward-thinking brands globally.
                    </p>
                    <div class="flex space-x-6">
                        @if($footer && !empty($footer['whatsapp']))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $footer['whatsapp']) }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-white hover:theme-bg hover:text-white transition-all">
                            <i class="fa-brands fa-whatsapp text-xl"></i>
                        </a>
                        @endif
                        @if($footer && !empty($footer['instagram']))
                        <a href="#" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-white hover:theme-bg hover:text-white transition-all">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="space-y-8">
                    <h4 class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Quick Access</h4>
                    <div class="flex flex-col space-y-4 text-slate-500 font-bold text-sm">
                        <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                        <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                        <a href="#" class="hover:text-white transition-colors">Global Network</a>
                    </div>
                </div>

                <div class="space-y-8">
                    <h4 class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Office</h4>
                    <div class="text-slate-500 font-bold text-sm leading-loose">
                        @if($footer && !empty($footer['address']))
                            <p>{{ $footer['address'] }}</p>
                        @endif
                        @if($footer && !empty($footer['email']))
                            <p class="text-white mt-4">{{ $footer['email'] }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center pt-16 border-t border-white/5 gap-8">
                <p class="text-slate-600 text-[10px] font-black uppercase tracking-[0.2em]">
                    &copy; {{ date('Y') }} {{ $brandingName }}. Engineered by FKStudio
                </p>
                <div class="flex space-x-10 text-[10px] font-black uppercase tracking-[0.2em] text-slate-600">
                    <span>v2.0 Stable Build</span>
                    <a href="#" class="hover:text-white transition-colors">Back to top ↑</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

