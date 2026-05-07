<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ tenant('branding_name') ?? 'FKStudio' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-600; }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-950 flex flex-col">
    <!-- Navbar -->
    <nav class="glass border-b border-white/5 px-6 py-4 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center space-x-4">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <span class="text-white font-black text-xl italic">{{ strtoupper(substr(tenant('branding_name') ?? 'F', 0, 1)) }}</span>
            </div>
            <div>
                <h1 class="text-sm font-black text-white uppercase tracking-widest">{{ tenant('branding_name') ?? 'FKStudio' }}</h1>
                <p class="text-[10px] text-emerald-400 font-mono"><i class="fa-solid fa-circle text-[8px] mr-1"></i>LIVE</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <nav class="hidden md:flex items-center space-x-6 mr-6 border-r border-white/5 pr-6">
                <a href="{{ route('tenant.dashboard') }}" class="text-xs font-bold {{ request()->routeIs('tenant.dashboard') ? 'text-blue-400' : 'text-slate-400 hover:text-white' }} transition-colors">DASHBOARD</a>
                <a href="{{ route('tenant.builder') }}" class="text-xs font-bold {{ request()->routeIs('tenant.builder') ? 'text-blue-400' : 'text-slate-400 hover:text-white' }} transition-colors">BUILDER</a>
                @php
                    $siteType = \App\Models\TenantSetting::first()?->site_type;
                @endphp
                @if($siteType === 'sales')
                    <a href="{{ route('tenant.products.index') }}" class="text-xs font-bold {{ request()->routeIs('tenant.products.*') ? 'text-blue-400' : 'text-slate-400 hover:text-white' }} transition-colors">PRODUK</a>
                    <a href="{{ route('tenant.orders') }}" class="text-xs font-bold {{ request()->routeIs('tenant.orders.*') ? 'text-blue-400' : 'text-slate-400 hover:text-white' }} transition-colors">PESANAN</a>
                @endif
            </nav>

            <a href="{{ route('tenant.home') }}" target="_blank" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-eye mr-1"></i> Kunjungi Web
            </a>
            
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-blue-400 font-bold border border-white/10">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </button>
                
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 glass rounded-xl shadow-2xl py-2 border-white/10">
                    <div class="px-4 py-2 border-b border-white/5 mb-2">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('tenant.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-red-400 hover:bg-white/5 transition-colors flex items-center">
                            <i class="fa-solid fa-power-off w-4"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 lg:p-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 rounded-xl text-sm font-bold flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>
