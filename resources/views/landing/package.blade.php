<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $package->getTranslation('name') }} - {{ $settings->site_name ?: 'FKStudio' }}</title>
    <meta name="description" content="{{ $package->getTranslation('description') }}">
    <meta name="keywords" content="{{ $package->getTranslation('name') }}, {{ $settings->seo_keywords }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $package->getTranslation('name') }} - {{ $settings->site_name }}">
    <meta property="og:description" content="{{ $package->getTranslation('description') }}">
    <meta property="og:image" content="{{ $settings->og_image ? $settings->og_image_url : ($settings->site_logo ? $settings->logo_url : '') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $package->getTranslation('name') }} - {{ $settings->site_name }}">
    <meta property="twitter:description" content="{{ $package->getTranslation('description') }}">
    <meta property="twitter:image" content="{{ $settings->og_image ? $settings->og_image_url : ($settings->site_logo ? $settings->logo_url : '') }}">

    @if ($settings->site_favicon)
        <link rel="icon" type="image/png" href="{{ $settings->favicon_url }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Swiper.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Animation on Scroll (AOS) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }

        .gradient-text {
            @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-600;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .blob {
            filter: blur(40px);
            opacity: 0.15;
            z-index: -1;
        }
    </style>
</head>

<body class="antialiased selection:bg-blue-500 selection:text-white" x-data="{ mobileMenu: false, scrolled: true, activeSection: '' }">

    @include('landing.sections.navbar')

    <main class="pt-32 pb-20">
        <section class="relative py-20 overflow-hidden">
            <!-- Background Accents -->
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600 rounded-full blob animate-pulse"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-700 rounded-full blob animate-pulse" style="animation-delay: 2s;"></div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-4xl mx-auto">
                    <div class="mb-12" data-aos="fade-right">
                        <a href="{{ route('home') }}#packages" class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors mb-8 group">
                            <i class="fa-solid fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
                            <span class="text-xs font-black uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Kembali ke Paket' : 'Back to Packages' }}</span>
                        </a>
                        
                        <div class="inline-flex items-center space-x-3 mb-6">
                            <span class="w-12 h-[1px] bg-blue-500/50"></span>
                            <span class="text-blue-500 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                                {{ app()->getLocale() == 'id' ? 'Detail Paket' : 'Package Detail' }}
                            </span>
                        </div>
                        
                        <h1 class="text-5xl md:text-7xl font-black text-white mb-6 tracking-tighter leading-none">
                            {{ $package->getTranslation('name') }}
                        </h1>
                        <div class="flex items-baseline gap-4 mb-8">
                            <span class="text-4xl md:text-5xl font-black text-blue-500 tracking-tighter">{{ $package->price }}</span>
                            <span class="text-slate-500 text-lg font-bold uppercase tracking-widest">/ Project</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <div class="lg:col-span-2 space-y-12" data-aos="fade-up">
                            <div class="glass p-8 md:p-12 rounded-[2.5rem] border border-white/5">
                                <h3 class="text-2xl font-black text-white mb-6 tracking-tight">{{ app()->getLocale() == 'id' ? 'Deskripsi' : 'Description' }}</h3>
                                <div class="text-slate-400 text-lg leading-relaxed space-y-4">
                                    {!! nl2br(e($package->getTranslation('description'))) !!}
                                </div>
                            </div>

                            <div class="glass p-8 md:p-12 rounded-[2.5rem] border border-white/5">
                                <h3 class="text-2xl font-black text-white mb-8 tracking-tight">{{ app()->getLocale() == 'id' ? 'Apa yang Anda Dapatkan' : 'What You Get' }}</h3>
                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @php
                                        $features = app()->getLocale() == 'id' ? $package->features_id : $package->features_en;
                                    @endphp
                                    @foreach ($features ?? [] as $feature)
                                        <li class="flex items-start gap-4 text-slate-300 group">
                                            <div class="mt-1 flex-shrink-0 w-6 h-6 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300">
                                                <i class="fa-solid fa-check text-[10px] text-blue-400 group-hover:text-white"></i>
                                            </div>
                                            <span class="group-hover:text-white transition-colors font-medium">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="lg:col-span-1" data-aos="fade-left">
                            <div class="sticky top-32">
                                <div class="relative group">
                                    <!-- Card Glow Backdrop -->
                                    <div class="absolute -inset-1 bg-gradient-to-b from-blue-600/30 to-indigo-600/30 rounded-[2.5rem] blur-xl opacity-50 group-hover:opacity-100 transition-opacity duration-700"></div>
                                    
                                    <div class="relative glass p-10 rounded-[2.5rem] border border-blue-500/30 text-center">
                                        <h4 class="text-white font-black uppercase tracking-widest text-xs mb-6">{{ app()->getLocale() == 'id' ? 'Siap Memulai?' : 'Ready to Start?' }}</h4>
                                        <p class="text-slate-400 text-sm mb-10 leading-relaxed">
                                            {{ app()->getLocale() == 'id' ? 'Amankan slot Anda sekarang dan mari kita bangun sesuatu yang luar biasa bersama.' : 'Secure your slot now and let\'s build something extraordinary together.' }}
                                        </p>
                                        
                                        <a href="{{ $package->cta_link ?? route('checkout', $package->id) }}"
                                           {{ $package->cta_link && str_starts_with($package->cta_link, 'http') ? 'target="_blank"' : '' }}
                                           class="relative group/btn block w-full py-6 bg-blue-600 text-white rounded-2xl font-black text-lg uppercase tracking-widest transition-all overflow-hidden shadow-2xl shadow-blue-600/40">
                                            <span class="relative z-10">{{ $package->getTranslation('cta_text') }}</span>
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                        </a>

                                        <p class="mt-8 text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                            <i class="fa-solid fa-shield-halved mr-2 text-blue-500/50"></i>
                                            {{ app()->getLocale() == 'id' ? 'Pembayaran Aman & Terpercaya' : 'Safe & Trusted Payment' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('landing.sections.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: true,
                offset: 100,
            });
        });
    </script>

</body>

</html>
