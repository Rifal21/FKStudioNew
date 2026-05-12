<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->seo_title ?: ($settings->site_name ?: 'FKStudio') }}</title>
    <meta name="description" content="{{ $settings->seo_description ?? '' }}">
    <meta name="keywords" content="{{ $settings->seo_keywords ?? '' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    @if ($settings->google_console_verification)
        <meta name="google-site-verification" content="{{ $settings->google_console_verification }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $settings->seo_title ?: ($settings->site_name ?: 'FKStudio') }}">
    <meta property="og:description" content="{{ $settings->seo_description ?? '' }}">
    <meta property="og:image" content="{{ $settings->og_image ? $settings->og_image_url : ($settings->site_logo ? $settings->logo_url : '') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $settings->seo_title ?: ($settings->site_name ?: 'FKStudio') }}">
    <meta property="twitter:description" content="{{ $settings->seo_description ?? '' }}">
    <meta property="twitter:image" content="{{ $settings->og_image ? $settings->og_image_url : ($settings->site_logo ? $settings->logo_url : '') }}">

    <!-- JSON-LD Schema Markup -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "{{ $settings->site_name }}",
        "url": "{{ url('/') }}",
        "logo": "{{ $settings->logo_url }}",
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "{{ $settings->contact_phone }}",
            "contactType": "customer service",
            "email": "{{ $settings->contact_email }}"
        },
        "sameAs": [
            @php
                $socials = $settings->social_links ?? [];
                $socialUrls = [];
                if (is_array($socials)) {
                    foreach($socials as $url) {
                        if($url) $socialUrls[] = '"' . $url . '"';
                    }
                }
                echo implode(",\n            ", $socialUrls);
            @endphp
        ]
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "url": "{{ url('/') }}",
        "name": "{{ $settings->site_name }}",
        "description": "{{ $settings->seo_description }}",
        "publisher": {
            "@@type": "Organization",
            "name": "{{ $settings->site_name }}",
            "logo": "{{ $settings->logo_url }}"
        }
    }
    </script>

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

<body class="antialiased selection:bg-blue-500 selection:text-white" x-data="{ mobileMenu: false, scrolled: false, activeSection: 'home' }"
    x-on:scroll.window="scrolled = (window.pageYOffset > 50); 
        let sections = ['home', 'about', 'services', 'portfolio', 'packages'];
        for (let i = sections.length - 1; i >= 0; i--) {
            let el = document.getElementById(sections[i]);
            if (el && window.pageYOffset >= el.offsetTop - 200) {
                activeSection = sections[i];
                break;
            }
        }">

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
