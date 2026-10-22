@php
    $pageTitle = trim(strip_tags((string) ($header ?? 'Dashboard'))) ?: 'Dashboard';
    $isSuper = Auth::user()->isSuperAdmin();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#FFCF0F">
    <title>{{ $pageTitle }} | Admin FKStudio</title>
    @include('partials.head-assets')
    @stack('head')
</head>
<body class="min-h-screen" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" :class="{ 'overflow-hidden': sidebarOpen }">
    <div class="lg:flex">
        @include('layouts.navigation')

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Top bar --}}
            <header class="sticky top-0 z-30 border-b border-line bg-bg/85 backdrop-blur-xl">
                <div class="flex h-16 items-center gap-3 px-4 lg:px-8">
                    <button type="button" class="btn-icon -ml-2 lg:hidden" @click="sidebarOpen = true" aria-controls="admin-sidebar" aria-label="Buka menu"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
                    <h1 class="min-w-0 flex-1 truncate text-lg font-extrabold md:text-xl">{{ $header ?? 'Dashboard' }}</h1>

                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-secondary btn-sm hidden sm:inline-flex"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> Lihat situs</a>
                    <x-ui.theme-toggle />

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                        <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full p-1 transition hover:bg-surface-2" aria-haspopup="menu" :aria-expanded="open" aria-label="Menu akun">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-gradient text-small font-bold uppercase text-navy">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.right role="menu" class="absolute right-0 mt-3 w-60 rounded-lg border border-line bg-surface p-2 shadow-pop">
                            <div class="border-b border-line px-3 pb-3 pt-2"><p class="truncate text-small font-bold">{{ Auth::user()->name }}</p><p class="truncate text-caption text-muted">{{ Auth::user()->email }}</p></div>
                            <div class="py-1 text-small font-medium">
                                <a href="{{ route('profile.edit') }}" role="menuitem" class="flex items-center gap-3 rounded-md px-3 py-2.5 hover:bg-surface-2"><i class="fa-solid fa-user w-4 text-brand-500" aria-hidden="true"></i> Profil saya</a>
                                <a href="{{ route('home') }}" role="menuitem" class="flex items-center gap-3 rounded-md px-3 py-2.5 hover:bg-surface-2"><i class="fa-solid fa-house w-4 text-brand-500" aria-hidden="true"></i> Lihat situs</a>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-line pt-1">
                                @csrf
                                <button type="submit" role="menuitem" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-small font-medium text-danger-700 hover:bg-danger-50 dark:text-red-300 dark:hover:bg-danger-500/15"><i class="fa-solid fa-right-from-bracket w-4" aria-hidden="true"></i> Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 pb-28 lg:px-8 lg:pb-10">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Mobile bottom navigation --}}
    @if ($isSuper)
        <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 pb-[env(safe-area-inset-bottom)] backdrop-blur-xl lg:hidden" aria-label="Navigasi cepat">
            <ul class="mx-auto grid max-w-lg grid-cols-5">
                @foreach ([
                    ['dashboard', 'dashboard', 'fa-gauge-high', 'Beranda', null],
                    ['dashboard.orders.index', 'dashboard.orders.*', 'fa-cart-shopping', 'Pesanan', $navCounts['orders'] ?: null],
                    ['dashboard.invoices.index', 'dashboard.invoices.*', 'fa-file-invoice-dollar', 'Invoice', null],
                    ['dashboard.tickets.index', 'dashboard.tickets.*', 'fa-headset', 'Tiket', $navCounts['tickets'] ?: null],
                ] as [$route, $match, $icon, $label, $badge])
                    @php($on = request()->routeIs($match))
                    <li>
                        <a href="{{ route($route) }}" @class(['relative flex min-h-[56px] flex-col items-center justify-center gap-1 text-caption font-semibold', 'text-brand-text' => $on, 'text-muted' => ! $on])>
                            <span class="relative"><i class="fa-solid {{ $icon }} text-lg" aria-hidden="true"></i>@if ($badge)<span class="absolute -right-3 -top-2 min-w-[1.1rem] rounded-full bg-pink-500 px-1 text-center text-[0.625rem] font-bold leading-4 text-white">{{ $badge }}</span>@endif</span>
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <button type="button" @click="sidebarOpen = true" class="flex min-h-[56px] w-full flex-col items-center justify-center gap-1 text-caption font-semibold text-muted" aria-controls="admin-sidebar">
                        <i class="fa-solid fa-grip text-lg" aria-hidden="true"></i> Menu
                    </button>
                </li>
            </ul>
        </nav>
    @endif

    <x-ui.toaster />
    @stack('scripts')
</body>
</html>
