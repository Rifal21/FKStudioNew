<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->getTranslation('site_name') ?: 'FKStudio' }} -
        {{ $settings->getTranslation('seo_title') ?: 'Modern Creative Agency' }}</title>
    <meta name="description" content="{{ $settings->getTranslation('seo_description') ?? '' }}">

    @if ($settings->site_favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings->site_favicon) }}">
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

    <!-- Custom Gradient Background -->
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

        @keyframes slow-zoom {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.1);
            }
        }

        .animate-slow-zoom {
            animation: slow-zoom 10s linear infinite alternate;
        }

        .animate-bounce-slow {
            animation: bounce 3s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes marquee-simple {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee-simple {
            animation: marquee-simple 30s linear infinite;
        }

        .testimonial-pagination .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.2);
            opacity: 1;
            width: 8px;
            height: 8px;
            transition: all 0.3s ease;
        }

        .testimonial-pagination .swiper-pagination-bullet-active {
            background: #3b82f6;
            width: 24px;
            border-radius: 4px;
        }
    </style>
</head>

<body class="antialiased selection:bg-blue-500 selection:text-white" x-data="{ mobileMenu: false, scrolled: false }"
    @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Background Decoration Blobs -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600 rounded-full blob animate-pulse"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-700 rounded-full blob animate-pulse"
            style="animation-delay: 2s;"></div>
    </div>

    <!-- Preloader (Simple) -->
    <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 800)" x-show="loading"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Navigation -->
    <nav :class="scrolled ? 'glass py-3' : 'bg-transparent py-5'" class="fixed w-full z-40 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center space-x-2 group">
                @if ($settings->site_logo)
                    <img src="{{ asset('storage/' . $settings->site_logo) }}"
                        class="h-10 w-auto group-hover:scale-110 transition-transform">
                @else
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <span class="text-white font-bold text-xl italic leading-none">F</span>
                    </div>
                @endif
                {{-- <span
                    class="text-2xl font-extrabold tracking-tighter">{{ $settings->getTranslation('site_name') ?? 'FKStudio' }}</span> --}}
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#home"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}</a>
                <a href="#about"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}</a>
                <a href="#services"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}</a>
                <a href="#portfolio"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}</a>

                <!-- Language Switcher -->
                <div class="flex items-center space-x-2 border-l border-slate-700 pl-6 ml-6">
                    <a href="{{ url('switch-language/id') }}"
                        class="text-xs transition-colors {{ app()->getLocale() == 'id' ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">ID</a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ url('switch-language/en') }}"
                        class="text-xs transition-colors {{ app()->getLocale() == 'en' ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">EN</a>
                </div>

                {{-- @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-5 py-2 bg-slate-800 text-white rounded-full text-sm font-bold border border-slate-700 hover:border-slate-500 transition-all">Admin</a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full text-sm font-bold shadow-lg shadow-blue-900/40 hover:scale-105 transition-all">{{ app()->getLocale() == 'id' ? 'Masuk' : 'Login' }}</a>
                @endauth --}}
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden text-white">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
                <svg x-show="mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-transition class="md:hidden absolute top-full left-0 w-full glass p-4 mt-2 border-t">
            <div class="flex flex-col space-y-4">
                <a href="#home"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}</a>
                <a href="#about"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}</a>
                <a href="#services"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}</a>
                <a href="#portfolio"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}</a>
                <div class="flex items-center space-x-4 p-2">
                    <a href="{{ url('switch-language/id') }}"
                        class="{{ app()->getLocale() == 'id' ? 'text-blue-400 font-bold' : 'text-slate-500' }}">Bahasa
                        Indonesia</a>
                    <a href="{{ url('switch-language/en') }}"
                        class="{{ app()->getLocale() == 'en' ? 'text-blue-400 font-bold' : 'text-slate-500' }}">English</a>
                </div>
                {{-- <a href="{{ route('login') }}"
                    class="w-full py-3 text-center bg-blue-600 text-white rounded-xl font-bold">Admin Panel</a> --}}
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="home"
        class="relative min-h-[100svh] flex items-center justify-center pt-32 pb-20 lg:py-0 overflow-hidden bg-slate-950">
        <!-- Floating Decorative Blobs -->
        <div
            class="absolute top-20 left-4 w-48 h-48 bg-blue-600/20 rounded-full blur-[80px] animate-pulse z-10 pointer-events-none">
        </div>
        <div
            class="absolute bottom-10 right-4 w-72 h-72 bg-indigo-600/10 rounded-full blur-[100px] animate-bounce-slow z-10 pointer-events-none">
        </div>

        <!-- Slider Background (Full Screen) -->
        <div class="absolute inset-0 z-0 bg-slate-950">
            <div class="swiper heroSwiper w-full h-full">
                <div class="swiper-wrapper">
                    @forelse($heroSlides as $slide)
                        <div class="swiper-slide relative bg-slate-950">
                            <img src="{{ asset('storage/' . $slide->image) }}"
                                class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 animate-slow-zoom">
                        </div>
                    @empty
                        @if ($hero->image)
                            <div class="swiper-slide relative bg-slate-950">
                                <img src="{{ asset('storage/' . $hero->image) }}"
                                    class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 animate-slow-zoom">
                            </div>
                        @else
                            <div class="swiper-slide relative bg-slate-950">
                                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                                    class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 animate-slow-zoom opacity-40">
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>
            <!-- Gradient Overlay for Readability - Darker on small screens -->
            <div
                class="absolute inset-0 bg-slate-950/70 lg:bg-transparent lg:bg-gradient-to-r lg:from-slate-950 lg:via-slate-950/80 lg:to-slate-950/20 z-10 pointer-events-none">
            </div>
        </div>

        <div class="container mx-auto px-6 md:px-8 relative z-20">
            <div class="max-w-5xl" data-aos="fade-up" data-aos-duration="1200">
                <div class="flex items-center space-x-4 mb-6 md:mb-8">
                    <span class="w-10 md:w-16 h-[2px] bg-blue-500"></span>
                    <span
                        class="text-blue-400 font-bold uppercase tracking-[0.3em] md:tracking-[0.4em] text-[10px] md:text-sm shadow-sm">Digital
                        Agency</span>
                </div>

                <h1
                    class="text-4xl sm:text-6xl lg:text-[7rem] font-black mb-6 md:mb-10 leading-[1.1] md:leading-[0.95] tracking-tighter">
                    <span
                        class="text-white drop-shadow-2xl">{{ app()->getLocale() == 'id' ? 'Solusi' : 'Creative' }}</span><br>
                    <span
                        class="gradient-text drop-shadow-xl">{{ app()->getLocale() == 'id' ? 'Digital Kreatif' : 'Digital Solution' }}</span>
                </h1>

                <p
                    class="text-sm sm:text-xl lg:text-2xl text-slate-300 mb-8 md:mb-12 max-w-2xl leading-relaxed font-light">
                    {{ $hero->getTranslation('subtitle') ?? 'We transform your ideas into extraordinary digital experiences that push boundaries.' }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 items-stretch sm:items-center">
                    <a href="{{ $hero->cta_link ?? '#contact' }}"
                        class="group relative px-7 py-4 sm:px-10 sm:py-5 bg-blue-600 text-white rounded-2xl font-black text-base md:text-lg overflow-hidden transition-all hover:bg-blue-700 shadow-[0_20px_50px_rgba(37,99,235,0.4)] w-full sm:w-auto text-center">
                        <span
                            class="relative z-10 tracking-wide">{{ $hero->getTranslation('cta_text') ?? 'Start Project' }}</span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                    </a>

                    <div class="hidden sm:flex items-center space-x-4">
                        <div class="flex -space-x-4">
                            <div
                                class="w-12 h-12 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center font-bold text-xs text-blue-400">
                                JK</div>
                            <div
                                class="w-12 h-12 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center font-bold text-xs text-indigo-400">
                                MA</div>
                            <div
                                class="w-12 h-12 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center font-bold text-xs text-cyan-400">
                                +5</div>
                        </div>
                        <div class="text-sm text-slate-400">
                            <span
                                class="text-white font-bold">{{ app()->getLocale() == 'id' ? 'Klien' : 'Clients' }}</span><br>
                            {{ app()->getLocale() == 'id' ? 'Puas Bergabung' : 'Happy Joining' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20 animate-bounce hidden lg:block">
            <div class="w-6 h-10 rounded-full border-2 border-white/20 flex justify-center pt-2">
                <div class="w-1 h-2 bg-blue-500 rounded-full"></div>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var swiper = new Swiper(".heroSwiper", {
                effect: "fade",
                fadeEffect: {
                    crossFade: true
                },
                speed: 3000,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                loop: true,
                watchSlidesProgress: true,
            });
        });
    </script>

    @if ($clients->isNotEmpty())
        <!-- Clients Section -->
        <section class="py-12 md:py-24 overflow-hidden border-b border-white/5 bg-slate-950/20">
            <div class="container mx-auto px-6 text-center mb-8 md:mb-14">
                <p class="text-blue-500 text-[9px] md:text-sm font-black uppercase tracking-[0.4em] mb-3"
                    data-aos="fade-up">
                    {{ app()->getLocale() == 'id' ? 'Dipercaya Oleh' : 'Trusted By' }}
                </p>
                <div class="w-12 h-0.5 bg-blue-600/30 mx-auto rounded-full" data-aos="fade-up" data-aos-delay="100">
                </div>
            </div>

            <div class="relative flex select-none py-4">
                <div
                    class="absolute left-0 top-0 h-full w-20 md:w-80 bg-gradient-to-r from-slate-950 to-transparent z-10 pointer-events-none">
                </div>
                <div
                    class="absolute right-0 top-0 h-full w-20 md:w-80 bg-gradient-to-l from-slate-950 to-transparent z-10 pointer-events-none">
                </div>
                <div class="flex gap-12 md:gap-40 items-center animate-marquee-simple whitespace-nowrap py-4">
                    @foreach ($clients->concat($clients)->concat($clients) as $client)
                        <div class="flex-shrink-0 px-6 md:px-14 opacity-60 hover:opacity-100 transition-opacity">
                            <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name ?? 'Client' }}"
                                class="h-10 md:h-24 w-auto object-contain brightness-110">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- About Section -->
    <section id="about" class="py-16 md:py-28 bg-slate-900/40 relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-20">
            <div class="flex flex-col md:grid md:grid-cols-2 md:gap-16 md:items-center">
                <div data-aos="fade-up" class="md:order-2 mb-12 md:mb-0 text-center md:text-left">
                    <span
                        class="text-blue-500 font-bold uppercase tracking-widest mb-3 inline-block text-[10px] md:text-sm">{{ app()->getLocale() == 'id' ? 'Kisah Kami' : 'Our Story' }}</span>
                    <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tighter">
                        {{ $about->getTranslation('title') }}</h2>
                    <p class="text-sm md:text-lg text-slate-400 leading-relaxed mb-8">
                        {{ $about->getTranslation('description') }}</p>

                    @php $stats = json_decode($about->stats, true) ?? []; @endphp
                    @if (!empty($stats))
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach ($stats as $stat)
                                <div class="text-center p-4 glass rounded-2xl md:rounded-[2rem]">
                                    <div class="text-2xl md:text-4xl font-black gradient-text mb-1">
                                        {{ $stat['value'] }}</div>
                                    <div
                                        class="text-[9px] md:text-xs font-bold uppercase tracking-tight text-slate-500">
                                        {{ app()->getLocale() == 'id' ? $stat['label_id'] : $stat['label_en'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div data-aos="fade-up" class="md:order-1">
                    <div class="relative group max-w-sm mx-auto md:max-w-none">
                        <div class="absolute -inset-4 bg-blue-600/20 rounded-[3rem] blur-2xl transition duration-1000">
                        </div>
                        <div
                            class="swiper aboutSwiper rounded-[2rem] md:rounded-[3rem] overflow-hidden aspect-[4/5] md:aspect-square shadow-2xl">
                            <div class="swiper-wrapper">
                                @foreach ($aboutSlides as $aslide)
                                    <div class="swiper-slide h-full">
                                        <img src="{{ asset('storage/' . $aslide->image) }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 md:py-32 relative bg-slate-950">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24">
                <span
                    class="text-blue-500 font-bold uppercase tracking-widest mb-3 inline-block text-[10px] md:text-sm"
                    data-aos="fade-up">{{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}</span>
                <h2 class="text-4xl md:text-6xl font-black mb-6 tracking-tighter" data-aos="fade-up">
                    {{ app()->getLocale() == 'id' ? 'Keahlian Kami' : 'Core Expertise' }}</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">
                @foreach ($services as $service)
                    <div class="group relative" data-aos="fade-up">
                        <div
                            class="relative glass p-8 md:p-12 rounded-[2.5rem] border border-white/5 h-full flex flex-col transition-all hover:translate-y-[-10px] hover:border-blue-500/30">
                            <div
                                class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                                <i
                                    class="{{ $service->icon ?? 'fa-solid fa-rocket' }} text-xl md:text-3xl text-white"></i>
                            </div>
                            <h3
                                class="text-xl md:text-2xl font-black mb-4 text-white group-hover:text-blue-400 transition-colors">
                                {{ $service->getTranslation('title') }}</h3>
                            <p class="text-sm md:text-base text-slate-400 leading-relaxed mb-8 flex-grow">
                                {{ $service->getTranslation('description') }}</p>
                            <div
                                class="flex items-center text-blue-500 font-bold uppercase tracking-widest text-[10px]">
                                <span>Learn More</span>
                                <i class="fa-solid fa-arrow-right ml-2"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="py-20 md:py-32 bg-slate-900/20">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 md:mb-20 gap-6"
                data-aos="fade-up">
                <div class="text-center md:text-left">
                    <h2 class="text-4xl md:text-6xl font-black mb-4 tracking-tighter">
                        {{ app()->getLocale() == 'id' ? 'Proyek Kami' : 'Featured Work' }}</h2>
                    <p class="text-slate-400 text-sm md:text-lg max-w-xl mx-auto md:mx-0">
                        {{ app()->getLocale() == 'id' ? 'Menampilkan karya terbaik dalam inovasi digital.' : 'Showcasing our best work in digital innovation.' }}
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-14">
                @foreach ($projects as $project)
                    <div class="group" data-aos="fade-up">
                        <div
                            class="relative overflow-hidden rounded-[2.5rem] md:rounded-[3.5rem] mb-6 aspect-video bg-slate-800 shadow-2xl group/preview">
                            @if ($project->image)
                                <img src="{{ asset('storage/' . $project->image) }}"
                                    class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                            @elseif($project->url)
                                <div class="w-full h-full relative bg-slate-900 overflow-hidden group/iframe">
                                    <div
                                        class="absolute inset-0 z-10 bg-transparent group-hover/iframe:bg-blue-600/5 transition-colors">
                                    </div>
                                    <!-- Full bleed iframe scaling -->
                                    <iframe src="{{ $project->url }}"
                                        class="absolute top-0 left-0 w-[300%] h-[300%] origin-top-left border-none pointer-events-none"
                                        style="transform: scale(0.333333);" loading="lazy" scrolling="no"></iframe>

                                    <!-- Fallback & Overlay -->
                                    <div
                                        class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-slate-950/40 opacity-100 group-hover/preview:opacity-100 transition-opacity">
                                        <div
                                            class="absolute top-6 right-6 px-3 py-1 glass rounded-full text-[8px] text-white font-black uppercase tracking-widest opacity-60 group-hover:opacity-100 transition-opacity">
                                            <i class="fa-solid fa-earth-asia mr-1"></i> Live Web
                                        </div>
                                        <a href="{{ $project->url }}" target="_blank"
                                            class="px-6 py-3 glass rounded-full text-xs text-white font-black uppercase tracking-[0.2em] shadow-2xl scale-90 group-hover:scale-100 transition-transform">
                                            View Project
                                        </a>
                                    </div>
                                </div>
                            @else
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80"
                                    class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                            @endif
                            <div
                                class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            </div>
                        </div>
                        <div class="flex justify-between items-center px-4">
                            <div>
                                <h4
                                    class="text-xl md:text-2xl font-black text-white transition-colors group-hover:text-blue-400">
                                    {{ $project->getTranslation('title') }}</h4>
                                <p class="text-[10px] md:text-xs text-slate-500 uppercase tracking-widest mt-1">
                                    {{ $project->getTranslation('category') ?? 'Digital' }}</p>
                            </div>
                            <a href="{{ $project->url }}"
                                class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                                <i class="fa-solid fa-arrow-right -rotate-45"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 md:py-28 bg-slate-900/40 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-10 md:mb-16 uppercase tracking-widest"
                data-aos="fade-up">
                {{ app()->getLocale() == 'id' ? 'Apa Kata Klien' : 'Client Feedback' }}</h2>

            <div class="swiper testimonialSwiper lg:max-w-6xl mx-auto overflow-hidden">
                <div class="swiper-wrapper py-10 md:py-16">
                    @foreach ($testimonials as $tm)
                        <div class="swiper-slide h-auto">
                            <div class="glass p-6 md:p-8 lg:p-10 rounded-[2rem] md:rounded-[3rem] text-left relative h-full flex flex-col justify-between"
                                data-aos="fade-up">
                                <svg class="absolute top-5 right-5 md:top-10 md:right-10 w-8 h-8 md:w-12 md:h-12 text-blue-500/20"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                </svg>
                                <p
                                    class="text-base md:text-xl italic text-slate-300 mb-5 md:mb-8 leading-relaxed pr-8">
                                    "{{ $tm->getTranslation('content') }}"</p>
                                <div class="flex items-center space-x-3 md:space-x-4 mt-6">
                                    @if ($tm->avatar)
                                        <img src="{{ asset('storage/' . $tm->avatar) }}"
                                            class="w-12 h-12 md:w-16 md:h-16 rounded-2xl border border-blue-500/30 object-cover shadow-2xl shadow-blue-500/20">
                                    @else
                                        <div
                                            class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl border border-blue-500/30 flex items-center justify-center font-bold text-blue-500 text-lg md:text-2xl shadow-xl">
                                            {{ substr($tm->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-bold text-sm md:text-base lg:text-xl text-white">
                                            {{ $tm->name }}
                                        </h4>
                                        <div class="flex items-center space-x-2">
                                            <p
                                                class="text-[8px] md:text-xs text-blue-400 uppercase tracking-widest font-black">
                                                {{ $tm->getTranslation('role') }}</p>
                                            <span class="w-1 h-1 bg-slate-700 rounded-full"></span>
                                            <div class="flex text-yellow-500">
                                                @for ($i = 0; $i < $tm->rating; $i++)
                                                    <svg class="w-2 h-2 md:w-3 md:h-3 fill-current"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Swiper Controls -->
                <div class="flex justify-center items-center mt-10 md:mt-12 space-x-6">
                    <button
                        class="testimonial-prev w-10 h-10 md:w-12 md:h-12 border border-white/5 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition-all outline-none">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <div class="testimonial-pagination flex justify-center !w-auto"></div>
                    <button
                        class="testimonial-next w-10 h-10 md:w-12 md:h-12 border border-white/5 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition-all outline-none">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
    </section>

    <!-- Footer / Contact -->
    <footer id="contact" class="pt-16 md:pt-28 pb-8 md:pb-12 bg-slate-950 border-t border-white/5">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-12 md:mb-20">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-6 md:mb-8">
                        @if ($settings->site_logo)
                            <img src="{{ asset('storage/' . $settings->site_logo) }}" class="h-8 md:h-10 w-auto">
                        @else
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg md:rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-lg md:text-xl italic leading-none">F</span>
                            </div>
                        @endif
                        <span
                            class="text-xl md:text-2xl font-black italic tracking-tighter">{{ $settings->getTranslation('site_name') ?? 'FKStudio' }}</span>
                    </div>
                    <p class="text-slate-400 max-w-sm text-base md:text-lg leading-relaxed mb-6 md:mb-8">
                        {{ $about->getTranslation('description') }}
                    </p>
                    <div class="flex space-x-3 md:space-x-4">
                        <!-- Socials -->
                        @php $socials = json_decode($settings->social_links, true) ?? []; @endphp

                        @if (!empty($socials['instagram']))
                            <a href="{{ $socials['instagram'] }}" target="_blank"
                                class="w-12 h-12 glass rounded-2xl flex items-center justify-center hover:bg-pink-600 hover:scale-110 transition-all"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.256-2.636-5.892-5.892-5.892zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg></a>
                        @endif

                        @if (!empty($socials['twitter']))
                            <a href="{{ $socials['twitter'] }}" target="_blank"
                                class="w-12 h-12 glass rounded-2xl flex items-center justify-center hover:bg-blue-400 hover:scale-110 transition-all"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                </svg></a>
                        @endif

                        @if (!empty($socials['facebook']))
                            <a href="{{ $socials['facebook'] }}" target="_blank"
                                class="w-12 h-12 glass rounded-2xl flex items-center justify-center hover:bg-blue-800 hover:scale-110 transition-all"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.324v-21.35c0-.732-.593-1.325-1.323-1.325z" />
                                </svg></a>
                        @endif

                        @if (!empty($socials['linkedin']))
                            <a href="{{ $socials['linkedin'] }}" target="_blank"
                                class="w-10 h-10 md:w-12 md:h-12 glass rounded-xl md:rounded-2xl flex items-center justify-center hover:bg-blue-700 hover:scale-110 transition-all"><svg
                                    class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.238 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                </svg></a>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-lg md:text-xl mb-6 md:mb-8 uppercase tracking-widest text-blue-500">
                        {{ app()->getLocale() == 'id' ? 'Tautan Cepat' : 'Quick Links' }}</h4>
                    <ul class="space-y-3 md:space-y-4">
                        <li><a href="#home"
                                class="text-slate-400 hover:text-white transition-colors text-sm md:text-base">Home</a>
                        </li>
                        <li><a href="#about"
                                class="text-slate-400 hover:text-white transition-colors text-sm md:text-base">About
                                Us</a>
                        </li>
                        <li><a href="#services"
                                class="text-slate-400 hover:text-white transition-colors text-sm md:text-base">Services</a>
                        </li>
                        <li><a href="#portfolio"
                                class="text-slate-400 hover:text-white transition-colors text-sm md:text-base">Our
                                Work</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg md:text-xl mb-6 md:mb-8 uppercase tracking-widest text-blue-500">
                        {{ app()->getLocale() == 'id' ? 'Hubungi Kami' : 'Contact' }}</h4>
                    <ul class="space-y-3 md:space-y-4 text-slate-400">
                        <li class="flex items-center space-x-3 text-sm md:text-base"><svg
                                class="w-4 h-4 md:w-5 md:h-5 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg> <span>{{ $settings->contact_email }}</span></li>
                        <li class="flex items-center space-x-3 text-sm md:text-base"><svg
                                class="w-4 h-4 md:w-5 md:h-5 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg> <span>{{ $settings->contact_phone }}</span></li>
                        <li class="flex items-start space-x-3 text-sm md:text-base"><svg
                                class="w-4 h-4 md:w-5 md:h-5 text-blue-500 mt-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg> <span class="leading-relaxed">{{ $settings->contact_address }}</span></li>
                    </ul>
                </div>
            </div>

            <div class="pt-6 md:pt-8 border-t border-white/5 text-center text-slate-500 text-xs md:text-sm">
                <p>{{ $settings->getTranslation('footer_text') }}</p>
            </div>
        </div>
    </footer>

    <!-- Floating AI Chatbot -->
    <div x-data="chatbot()" class="fixed bottom-6 right-6 z-[100] flex flex-col items-end">
        <!-- Chat Window -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-10 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-10 scale-95"
            class="mb-4 w-[90vw] sm:w-[400px] h-[500px] sm:h-[600px] glass rounded-[2rem] border border-white/10 shadow-2xl overflow-hidden flex flex-col">

            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                        <i class="fa-solid fa-robot text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">FKBot AI</h4>
                        <p class="text-[10px] text-blue-100 uppercase tracking-widest font-bold">Online Assistant</p>
                    </div>
                </div>
                <button @click="open = false" class="text-white/60 hover:text-white"><i
                        class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4 scrollbar-hide" id="chat-messages">
                <template x-for="(msg, index) in messages" :key="index + '-' + msg.role">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role === 'user' ? 'bg-blue-600 text-white rounded-2xl' :
                            'glass-dark text-slate-200 rounded-2xl'"
                            class="max-w-[85%] p-4 text-sm shadow-sm relative group">
                            <p x-html="formatMessage(msg.text)"></p>
                            <template
                                x-if="msg.role === 'assistant' && (msg.text.includes('WhatsApp') || msg.text.toLowerCase().includes('admin'))">
                                <a :href="'https://wa.me/{{ $settings->contact_phone }}?text=' + encodeURIComponent(
                                    'Halo FKStudio, saya ingin bertanya lebih lanjut setelah mengobrol dengan FKBot.'
                                )"
                                    target="_blank"
                                    class="mt-4 flex items-center justify-center space-x-2 w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold transition-all transform hover:scale-105">
                                    <i class="fa-brands fa-whatsapp text-lg"></i><span>Tanya Admin (WA)</span>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>
                <div x-show="loading" class="flex justify-start">
                    <div class="glass-dark p-4 rounded-2xl flex space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                    </div>
                </div>
            </div>

            <!-- Suggested Questions (Guide) -->
            <div class="px-6 pb-2 flex flex-wrap gap-2 overflow-x-auto scrollbar-hide">
                <template x-for="q in suggestions">
                    <button @click="input = q; send()"
                        class="whitespace-nowrap px-4 py-2 bg-white/5 border border-white/10 rounded-full text-[10px] text-slate-300 hover:bg-blue-600 hover:text-white hover:border-blue-500 transition-all">
                        <span x-text="q"></span>
                    </button>
                </template>
            </div>

            <div class="p-6 border-t border-white/5 bg-slate-900/40">
                <form @submit.prevent="send()" class="relative">
                    <input type="text" x-model="input"
                        placeholder="{{ app()->getLocale() == 'id' ? 'Ketik pesan...' : 'Type a message...' }}"
                        class="w-full bg-slate-950 border border-white/10 rounded-2xl py-4 pl-6 pr-14 text-sm focus:outline-none focus:border-blue-500/50 text-white placeholder-slate-600 mr-0">
                    <button type="submit"
                        class="absolute right-2 top-2 bottom-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all disabled:opacity-50"
                        :disabled="!input.trim() || loading">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <button @click="open = !open; if(open) { $nextTick(() => scrollToBottom()) }"
            class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-[0_20px_50px_rgba(37,99,235,0.4)] flex items-center justify-center text-white text-2xl hover:scale-110 active:scale-95 transition-all group relative">
            <i class="fa-solid fa-robot group-hover:animate-bounce" x-show="!open"></i><i
                class="fa-solid fa-chevron-down" x-show="open"></i>
            <span class="absolute inset-0 rounded-2xl bg-blue-500 animate-ping opacity-20 pointer-events-none"></span>
        </button>
    </div>

    <script>
        function chatbot() {
            return {
                open: false,
                loading: false,
                input: '',
                suggestions: [
                    '{{ app()->getLocale() == 'id' ? 'Layanan apa saja?' : 'What services?' }}',
                    '{{ app()->getLocale() == 'id' ? 'Lihat portofolio' : 'See portfolio' }}',
                    '{{ app()->getLocale() == 'id' ? 'Chat Admin WhatsApp' : 'Chat Admin via WhatsApp' }}'
                ],
                messages: [{
                    role: 'assistant',
                    text: '{{ app()->getLocale() == 'id' ? 'Halo! Ada yang bisa FKBot bantu hari ini?' : 'Hi! How can FKBot help you today?' }}'
                }],
                async send() {
                    const text = this.input.trim();
                    if (!text || this.loading) return;
                    this.messages.push({
                        role: 'user',
                        text: text
                    });
                    this.input = '';
                    this.loading = true;
                    this.$nextTick(() => this.scrollToBottom());
                    try {
                        const response = await fetch('{{ route('chat.send') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                message: text,
                                history: this.messages.slice(0, -
                                    1) // Send all messages except the current user input
                            })
                        });
                        const data = await response.json();
                        if (data.message) {
                            this.messages.push({
                                role: 'assistant',
                                text: data.message
                            });
                        } else {
                            this.messages.push({
                                role: 'assistant',
                                text: 'Error: ' + (data.error || 'Unknown error')
                            });
                        }
                    } catch (e) {
                        this.messages.push({
                            role: 'assistant',
                            text: 'Sorry, my server is offline. Try again later!'
                        });
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },
                scrollToBottom() {
                    const el = document.getElementById('chat-messages');
                    el.scrollTop = el.scrollHeight;
                },
                formatMessage(text) {
                    return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                once: true,
                mirror: false
            });
            new Swiper(".aboutSwiper", {
                spaceBetween: 0,
                centeredSlides: true,
                effect: "fade",
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false
                },
                loop: true,
                allowTouchMove: false
            });
            new Swiper(".testimonialSwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".testimonial-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".testimonial-next",
                    prevEl: ".testimonial-prev"
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30
                    },
                    1024: {
                        slidesPerView: 2,
                        spaceBetween: 40
                    }
                }
            });
        });
    </script>
</body>

</html>
