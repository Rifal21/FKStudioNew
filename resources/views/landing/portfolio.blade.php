<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app()->getLocale() == 'id' ? 'Galeri Proyek & Portofolio' : 'Our Work & Portfolio Gallery' }} - {{ $settings->site_name ?: 'FKStudio' }}</title>
    <meta name="description" content="{{ app()->getLocale() == 'id' ? 'Lihat galeri proyek, website custom, dan aplikasi seluler yang telah dibangun dengan standar performa dan UI/UX premium oleh FKStudio.' : 'Explore our collection of custom websites, e-commerce stores, and high-performance mobile apps built with premium UI/UX standards by FKStudio.' }}">
    <meta name="keywords" content="portofolio website, galeri proyek, hasil kerja, web development, {{ $settings->seo_keywords }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ app()->getLocale() == 'id' ? 'Portofolio Proyek' : 'Project Portfolio' }} - {{ $settings->site_name }}">
    <meta property="og:description" content="{{ app()->getLocale() == 'id' ? 'Eksplorasi karya-karya terbaik dari FKStudio.' : 'Explore the finest projects crafted by FKStudio.' }}">
    <meta property="og:image" content="{{ $settings->og_image ? $settings->og_image_url : ($settings->site_logo ? $settings->logo_url : '') }}">

    @if ($settings->site_favicon)
        <link rel="icon" type="image/png" href="{{ $settings->favicon_url }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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

    <!-- Custom Style Utilities -->
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

