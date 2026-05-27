<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app()->getLocale() == 'id' ? 'Paket Layanan Kami' : 'Our Service Packages' }} - {{ $settings->site_name ?: 'FKStudio' }}</title>
    <meta name="description" content="{{ app()->getLocale() == 'id' ? 'Pilih paket layanan terbaik dari FKStudio yang sesuai dengan kebutuhan bisnis digital Anda.' : 'Choose the best service package from FKStudio that suits your digital business needs.' }}">
    <meta name="keywords" content="paket layanan digital, jasa pembuatan website, daftar harga website, {{ $settings->seo_keywords }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ app()->getLocale() == 'id' ? 'Paket Layanan' : 'Service Packages' }} - {{ $settings->site_name }}">
    <meta property="og:description" content="{{ app()->getLocale() == 'id' ? 'Pilih paket layanan terbaik dari FKStudio.' : 'Choose the best service package from FKStudio.' }}">
    <meta property="og:image" content="{{ $settings->og_image ? $settings->og_image_url : ($settings->site_logo ? $settings->logo_url : '') }}">

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

        .glass-dark {
            background: rgba(15, 23, 42, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }
        .dark .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
        <!-- Floating Decor Blobs -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-300/40 dark:bg-blue-600/10 rounded-full blob animate-pulse"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-300/35 dark:bg-indigo-700/10 rounded-full blob animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <section class="relative py-20 overflow-hidden">
            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center max-w-4xl mx-auto mb-20 md:mb-32">
                    <div class="inline-flex items-center space-x-3 mb-6" data-aos="fade-up">
                        <span class="w-10 h-[1px] bg-blue-600/30"></span>
                        <span class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                            {{ app()->getLocale() == 'id' ? 'Daftar Produk' : 'Product List' }}
                        </span>
                        <span class="w-10 h-[1px] bg-blue-600/30"></span>
                    </div>
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-slate-900 dark:text-white mb-8 tracking-tighter leading-none" data-aos="fade-up" data-aos-delay="100">
                        {{ app()->getLocale() == 'id' ? 'Pilihan' : 'Our' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Terbaik.' : 'Packages.' }}</span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-600 dark:text-slate-350 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-delay="200">
                        Pilih paket yang paling sesuai dengan skala bisnis dan visi masa depan Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                    @foreach ($packages as $index => $package)
                        <div class="group relative h-full" data-aos="fade-up" data-aos-delay="{{ 100 * ($index % 3) }}">
                            @if ($package->is_featured)
                                <div class="absolute -top-5 left-1/2 -translate-x-1/2 z-20">
                                    <span class="bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.3em] px-6 py-2.5 rounded-full shadow-xl shadow-blue-600/40">
                                        {{ app()->getLocale() == 'id' ? 'Paling Populer' : 'Most Popular' }}
                                    </span>
                                </div>
                            @endif

                            <!-- Card Glow Backdrop -->
                            <div class="absolute -inset-1 bg-gradient-to-b {{ $package->is_featured ? 'from-blue-600/15 to-indigo-600/15 shadow-xl' : 'from-slate-200/20 to-transparent dark:from-slate-800/10' }} rounded-[3rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                            
                            <div class="relative h-full bg-white dark:bg-slate-900/40 p-8 md:p-12 rounded-[3rem] border border-slate-200/60 dark:border-white/5 shadow-md dark:shadow-lg hover:shadow-xl flex flex-col transition-all duration-500 group-hover:translate-y-[-10px] group-hover:border-blue-500/30 dark:group-hover:border-blue-500/25 transition-colors duration-500">
                                <div class="mb-10">
                                    <h3 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-slate-200 mb-4 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-500 tracking-tight">
                                        {{ $package->getTranslation('name') }}
                                    </h3>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl md:text-4xl font-black text-blue-600 dark:text-blue-400 tracking-tighter">{{ $package->price }}</span>
                                        <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest">/ Project</span>
                                    </div>
                                </div>

                                <div class="flex-grow">
                                    <p class="text-slate-500 dark:text-slate-400 text-sm md:text-base leading-relaxed mb-10 font-medium">
                                        {{ Str::limit($package->getTranslation('description'), 100) }}
                                    </p>

                                    <ul class="space-y-4 mb-10">
                                        @php
                                            $features = app()->getLocale() == 'id' ? $package->features_id : $package->features_en;
                                            $features = array_slice($features ?? [], 0, 5);
                                        @endphp
                                        @foreach ($features as $feature)
                                            <li class="flex items-start gap-4 text-sm text-slate-600 dark:text-slate-350 group/item">
                                                <div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-blue-55 bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center group-hover/item:bg-blue-600 transition-colors duration-300">
                                                    <i class="fa-solid fa-check text-[8px] text-blue-500 group-hover/item:text-white"></i>
                                                </div>
                                                <span class="group-hover/item:text-slate-900 dark:group-hover/item:text-white transition-colors font-medium">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <a href="{{ route('package.show', $package->slug) }}"
                                    class="relative group/btn block w-full py-5 text-center rounded-2xl font-black text-sm uppercase tracking-widest transition-all overflow-hidden {{ $package->is_featured ? 'bg-blue-600 text-white shadow-2xl shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                                    <span class="relative z-10">{{ app()->getLocale() == 'id' ? 'Lihat Detail' : 'View Details' }}</span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                </a>
                            </div>
                        </div>
                    @endforeach
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
                offset: 50,
            });
        });
    </script>

</body>

</html>
