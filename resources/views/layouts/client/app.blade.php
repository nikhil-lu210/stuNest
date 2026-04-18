<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name').' | Premium Student Housing')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('clients/js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('clients/css/base.css') }}">
    @livewireStyles
    @stack('styles')

    <script src="{{ asset('clients/js/home-search-form.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('head')
</head>
<body class="@yield('body_class', 'bg-white font-sans text-gray-900 antialiased')">
    @if (session('success'))
        <div
            class="fixed top-20 left-1/2 z-[70] max-w-md -translate-x-1/2 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-3 text-center text-sm font-medium text-emerald-900 shadow-lg"
            role="status"
        >
            {{ session('success') }}
        </div>
    @endif

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireScripts
    @stack('scripts')
    <script>
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
