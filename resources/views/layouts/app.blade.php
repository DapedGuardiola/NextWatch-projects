<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 , maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @isset($title)
    <title class="bg-white shadow">
        {{ $title }}
    </title>
    @endisset
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon / Tab Icon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/brand/logo2.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/brand/logo2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/logo2.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-[#212121] antialiased overflow-x-hidden flex flex-col min-h-screen">
    @include('layouts.navigation')
    
    <!-- Berikan class flex-1 agar footer otomatis tertendang ke bawah jika konten sedikit -->
    <div class="relative flex-1 bg-[#212121]">
        <!-- Page Heading -->
        @isset($header)
        <header class="bg-transparent">
            <div class="max-w-7xl text-white font-bold text-xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset
        
        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- FOOTER START -->
    <footer class="bg-[#1a1a1a] mt-6 border-t border-gray-800 text-gray-400 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                
                <!-- Sisi Kiri: Nama Aplikasi & Copyright -->
                <div class="text-center md:text-left">
                    <p class="text-white font-semibold text-lg mb-1">{{ config('app.name', 'FilmApp') }}</p>
                    <p class="text-sm">&copy; {{ date('Y') }} All rights reserved.</p>
                </div>

                <!-- Sisi Kapan/Tengah: Atribusi TMDB -->
                <div class="flex flex-col sm:flex-row items-center gap-4 max-w-md bg-[#222] p-3 rounded-lg border border-gray-800/60">
                    <!-- Logo TMDB Resmi (Primary Short Version) -->
                    <img src="https://www.themoviedb.org/assets/2/v4/logos/v2/blue_square_2-d537fb228cf3ded904ef09b136fe3fec72548ebc1fea3fbbd1ad9e36364db38b.svg" 
                         alt="TMDB Logo" 
                         class="h-8 object-contain shrink-0" />
                    
                    <!-- Teks Disclaimer Wajib -->
                    <p class="text-xs text-justify leading-relaxed text-gray-400">
                        This product uses the TMDB API but is not endorsed or certified by TMDB.
                    </p>
                </div>

            </div>
        </div>
    </footer>
    <!-- FOOTER END -->

    <x-discover-modal />
</body>

</html>