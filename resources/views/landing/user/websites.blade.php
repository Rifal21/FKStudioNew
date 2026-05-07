<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Website Saya | FKStudio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-600; }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-950" x-data="{ mobileMenu: false, scrolled: false, activeSection: '' }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-700/10 rounded-full blur-[120px]"></div>
    </div>

    @include('landing.sections.navbar')

    <div class="container mx-auto px-6 pt-32 pb-12 md:pb-24 max-w-6xl relative z-10">
        <div class="flex items-center justify-between mb-12">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-6 group">
                    <i class="fa-solid fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
                    Kembali ke Beranda
                </a>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tighter">
                    Daftar <span class="gradient-text">Website Saya.</span>
                </h1>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-6 py-3 bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-colors">
                    Logout
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($orders as $order)
                <div class="glass p-8 rounded-[2.5rem] border-white/5 relative overflow-hidden group hover:border-blue-500/50 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    
                    <div class="w-16 h-16 bg-blue-600/20 text-blue-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-globe text-2xl"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black text-white mb-2">{{ $order->branding_name }}</h3>
                    <p class="text-slate-400 text-sm mb-6">Generated dari: {{ $order->package_name }}</p>
                    
                    <div class="space-y-4">
                        <a href="http://{{ $order->subdomain }}.{{ $baseDomain }}:8000" target="_blank" class="w-full py-4 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20 flex items-center justify-center space-x-2">
                            <span>Kunjungi Website</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                        
                        <div class="flex items-center justify-center space-x-2 text-xs text-slate-500 font-medium">
                            <i class="fa-solid fa-link"></i>
                            <span>{{ $order->subdomain }}.{{ $baseDomain }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 lg:col-span-3 glass p-12 rounded-[2.5rem] text-center border-white/5 flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center text-slate-600 mb-6 border border-white/10">
                        <i class="fa-solid fa-laptop-code text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2">Belum ada website aktif</h3>
                    <p class="text-slate-400 mb-8 max-w-md mx-auto">Website Anda akan muncul di sini setelah pembayaran pesanan paket Anda dikonfirmasi oleh admin.</p>
                    <a href="{{ route('user.orders') }}" class="px-8 py-4 bg-white/10 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-white/20 transition-colors border border-white/10">
                        Cek Status Pesanan
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
