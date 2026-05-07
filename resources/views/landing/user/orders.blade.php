<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Saya | FKStudio</title>
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
                    Daftar <span class="gradient-text">Pesanan Saya.</span>
                </h1>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-6 py-3 bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-colors">
                    Logout
                </button>
            </form>
        </div>

        <div class="space-y-6">
            @forelse($orders as $order)
                <div class="glass p-6 md:p-8 rounded-[2rem] border-white/5 flex flex-col md:flex-row gap-8 items-start md:items-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    
                    <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center flex-shrink-0 border border-blue-500/20">
                        <i class="fa-solid fa-box text-2xl text-blue-500"></i>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-xl font-black text-white">{{ $order->package_name }}</h3>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    'paid' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'completed' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    'cancelled' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses[$order->status] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="text-slate-400 text-sm mb-4">
                            ID: <span class="font-mono text-slate-300">#{{ substr($order->id, 0, 8) }}</span> &bull; 
                            Tanggal: {{ $order->created_at->format('d M Y') }}
                        </div>
                        
                        <div class="flex items-center space-x-6 text-sm">
                            <div>
                                <span class="text-slate-500 block text-xs mb-1">Total Tagihan</span>
                                <span class="text-white font-bold">{{ $order->package_price }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-xs mb-1">Metode Pembayaran</span>
                                <span class="text-white font-bold">{{ $order->payment_method }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-auto flex flex-col space-y-3">
                        @if($order->status === 'pending')
                            <a href="{{ route('checkout.success', $order->id) }}" class="w-full md:w-auto px-6 py-3 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                Konfirmasi Pembayaran
                            </a>
                        @endif

                        @if($order->invoice_id)
                            <a href="{{ route('invoices.public.show', $order->invoice_id) }}" target="_blank" class="w-full md:w-auto px-6 py-3 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-emerald-500/20 transition-colors flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span>Lihat Invoice</span>
                            </a>
                        @endif
                        
                        @if($order->tickets->count() > 0)
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="w-full md:w-auto px-6 py-3 bg-white/5 text-slate-300 rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-white/10 border border-white/10 transition-colors flex items-center justify-center space-x-2">
                                    <span>Riwayat Tiket ({{ $order->tickets->count() }})</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform" :class="{'rotate-180': open}"></i>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" x-transition class="absolute top-full right-0 mt-2 w-full md:w-80 glass p-4 rounded-2xl z-50 shadow-2xl border-white/10">
                                    <div class="space-y-4">
                                        @foreach($order->tickets as $ticket)
                                            <div class="border-l-2 pl-3 pb-1 {{ $ticket->status === 'resolved' ? 'border-emerald-500' : 'border-blue-500' }}">
                                                <div class="text-[10px] text-slate-500 mb-1">{{ $ticket->created_at->format('d M Y H:i') }}</div>
                                                <div class="text-sm text-white font-medium mb-1">{{ $ticket->subject }}</div>
                                                <div class="text-[10px] uppercase font-bold tracking-widest {{ $ticket->status === 'resolved' ? 'text-emerald-400' : 'text-blue-400' }}">{{ str_replace('_', ' ', $ticket->status) }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="glass p-12 rounded-[2.5rem] text-center border-white/5 flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center text-slate-600 mb-6 border border-white/10">
                        <i class="fa-solid fa-box-open text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2">Belum ada pesanan</h3>
                    <p class="text-slate-400 mb-8 max-w-md mx-auto">Anda belum melakukan pemesanan paket apa pun. Jelajahi layanan kami dan mulai proyek pertama Anda!</p>
                    <a href="{{ route('home') }}#packages" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:scale-105 transition-transform shadow-xl shadow-blue-600/20">
                        Lihat Paket Layanan
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
