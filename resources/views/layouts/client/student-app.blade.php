<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('clients/js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('clients/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('clients/css/dashboard.css') }}">
    @stack('styles')

    @livewireStyles
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.3/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('head')
</head>
<body class="@yield('body_class', 'bg-gray-50 font-sans text-gray-900 antialiased flex h-screen w-full min-h-0 min-w-0 overflow-hidden')">
    @yield('content')

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireScripts
    <script>
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
