<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Saya | FKStudio</title>
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

    <div class="container mx-auto px-6 pt-32 pb-12 md:pb-24 max-w-4xl relative z-10">
        <div class="mb-12">
            <a href="{{ route('home') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-6 group">
                <i class="fa-solid fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Beranda
            </a>
            <h1 class="text-3xl md:text-5xl font-black text-white tracking-tighter">
                Pengaturan <span class="gradient-text">Profil Saya.</span>
            </h1>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="mb-8 p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-start space-x-4">
                <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 class="font-bold text-emerald-400">Profil Berhasil Diperbarui</h4>
                    <p class="text-sm text-emerald-400/80 mt-1">Data profil baru Anda telah disimpan dengan sukses.</p>
                </div>
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="mb-8 p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-start space-x-4">
                <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 class="font-bold text-emerald-400">Password Berhasil Diubah</h4>
                    <p class="text-sm text-emerald-400/80 mt-1">Sandi akun Anda telah berhasil diganti.</p>
                </div>
            </div>
        @endif

        <div class="space-y-12">
            <!-- Profile Info Form -->
            <div class="glass p-8 md:p-12 rounded-[2.5rem] border-white/5">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-xl text-blue-500 border border-blue-500/20">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">Informasi Profil</h3>
                        <p class="text-slate-400 text-xs mt-0.5">Perbarui nama pengguna dan alamat email Anda.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="text-xs font-black uppercase tracking-wider text-slate-400">Nama Lengkap</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            @error('name')
                                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="text-xs font-black uppercase tracking-wider text-slate-400">Alamat Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            @error('email')
                                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-colors shadow-lg shadow-blue-600/10">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Form -->
            <div class="glass p-8 md:p-12 rounded-[2.5rem] border-white/5">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-xl text-blue-500 border border-blue-500/20">
                        <i class="fa-solid fa-lock text-blue-500"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">Ubah Password</h3>
                        <p class="text-slate-400 text-xs mt-0.5">Pastikan akun Anda menggunakan kata sandi yang kuat.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="update_password_current_password" class="text-xs font-black uppercase tracking-wider text-slate-400">Password Saat Ini</label>
                            <input id="update_password_current_password" name="current_password" type="password" required autocomplete="current-password"
                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            @error('current_password', 'updatePassword')
                                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="update_password_password" class="text-xs font-black uppercase tracking-wider text-slate-400">Password Baru</label>
                            <input id="update_password_password" name="password" type="password" required autocomplete="new-password"
                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            @error('password', 'updatePassword')
                                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="update_password_password_confirmation" class="text-xs font-black uppercase tracking-wider text-slate-400">Konfirmasi Password</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-colors shadow-lg shadow-blue-600/10">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
