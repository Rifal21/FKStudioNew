<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja - {{ $brandingName }}</title>
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
            <a href="{{ route('tenant.home') }}" class="text-xl font-black text-white tracking-widest uppercase">
                <i class="fa-solid fa-arrow-left mr-4 text-gray-600"></i> {{ $brandingName }}
            </a>
            <span class="text-xs font-black uppercase tracking-widest text-gray-500">Keranjang Belanja</span>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-12">
        <h1 class="text-4xl font-black tracking-tighter mb-12 uppercase italic">Keranjang Anda</h1>

        @if(count($cart) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2 space-y-6">
                    @foreach($cart as $id => $item)
                    <div class="glass p-6 rounded-2xl flex items-center space-x-6 relative group">
                        <div class="w-20 h-20 rounded-xl bg-black overflow-hidden flex-shrink-0">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-800">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white">{{ $item['name'] }}</h3>
                            <p class="text-sm font-black text-gray-400">Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-white italic">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            <a href="{{ route('tenant.cart.remove', $id) }}" class="text-rose-500 hover:text-rose-400 transition-colors text-xs font-bold uppercase tracking-widest mt-2 block">Hapus</a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-6">
                    <div class="glass p-8 rounded-3xl sticky top-28 border-blue-500/20">
                        <h2 class="text-xl font-black text-white uppercase tracking-widest mb-6">Ringkasan</h2>
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-gray-400 text-sm">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-400 text-sm">
                                <span>Biaya Pengiriman</span>
                                <span class="text-emerald-500">Gratis</span>
                            </div>
                            <div class="h-px bg-white/10 my-4"></div>
                            <div class="flex justify-between text-white font-black text-xl italic">
                                <span>Total</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('tenant.checkout') }}" class="w-full block text-center py-5 bg-blue-600 text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-600/30 hover:bg-blue-700 transition-all">
                            Checkout Sekarang
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="py-20 text-center glass rounded-3xl border-dashed border-2 border-white/10">
                <i class="fa-solid fa-cart-shopping text-6xl text-gray-800 mb-8 block"></i>
                <h2 class="text-2xl font-black text-white mb-4 uppercase">Keranjang Kosong</h2>
                <p class="text-gray-500 mb-8 italic">Sepertinya Anda belum menambahkan produk apapun ke keranjang.</p>
                <a href="{{ route('tenant.home') }}" class="inline-flex items-center px-8 py-4 bg-white text-black font-black uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all">
                    Kembali Belanja
                </a>
            </div>
        @endif
    </main>
</body>
</html>
