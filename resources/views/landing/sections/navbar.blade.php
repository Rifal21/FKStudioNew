    <!-- Navigation -->
    <nav :class="scrolled ? 'glass py-3' : 'bg-transparent py-5'" class="fixed w-full z-40 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center space-x-2 group">
                @if ($settings->site_logo)
                    <img src="{{ $settings->logo_url }}"
                        class="h-10 w-auto group-hover:scale-110 transition-transform">
                @else
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <span class="text-white font-bold text-xl italic leading-none">F</span>
                    </div>
                @endif
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#home"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}</a>
                <a href="#about"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}</a>
                <a href="#services"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}</a>
                <a href="#portfolio"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}</a>
                <a href="#packages"
                    class="text-sm font-medium hover:text-blue-400 transition-colors uppercase tracking-widest">{{ app()->getLocale() == 'id' ? 'Paket' : 'Packages' }}</a>
                
                <!-- Language Switcher -->
                <div class="flex items-center space-x-2 border-l border-slate-700 pl-6 ml-6">
                    <a href="{{ url('switch-language/id') }}"
                        class="text-xs transition-colors {{ app()->getLocale() == 'id' ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">ID</a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ url('switch-language/en') }}"
                        class="text-xs transition-colors {{ app()->getLocale() == 'en' ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">EN</a>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden text-white">
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
        <div x-show="mobileMenu" x-transition class="md:hidden absolute top-full left-0 w-full glass p-4 mt-2 border-t">
            <div class="flex flex-col space-y-4">
                <a href="#home"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Beranda' : 'Home' }}</a>
                <a href="#about"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Tentang' : 'About' }}</a>
                <a href="#services"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Layanan' : 'Services' }}</a>
                <a href="#portfolio"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Proyek' : 'Projects' }}</a>
                <a href="#packages"
                    class="text-lg font-medium p-2">{{ app()->getLocale() == 'id' ? 'Paket' : 'Packages' }}</a>
                <div class="flex items-center space-x-4 p-2">
                    <a href="{{ url('switch-language/id') }}"
                        class="{{ app()->getLocale() == 'id' ? 'text-blue-400 font-bold' : 'text-slate-500' }}">Bahasa
                        Indonesia</a>
                    <a href="{{ url('switch-language/en') }}"
                        class="{{ app()->getLocale() == 'en' ? 'text-blue-400 font-bold' : 'text-slate-500' }}">English</a>
                </div>
            </div>
        </div>
    </nav>
