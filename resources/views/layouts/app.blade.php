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
<body class="font-sans bg-[#212121] antialiased overflow-x-hidden">
    @include('layouts.navigation')
    <div class="relative min-h-screen bg-[#212121]">
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
    <x-discover-modal />
</body>

</html>