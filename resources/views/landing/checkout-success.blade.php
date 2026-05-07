<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

                <div class="p-6 glass rounded-3xl border-blue-500/10">
                    <h4 class="text-sm font-black text-white mb-4 uppercase tracking-widest">Detail Pembayaran</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Total Tagihan</span>
                            <span class="text-white font-bold">{{ $order->package_price }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Metode</span>
                            <span class="text-white font-bold">{{ $order->payment_method }}</span>
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
                <div class="glass p-8 md:p-10 rounded-[3rem] border-white/5 shadow-2xl relative">
                    @if($order->payment_method === 'QRIS' && $settings->invoice_qris)
                        <div class="mb-10 p-6 bg-white rounded-3xl flex flex-col items-center">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Scan QRIS untuk Pembayaran</h4>
                            <img src="{{ $settings->invoice_qris_url }}" alt="QRIS Payment" class="w-full max-w-[250px] aspect-square object-contain">
                            <div class="mt-4 text-center">
                                <p class="text-xs font-bold text-slate-800 uppercase tracking-tighter">FK STUDIO PAYMENT</p>
                                <p class="text-[10px] text-slate-500">Silakan scan dan masukkan nominal <span class="text-blue-600 font-black">{{ $order->package_price }}</span></p>
                            </div>
                        </div>
                    @endif

                    <h3 class="text-xl font-black text-white mb-8 flex items-center">
                        <span class="w-10 h-10 bg-blue-600/20 text-blue-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                        Open Ticket
                    </h3>

                    <form action="{{ route('checkout.confirm', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Pesan (Opsional)</label>
                            <textarea name="message" rows="3" placeholder="Contoh: Saya sudah transfer dari rekening atas nama..."
                                class="block w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium text-white text-sm"></textarea>
                        </div>

                        <div class="space-y-2" x-data="{ fileName: null }">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Bukti Pembayaran</label>
                            <div class="relative group">
                                <input type="file" name="attachment" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="fileName = $event.target.files[0].name">
                                <div class="px-6 py-8 bg-white/5 border-2 border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center group-hover:border-blue-500/50 transition-all">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-600 mb-3 group-hover:text-blue-500 transition-colors"></i>
                                    <span class="text-sm font-bold text-slate-400 group-hover:text-white transition-colors" x-text="fileName || 'Klik atau seret file ke sini'"></span>
                                    <span class="text-[10px] text-slate-600 mt-1 uppercase">PNG, JPG up to 2MB</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
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
