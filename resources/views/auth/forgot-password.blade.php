@php
    $rightImage = file_exists(public_path('assets/images/forgot-password.gif'))
        ? 'assets/images/forgot-password.gif'
        : (file_exists(public_path('assets/img/animations/forgot_password.gif')) ? 'assets/img/animations/forgot_password.gif' : 'assets/img/animations/login.gif');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | {{ __('Forgot Password') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('Logo/stunest_favicon.ico') }}" />
    <link rel="apple-touch-icon" href="{{ asset('Logo/stunest_favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('clients/js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('clients/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('clients/css/dashboard.css') }}" />
</head>
<body class="min-h-screen antialiased text-gray-900 bg-white font-sans">
    <div class="min-h-screen flex flex-col lg:grid lg:grid-cols-2">
        <div class="flex flex-1 flex-col justify-center items-center w-full p-8 md:p-16 bg-white order-1">
            <div class="w-full max-w-md">
                <a href="{{ url('/') }}" class="inline-block mb-10">
                    <x-stunest-logo class="h-11 w-auto sm:h-12 max-w-[min(90vw,360px)] object-left object-contain" />
                </a>

                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">
                    {{ __('Forgot Password?') }}
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __("No worries, we'll send you reset instructions.") }}
                </p>

                @if (session('status'))
                    <p class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm text-emerald-800" role="status">
                        {{ session('status') }}
                    </p>
                @endif

                <form class="mt-8 space-y-5" method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('Email') }} <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                            placeholder="{{ __('name@example.com') }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('email') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                    >
                        {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                        ← {{ __('Back to log in') }}
                    </a>
                </p>
            </div>
        </div>

        <div
            class="hidden lg:flex relative flex-col items-center justify-center w-full p-10 xl:p-16 order-2 bg-gradient-to-br from-primary-50 via-primary-100/80 to-primary-100/90 min-h-[40vh] lg:min-h-0"
        >
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-primary-100/50 blur-3xl" aria-hidden="true"></div>
                <div class="absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-primary-200/50 blur-3xl" aria-hidden="true"></div>
            </div>
            <div class="relative flex flex-col items-center text-center max-w-lg">
                <div class="rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-gray-900/5 backdrop-blur-sm">
                    <img
                        src="{{ asset($rightImage) }}"
                        alt=""
                        class="max-w-md w-full h-auto object-contain rounded-xl"
                    />
                </div>
                <p class="mt-8 text-lg font-medium text-gray-800 max-w-sm leading-snug">
                    {{ __('We take account security seriously — reset your password in a few clicks.') }}
                </p>
                <p class="mt-2 text-sm text-gray-500">StuNest</p>
            </div>
        </div>
    </div>

    @include('sweetalert::alert')
</body>
</html>
