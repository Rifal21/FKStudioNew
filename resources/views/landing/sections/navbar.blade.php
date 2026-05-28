    <nav :class="(scrolled || mobileMenu) ? 'top-4 w-[calc(100%-2rem)] max-w-6xl bg-white/80 dark:bg-slate-950/80 backdrop-blur-xl border border-slate-200/40 dark:border-white/5 shadow-xl rounded-[2rem] lg:rounded-full py-3.5 px-6' : 'top-0 w-full bg-transparent py-8 px-6 sm:px-8 lg:px-12'"
        class="fixed left-1/2 -translate-x-1/2 z-40 transition-all duration-500 ease-out"
        x-data="{ 
            mobileMenu: false,
            isDark: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggle() {
                this.isDark = !this.isDark;
                if (this.isDark) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            }
        }">
        <div class="w-full flex justify-between items-center mx-auto transition-all duration-500" :class="(scrolled || mobileMenu) ? 'max-w-none' : 'max-w-7xl'">
            <!-- Logo -->
            <div class="flex items-center space-x-2 group">
                @if ($settings->site_logo)
                    <img src="{{ $settings->logo_url }}"
                        class="h-12 md:h-16 w-auto group-hover:scale-105 transition-transform duration-300">
                @else
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform duration-300">
                        <span class="text-white font-black text-xl md:text-2xl italic leading-none">F</span>
                    </div>
                @endif
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-2 xl:space-x-3">
                @php
                    $isHome = request()->routeIs('home');
                    $isPackagePage = request()->routeIs('package.*');
                @endphp

                <a href="{{ route('home') }}#home"
                    :class="(activeSection === 'home' && {{ $isHome ? 'true' : 'false' }}) ? 'text-blue-600 dark:text-blue-400 font-extrabold bg-blue-50/70 dark:bg-blue-950/40' : 'text-slate-600 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100/80 dark:hover:bg-white/5'"
                    class="text-[11px] font-black transition-all duration-300 uppercase tracking-[0.18em] px-4 py-2.5 rounded-full">
                    {{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}
                </a>
                <a href="{{ route('home') }}#about"
                    :class="(activeSection === 'about' && {{ $isHome ? 'true' : 'false' }}) ? 'text-blue-600 dark:text-blue-400 font-extrabold bg-blue-50/70 dark:bg-blue-950/40' : 'text-slate-600 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100/80 dark:hover:bg-white/5'"
                    class="text-[11px] font-black transition-all duration-300 uppercase tracking-[0.18em] px-4 py-2.5 rounded-full">
                    {{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}
                </a>
                <a href="{{ route('home') }}#services"
                    :class="(activeSection === 'services' && {{ $isHome ? 'true' : 'false' }}) ? 'text-blue-600 dark:text-blue-400 font-extrabold bg-blue-50/70 dark:bg-blue-950/40' : 'text-slate-600 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100/80 dark:hover:bg-white/5'"
                    class="text-[11px] font-black transition-all duration-300 uppercase tracking-[0.18em] px-4 py-2.5 rounded-full">
                    {{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}
                </a>
                <a href="{{ route('portfolio.index') }}"
                    :class="({{ request()->routeIs('portfolio.index') ? 'true' : 'false' }} || (activeSection === 'portfolio' && {{ $isHome ? 'true' : 'false' }})) ? 'text-blue-600 dark:text-blue-400 font-extrabold bg-blue-50/70 dark:bg-blue-950/40' : 'text-slate-600 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100/80 dark:hover:bg-white/5'"
                    class="text-[11px] font-black transition-all duration-300 uppercase tracking-[0.18em] px-4 py-2.5 rounded-full">
                    {{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}
                </a>
                <a href="{{ route('package.index') }}"
                    :class="({{ $isPackagePage ? 'true' : 'false' }} || (activeSection === 'packages' && {{ $isHome ? 'true' : 'false' }})) ? 'text-blue-600 dark:text-blue-400 font-extrabold bg-blue-50/70 dark:bg-blue-950/40' : 'text-slate-600 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100/80 dark:hover:bg-white/5'"
                    class="text-[11px] font-black transition-all duration-300 uppercase tracking-[0.18em] px-4 py-2.5 rounded-full">
                    {{ app()->getLocale() == 'id' ? 'Produk' : 'Products' }}
                </a>
                <a href="{{ route('blog.index') }}"
                    :class="(request()->routeIs('blog.*') || (activeSection === 'blog' && {{ $isHome ? 'true' : 'false' }})) ? 'text-blue-600 dark:text-blue-400 font-extrabold bg-blue-50/70 dark:bg-blue-950/40' : 'text-slate-600 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100/80 dark:hover:bg-white/5'"
                    class="text-[11px] font-black transition-all duration-300 uppercase tracking-[0.18em] px-4 py-2.5 rounded-full">
                    Blog
                </a>
                
                <!-- Divider -->
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-800 mx-2"></div>

                <!-- Language Switcher -->
                <div class="flex items-center space-x-1 bg-slate-100 dark:bg-slate-900/60 rounded-full p-1 border border-slate-200/50 dark:border-white/5">
                    <a href="{{ url('switch-language/id') }}"
                        class="px-2.5 py-1 rounded-full text-[10px] font-black transition-all {{ app()->getLocale() == 'id' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                        ID
                    </a>
                    <a href="{{ url('switch-language/en') }}"
                        class="px-2.5 py-1 rounded-full text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                        EN
                    </a>
                </div>

                <!-- Dark Mode Toggle Button -->
                <button @click="toggle()" class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shadow-sm focus:outline-none group">
                    <span x-show="isDark"><i class="fa-solid fa-sun text-xs group-hover:rotate-[360deg] transition-transform duration-700"></i></span>
                    <span x-show="!isDark"><i class="fa-solid fa-moon text-xs group-hover:rotate-[360deg] transition-transform duration-700"></i></span>
                </button>

                <!-- Auth Links -->
                <div class="flex items-center space-x-3 ml-2">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-blue-600 text-white text-[10px] md:text-xs font-black uppercase tracking-widest rounded-full hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/35 hover:-translate-y-0.5 duration-300 group flex items-center">
                                <span>Dashboard</span>
                                <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @else
                            <a href="{{ route('user.orders') }}" class="px-6 py-3 bg-blue-600 text-white text-[10px] md:text-xs font-black uppercase tracking-widest rounded-full hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/35 hover:-translate-y-0.5 duration-300 group flex items-center">
                                <span>Pesanan Saya</span>
                                <i class="fa-solid fa-box ml-2 group-hover:scale-110 transition-transform"></i>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 text-[11px] font-black uppercase tracking-[0.15em] text-slate-600 dark:text-slate-350 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-full hover:bg-slate-100/50 dark:hover:bg-white/5">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full hover:shadow-[0_8px_30px_rgba(37,99,235,0.45)] hover:-translate-y-0.5 transition-all duration-300 group flex items-center">
                            <span>Join Us</span>
                            <i class="fa-solid fa-user-plus ml-2 opacity-70 group-hover:scale-110 transition-transform"></i>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-slate-800 dark:text-slate-200 focus:outline-none">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
                <svg x-show="mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak x-transition 
            :class="(scrolled || mobileMenu) ? 'top-[calc(100%+0.5rem)] left-0 w-full bg-white/95 dark:bg-slate-950/95 border border-slate-200/40 dark:border-white/5 rounded-3xl shadow-2xl' : 'top-full left-0 w-full bg-white/95 dark:bg-slate-950/95 border-t border-slate-200/80 dark:border-slate-800 rounded-b-2xl shadow-xl'"
            class="lg:hidden absolute backdrop-blur-2xl p-6 mt-2 transition-all duration-500 z-30">
            <div class="flex flex-col space-y-2">
                <a href="{{ route('home') }}#home" @click="mobileMenu = false"
                    :class="(activeSection === 'home' && {{ $isHome ? 'true' : 'false' }}) ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900/50 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}</a>
                <a href="{{ route('home') }}#about" @click="mobileMenu = false"
                    :class="(activeSection === 'about' && {{ $isHome ? 'true' : 'false' }}) ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900/50 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}</a>
                <a href="{{ route('home') }}#services" @click="mobileMenu = false"
                    :class="(activeSection === 'services' && {{ $isHome ? 'true' : 'false' }}) ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900/50 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}</a>
                <a href="{{ route('portfolio.index') }}" @click="mobileMenu = false"
                    :class="({{ request()->routeIs('portfolio.index') ? 'true' : 'false' }} || (activeSection === 'portfolio' && {{ $isHome ? 'true' : 'false' }})) ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-600 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-900/50 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}</a>
                <a href="{{ route('package.index') }}" @click="mobileMenu = false"
                    :class="({{ $isPackagePage ? 'true' : 'false' }} || (activeSection === 'packages' && {{ $isHome ? 'true' : 'false' }})) ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900/50 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Produk' : 'Products' }}</a>
                <a href="{{ route('blog.index') }}" @click="mobileMenu = false"
                    :class="({{ request()->routeIs('blog.*') ? 'true' : 'false' }}) ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900/50 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">Blog</a>
                
                <div class="flex items-center justify-between px-4 py-4 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('switch-language/id') }}"
                            class="{{ app()->getLocale() == 'id' ? 'text-blue-600 font-bold' : 'text-slate-500 dark:text-slate-400' }} text-sm">ID</a>
                        <a href="{{ url('switch-language/en') }}"
                            class="{{ app()->getLocale() == 'en' ? 'text-blue-600 font-bold' : 'text-slate-500 dark:text-slate-400' }} text-sm">EN</a>
                    </div>
                    <!-- Mobile Theme Toggler -->
                    <button @click="toggle()" class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shadow-sm focus:outline-none">
                        <span x-show="isDark"><i class="fa-solid fa-sun text-sm"></i></span>
                        <span x-show="!isDark"><i class="fa-solid fa-moon text-sm"></i></span>
                    </button>
                </div>

                <div class="flex flex-col space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('dashboard') }}" class="w-full py-4 bg-blue-600 text-white text-center font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/20">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('user.orders') }}" class="w-full py-4 bg-blue-600 text-white text-center font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/20">
                                Pesanan Saya
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full py-4 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-center font-black uppercase tracking-widest rounded-2xl border border-slate-200/80 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="w-full py-4 bg-blue-600 text-white text-center font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/20">
                            Join Us
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
