<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Criar nova loja</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/filament.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('scripts')
</head>

<body class="bg-gradient-to-b from-cyan-500 to-blue-500 antialiased">
    <div class="flex min-h-screen items-center justify-center p-6">
        <main class="card m-auto w-[500px] rounded-lg bg-white p-7 shadow-2xl">
            {{ $slot }}
        </main>
    </div>

    @livewire('notifications')
    @livewireScripts
</body>

</html>
