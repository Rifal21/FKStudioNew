<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $bankName = '';
    $bankNumber = '';
    $bankOwner = '';
    
    if ($order->payment_method !== 'QRIS' && !str_starts_with($order->payment_method ?? '', 'Duitku')) {
        $parts = explode(' - ', $order->payment_method);
        $bankName = $parts[0] ?? $order->payment_method;
        $bankNumber = $parts[1] ?? '';
        
        foreach ($settings->payment_methods ?? [] as $method) {
            if (strcasecmp($method['bank'], $bankName) === 0 && $method['number'] == $bankNumber) {
                $bankOwner = $method['name'];
                break;
            }
        }
    }
    
    $isPelunasanPayment = ($order->status === 'paid' && $order->payment_scheme === 'dp' && $order->final_invoice_id && $order->finalInvoice && $order->finalInvoice->status === 'Unpaid');
    $upfrontAmount = $isPelunasanPayment ? $order->remaining_balance : ($order->payment_scheme === 'dp' ? $order->dp_amount : $order->total_amount);
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Berhasil - {{ $order->package_name }} | FKStudio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-blue-600; }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-950 flex flex-col" x-data="{ mobileMenu: false, scrolled: false, activeSection: '' }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-emerald-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-blue-700/10 rounded-full blur-[120px]"></div>
    </div>

    @include('landing.sections.navbar')

    <div class="flex-grow flex items-center justify-center px-6 pt-32 pb-20 w-full relative z-10">
        <div class="max-w-4xl w-full">
        @if(session('success'))
            <div class="mb-8 p-6 glass border-emerald-500/30 rounded-3xl flex items-center space-x-4 animate-bounce">
                <div class="w-12 h-12 bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-check text-xl"></i>
                </div>
                <div class="text-white font-bold">{{ session('success') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Instructions -->
            <div class="space-y-8">
                <div>
                    <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-6">
                        Pesanan <span class="gradient-text">Diterima!</span>
                    </h1>
                    <p class="text-slate-400 leading-relaxed">
                        Terima kasih telah mempercayai FKStudio. Pesanan Anda telah tercatat dengan ID: <span class="text-white font-mono">{{ substr($order->id, 0, 8) }}</span>.
                    </p>
                </div>

                <div class="space-y-6">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-500">Langkah Selanjutnya</h3>
                    
                    <div class="flex space-x-4">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center font-black text-white flex-shrink-0">1</div>
                        <div>
                            <p class="text-sm text-white font-bold mb-1">Lakukan Pembayaran</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Transfer sesuai nominal ke rekening yang telah Anda pilih.</p>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center font-black text-white flex-shrink-0">2</div>
                        <div>
                            <p class="text-sm text-white font-bold mb-1">Unggah Bukti Transfer</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Gunakan form di samping untuk mengunggah bukti pembayaran Anda.</p>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center font-black text-white flex-shrink-0">3</div>
                        <div>
                            <p class="text-sm text-white font-bold mb-1">Aktivasi Layanan</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Admin akan memverifikasi pembayaran Anda dalam waktu maksimal 1x24 jam.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 glass rounded-3xl border-blue-500/10 space-y-4">
                    <h4 class="text-xs font-black text-white uppercase tracking-widest border-b border-white/5 pb-3">Rincian Pembelian</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal Paket:</span>
                            <span class="font-bold text-white">{{ $order->package_price }}</span>
                        </div>
                        @if($order->buy_domain && $order->domain_price)
                            <div class="flex justify-between text-slate-400">
                                <span>Domain ({{ $order->domain_name }}):</span>
                                <span class="font-bold text-white">Rp {{ number_format($order->domain_price, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-emerald-400">
                                <span>Potongan Voucher ({{ $order->voucher_code }}):</span>
                                <span class="font-bold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-slate-400">
                            <span>PPN ({{ $settings->tax_rate ?? 11 }}%):</span>
                            <span class="font-bold text-white">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-white/5 pt-2 flex justify-between text-sm font-black text-white">
                            <span>Grand Total:</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Form (Ticket) -->
            <!-- Confirmation Form (Ticket) / Duitku Status -->
            @if(str_starts_with($order->payment_method, 'Duitku'))
                <div class="glass p-8 md:p-10 rounded-[3rem] border-white/5 shadow-2xl flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-blue-500/20 text-blue-500 rounded-full flex items-center justify-center mb-6 animate-pulse">
                        <i class="fa-solid fa-spinner fa-spin text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">Menunggu Konfirmasi</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-8">
                        Kami sedang menunggu konfirmasi pembayaran otomatis dari sistem Duitku. Halaman ini akan otomatis dialihkan jika pembayaran berhasil.
                    </p>
                    <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 animate-[loading_2s_ease-in-out_infinite]" style="width: 30%"></div>
                    </div>
                    <script>
                        // Auto refresh or check status
                        setTimeout(() => {
                            window.location.reload();
                        }, 5000);
                    </script>
                </div>
            @else
                <div class="glass p-8 md:p-10 rounded-[3rem] border-white/5 shadow-2xl relative space-y-6">
                     <!-- Transfer Amount Card -->
                     <div class="p-6 bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 rounded-3xl select-none">
                         <p class="text-[9px] font-black uppercase tracking-wider text-emerald-500 mb-1">
                             {{ $isPelunasanPayment ? 'Jumlah Pembayaran Pelunasan (50%)' : ($order->payment_scheme === 'dp' ? 'Jumlah Down Payment (DP 50%)' : 'Jumlah Transfer Lunas (100%)') }}
                         </p>
                         <div class="flex items-center justify-between">
                             <div class="flex items-center space-x-2">
                                 <span class="text-2xl font-black text-white tracking-tighter">
                                     Rp {{ number_format($upfrontAmount, 0, ',', '.') }}
                                 </span>
                                 <button type="button" onclick="navigator.clipboard.writeText('{{ (int) $upfrontAmount }}'); alert('Jumlah transfer berhasil disalin!');" class="p-1.5 text-emerald-400 hover:text-white hover:bg-white/10 rounded-lg transition-all" title="Salin Nominal">
                                     <i class="fa-regular fa-copy text-sm"></i>
                                 </button>
                             </div>
                         </div>
                         @if($order->payment_scheme === 'dp' && !$isPelunasanPayment)
                             <p class="text-[9px] text-slate-500 mt-2">
                                 Sisa pelunasan sebesar <span class="font-bold text-indigo-400">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</span> akan ditagihkan setelah website selesai dikerjakan.
                             </p>
                         @endif
                     </div>

                    <!-- Destination Card (QRIS or Bank) -->
                    @if($order->payment_method === 'QRIS' && $settings->invoice_qris)
                        <div class="p-6 bg-white rounded-3xl flex flex-col items-center select-none">
                            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Scan QRIS untuk Pembayaran</h4>
                            <img src="{{ $settings->invoice_qris_url }}" alt="QRIS Payment" class="w-full max-w-[200px] aspect-square object-contain">
                            <div class="mt-4 text-center">
                                <p class="text-xs font-black text-slate-800 uppercase tracking-tighter">FK STUDIO PAYMENT</p>
                                <p class="text-[9px] text-slate-500">Scan QRIS di atas untuk menyelesaikan pembayaran</p>
                            </div>
                        </div>
                    @elseif($bankNumber)
                        <div class="p-6 bg-white/5 border border-white/10 rounded-3xl relative overflow-hidden group select-none">
                            <div class="absolute -right-6 -bottom-6 text-white/5 text-8xl font-black">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="px-3 py-1 bg-blue-500/15 text-blue-400 font-black uppercase text-[8px] tracking-wider rounded-full">
                                    Transfer Bank
                                </div>
                                <div class="font-black text-white text-base tracking-wider uppercase">
                                    {{ $bankName }}
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Nomor Rekening</p>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xl font-bold text-white tracking-wider font-mono">{{ $bankNumber }}</span>
                                        <button type="button" onclick="navigator.clipboard.writeText('{{ $bankNumber }}'); alert('Nomor rekening berhasil disalin!');" class="p-1.5 text-slate-500 hover:text-white hover:bg-white/10 rounded-lg transition-all" title="Salin Rekening">
                                            <i class="fa-regular fa-copy text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1">Nama Penerima</p>
                                    <p class="text-xs font-bold text-white uppercase">{{ $bankOwner ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <h3 class="text-lg font-black text-white flex items-center pt-4 border-t border-white/5">
                        <span class="w-8 h-8 bg-blue-600/20 text-blue-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                        Konfirmasi Pembayaran
                    </h3>

                    <form action="{{ route('checkout.confirm', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1">Pesan (Opsional)</label>
                            <textarea name="message" rows="3" placeholder="Contoh: Saya sudah transfer dari rekening atas nama..."
                                class="block w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium text-white text-xs placeholder-slate-600 resize-none"></textarea>
                        </div>

                        <div class="space-y-2" x-data="{ fileName: null }">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1">Bukti Pembayaran <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <input type="file" name="attachment" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="fileName = $event.target.files[0].name">
                                <div class="px-6 py-6 bg-white/5 border border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center group-hover:border-blue-500/50 transition-all">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-600 mb-2 group-hover:text-blue-500 transition-colors"></i>
                                    <span class="text-xs font-bold text-slate-400 group-hover:text-white transition-colors text-center" x-text="fileName || 'Klik atau seret bukti transfer ke sini'"></span>
                                    <span class="text-[8px] text-slate-600 mt-1 uppercase">PNG, JPG up to 2MB</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                            Kirim Konfirmasi
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('home') }}" class="text-xs font-black text-slate-500 hover:text-white uppercase tracking-widest transition-colors">
                Kembali ke Beranda
            </a>
        </div>
    </div>
    </div>
</body>
</html>
