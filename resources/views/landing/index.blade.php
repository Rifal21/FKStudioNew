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

        @keyframes slow-zoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }

        .animate-slow-zoom {
            animation: slow-zoom 10s linear infinite alternate;
        }

        .animate-bounce-slow {
            animation: bounce 3s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes marquee-simple {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
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

    <!-- Preloader -->
    <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 800)" x-show="loading"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    @include('landing.sections.navbar')

    @include('landing.sections.hero')

    @include('landing.sections.clients')

    @include('landing.sections.about')

    @include('landing.sections.services')

    @include('landing.sections.portfolio')

    @include('landing.sections.packages')

    @include('landing.sections.testimonials')

    @include('landing.sections.footer')

    @include('landing.sections.scripts')

</body>

</html>
