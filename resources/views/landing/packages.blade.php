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

        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/5 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/5 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center max-w-4xl mx-auto mb-20 md:mb-32">
                    <div class="inline-flex items-center space-x-3 mb-6" data-aos="fade-up">
                        <span class="w-10 h-[1px] bg-blue-500/50"></span>
                        <span class="text-blue-500 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                            {{ app()->getLocale() == 'id' ? 'Daftar Produk' : 'Product List' }}
                        </span>
                        <span class="w-10 h-[1px] bg-blue-500/50"></span>
                    </div>
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-white mb-8 tracking-tighter leading-none" data-aos="fade-up" data-aos-delay="100">
                        {{ app()->getLocale() == 'id' ? 'Pilihan' : 'Our' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Terbaik.' : 'Packages.' }}</span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-delay="200">
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
                            <div class="absolute -inset-1 bg-gradient-to-b from-blue-600/20 to-indigo-600/20 rounded-[3rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                            
                            <div class="relative h-full glass p-8 md:p-12 rounded-[3rem] border border-white/5 flex flex-col transition-all duration-500 group-hover:translate-y-[-10px] group-hover:border-blue-500/30">
                                <div class="mb-10">
                                    <h3 class="text-2xl md:text-3xl font-black text-white mb-4 group-hover:text-blue-400 transition-colors duration-500 tracking-tight">
                                        {{ $package->getTranslation('name') }}
                                    </h3>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl md:text-4xl font-black text-blue-500 tracking-tighter">{{ $package->price }}</span>
                                        <span class="text-slate-500 text-[10px] font-black uppercase tracking-widest">/ Project</span>
                                    </div>
                                </div>

                                <div class="flex-grow">
                                    <p class="text-slate-400 text-sm md:text-base leading-relaxed mb-10 font-medium">
                                        {{ Str::limit($package->getTranslation('description'), 100) }}
                                    </p>

                                    <ul class="space-y-4 mb-10">
                                        @php
                                            $features = app()->getLocale() == 'id' ? $package->features_id : $package->features_en;
                                            $features = array_slice($features ?? [], 0, 5);
                                        @endphp
                                        @foreach ($features as $feature)
                                            <li class="flex items-start gap-4 text-sm text-slate-300 group/item">
                                                <div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center group-hover/item:bg-blue-500 transition-colors duration-300">
                                                    <i class="fa-solid fa-check text-[8px] text-blue-400 group-hover/item:text-white"></i>
                                                </div>
                                                <span class="group-hover/item:text-white transition-colors font-medium">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <a href="{{ route('package.show', $package->slug) }}"
                                    class="relative group/btn block w-full py-5 text-center rounded-2xl font-black text-sm uppercase tracking-widest transition-all overflow-hidden {{ $package->is_featured ? 'bg-blue-600 text-white shadow-2xl shadow-blue-600/30' : 'bg-white/5 text-white hover:bg-white/10 border border-white/10' }}">
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
