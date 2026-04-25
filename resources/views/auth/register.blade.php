@php
    $rightImage = file_exists(public_path('assets/images/register.gif'))
        ? 'assets/images/register.gif'
        : (file_exists(public_path('assets/img/animations/register.gif')) ? 'assets/img/animations/register.gif' : 'assets/img/animations/login.gif');
    $regRole = old('role', 'student');
    if (! in_array($regRole, ['student', 'landlord', 'institute'], true)) {
        $regRole = 'student';
    }
    $alpineState = ['role' => $regRole];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | {{ __('Create an Account') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('clients/js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('clients/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('clients/css/dashboard.css') }}" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen antialiased text-gray-900 bg-white font-sans">
    <div class="min-h-screen flex flex-col lg:grid lg:grid-cols-2">
        <div class="flex flex-1 flex-col justify-center items-center w-full p-8 md:p-16 bg-white order-1">
            <div class="w-full max-w-lg">
                <a href="{{ url('/') }}" class="inline-block mb-10">
                    <img
                        src="{{ asset('Logo/logo_black_01.png') }}"
                        alt="{{ config('app.name') }}"
                        class="h-8 w-auto"
                    />
                </a>

                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">
                    {{ __('Create an Account') }}
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('Join StuNest today and find your perfect student home.') }}
                </p>

                <form
                    id="formRegister"
                    class="mt-6 space-y-5"
                    method="POST"
                    action="{{ route('register') }}"
                    x-data='@json($alpineState)'
                >
                    @csrf
                    <input type="hidden" name="role" x-model="role" />

                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ __('I am registering as') }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <button
                                type="button"
                                @click="role = 'student'"
                                :class="role === 'student' ? 'ring-2 ring-gray-900 bg-gray-900 text-white shadow-sm' : 'ring-1 ring-gray-200 bg-white text-gray-800 hover:bg-gray-50'"
                                class="rounded-xl px-3 py-2.5 text-sm font-medium text-center transition-all"
                            >
                                {{ __('Student') }}
                            </button>
                            <button
                                type="button"
                                @click="role = 'landlord'"
                                :class="role === 'landlord' ? 'ring-2 ring-gray-900 bg-gray-900 text-white shadow-sm' : 'ring-1 ring-gray-200 bg-white text-gray-800 hover:bg-gray-50'"
                                class="rounded-xl px-3 py-2.5 text-sm font-medium text-center transition-all"
                            >
                                {{ __('Landlord') }}
                            </button>
                            <button
                                type="button"
                                @click="role = 'institute'"
                                :class="role === 'institute' ? 'ring-2 ring-gray-900 bg-gray-900 text-white shadow-sm' : 'ring-1 ring-gray-200 bg-white text-gray-800 hover:bg-gray-50'"
                                class="rounded-xl px-3 py-2.5 text-sm font-medium text-center transition-all"
                            >
                                {{ __('Institute') }}
                            </button>
                        </div>
                        @error('role')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('First name') }} <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                name="first_name"
                                id="first_name"
                                value="{{ old('first_name') }}"
                                required
                                autocomplete="given-name"
                                placeholder="{{ __('First name') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('first_name') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            @error('first_name')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('Last name') }} <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                name="last_name"
                                id="last_name"
                                value="{{ old('last_name') }}"
                                required
                                autocomplete="family-name"
                                placeholder="{{ __('Last name') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('last_name') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            @error('last_name')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

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
                            placeholder="{{ __('name@example.com') }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('email') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div
                        x-cloak
                        x-show="role === 'student'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="space-y-4"
                    >
                        <div>
                            <label for="student_id_number" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('Student ID number') }} <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                name="student_id_number"
                                id="student_id_number"
                                value="{{ old('student_id_number') }}"
                                :required="role === 'student'"
                                placeholder="{{ __('Your student ID') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('student_id_number') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            @error('student_id_number')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('University / Institute') }} <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <select
                                name="institution_id"
                                id="institution_id"
                                :required="role === 'student'"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('institution_id') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            >
                                <option value="">{{ __('Select your institution') }}</option>
                                @foreach ($institutes as $institute)
                                    <option value="{{ $institute->id }}" @selected((string) old('institution_id') === (string) $institute->id)>
                                        {{ $institute->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div
                        x-cloak
                        x-show="role === 'landlord'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="space-y-4"
                    >
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('Company name') }} <span class="text-gray-400">({{ __('optional') }})</span>
                            </label>
                            <input
                                type="text"
                                name="company_name"
                                id="company_name"
                                value="{{ old('company_name') }}"
                                placeholder="{{ __('Company or trading name') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('company_name') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            @error('company_name')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div
                        x-cloak
                        x-show="role === 'institute'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="space-y-4"
                    >
                        <div>
                            <label for="institute_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('Institute / University name') }} <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                name="institute_name"
                                id="institute_name"
                                value="{{ old('institute_name') }}"
                                :required="role === 'institute'"
                                placeholder="{{ __('Official institution name') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('institute_name') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            @error('institute_name')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('Department') }} <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                name="department"
                                id="department"
                                value="{{ old('department') }}"
                                :required="role === 'institute'"
                                placeholder="{{ __('Your department or faculty') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('department') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            @error('department')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div
                        x-cloak
                        x-show="role === 'landlord' || role === 'institute'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('Phone number') }} <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            value="{{ old('phone') }}"
                            :required="role === 'landlord' || role === 'institute'"
                            autocomplete="tel"
                            placeholder="{{ __('Phone number') }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('phone') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('phone')
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
                                autocomplete="new-password"
                                placeholder="{{ __('Create a password') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 pr-11 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            <button
                                type="button"
                                class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                data-password-toggle-for="password"
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

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('Confirm Password') }} <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="{{ __('Confirm your password') }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 pr-11 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password_confirmation') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            <button
                                type="button"
                                class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                data-password-toggle-for="password_confirmation"
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
                        @error('password_confirmation')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                    >
                        {{ __('Sign Up') }}
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="font-medium text-gray-900 hover:text-gray-600 transition-colors">
                        {{ __('Log in') }}
                    </a>
                </p>
            </div>
        </div>

        <div
            class="hidden lg:flex relative flex-col items-center justify-center w-full p-10 xl:p-16 order-2 bg-gradient-to-br from-indigo-50 via-sky-50/80 to-violet-50/90 min-h-[40vh] lg:min-h-0"
        >
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-indigo-100/50 blur-3xl" aria-hidden="true"></div>
                <div class="absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-sky-100/60 blur-3xl" aria-hidden="true"></div>
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
                    {{ __('Start your next chapter in a home built for students.') }}
                </p>
                <p class="mt-2 text-sm text-gray-500">StuNest</p>
            </div>
        </div>
    </div>

    <script>
        (function () {
            document.querySelectorAll('.password-toggle[data-password-toggle-for]').forEach(function (btn) {
                var id = btn.getAttribute('data-password-toggle-for');
                var input = document.getElementById(id);
                if (!input) return;
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
            });
        })();
    </script>

    <style> [x-cloak] { display: none !important; } </style>
    @include('sweetalert::alert')
</body>
</html>
