<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $blog->getTranslation('title') }} - {{ $settings->site_name ?: 'FKStudio' }}</title>
    <meta name="description" content="{{ strip_tags(Str::limit($blog->getTranslation('content'), 150)) }}">
    <meta name="keywords" content="{{ $blog->getTranslation('category') }}, blog teknologi, {{ $settings->seo_keywords }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $blog->getTranslation('title') }}">
    <meta property="og:description" content="{{ strip_tags(Str::limit($blog->getTranslation('content'), 150)) }}">
    <meta property="og:image" content="{{ $blog->image ? $blog->media_url : ($settings->site_logo ? $settings->logo_url : '') }}">

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

        /* Rich Text Editor Styling Overrides */
        .blog-content p {
            margin-bottom: 1.75rem;
            line-height: 1.85;
        }
        .blog-content strong {
            font-weight: 800;
            color: #0f172a;
        }
        .dark .blog-content strong {
            color: #ffffff;
        }
        .blog-content ul {
            list-style-type: disc !important;
            margin-left: 1.5rem !important;
            margin-bottom: 1.75rem !important;
            padding-left: 0.5rem !important;
        }
        .blog-content ol {
            list-style-type: decimal !important;
            margin-left: 1.5rem !important;
            margin-bottom: 1.75rem !important;
            padding-left: 0.5rem !important;
        }
        .blog-content li {
            margin-bottom: 0.5rem !important;
            line-height: 1.75;
        }
        .blog-content a {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 700;
        }
        .dark .blog-content a {
            color: #60a5fa;
        }
        .blog-content blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            color: #475569;
        }
        .dark .blog-content blockquote {
            color: #94a3b8;
        }
        .blog-content img {
            border-radius: 1.75rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
            margin: 2.5rem auto;
            max-width: 100%;
            height: auto;
        }
        .blog-content iframe {
            width: 100% !important;
            aspect-ratio: 16/9;
            border-radius: 1.75rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
            margin: 2.5rem auto;
            border: none !important;
        }
    </style>
</head>

<body class="antialiased selection:bg-blue-500 selection:text-white bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300" x-data="{ mobileMenu: false, scrolled: true }">

    <!-- Global Navigation -->
    @include('landing.sections.navbar')

    <!-- Main Container -->
    <main class="pt-32 pb-24">
        
        <!-- Background Neon Blobs -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-0">
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-300/40 dark:bg-blue-600/10 rounded-full blob animate-pulse"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-300/35 dark:bg-indigo-700/10 rounded-full blob animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-6 max-w-4xl relative z-10">
            
            <!-- Breadcrumbs / Action Dock -->
            <div class="flex items-center justify-between mb-8" data-aos="fade-up" data-aos-once="true">
                <a href="{{ route('blog.index') }}" 
                   class="inline-flex items-center space-x-2 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 font-black uppercase tracking-wider text-[10px] md:text-xs transition-colors">
                    <i class="fa-solid fa-arrow-left text-[9px]"></i>
                    <span>{{ app()->getLocale() == 'id' ? 'Kembali ke Blog' : 'Back to Blog' }}</span>
                </a>

                <span class="px-3 py-1 bg-white/70 dark:bg-slate-900/40 border border-slate-200/50 dark:border-white/5 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-wider rounded-xl backdrop-blur-md shadow-sm">
                    {{ $blog->getTranslation('category') ?? 'Technology' }}
                </span>
            </div>

            <!-- Blog Header Info -->
            <div class="space-y-6 mb-12 text-left" data-aos="fade-up" data-aos-once="true" data-aos-delay="100">
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ $blog->getTranslation('title') }}
                </h1>

                <!-- Author & Date Profile Banner -->
                <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-white/5 pb-8">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center font-bold text-white shadow-md">
                            {{ substr($blog->author_name ?: $blog->author->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900 dark:text-white">{{ $blog->author_name ?: $blog->author->name }}</p>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                    <!-- View Count Badge -->
                    <div class="flex items-center space-x-1.5 px-4 py-2 bg-slate-100/80 dark:bg-slate-900/50 rounded-2xl border border-slate-200/50 dark:border-white/5">
                        <i class="fa-solid fa-eye text-slate-400 dark:text-slate-500 text-xs"></i>
                        <span class="text-xs font-black text-slate-500 dark:text-slate-400">
                            {{ number_format($blog->views) }}
                            {{ app()->getLocale() == 'id' ? 'dibaca' : 'views' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cover Image full-bleed mockup container -->
            <div class="relative overflow-hidden rounded-[2.5rem] mb-12 bg-slate-100 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-xl flex flex-col" 
                 data-aos="fade-up" data-aos-once="true" data-aos-delay="200">
                
                <!-- macOS top Bezel -->
                <div class="shrink-0 h-11 bg-slate-150/80 dark:bg-slate-900/85 border-b border-slate-200/50 dark:border-white/5 px-5 flex items-center justify-between transition-colors">
                    <div class="flex space-x-1.5 shrink-0">
                        <div class="w-2 h-2 rounded-full bg-red-400/80"></div>
                        <div class="w-2 h-2 rounded-full bg-yellow-400/80"></div>
                        <div class="w-2 h-2 rounded-full bg-green-400/80"></div>
                    </div>
                    <div class="w-1/2 bg-slate-200/50 dark:bg-white/[0.04] border border-slate-200/30 dark:border-white/5 py-0.5 px-3 rounded-lg text-[8px] font-mono text-slate-500 dark:text-slate-400 text-center truncate flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-lock text-[7px] text-green-500"></i>
                        <span>fkstudio.co/blog/{{ $blog->slug }}</span>
                    </div>
                    <div class="w-5 shrink-0"></div>
                </div>

                <div class="w-full aspect-[21/9] relative overflow-hidden bg-slate-200 dark:bg-slate-900">
                    @if ($blog->image)
                        <img src="{{ $blog->media_url }}"
                            alt="{{ $blog->getTranslation('title') }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-650/40 to-indigo-650/40 flex items-center justify-center">
                            <i class="fa-solid fa-newspaper text-6xl text-slate-350 dark:text-slate-650"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reading Body Content -->
            <div class="blog-content prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed text-base md:text-lg font-medium text-left" 
                 data-aos="fade-up" data-aos-once="true" data-aos-delay="250">
                {!! $blog->content !!}
            </div>

            <!-- Recommendation related posts section at the bottom -->
            @if(count($relatedBlogs) > 0)
                <div class="mt-24 pt-16 border-t border-slate-200/60 dark:border-white/5">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-10 tracking-tight text-left">
                        {{ app()->getLocale() == 'id' ? 'Artikel Terkait' : 'Related Articles' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($relatedBlogs as $rBlog)
                            <div class="group flex flex-col transition-all duration-500">
                                <div class="relative overflow-hidden rounded-3xl mb-4 aspect-video bg-slate-100 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-md group-hover:scale-[1.02] group-hover:-translate-y-1.5 transition-all duration-500 flex flex-col">
                                    <div class="w-full h-full relative overflow-hidden">
                                        @if ($rBlog->image)
                                            <img src="{{ $rBlog->media_url }}" alt="{{ $rBlog->getTranslation('title') }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-650/30 to-indigo-650/30 flex items-center justify-center">
                                                <i class="fa-solid fa-newspaper text-3xl text-slate-300"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="px-1 text-left">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">
                                        {{ $rBlog->getTranslation('category') ?? 'Technology' }}
                                    </span>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white mt-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                        <a href="{{ route('blog.show', $rBlog->slug) }}">{{ $rBlog->getTranslation('title') }}</a>
                                    </h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

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
