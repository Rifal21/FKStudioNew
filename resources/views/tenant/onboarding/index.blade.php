<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onboarding - {{ tenant('branding_name') ?? 'FKStudio' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-600; }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-950 flex flex-col items-center justify-center p-6">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-700/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-4xl" x-data="{ selected: null }">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter mb-4">
                Pilih <span class="gradient-text">Tujuan Website</span> Anda
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto">Kami akan menyesuaikan struktur dan desain website Anda berdasarkan apa yang menjadi fokus utama bisnis Anda saat ini.</p>
        </div>

        <form method="POST" action="{{ route('tenant.onboarding.store') }}" class="space-y-8">
            @csrf
            <input type="hidden" name="site_type" x-model="selected">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Branding Card -->
                <button type="button" @click="selected = 'branding'" 
                    :class="selected === 'branding' ? 'border-blue-500 bg-blue-900/20' : 'border-white/10 hover:border-white/30 glass'"
                    class="relative p-8 rounded-3xl border-2 transition-all duration-300 text-left group overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-0 transition-opacity" :class="selected === 'branding' ? 'opacity-100' : ''">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    
                    <div class="w-16 h-16 bg-blue-500/20 rounded-2xl flex items-center justify-center text-blue-400 text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-gem"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black text-white mb-2">Company Profile & Branding</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">Sempurna untuk membangun kepercayaan. Fokus pada menampilkan portofolio, layanan, tim, dan cerita di balik bisnis Anda.</p>
                    
                    <ul class="space-y-2 text-xs font-bold text-slate-300">
                        <li><i class="fa-solid fa-check text-blue-400 mr-2"></i> Desain Elegan & Profesional</li>
                        <li><i class="fa-solid fa-check text-blue-400 mr-2"></i> Galeri Portofolio</li>
                        <li><i class="fa-solid fa-check text-blue-400 mr-2"></i> Formulir Kontak / Leads</li>
                    </ul>
                </button>

                <!-- Sales Card -->
                <button type="button" @click="selected = 'sales'"
                    :class="selected === 'sales' ? 'border-emerald-500 bg-emerald-900/20' : 'border-white/10 hover:border-white/30 glass'"
                    class="relative p-8 rounded-3xl border-2 transition-all duration-300 text-left group overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-0 transition-opacity" :class="selected === 'sales' ? 'opacity-100' : ''">
                        <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black text-white mb-2">Penjualan & Konversi</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">Dirancang khusus untuk menghasilkan penjualan (Sales Page). Fokus pada produk, testimoni, dan tombol pembelian langsung.</p>
                    
                    <ul class="space-y-2 text-xs font-bold text-slate-300">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Desain High-Conversion</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Showcase Produk & Harga</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Tombol Order WhatsApp/Link</li>
                    </ul>
                </button>
            </div>

            <div class="flex justify-center pt-8">
                <button type="submit" :disabled="!selected"
                    :class="!selected ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 hover:shadow-blue-500/25'"
                    class="px-12 py-4 bg-white text-slate-900 rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-xl">
                    Lanjutkan ke Dashboard <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</body>
</html>
