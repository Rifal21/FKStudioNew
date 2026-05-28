<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout Pelunasan - {{ $order->package_name }} | FKStudio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-600; }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-950" 
    x-data="{ 
        mobileMenu: false, 
        scrolled: false, 
        activeSection: '',
        agree: false,
        formatCurrency(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); }
    }" 
    @scroll.window="scrolled = (window.pageYOffset > 50)">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-blue-700/10 rounded-full blur-[120px]"></div>
    </div>

    @include('landing.sections.navbar')

    <div class="container mx-auto px-6 pt-32 pb-12 md:pb-24 relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12">
                <a href="{{ route('user.orders') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-8 group">
                    <i class="fa-solid fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
                    Kembali ke Pesanan Saya
                </a>
                <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter">
                    Pelunasan <span class="gradient-text">Proyek Website.</span>
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Order Summary -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="glass p-8 rounded-[2.5rem] border-blue-500/20 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-6 opacity-10">
                            <i class="fa-solid fa-receipt text-6xl text-blue-500"></i>
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-blue-400 mb-6">Ringkasan Invoice</h3>
                        <h2 class="text-2xl font-black text-white mb-2">{{ $order->package_name }}</h2>
                        <div class="text-slate-400 text-xs mb-6">Brand: <span class="font-bold text-white">{{ $order->website_name }}</span></div>
                        
                        <!-- Detailed Pricing Items -->
                        <div class="border-t border-white/5 pt-6 space-y-2">
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>Grand Total Proyek:</span>
                                <span class="font-bold text-white">{{ 'Rp ' . number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-emerald-400">
                                <span>DP (50%) Terbayar:</span>
                                <span class="font-bold">- {{ 'Rp ' . number_format($order->dp_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-white/5 pt-6 mt-6">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 mb-2">Total Tagihan Pelunasan (50%)</div>
                            <div class="text-4xl font-black text-white tracking-tighter">{{ 'Rp ' . number_format($order->remaining_balance, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="glass p-8 rounded-[2.5rem] bg-blue-600/5">
                        <h4 class="font-bold text-white mb-2">Konfirmasi Instan</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Pilih metode **Pembayaran Otomatis** via gateway Duitku untuk aktivasi proyek instan 24/7 tanpa perlu upload bukti pembayaran secara manual.
                        </p>
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <form action="{{ route('checkout.pelunasan.process', $order->id) }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="glass p-8 md:p-12 rounded-[3rem] shadow-2xl">
                            @if(session('error'))
                                <div class="mb-8 p-6 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-red-500/20 text-red-500 rounded-xl flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-red-500">Gagal Memproses Pembayaran</h4>
                                        <p class="text-sm text-red-400/80 mt-1">{{ session('error') }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-12">
                                <h3 class="text-xl font-black text-white flex items-center mb-2">
                                    <span class="w-10 h-10 bg-blue-600/20 text-blue-500 rounded-xl flex items-center justify-center mr-4">
                                        <i class="fa-solid fa-credit-card"></i>
                                    </span>
                                    Metode Pembayaran Pelunasan
                                </h3>
                                <p class="text-slate-400 text-sm mb-8 ml-14">Pilih metode pembayaran pelunasan ulang yang Anda inginkan.</p>
                                
                                <div class="space-y-10">
                                    <!-- Manual Transfer Section -->
                                    <div class="space-y-4">
                                        <div class="flex items-center space-x-3 px-2">
                                            <div class="h-px flex-grow bg-white/5"></div>
                                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 whitespace-nowrap">Transfer Manual</h4>
                                            <div class="h-px flex-grow bg-white/5"></div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($settings->payment_methods ?? [] as $index => $bank)
                                                <label class="relative group cursor-pointer block">
                                                    <input type="radio" name="payment_method" value="{{ $bank['bank'] }} - {{ $bank['number'] }}" class="peer hidden" {{ $index == 0 ? 'checked' : '' }}>
                                                    <div class="h-full p-5 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                        <div class="absolute top-0 right-0 p-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                            <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                                                        </div>
                                                        <div class="flex items-center space-x-4">
                                                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl text-white group-hover:scale-110 transition-transform">
                                                                <i class="fa-solid fa-building-columns"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-black text-white uppercase tracking-wider text-sm">{{ $bank['bank'] }}</div>
                                                                <div class="text-[10px] text-slate-500">{{ $bank['name'] }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                            
                                            @if($settings->invoice_qris)
                                                 <label class="relative group cursor-pointer block">
                                                     <input type="radio" name="payment_method" value="QRIS" class="peer hidden">
                                                     <div class="h-full p-5 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                         <div class="absolute top-0 right-0 p-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                             <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                                                         </div>
                                                         <div class="flex items-center space-x-4">
                                                             <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl text-white group-hover:scale-110 transition-transform">
                                                                 <i class="fa-solid fa-qrcode"></i>
                                                             </div>
                                                             <div>
                                                                 <div class="font-black text-white uppercase tracking-wider text-sm">QRIS</div>
                                                                 <div class="text-[10px] text-slate-500">Konfirmasi Manual</div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </label>
                                             @endif
                                        </div>
                                    </div>

                                    <!-- Automated Payment Section -->
                                    <div class="space-y-4">
                                        <div class="flex items-center space-x-3 px-2">
                                            <div class="h-px flex-grow bg-white/5"></div>
                                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400 whitespace-nowrap">Pembayaran Otomatis</h4>
                                            <div class="h-px flex-grow bg-white/5"></div>
                                        </div>
                                        
                                        @if(count($duitkuMethods) > 0)
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                                @foreach($duitkuMethods as $method)
                                                    <label class="relative group cursor-pointer block">
                                                        <input type="radio" name="payment_method" value="Duitku|{{ $method['paymentMethod'] }}" class="peer hidden">
                                                        <div class="h-full p-4 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                            <div class="absolute top-0 right-0 p-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                                <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                                                            </div>
                                                            <div class="flex flex-col items-center text-center space-y-3">
                                                                <div class="w-full h-10 bg-white/10 rounded-lg overflow-hidden flex items-center justify-center p-2 group-hover:scale-105 transition-transform">
                                                                    <img src="{{ $method['paymentImage'] }}" alt="{{ $method['paymentName'] }}" class="max-w-full max-h-full object-contain filter brightness-110">
                                                                </div>
                                                                <div class="text-[10px] font-bold text-white leading-tight line-clamp-1 uppercase tracking-tighter">{{ $method['paymentName'] }}</div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <label class="relative group cursor-pointer block">
                                                <input type="radio" name="payment_method" value="Duitku" class="peer hidden">
                                                <div class="p-6 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                    <div class="absolute top-0 right-0 p-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                        <i class="fa-solid fa-circle-check text-blue-500 text-xl"></i>
                                                    </div>
                                                    <div class="flex items-center space-x-4">
                                                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl text-white group-hover:scale-110 transition-transform">
                                                            <i class="fa-solid fa-wallet"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-black text-white uppercase tracking-widest">Duitku (Otomatis)</div>
                                                            <div class="text-xs text-slate-400">E-Wallet, VA, & Retail Outlets</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>

                             <div class="bg-white/5 border border-white/10 rounded-[2rem] p-6 mb-8">
                                <div class="flex items-start space-x-4">
                                   <div class="relative flex items-center">
                                       <input type="checkbox" id="agree" x-model="agree" class="peer h-6 w-6 rounded-lg border-2 border-white/10 bg-white/5 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-950 transition-all cursor-pointer checked:border-blue-500">
                                       <i class="fa-solid fa-check absolute inset-0 m-auto text-[10px] text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                                   </div>
                                   <label for="agree" class="text-sm text-slate-400 leading-relaxed cursor-pointer select-none group">
                                       Saya menyatakan setuju untuk melanjutkan pembayaran pelunasan sisa 50% untuk pengerjaan proyek website ini.
                                   </label>
                                </div>
                             </div>

                            <button type="submit" 
                                :disabled="!agree"
                                :class="agree ? 'bg-blue-600 hover:bg-blue-700 hover:scale-[1.02]' : 'bg-slate-800 text-slate-500 cursor-not-allowed'"
                                class="w-full py-6 text-white rounded-2xl font-black uppercase tracking-[0.2em] shadow-2xl transition-all">
                                Bayar Pelunasan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
