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

        <style>
            .glass {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        </style>
    </head>
    <body class="font-sans text-white antialiased overflow-x-hidden">
        <div class="min-h-screen relative flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-[#020617]">
            <!-- Animated Background Blobs -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
                <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-blue-600/20 rounded-full blur-[120px] animate-pulse"></div>
                <div class="absolute top-[20%] -right-[5%] w-[40%] h-[40%] bg-indigo-600/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
                <div class="absolute -bottom-[10%] left-[20%] w-[45%] h-[45%] bg-blue-500/10 rounded-full blur-[110px] animate-pulse" style="animation-delay: 4s"></div>
            </div>

            <div class="relative z-10 w-full max-w-[480px]">
                <div class="text-center mb-10">
                    <a href="/" class="inline-block transform hover:scale-110 transition-transform duration-300">
                        @php
                            $settings = \App\Models\SiteSetting::first();
                        @endphp
                        @if($settings && $settings->site_logo)
                            <img src="{{ $settings->logo_url }}" alt="{{ $settings->site_name }}" class="h-16 w-auto mx-auto drop-shadow-2xl">
                        @else
                            <div class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-3xl shadow-2xl flex items-center justify-center text-white text-3xl font-black italic mx-auto">
                                FK
                            </div>
                        @endif
                    </a>
                </div>

                <div class="glass p-8 sm:p-12 rounded-[3rem] shadow-2xl">
                    {{ $slot }}
                </div>

                <div class="mt-12 text-center">
                    <p class="text-slate-500 text-sm font-bold tracking-widest uppercase opacity-60">
                        &copy; {{ date('Y') }} {{ $settings->site_name ?? config('app.name') }}
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
