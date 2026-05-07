<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandingName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-600; }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-950 flex flex-col items-center justify-center">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-700/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="text-center space-y-6">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-white/5 border border-white/10 rounded-3xl mb-4 shadow-2xl">
            <i class="fa-solid fa-rocket text-4xl text-blue-500 animate-bounce"></i>
        </div>
        <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter">
            <span class="gradient-text">{{ $brandingName }}</span>
        </h1>
        <p class="text-slate-400 text-lg md:text-xl max-w-2xl mx-auto px-6">
            Selamat datang di website baru Anda. Landing page ini berhasil di-generate secara otomatis oleh sistem FKStudio dan siap untuk dikustomisasi.
        </p>
        
        <div class="pt-8 flex justify-center space-x-4">
            @auth
                <a href="{{ route('tenant.dashboard') }}" class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/30">
                    Masuk Dashboard
                </a>
            @else
                <a href="{{ route('tenant.login') }}" class="px-8 py-4 bg-white/10 border border-white/20 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-white/20 transition-all backdrop-blur-md">
                    Login Admin
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
