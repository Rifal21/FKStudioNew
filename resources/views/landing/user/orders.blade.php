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
                <div class="glass p-6 md:p-8 rounded-[2rem] border-white/5 flex flex-col gap-6 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    
                    <!-- Main Card Top -->
                    <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
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
                            
                            <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm">
                                <div>
                                    <span class="text-slate-500 block text-[10px] mb-0.5 uppercase tracking-wider">Total Nilai Proyek</span>
                                    <span class="text-white font-black">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] mb-0.5 uppercase tracking-wider">Skema</span>
                                    <span class="text-white font-bold uppercase tracking-wider text-[9px] bg-white/5 px-2 py-0.5 rounded block mt-0.5">
                                        {{ $order->payment_scheme === 'dp' ? 'Bertahap / DP (50%)' : 'Lunas (100%)' }}
                                    </span>
                                </div>
                                @if($order->payment_scheme === 'dp')
                                    <div>
                                        <span class="text-slate-500 block text-[10px] mb-0.5 uppercase tracking-wider">DP (50%)</span>
                                        <span class="font-bold block {{ in_array($order->status, ['paid', 'completed']) ? 'text-emerald-400' : 'text-amber-400' }}">
                                            Rp {{ number_format($order->dp_amount, 0, ',', '.') }}
                                            <span class="text-[9px] uppercase font-black tracking-wider ml-1">[{{ in_array($order->status, ['paid', 'completed']) ? 'Lunas' : 'Belum Dibayar' }}]</span>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block text-[10px] mb-0.5 uppercase tracking-wider">Sisa Pelunasan</span>
                                        <span class="font-bold block {{ $order->status === 'completed' ? 'text-emerald-400' : ($order->final_invoice_id ? 'text-amber-400' : 'text-slate-500') }}">
                                            Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}
                                            <span class="text-[9px] uppercase font-black tracking-wider ml-1">
                                                @if($order->status === 'completed')
                                                    [Lunas]
                                                @elseif($order->final_invoice_id)
                                                    [Ditagih]
                                                @else
                                                    [Belum Ditagih]
                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-slate-500 block text-[10px] mb-0.5 uppercase tracking-wider">Metode Pembayaran</span>
                                    <span class="text-white font-bold block">{{ str_replace('Duitku|', '', $order->payment_method ?: 'Belum memilih') }}</span>
                                </div>
                            </div>
                            <!-- Detailed Breakdown Box -->
                            <div class="mt-6 p-4 bg-white/5 border border-white/5 rounded-2xl max-w-xl space-y-2 select-none">
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 border-b border-white/5 pb-2 flex items-center">
                                    <i class="fa-solid fa-receipt mr-2 text-blue-500"></i> Rincian Harga Proyek
                                </div>
                                <div class="flex justify-between text-xs text-slate-400">
                                    <span>Harga Paket & Layanan:</span>
                                    <span class="font-bold text-white">Rp {{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 0, ',', '.') }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div class="flex justify-between text-xs text-emerald-400">
                                        <span>Potongan Voucher (<span class="font-mono text-[10px]">{{ $order->voucher_code }}</span>):</span>
                                        <span class="font-bold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @php
                                    $orderTaxRate = ($order->subtotal_amount - $order->discount_amount) > 0 
                                        ? round(($order->tax_amount / ($order->subtotal_amount - $order->discount_amount)) * 100) 
                                        : 11;
                                @endphp
                                <div class="flex justify-between text-xs text-slate-400">
                                    <span>PPN ({{ $orderTaxRate }}%):</span>
                                    <span class="font-bold text-white">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-xs border-t border-white/5 pt-2 font-black text-white">
                                    <span>Grand Total Terhitung:</span>
                                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full md:w-auto flex flex-col sm:flex-row md:flex-col gap-3">
                            @if($order->payment_scheme === 'dp')
                                @if($order->status === 'pending')
                                    <a href="{{ $order->payment_url ?: route('checkout.success', $order->id) }}" class="w-full md:w-auto px-6 py-3 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                        Bayar DP (50%) Sekarang
                                    </a>
                                @elseif($order->status === 'paid' && $order->final_invoice_id && $order->finalInvoice && $order->finalInvoice->status === 'Unpaid')
                                    <a href="{{ route('checkout.pelunasan', $order->id) }}" class="w-full md:w-auto px-6 py-3 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-600/20">
                                        Bayar Pelunasan (50%) Sekarang
                                    </a>
                                @endif
                            @else
                                @if($order->status === 'pending')
                                    <a href="{{ $order->payment_url ?: route('checkout.success', $order->id) }}" class="w-full md:w-auto px-6 py-3 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                        Bayar Sekarang
                                    </a>
                                @endif
                            @endif

                            @if($order->invoice_id)
                                <a href="{{ route('invoices.public.show', $order->invoice_id) }}" target="_blank" class="w-full md:w-auto px-6 py-3 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-emerald-500/20 transition-colors flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <span>Invoice @if($order->payment_scheme === 'dp') DP @endif</span>
                                </a>
                            @endif

                            @if($order->final_invoice_id)
                                <a href="{{ route('invoices.public.show', $order->final_invoice_id) }}" target="_blank" class="w-full md:w-auto px-6 py-3 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-xs font-black uppercase tracking-widest text-center hover:bg-indigo-500/20 transition-colors flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <span>Invoice Pelunasan</span>
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

                    <!-- Divider -->
                    <div class="h-px bg-white/5 w-full"></div>

                    <!-- Booking & Work Status Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Booking Details Column -->
                        <div class="lg:col-span-1 space-y-4 bg-white/[0.01] border border-white/5 p-5 rounded-2xl">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500">Detail Kebutuhan Website</h4>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[10px] text-slate-500 block uppercase tracking-widest">Nama Brand</span>
                                    <span class="text-sm font-bold text-white">{{ $order->website_name }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 block uppercase tracking-widest">Domain Diinginkan</span>
                                    <span class="text-sm font-bold text-white">{{ $order->website_url ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 block uppercase tracking-widest">Jenis Bisnis</span>
                                    <span class="text-sm font-bold text-white">{{ $order->business_type }}</span>
                                </div>
                                @if($order->client_notes)
                                    <div>
                                        <span class="text-[10px] text-slate-500 block uppercase tracking-widest">Catatan / Brief</span>
                                        <p class="text-xs text-slate-400 leading-relaxed max-w-sm mt-0.5 line-clamp-3 hover:line-clamp-none transition-all cursor-pointer">{{ $order->client_notes }}</p>
                                    </div>
                                @endif

                                @if($order->buy_domain && $order->domain_name)
                                    <div class="mt-4 pt-4 border-t border-white/5 space-y-2">
                                        <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest flex items-center">
                                            <i class="fa-solid fa-server mr-1.5"></i> Pembelian Domain
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-500 block uppercase tracking-widest">Domain Anda</span>
                                            <span class="text-sm font-mono font-bold text-white break-all">{{ $order->domain_name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-500 block uppercase tracking-widest">Status Domain</span>
                                            @php
                                                $uDomStatusClasses = [
                                                    'pending' => 'text-amber-400 bg-amber-500/10 border-amber-500/25',
                                                    'registered' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/25',
                                                    'failed' => 'text-red-400 bg-red-500/10 border-red-500/25',
                                                ];
                                                $uDomStatusLabels = [
                                                    'pending' => 'Proses Registrasi',
                                                    'registered' => 'Aktif / Terdaftar',
                                                    'failed' => 'Registrasi Tertunda',
                                                ];
                                            @endphp
                                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border {{ $uDomStatusClasses[$order->domain_status] ?? 'text-slate-400 bg-slate-500/10 border-slate-500/25' }}">
                                                {{ $uDomStatusLabels[$order->domain_status] ?? $order->domain_status }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stepper / Work Status Column -->
                        <div class="lg:col-span-2 flex flex-col justify-between p-1">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500">Progress Pengerjaan</h4>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[10px] text-slate-500 uppercase tracking-widest">Estimasi Selesai:</span>
                                    <span class="text-xs font-black text-white">
                                        {{ $order->delivery_date ? $order->delivery_date->format('d M Y') : 'Menunggu antrean' }}
                                    </span>
                                </div>
                            </div>

                            @if($order->work_status === 'cancelled')
                                <div class="p-6 bg-red-500/5 border border-red-500/10 rounded-2xl flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-red-500/20 text-red-500 rounded-xl flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-ban text-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-white">Pengerjaan Dibatalkan</h5>
                                        <p class="text-xs text-slate-400 leading-normal mt-0.5">Silakan hubungi tim kami untuk detail atau informasi lebih lanjut terkait pembatalan ini.</p>
                                    </div>
                                </div>
                            @else
                                <!-- Stepper Visualizer -->
                                <div class="relative py-4">
                                    <!-- Stepper Progress Line -->
                                    <div class="absolute left-6 md:left-0 top-[2.2rem] md:top-8 w-0.5 md:w-full h-[70%] md:h-0.5 bg-white/5 -z-10"></div>
                                    <div class="absolute left-6 md:left-0 top-[2.2rem] md:top-8 w-0.5 md:w-full h-[70%] md:h-0.5 bg-gradient-to-r from-blue-500 to-indigo-600 -z-10 transition-all duration-1000 origin-left"
                                         style="transform: scaleX({{ $order->workStatusStep() / 3 }}) scaleY(1); @media(max-width: 768px) { transform: scaleY({{ $order->workStatusStep() / 3 }}) scaleX(1); }"></div>

                                    @php
                                        $currentStep = $order->workStatusStep();
                                        $steps = [
                                            ['key' => 'pending', 'label' => 'Menunggu', 'icon' => 'fa-clock'],
                                            ['key' => 'in_progress', 'label' => 'Pengerjaan', 'icon' => 'fa-laptop-code'],
                                            ['key' => 'revision', 'label' => 'Revisi', 'icon' => 'fa-rotate-left'],
                                            ['key' => 'completed', 'label' => 'Selesai', 'icon' => 'fa-circle-check'],
                                        ];
                                    @endphp

                                    <div class="flex flex-col md:flex-row md:justify-between space-y-4 md:space-y-0">
                                        @foreach($steps as $index => $step)
                                            @php
                                                $isCompleted = $index < $currentStep;
                                                $isActive = $index === $currentStep;
                                            @endphp
                                            <div class="flex md:flex-col items-center text-left md:text-center md:flex-1 relative group/step">
                                                <!-- Step Bubble -->
                                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center z-10 transition-all duration-500 border shrink-0
                                                    {{ $isCompleted ? 'bg-gradient-to-br from-blue-500 to-indigo-600 border-transparent text-white shadow-lg shadow-blue-500/20' : '' }}
                                                    {{ $isActive ? 'bg-slate-900 border-blue-500 text-blue-400 shadow-md ring-4 ring-blue-500/10' : '' }}
                                                    {{ !$isCompleted && !$isActive ? 'bg-slate-900/80 border-white/5 text-slate-500' : '' }}
                                                ">
                                                    @if($isCompleted)
                                                        <i class="fa-solid fa-check text-sm"></i>
                                                    @else
                                                        <i class="fa-solid {{ $step['icon'] }} text-xs"></i>
                                                    @endif
                                                </div>

                                                <!-- Step label -->
                                                <div class="ml-4 md:ml-0 md:mt-3 flex flex-col md:items-center">
                                                    <span class="text-xs font-black uppercase tracking-wider block
                                                        {{ $isActive ? 'text-blue-400' : ($isCompleted ? 'text-slate-300' : 'text-slate-500') }}
                                                    ">
                                                        {{ $step['label'] }}
                                                    </span>
                                                    <span class="text-[9px] font-bold text-slate-500 tracking-wider block mt-0.5">
                                                        @if($isActive)
                                                            Tahapan Aktif
                                                        @elseif($isCompleted)
                                                            Selesai
                                                        @else
                                                            Menunggu
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
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
