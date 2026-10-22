@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'landing' => false,
    'bodyClass' => '',
    'noindex' => false,   // private pages: checkout, orders, styleguide
    'type' => 'website',  // 'article' for blog posts
    'publishedAt' => null,
    'modifiedAt' => null,
])

@php
    $siteName = $site->site_name ?: 'FKStudio';
    $pageTitle = $title ? "{$title} | {$siteName}" : ($site->seo_title ?: $siteName);
    $pageDescription = $description ?: ($site->seo_description ?: \App\Support\Seo::FALLBACK_DESCRIPTION);
    $pageImage = $image ?? ($site->og_image ? $site->og_image_url : ($site->site_logo ? $site->logo_url : null));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FFCF0F">

    <title>{{ $pageTitle }}</title>
    @if ($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    @if ($site->seo_keywords)
        <meta name="keywords" content="{{ $site->seo_keywords }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    @endif

    @if ($site->bing_verification)
        <meta name="msvalidate.01" content="{{ $site->bing_verification }}">
    @endif
    @if ($site->google_console_verification)
        <meta name="google-site-verification" content="{{ $site->google_console_verification }}">
    @endif

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'id' ? 'id_ID' : 'en_US' }}">
    @if ($type === 'article' && $publishedAt)
        <meta property="article:published_time" content="{{ $publishedAt }}">
        @if ($modifiedAt)<meta property="article:modified_time" content="{{ $modifiedAt }}">@endif
    @endif
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

    <x-seo.schema :data="['@graph' => [\App\Support\Seo::organization($site), \App\Support\Seo::website($site)]]" />

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
