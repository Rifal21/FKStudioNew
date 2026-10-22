@php
    $id = app()->getLocale() === 'id';
    $isHome = request()->routeIs('home');
    $links = [
        ['label' => $id ? 'Beranda' : 'Home', 'href' => route('home') . '#home', 'section' => 'home', 'active' => false],
        ['label' => $id ? 'Tentang' : 'About', 'href' => route('home') . '#about', 'section' => 'about', 'active' => false],
        ['label' => $id ? 'Layanan' : 'Services', 'href' => route('home') . '#services', 'section' => 'services', 'active' => false],
        ['label' => $id ? 'Proyek' : 'Projects', 'href' => route('portfolio.index'), 'section' => 'portfolio', 'active' => request()->routeIs('portfolio.*')],
        ['label' => $id ? 'Produk' : 'Products', 'href' => route('package.index'), 'section' => 'packages', 'active' => request()->routeIs('package.*')],
        ['label' => 'Blog', 'href' => route('blog.index'), 'section' => 'blog', 'active' => request()->routeIs('blog.*')],
    ];
@endphp

<header
    x-data="{
        scrolled: false,
        section: 'home',
        sections: ['home', 'about', 'services', 'portfolio', 'packages'],
        onScroll() {
            this.scrolled = window.scrollY > 24;
            @if ($isHome)
            for (let i = this.sections.length - 1; i >= 0; i--) {
                const el = document.getElementById(this.sections[i]);
                if (el && window.scrollY >= el.offsetTop - 160) { this.section = this.sections[i]; return; }
            }
            this.section = 'home';
            @endif
        },
    }"
    x-init="onScroll()"
    @scroll.window.passive="onScroll()"
    @keydown.escape.window="mobileMenu = false"
    class="fixed inset-x-0 top-0 z-50 px-3 transition-all duration-300 sm:px-4"
