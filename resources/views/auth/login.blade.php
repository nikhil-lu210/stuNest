<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | {{ __('Log in') }}</title>
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
        {{-- Form column --}}
        <div class="flex flex-1 flex-col justify-center items-center w-full p-8 md:p-16 bg-white order-1">
            <div class="w-full max-w-md">
                <a href="{{ url('/') }}" class="inline-block mb-10">
                    <x-stunest-logo class="h-9 w-auto max-w-[220px] object-left object-contain" />
                </a>

                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">
                    {{ __('Welcome back') }}
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('Please enter your details to sign in') }}
                </p>

                <form id="formAuthentication" class="mt-8 space-y-5" method="POST" action="{{ route('login') }}">
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

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('Password') }} <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="current-password"
                                placeholder="{{ __('Your password') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 pr-11 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            <button
                                type="button"
                                class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                aria-label="{{ __('Show password') }}"
                                data-show-label="{{ __('Show password') }}"
                                data-hide-label="{{ __('Hide password') }}"
                            >
                                <svg class="h-5 w-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg class="h-5 w-5 icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21M12.565 12.565 4.5 4.5" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-row flex-wrap items-center justify-between gap-3 text-sm">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none text-gray-600">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember-me"
                                value="1"
                                class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900/20"
                                {{ old('remember') ? 'checked' : '' }}
                            />
                            <span>{{ __('Remember me') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="font-medium text-gray-900 hover:text-gray-600 transition-colors"
                            >
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                    >
                        {{ __('Log in') }}
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="mt-8 text-center text-sm text-gray-500">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="font-medium text-gray-900 hover:text-gray-600 transition-colors">
                            {{ __('Sign up') }}
                        </a>
                    </p>
                @endif

                @if (app()->environment('local') && config('app.debug'))
                    @php
                        $demoPassword = \Database\Seeders\Demo\CyprusPortalDemoSeeder::DEMO_PASSWORD;
                    @endphp
                    <div class="mt-8">
                        <button
                            type="button"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100"
                            onclick="document.getElementById('localDevCredentialsDialog').showModal()"
                        >
                            {{ __('Show credentials') }}
                        </button>
                    </div>

                    <dialog
                        id="localDevCredentialsDialog"
                        class="w-[min(100%,32rem)] max-h-[min(90vh,36rem)] rounded-2xl border border-gray-200 p-0 shadow-2xl backdrop:bg-gray-900/40"
                    >
                        <form method="dialog" class="flex max-h-[min(90vh,36rem)] flex-col">
                            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                                <h2 class="text-base font-semibold text-gray-900">{{ __('Seeded login credentials') }}</h2>
                                <button
                                    type="submit"
                                    class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                                    aria-label="{{ __('Close') }}"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="overflow-y-auto custom-scrollbar p-5">
                                <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                                    Shown only when <code class="text-gray-700">APP_ENV</code> is <code class="text-gray-700">local</code> and
                                    <code class="text-gray-700">APP_DEBUG</code> is <code class="text-gray-700">true</code>.
                                    Run <code class="text-gray-700">php artisan db:seed</code> (or the Cyprus demo seeder) so these accounts exist.
                                </p>
                                <div class="overflow-x-auto rounded-xl border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-600">{{ __('Role') }}</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-600">{{ __('Email') }}</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-600">{{ __('Password') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white">
                                            <tr><td class="px-3 py-2">Developer</td><td class="px-3 py-2"><code class="text-xs user-select-all">developer@mail.com</code></td><td class="px-3 py-2"><code class="text-xs user-select-all">{{ $demoPassword }}</code></td></tr>
                                            <tr><td class="px-3 py-2">Super Admin</td><td class="px-3 py-2"><code class="text-xs user-select-all">superadmin@mail.com</code></td><td class="px-3 py-2"><code class="text-xs user-select-all">{{ $demoPassword }}</code></td></tr>
                                            <tr><td class="px-3 py-2">Institute representative</td><td class="px-3 py-2"><code class="text-xs user-select-all">institute.rep@ucy.ac.cy</code></td><td class="px-3 py-2"><code class="text-xs user-select-all">{{ $demoPassword }}</code></td></tr>
                                            <tr><td class="px-3 py-2">Student</td><td class="px-3 py-2"><code class="text-xs user-select-all">demo.student@ucy.ac.cy</code></td><td class="px-3 py-2"><code class="text-xs user-select-all">{{ $demoPassword }}</code></td></tr>
                                            <tr><td class="px-3 py-2">Landlord</td><td class="px-3 py-2"><code class="text-xs user-select-all">demo.landlord@stunest.test</code></td><td class="px-3 py-2"><code class="text-xs user-select-all">{{ $demoPassword }}</code></td></tr>
                                            <tr><td class="px-3 py-2">Agent</td><td class="px-3 py-2"><code class="text-xs user-select-all">demo.agent@stunest.test</code></td><td class="px-3 py-2"><code class="text-xs user-select-all">{{ $demoPassword }}</code></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </dialog>
                @endif
            </div>
        </div>

        {{-- Illustration column --}}
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
                        src="{{ asset('assets/img/animations/login.gif') }}"
                        alt=""
                        class="max-w-md w-full h-auto object-contain rounded-xl"
                    />
                </div>
                <p class="mt-8 text-lg font-medium text-gray-800 max-w-sm leading-snug">
                    {{ __('Your home between lectures — find verified student housing in one place.') }}
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    StuNest
                </p>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var input = document.getElementById('password');
            var btn = document.querySelector('.password-toggle');
            if (!input || !btn) return;
            var eye = btn.querySelector('.icon-eye');
            var eyeOff = btn.querySelector('.icon-eye-off');
            btn.addEventListener('click', function () {
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                if (eye && eyeOff) {
                    eye.classList.toggle('hidden', isHidden);
                    eyeOff.classList.toggle('hidden', !isHidden);
                }
                btn.setAttribute('aria-label', isHidden
                    ? (btn.getAttribute('data-hide-label') || 'Hide password')
                    : (btn.getAttribute('data-show-label') || 'Show password'));
            });
        })();
    </script>

    @include('sweetalert::alert')
</body>
</html>
