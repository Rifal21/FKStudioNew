<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app()->getLocale() == 'id' ? 'Wawasan & Artikel Terbaru' : 'Insights & Latest Articles' }} - {{ $settings->site_name ?: 'FKStudio' }}</title>
    <meta name="description" content="{{ app()->getLocale() == 'id' ? 'Jelajahi wawasan teknologi terbaru, panduan desain web premium, tips e-commerce, dan tren transformasi digital dari tim ahli FKStudio.' : 'Explore the latest tech insights, premium web design tutorials, e-commerce strategies, and digital transformation trends from the expert team at FKStudio.' }}">
    <meta name="keywords" content="blog teknologi, tips website, panduan bisnis digital, web dev blog, {{ $settings->seo_keywords }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ app()->getLocale() == 'id' ? 'Wawasan & Blog FKStudio' : 'FKStudio Insights & Blog' }}">
    <meta property="og:description" content="{{ app()->getLocale() == 'id' ? 'Wawasan bisnis, tips desain, dan teknologi terbaru dari FKStudio.' : 'Business insights, design tips, and state-of-the-art tech trends from FKStudio.' }}">
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
    <main class="pt-32 pb-24" x-data="{ activeCategory: 'all', searchQuery: '' }">
        
        <!-- Background Neon Blobs -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-0">
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-300/40 dark:bg-blue-600/10 rounded-full blob animate-pulse"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-300/35 dark:bg-indigo-700/10 rounded-full blob animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            
            <!-- Hero Heading Block -->
            <div class="text-center max-w-4xl mx-auto mb-16 md:mb-20">
                <div class="inline-flex items-center space-x-3 mb-6" data-aos="fade-up" data-aos-once="true">
                    <span class="w-10 h-[1px] bg-blue-600/30"></span>
                    <span class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                        {{ app()->getLocale() == 'id' ? 'Wawasan Digital' : 'Digital Insights' }}
                    </span>
                    <span class="w-10 h-[1px] bg-blue-600/30"></span>
                </div>
                
                <h1 class="text-5xl md:text-7xl lg:text-9xl font-black text-slate-900 dark:text-white mb-8 tracking-tighter leading-none" data-aos="fade-up" data-aos-once="true" data-aos-delay="100">
                    {{ app()->getLocale() == 'id' ? 'Artikel' : 'Our' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Kami.' : 'Blog.' }}</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-once="true" data-aos-delay="200">
                    {{ app()->getLocale() == 'id' ? 'Pelajari strategi bisnis digital, tip visual premium, dan pembaruan arsitektur sistem termutakhir.' : 'Discover state-of-the-art visual guides, server ecosystems, and digital product creation hacks.' }}
                </p>

                <!-- Premium Search Glass Input -->
                <div class="max-w-md mx-auto mt-10 md:mt-12" data-aos="fade-up" data-aos-once="true" data-aos-delay="250">
                    <div class="relative glass rounded-full overflow-hidden p-1 shadow-md border flex items-center">
                        <div class="pl-4 text-slate-400 flex items-center">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <input type="text" x-model="searchQuery" 
                            placeholder="{{ app()->getLocale() == 'id' ? 'Cari artikel wawasan...' : 'Search tech articles...' }}"
                            class="w-full bg-transparent border-none focus:ring-0 text-sm font-semibold pl-3 text-slate-800 dark:text-white placeholder-slate-450 dark:placeholder-slate-500 py-3">
                    </div>
                </div>
            </div>

            <!-- Pluck Unique Localized Categories for Filters -->
            @php
                $categories = $blogs->map(function($b) {
                    return [
                        'raw' => $b->category_id,
                        'name' => $b->getTranslation('category')
                    ];
                })->unique('raw')->values();
            @endphp

            <!-- Category Filter Tabs Dock -->
            <div class="flex flex-wrap justify-center gap-3 mb-16 md:mb-20" data-aos="fade-up" data-aos-once="true" data-aos-delay="300">
                <!-- "All Insights" Tab -->
                <button @click="activeCategory = 'all'" 
                        :class="activeCategory === 'all' ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/20 border-blue-650' : 'bg-white/70 dark:bg-slate-900/40 border-slate-200/50 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900/80'" 
                        class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider border backdrop-blur-md transition-all duration-350">
                    {{ app()->getLocale() == 'id' ? 'Semua Artikel' : 'All Insights' }}
                </button>

                @foreach($categories as $category)
                    <button @click="activeCategory = '{{ $category['raw'] }}'" 
                            :class="activeCategory === '{{ $category['raw'] }}' ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/20 border-blue-650' : 'bg-white/70 dark:bg-slate-900/40 border-slate-200/50 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900/80'" 
                            class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider border backdrop-blur-md transition-all duration-350">
                        {{ $category['name'] }}
                    </button>
                @endforeach
            </div>

            <!-- Standalone 3-Column Blog Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                @foreach ($blogs as $index => $blog)
                    @php
                        $title = $blog->getTranslation('title');
                        $contentSnippet = strip_tags($blog->getTranslation('content'));
                        $category = $blog->getTranslation('category');
                    @endphp
                    <!-- Blog Card Wrapper -->
                    <div x-show="(activeCategory === 'all' || activeCategory === '{{ $blog->category_id }}') && 
                                 ('{{ strtolower($title) }}'.includes(searchQuery.toLowerCase()) || 
                                  '{{ strtolower($contentSnippet) }}'.includes(searchQuery.toLowerCase()))" 
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
                                
                                <!-- Fake URL -->
                                <div class="w-3/5 bg-slate-200/50 dark:bg-white/[0.04] border border-slate-200/30 dark:border-white/5 py-0.5 px-3 rounded-lg text-[8px] font-mono text-slate-500 dark:text-slate-400 text-center truncate flex items-center justify-center space-x-1.5 hover:bg-slate-200 dark:hover:bg-white/[0.06] transition-all">
                                    <i class="fa-solid fa-lock text-[7px] text-green-500"></i>
                                    <span>fkstudio.co/blog/{{ $blog->slug }}</span>
                                </div>
                                
                                <!-- Browser Actions Spacer -->
                                <div class="flex space-x-1 shrink-0 opacity-40">
                                    <div class="w-1 h-1 rounded-full bg-slate-650"></div>
                                    <div class="w-1 h-1 rounded-full bg-slate-650"></div>
                                </div>
                            </div>

                            <!-- Screen Contents (Image Preview) -->
                            <div class="flex-1 w-full relative overflow-hidden bg-slate-200 dark:bg-slate-900 flex items-center justify-center">
                                @if ($blog->image)
                                    <img src="{{ $blog->media_url }}"
                                        alt="{{ $title }}"
                                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-650/40 to-indigo-650/40 flex items-center justify-center">
                                        <i class="fa-solid fa-newspaper text-5xl text-slate-350 dark:text-slate-600"></i>
                                    </div>
                                @endif

                                <!-- Category Pill floating in mockup -->
                                <div class="absolute top-3 right-3 z-20">
                                    <span class="px-3 py-1 bg-white/80 dark:bg-slate-950/70 border border-slate-200/50 dark:border-white/5 text-slate-800 dark:text-slate-200 text-[8px] font-black uppercase tracking-wider rounded-xl backdrop-blur-md shadow-sm">
                                        {{ $category ?? 'Technology' }}
                                    </span>
                                </div>

                                <!-- Hover Overlay Link -->
                                <div class="absolute inset-0 bg-slate-950/45 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-20 flex items-center justify-center backdrop-blur-[4px]">
                                    <a href="{{ route('blog.show', $blog->slug) }}" 
                                       class="group/btn w-16 h-16 bg-white/95 dark:bg-slate-900/95 border border-slate-200/50 dark:border-white/10 rounded-full flex items-center justify-center text-slate-800 dark:text-white transform scale-50 group-hover:scale-100 transition-all duration-500 hover:bg-blue-600 hover:text-white hover:scale-105 hover:border-blue-600 shadow-2xl">
                                        <i class="fa-solid fa-book-open text-lg transition-transform duration-300"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card Details Column -->
                        <div class="flex items-start justify-between px-3 mt-4">
                            <div class="space-y-2.5 max-w-[78%]">
                                <div class="flex items-center space-x-2 text-[8px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                                    <span class="text-blue-600 dark:text-blue-400">{{ $category ?? 'Technology' }}</span>
                                    <span>•</span>
                                    <span>{{ $blog->published_at ? $blog->published_at->format('d M Y') : $blog->created_at->format('d M Y') }}</span>
                                    <span>•</span>
                                    <span>By: {{ $blog->author_name ?: $blog->author->name }}</span>
                                </div>
                                <h4 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-500 line-clamp-2">
                                    <a href="{{ route('blog.show', $blog->slug) }}">{{ $title }}</a>
                                </h4>
                                <p class="text-slate-500 dark:text-slate-400 font-medium text-xs leading-relaxed line-clamp-2">
                                    {{ $contentSnippet }}
                                </p>
                            </div>
                            
                            <!-- Arrow Action Button -->
                            <div class="pt-6 shrink-0">
                                <a href="{{ route('blog.show', $blog->slug) }}" 
                                   class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all duration-500 transform group-hover:rotate-45 group-hover:scale-105">
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty search placeholder -->
            <div x-show="document.querySelectorAll('[x-show*=\'searchQuery\']:not([style*=\'none\'])').length === 0" 
                 class="text-center py-24 glass rounded-[3rem] mt-10" style="display:none;">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-900/60 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-350 dark:text-slate-650 text-4xl">
                    <i class="fa-solid fa-search"></i>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-black tracking-tight text-xl">
                    {{ app()->getLocale() == 'id' ? 'Tidak ada artikel yang cocok' : 'No matching insights found' }}
                </p>
                <p class="text-xs text-slate-450 dark:text-slate-550 mt-2 font-medium">
                    {{ app()->getLocale() == 'id' ? 'Silakan periksa ejaan Anda atau masukkan kata kunci lainnya.' : 'Please double check your spelling or search another keyword.' }}
                </p>
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
