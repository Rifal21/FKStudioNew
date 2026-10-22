@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'landing' => false,
    'bodyClass' => '',
])

@php
    $siteName = $site->site_name ?: 'FKStudio';
    $pageTitle = $title ? "{$title} | {$siteName}" : ($site->seo_title ?: $siteName);
    $pageDescription = $description ?? $site->seo_description;
    $pageImage = $image ?? ($site->og_image ? $site->og_image_url : ($site->site_logo ? $site->logo_url : null));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#7C3AED">

    <title>{{ $pageTitle }}</title>
    @if ($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    @if ($site->seo_keywords)
        <meta name="keywords" content="{{ $site->seo_keywords }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">

    @if ($site->google_console_verification)
        <meta name="google-site-verification" content="{{ $site->google_console_verification }}">
    @endif

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    @if ($site->site_favicon)
        <link rel="icon" type="image/png" href="{{ $site->favicon_url }}">
    @endif

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => $site->site_logo ? $site->logo_url : null,
            'contactPoint' => array_filter([
                '@type' => 'ContactPoint',
                'telephone' => $site->contact_phone,
                'email' => $site->contact_email,
                'contactType' => 'customer service',
            ]),
            'sameAs' => array_values(array_filter(is_array($site->social_links) ? $site->social_links : [])),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @include('partials.head-assets', ['landing' => $landing])
    {{ $head ?? '' }}
</head>
<body class="min-h-screen {{ $bodyClass }}" x-data="{ mobileMenu: false }" :class="{ 'overflow-hidden': mobileMenu }">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-brand-600 focus:px-4 focus:py-2 focus:text-white">
        {{ app()->getLocale() === 'id' ? 'Lewati ke konten' : 'Skip to content' }}
    </a>

    {{ $navbar ?? '' }}

    <main id="main">
        {{ $slot }}
    </main>

    {{ $footer ?? '' }}

    <x-ui.toaster />
    {{ $scripts ?? '' }}
</body>
</html>
