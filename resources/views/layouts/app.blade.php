<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @isset($title)
    <title class="bg-white shadow">
        {{ $title }}
    </title>
    @endisset
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="relative min-h-screen bg-grey-300">
        @include('layouts.navigation')
        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset
        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    {{-- Kirim data genres & languages ke modal --}}
    @php
        $discoverService = app(\App\Services\DiscoverService::class);
        $genres    = $genres ?? $discoverService->getGenres();
        $languages = $languages ?? $discoverService->getLanguages();
    @endphp

    <x-discover-modal :genres="$genres" :languages="$languages" />
</body>

</html>