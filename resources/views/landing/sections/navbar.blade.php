    <!-- Navigation -->
    <nav :class="scrolled ? 'bg-[#0f172a]/80 backdrop-blur-xl border-b border-white/5 shadow-2xl py-4' : 'bg-transparent py-8'" class="fixed w-full z-40 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-2 group">
                @if ($settings->site_logo)
                    <img src="{{ $settings->logo_url }}"
                        class="h-10 md:h-12 w-auto group-hover:scale-105 transition-transform duration-300">
                @else
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform duration-300">
                        <span class="text-white font-black text-xl md:text-2xl italic leading-none">F</span>
                    </div>
                @endif
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-6 xl:space-x-8">
                @php
                    $isHome = request()->routeIs('home');
                    $isPackagePage = request()->routeIs('package.*');
                @endphp

                <a href="{{ route('home') }}#home"
                    :class="(activeSection === 'home' && {{ $isHome ? 'true' : 'false' }}) ? 'text-blue-400 font-bold' : 'text-slate-300 hover:text-white'"
                    class="text-xs font-bold transition-colors uppercase tracking-[0.15em] relative group">
                    {{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}
                    <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-blue-500 transform origin-left transition-transform duration-300" :class="(activeSection === 'home' && {{ $isHome ? 'true' : 'false' }}) ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"></span>
                </a>
                <a href="{{ route('home') }}#about"
                    :class="(activeSection === 'about' && {{ $isHome ? 'true' : 'false' }}) ? 'text-blue-400 font-bold' : 'text-slate-300 hover:text-white'"
                    class="text-xs font-bold transition-colors uppercase tracking-[0.15em] relative group">
                    {{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}
                    <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-blue-500 transform origin-left transition-transform duration-300" :class="(activeSection === 'about' && {{ $isHome ? 'true' : 'false' }}) ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"></span>
                </a>
                <a href="{{ route('home') }}#services"
                    :class="(activeSection === 'services' && {{ $isHome ? 'true' : 'false' }}) ? 'text-blue-400 font-bold' : 'text-slate-300 hover:text-white'"
                    class="text-xs font-bold transition-colors uppercase tracking-[0.15em] relative group">
                    {{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}
                    <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-blue-500 transform origin-left transition-transform duration-300" :class="(activeSection === 'services' && {{ $isHome ? 'true' : 'false' }}) ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"></span>
                </a>
                <a href="{{ route('home') }}#portfolio"
                    :class="(activeSection === 'portfolio' && {{ $isHome ? 'true' : 'false' }}) ? 'text-blue-400 font-bold' : 'text-slate-300 hover:text-white'"
                    class="text-xs font-bold transition-colors uppercase tracking-[0.15em] relative group">
                    {{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}
                    <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-blue-500 transform origin-left transition-transform duration-300" :class="(activeSection === 'portfolio' && {{ $isHome ? 'true' : 'false' }}) ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"></span>
                </a>
                <a href="{{ route('package.index') }}"
                    :class="({{ $isPackagePage ? 'true' : 'false' }} || (activeSection === 'packages' && {{ $isHome ? 'true' : 'false' }})) ? 'text-blue-400 font-bold' : 'text-slate-300 hover:text-white'"
                    class="text-xs font-bold transition-colors uppercase tracking-[0.15em] relative group">
                    {{ app()->getLocale() == 'id' ? 'Produk' : 'Products' }}
                    <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-blue-500 transform origin-left transition-transform duration-300" :class="({{ $isPackagePage ? 'true' : 'false' }} || (activeSection === 'packages' && {{ $isHome ? 'true' : 'false' }})) ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100'"></span>
                </a>
                
                <!-- Divider -->
                <div class="w-px h-6 bg-white/10 mx-2"></div>

                <!-- Language Switcher -->
                <div class="flex items-center space-x-1 bg-white/5 rounded-full p-1 border border-white/5">
                    <a href="{{ url('switch-language/id') }}"
                        class="px-3 py-1 rounded-full text-[10px] font-black transition-all {{ app()->getLocale() == 'id' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                        ID
                    </a>
                    <a href="{{ url('switch-language/en') }}"
                        class="px-3 py-1 rounded-full text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                        EN
                    </a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-3 ml-2">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-blue-600 text-white text-[10px] md:text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 group flex items-center">
                                <span>Dashboard</span>
                                <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @else
                            <a href="{{ route('user.websites') }}" class="px-4 py-2 text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-300 hover:text-white transition-colors">
                                Website Saya
                            </a>
                            <a href="{{ route('user.orders') }}" class="px-5 py-2.5 bg-blue-600 text-white text-[10px] md:text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 group flex items-center">
                                <span>Pesanan Saya</span>
                                <i class="fa-solid fa-box ml-2 group-hover:scale-110 transition-transform"></i>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-white transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:from-blue-500 hover:to-indigo-500 transition-all shadow-lg shadow-blue-600/20 group flex items-center">
                            <span>Join Us</span>
                            <i class="fa-solid fa-user-plus ml-2 opacity-70 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-white">
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
        <div x-show="mobileMenu" x-transition class="lg:hidden absolute top-full left-0 w-full glass p-4 mt-2 border-t">
            <div class="flex flex-col space-y-2">
                <a href="{{ route('home') }}#home" @click="mobileMenu = false"
                    :class="(activeSection === 'home' && {{ $isHome ? 'true' : 'false' }}) ? 'bg-blue-600/10 text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-white/5 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}</a>
                <a href="{{ route('home') }}#about" @click="mobileMenu = false"
                    :class="(activeSection === 'about' && {{ $isHome ? 'true' : 'false' }}) ? 'bg-blue-600/10 text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-white/5 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}</a>
                <a href="{{ route('home') }}#services" @click="mobileMenu = false"
                    :class="(activeSection === 'services' && {{ $isHome ? 'true' : 'false' }}) ? 'bg-blue-600/10 text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-white/5 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}</a>
                <a href="{{ route('home') }}#portfolio" @click="mobileMenu = false"
                    :class="(activeSection === 'portfolio' && {{ $isHome ? 'true' : 'false' }}) ? 'bg-blue-600/10 text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-white/5 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}</a>
                <a href="{{ route('package.index') }}" @click="mobileMenu = false"
                    :class="({{ $isPackagePage ? 'true' : 'false' }} || (activeSection === 'packages' && {{ $isHome ? 'true' : 'false' }})) ? 'bg-blue-600/10 text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-white/5 border-l-4 border-transparent'"
                    class="text-lg px-4 py-3 rounded-r-xl transition-all">{{ app()->getLocale() == 'id' ? 'Produk' : 'Products' }}</a>
                
                <div class="flex items-center space-x-4 px-4 py-4 mt-2 border-t border-white/5">
                    <a href="{{ url('switch-language/id') }}"
                        class="{{ app()->getLocale() == 'id' ? 'text-blue-400 font-bold' : 'text-slate-500' }}">Bahasa
                        Indonesia</a>
                    <a href="{{ url('switch-language/en') }}"
                        class="{{ app()->getLocale() == 'en' ? 'text-blue-400 font-bold' : 'text-slate-500' }}">English</a>
                </div>

                <div class="flex flex-col space-y-4 pt-4 border-t border-white/10">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('dashboard') }}" class="w-full py-4 bg-blue-600 text-white text-center font-black uppercase tracking-widest rounded-2xl">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('user.websites') }}" class="w-full py-4 bg-white/5 border border-white/10 text-slate-300 text-center font-black uppercase tracking-widest rounded-2xl hover:text-white hover:bg-white/10 transition-all">
                                Website Saya
                            </a>
                            <a href="{{ route('user.orders') }}" class="w-full py-4 bg-blue-600 text-white text-center font-black uppercase tracking-widest rounded-2xl">
                                Pesanan Saya
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full py-4 bg-white/5 text-white text-center font-black uppercase tracking-widest rounded-2xl border border-white/10">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="w-full py-4 bg-blue-600 text-white text-center font-black uppercase tracking-widest rounded-2xl">
                            Join Us
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
