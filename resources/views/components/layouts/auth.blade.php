@props(['title' => null, 'heading' => null, 'subheading' => null])

@php
    $siteName = $site->site_name ?: 'FKStudio';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FFCF0F">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ? "{$title} | {$siteName}" : $siteName }}</title>
    @if ($site->site_favicon)
        <link rel="icon" type="image/png" href="{{ $site->favicon_url }}">
    @endif
    @include('partials.head-assets')
</head>
<body class="min-h-screen">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Brand panel --}}
        <aside class="relative hidden overflow-hidden bg-sun-400 p-12 text-navy lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full border-2 border-navy bg-brand-300"></div>
            <div class="pointer-events-none absolute -bottom-52 -left-24 h-96 w-96 rounded-full border-2 border-navy bg-brand-200"></div>

            <a href="{{ route('home') }}" class="relative inline-flex items-center gap-3">
                @if ($site->site_logo)
                    <img src="{{ $site->logo_url }}" alt="{{ $siteName }}" class="h-12 w-auto rounded-md bg-white p-1.5">
                @else
                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/55 text-xl font-extrabold">FK</span>
                @endif
                <span class="text-xl font-extrabold">{{ $siteName }}</span>
            </a>

            <div class="relative max-w-md">
                <h2 class="text-4xl font-extrabold leading-tight text-navy">
                    {{ app()->getLocale() === 'id' ? 'Wujudkan website impian bisnis Anda.' : 'Build the website your business deserves.' }}
                </h2>
                <ul class="mt-8 space-y-4 text-navy/85">
                    @foreach ([
                        ['fa-bolt', app()->getLocale() === 'id' ? 'Proses cepat dan transparan' : 'Fast and transparent process'],
                        ['fa-shield-heart', app()->getLocale() === 'id' ? 'Pembayaran aman, bisa DP' : 'Secure payments, deposit available'],
                        ['fa-headset', app()->getLocale() === 'id' ? 'Dukungan responsif setiap hari' : 'Responsive support every day'],
                    ] as [$icon, $text])
                        <li class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-white/55"><i class="fa-solid {{ $icon }}"></i></span>
                            <span>{{ $text }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="relative text-small text-navy/75">&copy; {{ date('Y') }} {{ $siteName }}</p>
        </aside>

        {{-- Form panel --}}
        <div class="flex flex-col px-4 py-6 sm:px-8">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 lg:invisible">
                    @if ($site->site_logo)
                        <img src="{{ $site->logo_url }}" alt="{{ $siteName }}" class="h-9 w-auto">
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-md bg-brand-gradient text-small font-extrabold text-navy">FK</span>
                    @endif
                    <span class="font-bold">{{ $siteName }}</span>
                </a>
                <x-ui.theme-toggle />
            </div>

            <div class="flex flex-1 items-center justify-center py-8">
                <div class="w-full max-w-md">
                    @if ($heading)
                        <h1 class="text-3xl font-extrabold">{{ $heading }}</h1>
                    @endif
                    @if ($subheading)
                        <p class="mt-2 text-muted">{{ $subheading }}</p>
                    @endif
                    <div class="{{ $heading ? 'mt-8' : '' }}">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-ui.toaster />
</body>
</html>
