<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? __('List a Room/Seat')).' | '.config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('clients/js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('clients/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('clients/css/dashboard.css') }}">
    @stack('styles')

    @livewireStyles
    @vite(['resources/js/property-wizard.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.3/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('head')
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased flex h-screen w-full min-h-0 min-w-0 overflow-hidden">
@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    abort_unless($user, 403);
    $user->loadMissing(['institution']);
    $institutionLabel = $user->institution?->name ?? __('Student');
    $avatarUrl = $user->hasMedia('avatar')
        ? $user->getFirstMediaUrl('avatar', 'profile_view')
        : null;
@endphp
    <div class="flex h-screen w-full min-h-0 min-w-0 overflow-hidden">
        @include('layouts.client.partials.student-sidebar-linked', [
            'user' => $user,
            'institutionLabel' => $institutionLabel,
            'avatarUrl' => $avatarUrl,
            'active' => 'listings',
        ])

        <main class="flex min-h-0 min-w-0 flex-1 flex-col bg-gray-50 pt-16 md:pt-0">
            <div class="hidden h-20 w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6 md:px-8 md:flex sticky top-0 z-40">
                <h1 class="text-xl font-semibold tracking-tight">{{ $title ?? __('List a Room/Seat') }}</h1>
                <div class="flex items-center gap-4">
                    <livewire:student.notification-bell />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="mx-auto w-full max-w-6xl px-4 py-4 pb-24 md:px-8 md:py-8 md:pb-8">
                    @if ($user->account_status === \App\Models\User::ACCOUNT_STATUS_UNVERIFIED)
                        <div class="mb-6 flex shrink-0 flex-col items-start justify-between gap-4 rounded-2xl border border-blue-100 bg-blue-50 p-4 sm:flex-row sm:items-center">
                            <div class="flex items-start gap-3">
                                <i data-lucide="shield-alert" class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900">{{ __('Verify your student status') }}</h4>
                                    <p class="mt-0.5 text-sm text-blue-700">{{ __('Upload your university ID to fast-track your housing applications.') }}</p>
                                </div>
                            </div>
                            <button type="button" class="whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                                {{ __('Verify Now') }}
                            </button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

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