<body class="antialiased selection:bg-blue-500 selection:text-white bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300" x-data="{ mobileMenu: false, scrolled: true }">

    <!-- Global Navigation -->
    @include('landing.sections.navbar')

    <!-- Main Container -->
    <main class="pt-32 pb-24" x-data="{ activeCategory: 'all' }">
        
        <!-- Background Neon Blobs -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-0">
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-300/40 dark:bg-blue-600/10 rounded-full blob animate-pulse"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-300/35 dark:bg-indigo-700/10 rounded-full blob animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            
            <!-- Hero Heading Block -->
            <div class="text-center max-w-4xl mx-auto mb-20 md:mb-28">
                <div class="inline-flex items-center space-x-3 mb-6" data-aos="fade-up" data-aos-once="true">
                    <span class="w-10 h-[1px] bg-blue-600/30"></span>
                    <span class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                        {{ app()->getLocale() == 'id' ? 'Galeri Kreasi' : 'Creations Gallery' }}
                    </span>
                    <span class="w-10 h-[1px] bg-blue-600/30"></span>
                </div>
                
                <h1 class="text-5xl md:text-7xl lg:text-9xl font-black text-slate-900 dark:text-white mb-8 tracking-tighter leading-none" data-aos="fade-up" data-aos-once="true" data-aos-delay="100">
                    {{ app()->getLocale() == 'id' ? 'Karya' : 'Our' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Kami.' : 'Work.' }}</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-650 dark:text-slate-350 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-once="true" data-aos-delay="200">
                    {{ app()->getLocale() == 'id' ? 'Koleksi produk digital premium dengan standar fungsionalitas dan arsitektur tinggi.' : 'A selection of high-end custom web platforms, interfaces, and apps built for modern performance.' }}
                </p>
            </div>

            <!-- Pluck Unique Localized Categories for Filters -->
            @php
                $categories = $projects->map(function($p) {
                    return [
                        'raw' => $p->category_id,
                        'name' => $p->getTranslation('category')
                    ];
                })->unique('raw')->values();
            @endphp

            <!-- Category Filter Tabs Dock -->
            <div class="flex flex-wrap justify-center gap-3.5 mb-16 md:mb-24" data-aos="fade-up" data-aos-once="true" data-aos-delay="250">
                <!-- "All Works" Tab -->
                <button @click="activeCategory = 'all'" 
                        :class="activeCategory === 'all' ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/20 border-blue-650' : 'bg-white/70 dark:bg-slate-900/40 border-slate-200/50 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900/80'" 
                        class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider border backdrop-blur-md transition-all duration-350">
                    {{ app()->getLocale() == 'id' ? 'Semua Karya' : 'All Works' }}
                </button>

                @foreach($categories as $category)
                    <button @click="activeCategory = '{{ $category['raw'] }}'" 
                            :class="activeCategory === '{{ $category['raw'] }}' ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/20 border-blue-650' : 'bg-white/70 dark:bg-slate-900/40 border-slate-200/50 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900/80'" 
                            class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider border backdrop-blur-md transition-all duration-350">
                        {{ $category['name'] }}
                    </button>
                @endforeach
            </div>

            <!-- Standalone 3-Column Gallery Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                @foreach ($projects as $index => $project)
                    <!-- Project Card Wrapper -->
                    <div x-show="activeCategory === 'all' || activeCategory === '{{ $project->category_id }}'" 
                         x-transition:enter="transition ease-out duration-500 transform"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-6"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="group relative flex flex-col transition-all duration-500"
                         data-aos="fade-up"
                         data-aos-once="true"
                         data-aos-delay="{{ 100 * ($index % 3) }}">
                        
                        <!-- High-Fidelity Ultra-Dark Glass Browser Mockup -->
                        <div class="relative overflow-hidden rounded-[2.2rem] mb-6 aspect-video bg-slate-100 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-md dark:shadow-xl group/card transition-all duration-1000 group-hover:scale-[1.02] group-hover:-translate-y-2.5 group-hover:shadow-[0_30px_70px_rgba(59,130,246,0.08)] group-hover:border-slate-350 dark:group-hover:border-white/10 flex flex-col">
                            
                            <!-- Browser Navigation Header Bezel -->
                            <div class="shrink-0 h-11 bg-slate-100/80 dark:bg-slate-900/80 border-b border-slate-200/60 dark:border-white/5 px-5 flex items-center justify-between z-30 transition-colors">
                                <!-- macOS close/minimize buttons -->
                                <div class="flex space-x-1.5 shrink-0">
                                    <div class="w-2 h-2 rounded-full bg-red-400/80"></div>
                                    <div class="w-2 h-2 rounded-full bg-yellow-400/80"></div>
                                    <div class="w-2 h-2 rounded-full bg-green-400/80"></div>
                                </div>
                                
                                <!-- Fake interactive search/URL input path -->
                                <div class="w-3/5 bg-slate-200/50 dark:bg-white/[0.04] border border-slate-200/30 dark:border-white/5 py-0.5 px-3 rounded-lg text-[8px] font-mono text-slate-500 dark:text-slate-400 text-center truncate flex items-center justify-center space-x-1.5 hover:bg-slate-200 dark:hover:bg-white/[0.06] transition-all">
                                    <i class="fa-solid fa-lock text-[7px] text-green-500"></i>
                                    <span>fkstudio.co/work/{{ Str::slug($project->getTranslation('title')) }}</span>
                                </div>
                                
                                <!-- Browser Actions Spacer -->
                                <div class="flex space-x-1 shrink-0 opacity-40">
                                    <div class="w-1 h-1 rounded-full bg-slate-650"></div>
                                    <div class="w-1 h-1 rounded-full bg-slate-650"></div>
                                </div>
                            </div>

                            <!-- Screen Contents (Image/Iframe Preview) -->
                            <div class="flex-1 w-full relative overflow-hidden bg-slate-200 dark:bg-slate-900 flex items-center justify-center">
                                @if ($project->image)
                                    <img src="{{ $project->media_url }}"
                                        alt="{{ $project->getTranslation('title') }}"
                                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                @elseif($project->url)
                                    <div class="w-full h-full relative bg-slate-100 dark:bg-slate-900 overflow-hidden">
                                        <iframe src="{{ $project->url }}"
                                            title="{{ $project->getTranslation('title') }}"
                                            class="absolute top-0 left-0 w-[300%] h-[300%] origin-top-left border-none pointer-events-none opacity-60 group-hover:opacity-80 transition-opacity duration-1000"
                                            style="transform: scale(0.333333);" loading="lazy" scrolling="no"></iframe>
                                    </div>
                                @else
                                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80"
                                        alt="Default project image"
                                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                @endif

                                <!-- Category Pill floating in mockup -->
                                <div class="absolute top-3 right-3 z-20">
                                    <span class="px-3 py-1 bg-white/80 dark:bg-slate-950/70 border border-slate-200/50 dark:border-white/5 text-slate-800 dark:text-slate-200 text-[8px] font-black uppercase tracking-wider rounded-xl backdrop-blur-md shadow-sm">
                                        {{ $project->getTranslation('category') ?? 'Digital' }}
                                    </span>
                                </div>

                                <!-- Hover Live Launch Overlay Link -->
                                <div class="absolute inset-0 bg-slate-950/45 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-20 flex items-center justify-center backdrop-blur-[4px]">
                                    <a href="{{ $project->url ?? '#' }}" target="_blank" 
                                       class="group/btn w-16 h-16 bg-white/95 dark:bg-slate-900/95 border border-slate-200/50 dark:border-white/10 rounded-full flex items-center justify-center text-slate-800 dark:text-white transform scale-50 group-hover:scale-100 transition-all duration-500 hover:bg-blue-600 hover:text-white hover:scale-105 hover:border-blue-600 shadow-2xl">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-lg transition-transform duration-300 group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card Details Column -->
                        <div class="flex items-start justify-between px-3 mt-4">
                            <div class="space-y-2 max-w-[78%]">
                                <span class="text-[8px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">
                                    {{ $project->getTranslation('category') ?? 'Digital' }}
                                </span>
                                <h4 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white leading-none tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-500">
                                    {{ $project->getTranslation('title') }}
                                </h4>
                                <p class="text-slate-500 dark:text-slate-400 font-medium text-xs leading-relaxed line-clamp-2">
                                    {{ $project->getTranslation('description') }}
                                </p>
                            </div>
                            
                            <!-- Arrow Action Button -->
                            <div class="pt-1 shrink-0">
                                <a href="{{ $project->url ?? '#' }}" target="_blank" 
                                   class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all duration-500 transform group-hover:rotate-45 group-hover:scale-105">
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Global Footer -->
    @include('landing.sections.footer')

    <!-- AOS Script initialization -->
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
