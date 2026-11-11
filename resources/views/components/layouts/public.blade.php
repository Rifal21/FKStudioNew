@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'imageAlt' => null,
    'landing' => false,
    'bodyClass' => '',
    'noindex' => false,   // private pages: checkout, orders, styleguide
    'robots' => null,     // explicit robots directive, e.g. "noindex, follow" for search results
    'canonical' => null,  // when the page's canonical URL is not the bare current URL (filters, pagination)
    'type' => 'website',  // 'article' for blog posts
    'publishedAt' => null,
    'modifiedAt' => null,
])

@php
    $siteName = $site->site_name ?: \App\Support\Seo::BRAND;
    $pageTitle = $title ? "{$title} | {$siteName}" : ($site->seo_title ?: \App\Support\Seo::FALLBACK_TITLE);
    $pageDescription = $description ?: ($site->seo_description ?: \App\Support\Seo::FALLBACK_DESCRIPTION);
    $pageImage = $image ?? ($site->og_image ? $site->og_image_url : ($site->site_logo ? $site->logo_url : null));
    $pageImageAlt = $imageAlt ?: ($title ?: $siteName);
    $imageMeta = \App\Support\Seo::imageMeta($pageImage);
    // Share cards already show the site name next to the title, so the "| Brand" suffix only gets truncated.
    $shareTitle = $title ?: $pageTitle;
    $canonicalUrl = $canonical ?: url()->current();
    $robotsContent = $robots ?: ($noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1');
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
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="robots" content="{{ $robotsContent }}">

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
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $shareTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    @if ($pageImage)
        <meta property="og:image" content="{{ $pageImage }}">
        <meta property="og:image:secure_url" content="{{ $pageImage }}">
        @if ($imageMeta['type'] ?? null)<meta property="og:image:type" content="{{ $imageMeta['type'] }}">@endif
        @if ($imageMeta['width'] ?? null)
            <meta property="og:image:width" content="{{ $imageMeta['width'] }}">
            <meta property="og:image:height" content="{{ $imageMeta['height'] }}">
        @endif
        <meta property="og:image:alt" content="{{ $pageImageAlt }}">
    @endif
    <meta name="twitter:card" content="{{ $pageImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $shareTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    @if ($pageImage)
        <meta name="twitter:image" content="{{ $pageImage }}">
        <meta name="twitter:image:alt" content="{{ $pageImageAlt }}">
    @endif

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
