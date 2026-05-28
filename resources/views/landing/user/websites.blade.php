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
    <!-- Background Decoration Blobs -->
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
                    Website <span class="gradient-text">Saya.</span>
                </h1>
            </div>
            
            <a href="{{ route('user.orders') }}" class="px-6 py-3 bg-blue-600/10 text-blue-400 border border-blue-500/20 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-colors">
                Lihat Pesanan
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($orders as $order)
                <div class="glass p-8 rounded-[2.5rem] border-white/5 flex flex-col justify-between relative overflow-hidden group min-h-[320px]">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center border border-emerald-500/20">
                                <i class="fa-solid fa-globe text-2xl text-emerald-400"></i>
                            </div>
                            <span class="px-3.5 py-1.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full text-[9px] font-black uppercase tracking-widest">
                                Online & Aktif
                            </span>
                        </div>

                        <!-- Content Card -->
                        <div class="space-y-2 mb-8">
                            <h3 class="text-2xl font-black text-white leading-tight uppercase tracking-tight group-hover:text-blue-400 transition-colors">
                                {{ $order->website_name ?: 'Website Proyek' }}
                            </h3>
                            <p class="text-xs text-slate-400">
                                Paket Layanan: <span class="font-black text-slate-200">{{ $order->package_name }}</span>
                            </p>
                            <div class="inline-flex items-center space-x-2 bg-white/5 border border-white/10 px-3.5 py-2 rounded-xl text-xs font-mono text-blue-400 mt-2">
                                <i class="fa-solid fa-link text-[10px]"></i>
                                <span>{{ $order->website_url }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t border-white/5 flex items-center justify-between gap-4">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                            Selesai: {{ $order->updated_at->format('d M Y') }}
                        </span>
                        
                        <div class="flex space-x-2">
                            @if($order->finalInvoice)
                                <a href="{{ route('invoices.public.show', $order->finalInvoice->id) }}" target="_blank" class="w-10 h-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-slate-300 hover:bg-white/10 hover:text-white transition-colors" title="Invoice Pelunasan">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>
                            @endif
                            
                            <a href="https://{{ $order->website_url }}" target="_blank" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-colors flex items-center space-x-2 shadow-lg shadow-blue-600/10">
                                <span>Kunjungi</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="md:col-span-2 glass py-20 px-8 rounded-[3rem] border-white/5 text-center flex flex-col items-center max-w-xl mx-auto">
                    <div class="w-20 h-20 bg-blue-600/10 rounded-[2rem] flex items-center justify-center border border-blue-500/20 mb-8 animate-bounce">
                        <i class="fa-solid fa-laptop-code text-3xl text-blue-500"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2">Belum Ada Website Selesai</h3>
                    <p class="text-sm text-slate-400 leading-relaxed mb-8">
                        Proyek pengerjaan website Anda masih dalam proses pengembangan atau belum dimulai. Silakan periksa detail kemajuan di menu Pesanan Saya.
                    </p>
                    <a href="{{ route('user.orders') }}" class="px-8 py-4 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-600/25">
                        Periksa Pesanan Saya
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
