<aside
    class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 transition-transform duration-300 transform lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen shadow-2xl shrink-0"
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
    <div class="h-full flex flex-col">
        <!-- Logo Area -->
        <div class="p-6 flex items-center justify-between border-b border-slate-800/50">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-rocket text-xl"></i>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-white font-black tracking-tighter text-lg">FKSTUDIO</span>
                    <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500">Dashboard v2.0</span>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-8 px-4 space-y-1 custom-scrollbar">
            <p class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-600 mb-4">Main Menu</p>
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-chart-line w-5 group-hover:scale-110 transition-transform"></i>
                <span>Statistics</span>
            </a>

            <a href="{{ route('dashboard.settings.edit') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.settings.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-gears w-5 group-hover:scale-110 transition-transform"></i>
                <span>General Settings</span>
            </a>

            <p class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-600 mt-8 mb-4">Landing Content</p>

            <a href="{{ route('dashboard.hero.edit') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.hero.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-wand-magic-sparkles w-5 group-hover:scale-110 transition-transform"></i>
                <span>Hero Section</span>
            </a>

            <a href="{{ route('dashboard.about.edit') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.about.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-address-card w-5 group-hover:scale-110 transition-transform"></i>
                <span>About Section</span>
            </a>

            <a href="{{ route('dashboard.services.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.services.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-briefcase w-5 group-hover:scale-110 transition-transform"></i>
                <span>Our Services</span>
            </a>

            <a href="{{ route('dashboard.projects.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.projects.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-layer-group w-5 group-hover:scale-110 transition-transform"></i>
                <span>Portfolio</span>
            </a>

            <a href="{{ route('dashboard.packages.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.packages.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-tags w-5 group-hover:scale-110 transition-transform"></i>
                <span>Service Packages</span>
            </a>

            <a href="{{ route('dashboard.testimonials.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.testimonials.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-comment-dots w-5 group-hover:scale-110 transition-transform"></i>
                <span>Testimonials</span>
            </a>

            <a href="{{ route('dashboard.clients.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard.clients.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-handshake w-5 group-hover:scale-110 transition-transform"></i>
                <span>Clients</span>
            </a>

        </div>

        <!-- User Footer -->
        <div class="p-4 bg-slate-950/50 border-t border-slate-800/50 mt-auto">
            <div class="flex items-center space-x-3 p-2 bg-slate-800/30 rounded-2xl border border-white/5">
                <div class="w-10 h-10 bg-slate-700 rounded-xl flex items-center justify-center font-bold text-blue-400">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 text-slate-500 hover:text-red-400 transition-colors">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- Overlay for mobile -->
<div 
    x-show="sidebarOpen" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false" 
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"
    style="display: none;">
</div>
