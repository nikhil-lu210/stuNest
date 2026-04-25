@php
    $rightImage = file_exists(public_path('assets/images/register.gif'))
        ? 'assets/images/register.gif'
        : (file_exists(public_path('assets/img/animations/register.gif')) ? 'assets/img/animations/register.gif' : 'assets/img/animations/login.gif');
@endphp
<div class="min-h-screen flex flex-col lg:grid lg:grid-cols-2">
    <div class="flex flex-1 flex-col justify-center items-center w-full p-8 md:p-16 bg-white order-1">
        <div class="w-full max-w-lg">
            <a href="{{ url('/') }}" class="inline-block mb-8" wire:navigate>
                <img
                    src="{{ asset('Logo/logo_black_01.png') }}"
                    alt="{{ config('app.name') }}"
                    class="h-8 w-auto"
                />
            </a>

            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">{{ __('Partner with StuNest') }}</h1>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('List your properties and connect with verified university students.') }}
            </p>

            <form class="mt-8 space-y-5" wire:submit.prevent="register">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="landlord_first_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('First name') }} <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="landlord_first_name"
                            wire:model.blur="first_name"
                            autocomplete="given-name"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('first_name') border-red-300 @enderror"
                        />
                        @error('first_name')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="landlord_last_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Last name') }} <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="landlord_last_name"
                            wire:model.blur="last_name"
                            autocomplete="family-name"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('last_name') border-red-300 @enderror"
                        />
                        @error('last_name')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="landlord_email" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email') }} <span class="text-red-500">*</span></label>
                    <input
                        type="email"
                        id="landlord_email"
                        wire:model.blur="email"
                        autocomplete="email"
                        class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('email') border-red-300 @enderror"
                    />
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="landlord_phone" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Phone number') }} <span class="text-red-500">*</span></label>
                    <input
                        type="tel"
                        id="landlord_phone"
                        wire:model.blur="phone"
                        autocomplete="tel"
                        class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('phone') border-red-300 @enderror"
                    />
                    @error('phone')
                        <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="landlord_company_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('Company / Agency name') }} <span class="font-normal text-gray-400">({{ __('Optional') }})</span>
                    </label>
                    <input
                        type="text"
                        id="landlord_company_name"
                        wire:model.blur="company_name"
                        autocomplete="organization"
                        class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('company_name') border-red-300 @enderror"
                    />
                    @error('company_name')
                        <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="landlord_password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Password') }} <span class="text-red-500">*</span></label>
                    <input
                        type="password"
                        id="landlord_password"
                        wire:model.blur="password"
                        autocomplete="new-password"
                        class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password') border-red-300 @enderror"
                    />
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="landlord_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm password') }} <span class="text-red-500">*</span></label>
                    <input
                        type="password"
                        id="landlord_password_confirmation"
                        wire:model.blur="password_confirmation"
                        autocomplete="new-password"
                        class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="register">{{ __('Create Landlord Account') }}</span>
                    <span wire:loading wire:target="register">{{ __('Creating Account...') }}</span>
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-gray-500">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-medium text-gray-900 hover:text-gray-600" wire:navigate>{{ __('Log in') }}</a>
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
                {{ __('Reach thousands of students looking for vetted, quality accommodation.') }}
            </p>
            <p class="mt-2 text-sm text-gray-500">{{ config('app.name') }}</p>
        </div>
    </div>
</div>
