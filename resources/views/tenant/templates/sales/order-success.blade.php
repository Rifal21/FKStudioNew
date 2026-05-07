<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Berhasil - {{ $brandingName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #050505; color: #ffffff; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full glass p-12 rounded-[3rem] text-center space-y-8 border-emerald-500/20">
        <div class="w-24 h-24 bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center text-4xl mx-auto animate-bounce">
            <i class="fa-solid fa-check-double"></i>
        </div>
        
        <div>
            <h1 class="text-4xl font-black tracking-tighter mb-4 uppercase italic">Terima Kasih!</h1>
            <p class="text-gray-400 font-medium leading-relaxed">Pesanan Anda <span class="text-white font-bold">#{{ $order->order_number }}</span> telah kami terima dan sedang diproses.</p>
        </div>

        <div class="bg-black/40 p-6 rounded-2xl text-left space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 uppercase tracking-widest text-[10px] font-bold">Total Pembayaran</span>
                <span class="text-white font-black italic">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 uppercase tracking-widest text-[10px] font-bold">Metode Pembayaran</span>
                <span class="text-white font-bold uppercase tracking-widest text-[10px]">{{ str_replace('_', ' ', $order->payment_method) }}</span>
            </div>
        </div>

        <a href="{{ route('tenant.order.invoice', $order) }}" class="flex items-center justify-center space-x-3 w-full py-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group">
            <i class="fa-solid fa-file-pdf text-rose-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-xs font-black uppercase tracking-widest text-white">Download Invoice (PDF)</span>
        </a>

        <div class="space-y-4">
            <p class="text-xs text-gray-500 italic">Tim kami akan menghubungi Anda melalui WhatsApp untuk proses selanjutnya.</p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('tenant.home') }}" class="flex-1 py-4 bg-white text-black font-black uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all text-sm">
                    Kembali Beranda
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', tenant('whatsapp') ?? '') }}?text=Halo, saya ingin konfirmasi pesanan #{{ $order->order_number }}" class="flex-1 py-4 bg-emerald-600 text-white font-black uppercase tracking-widest rounded-xl hover:bg-emerald-700 transition-all text-sm">
                    Chat Admin <i class="fa-brands fa-whatsapp ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
