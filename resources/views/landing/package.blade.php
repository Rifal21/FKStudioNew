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

    <!-- Theme Switcher Prevention script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .gradient-text {
            @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .blob {
            filter: blur(80px);
            opacity: 0.35;
            z-index: -1;
        }
    </style>
</head>

<body class="antialiased selection:bg-blue-500 selection:text-white bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300" x-data="{ mobileMenu: false, scrolled: true, activeSection: '' }">

    @include('landing.sections.navbar')

    <main class="pt-32 pb-20">
        <section class="relative py-20 overflow-hidden">
            <!-- Background Accents -->
            <div class="absolute top-1/4 -left-20 w-[30rem] h-[30rem] bg-blue-500/10 dark:bg-blue-600/5 rounded-full blob animate-pulse"></div>
            <div class="absolute bottom-1/4 -right-20 w-[30rem] h-[30rem] bg-indigo-500/10 dark:bg-indigo-600/5 rounded-full blob animate-pulse" style="animation-delay: 2s;"></div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-6xl mx-auto">
                    <div class="mb-16" data-aos="fade-right">
                        <!-- Sleek capsule Back Button -->
                        <a href="{{ route('home') }}#packages" class="inline-flex items-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-blue-600 dark:text-blue-400 rounded-full transition-all group font-black border border-slate-200/50 dark:border-white/5 shadow-sm text-xs mb-8 uppercase tracking-widest">
                            <i class="fa-solid fa-arrow-left mr-2 group-hover:-translate-x-1.5 transition-transform"></i>
                            {{ app()->getLocale() == 'id' ? 'Kembali ke Paket' : 'Back to Packages' }}
                        </a>
                        
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <div class="inline-flex items-center space-x-2 bg-blue-500/10 border border-blue-500/20 px-3 py-1 rounded-full text-blue-600 dark:text-blue-450 font-black uppercase tracking-widest text-[9px] mb-6">
                                    <i class="fa-solid fa-gem text-[10px] animate-pulse"></i>
                                    <span>{{ app()->getLocale() == 'id' ? 'Layanan Unggulan' : 'Premium Service' }}</span>
                                </div>
                                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-slate-900 dark:text-white tracking-tighter leading-none">
                                    {{ $package->getTranslation('name') }}
                                </h1>
                            </div>
                            
                            <div class="flex-shrink-0 md:text-right">
                                <div class="inline-flex items-baseline bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 px-8 py-5 rounded-[2rem] shadow-xl">
                                    <span class="text-3xl md:text-5xl font-black text-blue-600 dark:text-blue-400 tracking-tighter">{{ $package->price }}</span>
                                    <span class="text-slate-400 text-xs font-bold uppercase tracking-widest ml-3">/ Project</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <div class="lg:col-span-2 space-y-12" data-aos="fade-up">
                            <!-- Description Card -->
                            <div class="glass p-8 md:p-12 rounded-[3rem] border border-slate-200/60 dark:border-white/5 shadow-md dark:shadow-xl transition-all duration-300">
                                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6 tracking-tight flex items-center">
                                    <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center mr-3 text-sm">
                                        <i class="fa-solid fa-align-left"></i>
                                    </span>
                                    {{ app()->getLocale() == 'id' ? 'Deskripsi Detail' : 'Detailed Description' }}
                                </h3>
                                <div class="text-slate-600 dark:text-slate-300 text-base md:text-lg leading-relaxed space-y-4 font-medium italic border-l-4 border-blue-500/40 pl-6">
                                    {!! nl2br(e($package->getTranslation('description'))) !!}
                                </div>
                            </div>

                            <!-- Interactive Grid Checklist Card -->
                            <div class="glass p-8 md:p-12 rounded-[3rem] border border-slate-200/60 dark:border-white/5 shadow-md dark:shadow-xl transition-all duration-300">
                                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-8 tracking-tight flex items-center">
                                    <span class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center mr-3 text-sm">
                                        <i class="fa-solid fa-cubes"></i>
                                    </span>
                                    {{ app()->getLocale() == 'id' ? 'Fitur & Keuntungan Utama' : 'Key Features & Benefits' }}
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @php
                                        $features = app()->getLocale() == 'id' ? $package->features_id : $package->features_en;
                                    @endphp
                                    @foreach ($features ?? [] as $feature)
                                        <div class="bg-white/50 dark:bg-slate-900/40 p-5 rounded-2xl border border-slate-200/60 dark:border-white/5 shadow-sm hover:shadow-md hover:scale-[1.02] hover:border-blue-500/30 transition-all duration-300 flex items-start gap-4 group">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/20 flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-300">
                                                <i class="fa-solid fa-check text-xs text-blue-600 dark:text-blue-400 group-hover:text-white transition-colors"></i>
                                            </div>
                                            <span class="text-slate-700 dark:text-slate-350 font-semibold text-sm leading-relaxed group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1" data-aos="fade-left">
                            <div class="sticky top-32">
                                <div class="relative group">
                                    <!-- Card Glow Backdrop -->
                                    <div class="absolute -inset-1.5 bg-gradient-to-b from-blue-600/20 dark:from-blue-600/30 to-indigo-600/20 dark:to-indigo-600/30 rounded-[3rem] blur-2xl opacity-60 group-hover:opacity-100 transition-opacity duration-700"></div>
                                    
                                    <div class="relative bg-white dark:bg-slate-950 p-8 md:p-10 rounded-[3rem] border border-blue-200/60 dark:border-blue-800/40 shadow-2xl text-center transition-colors duration-500">
                                        <h4 class="text-slate-900 dark:text-white font-black uppercase tracking-widest text-xs mb-6">{{ app()->getLocale() == 'id' ? 'Siap Memulai?' : 'Ready to Start?' }}</h4>
                                        <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed font-medium">
                                            {{ app()->getLocale() == 'id' ? 'Amankan slot pemesanan Anda sekarang dan mari bangun website impian Anda bersama.' : 'Secure your custom development slot now and let\'s build your dream website together.' }}
                                        </p>
                                        
                                        <!-- Interactive Features Pills (DP & Voucher) -->
                                        <div class="space-y-3 mb-8">
                                            <div class="flex items-center gap-3 p-3 bg-indigo-500/5 dark:bg-indigo-500/10 border border-indigo-500/20 rounded-2xl text-left">
                                                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 flex-shrink-0">
                                                    <i class="fa-solid fa-receipt text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[9px] font-black uppercase tracking-wider text-indigo-500">Down Payment (DP 50%)</div>
                                                    <div class="text-[8px] text-slate-500 dark:text-slate-400 font-medium">Mulai dengan DP 50%, pelunasan setelah selesai</div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center gap-3 p-3 bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-left">
                                                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 flex-shrink-0">
                                                    <i class="fa-solid fa-ticket text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[9px] font-black uppercase tracking-wider text-emerald-500">Dukungan Voucher Diskon</div>
                                                    <div class="text-[8px] text-slate-500 dark:text-slate-400 font-medium font-bold">Gunakan kode voucher di checkout untuk potongan extra</div>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ $package->cta_link ?? route('checkout', $package->id) }}"
                                           {{ $package->cta_link && str_starts_with($package->cta_link, 'http') ? 'target="_blank"' : '' }}
                                           class="relative group/btn block w-full py-6 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-base uppercase tracking-widest transition-all overflow-hidden shadow-xl shadow-blue-600/35 hover:-translate-y-0.5">
                                            <span class="relative z-10">{{ $package->getTranslation('cta_text') }}</span>
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                        </a>

                                        <!-- Elegant Trust Badges -->
                                        <div class="grid grid-cols-3 gap-2 mt-8 pt-6 border-t border-slate-100 dark:border-white/5">
                                            <div class="text-center">
                                                <i class="fa-solid fa-bolt text-sm text-blue-500 mb-1"></i>
                                                <div class="text-[8px] font-black uppercase text-slate-400">Fast Launch</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="fa-solid fa-shield-halved text-sm text-emerald-500 mb-1"></i>
                                                <div class="text-[8px] font-black uppercase text-slate-400">Secure Pay</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="fa-solid fa-headset text-sm text-purple-500 mb-1"></i>
                                                <div class="text-[8px] font-black uppercase text-slate-400">24/7 VIP Support</div>
                                            </div>
                                        </div>
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