>
    <div
        class="mx-auto flex items-center justify-between gap-4 transition-all duration-300"
        :class="(scrolled || mobileMenu)
            ? 'mt-3 max-w-6xl rounded-full border border-line bg-surface/85 px-4 py-2 shadow-soft backdrop-blur-xl sm:px-5'
            : 'mt-0 max-w-page px-1 py-4 sm:px-3'"
    >
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $site->site_name ?: 'FKStudio' }}">
            @if ($site->site_logo)
                <img src="{{ $site->logo_url }}" alt="{{ $site->site_name }}" class="h-9 w-auto sm:h-10" width="120" height="40">
            @else
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-gradient text-small font-extrabold text-white shadow-glow">FK</span>
                <span class="hidden text-lg font-extrabold sm:inline">{{ $site->site_name ?: 'FKStudio' }}</span>
            @endif
        </a>

        {{-- Desktop menu --}}
        <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ $id ? 'Menu utama' : 'Main menu' }}">
            @foreach ($links as $link)
                <a
                    href="{{ $link['href'] }}"
                    class="rounded-full px-3.5 py-2 text-small font-semibold transition"
                    :class="({{ $link['active'] ? 'true' : 'false' }} || ({{ $isHome ? 'true' : 'false' }} && section === '{{ $link['section'] }}'))
                        ? 'bg-brand-soft text-brand-text'
                        : 'text-muted hover:bg-surface-2 hover:text-ink'"
                >{{ $link['label'] }}</a>
            @endforeach
        </nav>

        {{-- Desktop actions --}}
        <div class="hidden items-center gap-2 lg:flex">
            <div class="flex items-center rounded-full border border-line bg-surface-2 p-0.5 text-caption font-bold" role="group" aria-label="{{ $id ? 'Bahasa' : 'Language' }}">
                @foreach (['id' => 'ID', 'en' => 'EN'] as $code => $label)
                    <a href="{{ url('switch-language/' . $code) }}"
                        @class(['rounded-full px-2.5 py-1 transition', 'bg-brand-600 text-white' => app()->getLocale() === $code, 'text-muted hover:text-ink' => app()->getLocale() !== $code])
                        @if (app()->getLocale() === $code) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </div>

            <x-ui.theme-toggle class="rounded-full" />

            @auth
                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                    <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full p-1 pr-2.5 transition hover:bg-surface-2" aria-haspopup="menu" :aria-expanded="open">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-gradient text-small font-bold uppercase text-white">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        <i class="fa-solid fa-chevron-down text-caption text-muted transition" :class="open && 'rotate-180'" aria-hidden="true"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right role="menu"
                        class="absolute right-0 mt-3 w-64 rounded-lg border border-line bg-surface p-2 shadow-pop">
                        <div class="border-b border-line px-3 pb-3 pt-2">
                            <p class="text-caption text-muted">{{ $id ? 'Masuk sebagai' : 'Signed in as' }}</p>
                            <p class="truncate text-small font-bold">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="py-1 text-small font-medium">
                            @if (auth()->user()->isSuperAdmin())
                                <a href="{{ route('dashboard') }}" role="menuitem" class="flex items-center gap-3 rounded-md px-3 py-2.5 hover:bg-surface-2"><i class="fa-solid fa-gauge-high w-4 text-brand-500" aria-hidden="true"></i>Dashboard Admin</a>
                            @endif
                            <a href="{{ route('user.orders') }}" role="menuitem" class="flex items-center gap-3 rounded-md px-3 py-2.5 hover:bg-surface-2"><i class="fa-solid fa-box w-4 text-brand-500" aria-hidden="true"></i>{{ $id ? 'Pesanan Saya' : 'My Orders' }}</a>
                            @if ($hasWebsite)
                                <a href="{{ route('user.websites') }}" role="menuitem" class="flex items-center gap-3 rounded-md px-3 py-2.5 hover:bg-surface-2"><i class="fa-solid fa-globe w-4 text-mint-500" aria-hidden="true"></i>{{ $id ? 'Website Saya' : 'My Website' }}</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" role="menuitem" class="flex items-center gap-3 rounded-md px-3 py-2.5 hover:bg-surface-2"><i class="fa-solid fa-user w-4 text-brand-500" aria-hidden="true"></i>{{ $id ? 'Profil Saya' : 'My Profile' }}</a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-line pt-1">
                            @csrf
                            <button type="submit" role="menuitem" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-small font-medium text-danger-700 hover:bg-danger-50 dark:text-red-300 dark:hover:bg-danger-500/15">
                                <i class="fa-solid fa-right-from-bracket w-4" aria-hidden="true"></i>{{ $id ? 'Keluar' : 'Log out' }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-ghost btn-sm">{{ $id ? 'Masuk' : 'Log in' }}</a>
                <a href="{{ route('register') }}" class="btn-primary btn-sm rounded-full">{{ $id ? 'Daftar' : 'Sign up' }}</a>
            @endauth
        </div>

        {{-- Mobile controls --}}
        <div class="flex items-center gap-2 lg:hidden">
            <x-ui.theme-toggle class="h-10 w-10 rounded-full" />
            <button type="button" @click="mobileMenu = !mobileMenu"
                class="flex h-10 w-10 items-center justify-center rounded-full border border-line bg-surface text-ink"
                :aria-expanded="mobileMenu" aria-controls="mobile-menu" aria-label="{{ $id ? 'Buka menu' : 'Open menu' }}">
                <i class="fa-solid" :class="mobileMenu ? 'fa-xmark' : 'fa-bars'" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div
        id="mobile-menu"
        x-show="mobileMenu" x-cloak
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-2 opacity-0"
        class="mx-auto mt-2 max-h-[calc(100svh-6rem)] max-w-6xl overflow-y-auto rounded-xl border border-line bg-surface p-4 shadow-pop lg:hidden"
    >
        <nav class="flex flex-col gap-1" aria-label="{{ $id ? 'Menu seluler' : 'Mobile menu' }}">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" @click="mobileMenu = false"
                    class="flex min-h-[48px] items-center justify-between rounded-md px-4 text-body font-semibold transition"
                    :class="({{ $link['active'] ? 'true' : 'false' }} || ({{ $isHome ? 'true' : 'false' }} && section === '{{ $link['section'] }}')) ? 'bg-brand-soft text-brand-text' : 'text-ink hover:bg-surface-2'">
                    {{ $link['label'] }}
                    <i class="fa-solid fa-chevron-right text-caption text-muted" aria-hidden="true"></i>
                </a>
            @endforeach
        </nav>

        <div class="my-4 flex items-center justify-between rounded-md bg-surface-2 px-4 py-3">
            <span class="text-small font-semibold">{{ $id ? 'Bahasa' : 'Language' }}</span>
            <div class="flex items-center gap-1 text-small font-bold">
                @foreach (['id' => 'ID', 'en' => 'EN'] as $code => $label)
                    <a href="{{ url('switch-language/' . $code) }}"
                        @class(['rounded-full px-3 py-1.5', 'bg-brand-600 text-white' => app()->getLocale() === $code, 'text-muted' => app()->getLocale() !== $code])>{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @auth
            <div class="space-y-1 rounded-md border border-line p-2">
                <div class="flex items-center gap-3 px-2 py-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-gradient font-bold uppercase text-white">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <div class="min-w-0">
                        <p class="text-caption text-muted">{{ $id ? 'Masuk sebagai' : 'Signed in as' }}</p>
                        <p class="truncate text-small font-bold">{{ auth()->user()->name }}</p>
                    </div>
                </div>
                @if (auth()->user()->isSuperAdmin())
                    <a href="{{ route('dashboard') }}" class="flex min-h-[44px] items-center gap-3 rounded-md px-3 text-small font-semibold hover:bg-surface-2"><i class="fa-solid fa-gauge-high w-4 text-brand-500" aria-hidden="true"></i>Dashboard Admin</a>
                @endif
                <a href="{{ route('user.orders') }}" class="flex min-h-[44px] items-center gap-3 rounded-md px-3 text-small font-semibold hover:bg-surface-2"><i class="fa-solid fa-box w-4 text-brand-500" aria-hidden="true"></i>{{ $id ? 'Pesanan Saya' : 'My Orders' }}</a>
                @if ($hasWebsite)
                    <a href="{{ route('user.websites') }}" class="flex min-h-[44px] items-center gap-3 rounded-md px-3 text-small font-semibold hover:bg-surface-2"><i class="fa-solid fa-globe w-4 text-mint-500" aria-hidden="true"></i>{{ $id ? 'Website Saya' : 'My Website' }}</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="flex min-h-[44px] items-center gap-3 rounded-md px-3 text-small font-semibold hover:bg-surface-2"><i class="fa-solid fa-user w-4 text-brand-500" aria-hidden="true"></i>{{ $id ? 'Profil Saya' : 'My Profile' }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex min-h-[44px] w-full items-center gap-3 rounded-md px-3 text-left text-small font-semibold text-danger-700 hover:bg-danger-50 dark:text-red-300 dark:hover:bg-danger-500/15"><i class="fa-solid fa-right-from-bracket w-4" aria-hidden="true"></i>{{ $id ? 'Keluar' : 'Log out' }}</button>
                </form>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('login') }}" class="btn-secondary">{{ $id ? 'Masuk' : 'Log in' }}</a>
                <a href="{{ route('register') }}" class="btn-primary">{{ $id ? 'Daftar' : 'Sign up' }}</a>
            </div>
        @endauth
    </div>
</header>
