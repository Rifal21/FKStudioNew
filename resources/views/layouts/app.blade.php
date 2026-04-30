<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body class="font-sans antialiased bg-[#f8fafc] text-[#1e293b]" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.navigation')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Header -->
            <div class="lg:hidden flex items-center justify-between bg-white border-b px-4 py-3 sticky top-0 z-40">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-black italic">FK</div>
                    <span class="font-bold tracking-tight">FKStudio Admin</span>
                </div>
                <button @click="sidebarOpen = true" class="p-2 text-gray-500 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/70 backdrop-blur-md border-b sticky top-0 z-30 hidden lg:block">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-10 flex justify-between items-center">
                        <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $header }}</div>
                        <div class="flex items-center space-x-4">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ now()->format('l, d F Y') }}</span>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-10">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: '#3b82f6'
            });
        </script>
    @endif
</body>

</html>
