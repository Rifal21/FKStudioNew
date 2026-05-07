<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - {{ $brandingName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #050505; color: #ffffff; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="antialiased min-h-screen">
    <nav class="w-full border-b border-white/10 bg-black/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('tenant.cart') }}" class="text-xl font-black text-white tracking-widest uppercase">
                <i class="fa-solid fa-arrow-left mr-4 text-gray-600"></i> {{ $brandingName }}
            </a>
            <span class="text-xs font-black uppercase tracking-widest text-gray-500">Checkout</span>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-12">
        <h1 class="text-4xl font-black tracking-tighter mb-12 uppercase italic text-center">Data Pengiriman</h1>

        <form action="{{ route('tenant.checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            @csrf
            <div class="lg:col-span-2 space-y-6">
                <div class="glass p-8 rounded-3xl space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Nama Lengkap</label>
                            <input type="text" name="customer_name" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Alamat Email</label>
                            <input type="email" name="customer_email" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 transition-all">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Nomor WhatsApp</label>
                        <input type="text" name="customer_phone" placeholder="Contoh: 08123456789" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Alamat Lengkap</label>
                        <textarea name="customer_address" rows="4" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 transition-all"></textarea>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Metode Pembayaran</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 glass rounded-xl cursor-pointer hover:border-blue-500/50 transition-all">
                                <input type="radio" name="payment_method" value="bank_transfer" required class="w-4 h-4 text-blue-600 bg-black border-white/10 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-white">Transfer Bank (Manual)</span>
                            </label>
                            <label class="relative flex items-center p-4 glass rounded-xl cursor-pointer hover:border-blue-500/50 transition-all">
                                <input type="radio" name="payment_method" value="cod" required class="w-4 h-4 text-blue-600 bg-black border-white/10 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-white">Bayar di Tempat (COD)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass p-8 rounded-3xl sticky top-28 border-emerald-500/20">
                    <h2 class="text-xl font-black text-white uppercase tracking-widest mb-6 italic">Total Bayar</h2>
                    <div class="space-y-4 mb-8">
                        @foreach($cart as $item)
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                            <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="h-px bg-white/10 my-4"></div>
                        <div class="flex justify-between text-white font-black text-2xl italic">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-5 bg-emerald-600 text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/30 hover:bg-emerald-700 transition-all">
                        Konfirmasi Pesanan <i class="fa-solid fa-circle-check ml-2"></i>
                    </button>
                    <p class="text-[10px] text-gray-600 mt-6 text-center italic">Dengan menekan tombol di atas, Anda setuju untuk melakukan pembelian.</p>
                </div>
            </div>
        </form>
    </main>
</body>
</html>
