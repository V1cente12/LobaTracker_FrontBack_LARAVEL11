<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'La Baraja Mágica' }}</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo_loba.ico') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

</head>
<style>
    body {
        background-image: url('/images/dash.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>
<body class="font-sans antialiased bg-gray-200"> <!-- Aplicamos el fondo azul a todo el body -->
    <x-banner />

    <div class="min-h-screen"> <!-- Esta clase aún mantiene la estructura, pero sin fondo blanco -->
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow"> <!-- Cambiar el fondo si es necesario -->
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
</body>
</html>
