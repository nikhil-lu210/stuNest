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

            @if ($step === 1)
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">{{ __('Register your institution') }}</h1>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('Create the official StuNest profile for your university. We will verify your work email before activating your account.') }}
                </p>

                <form class="mt-8 space-y-5" wire:submit="sendOtp">
                    <div>
                        <label for="institute_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Institution name') }} <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="institute_name"
                            wire:model.blur="institute_name"
                            autocomplete="organization"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('institute_name') border-red-300 @enderror"
                            placeholder="{{ __('e.g. University of Example') }}"
                        />
                        @error('institute_name')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="institute_email_code" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Official student email domain') }} <span class="text-red-500">*</span></label>
                        <p class="mb-1.5 text-xs text-gray-500">{{ __('The domain students use for university email (same as on your student cards), e.g. nup.ac.cy') }}</p>
                        <input
                            type="text"
                            id="institute_email_code"
                            wire:model.blur="institute_email_code"
                            autocomplete="off"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('institute_email_code') border-red-300 @enderror"
                            placeholder="nup.ac.cy"
                        />
                        @error('institute_email_code')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Department / office') }} <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="department"
                            wire:model.blur="department"
                            autocomplete="organization-title"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('department') border-red-300 @enderror"
                            placeholder="{{ __('e.g. Accommodation & Welfare') }}"
                        />
                        @error('department')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rep_first_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Your first name') }} <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                id="rep_first_name"
                                wire:model.blur="first_name"
                                autocomplete="given-name"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('first_name') border-red-300 @enderror"
                            />
                            @error('first_name')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="rep_last_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Your last name') }} <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                id="rep_last_name"
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
                        <label for="rep_email" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Work email') }} <span class="text-red-500">*</span></label>
                        <p class="mb-1.5 text-xs text-gray-500">{{ __('Must be on your official institution domain so we can verify you.') }}</p>
                        <input
                            type="email"
                            id="rep_email"
                            wire:model.blur="email"
                            autocomplete="email"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('email') border-red-300 @enderror"
                        />
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rep_phone" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Phone number') }} <span class="text-red-500">*</span></label>
                        <input
                            type="tel"
                            id="rep_phone"
                            wire:model.blur="phone"
                            autocomplete="tel"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('phone') border-red-300 @enderror"
                        />
                        @error('phone')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rep_password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Password') }} <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            id="rep_password"
                            wire:model="password"
                            autocomplete="new-password"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password') border-red-300 @enderror"
                        />
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rep_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm password') }} <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            id="rep_password_confirmation"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password_confirmation') border-red-300 @enderror"
                        />
                        @error('password_confirmation')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="sendOtp">{{ __('Register & send code') }}</span>
                        <span wire:loading wire:target="sendOtp">{{ __('Sending…') }}</span>
                    </button>
                </form>
            @else
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">{{ __('Verify your email') }}</h1>
                <p class="mt-2 text-sm text-gray-500">{{ __('Enter the 6-digit code we sent to your work inbox.') }}</p>

                <form class="mt-8 space-y-6" wire:submit.prevent="verifyAndRegister">
                    <div>
                        <p id="inst-reg-otp-label" class="sr-only">{{ __('Enter the 6-digit verification code') }}</p>
                        <div
                            class="flex flex-wrap justify-center gap-2"
                            x-data
                            role="group"
                            aria-labelledby="inst-reg-otp-label"
                        >
                            @foreach (range(0, 5) as $i)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    id="inst-reg-otp-{{ $i }}"
                                    @class([
                                        'h-12 w-11 sm:w-12 rounded-xl border text-center text-xl font-semibold tabular-nums text-gray-900 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10',
                                        'border-red-300' => $errors->has('enteredOtp'),
                                        'border-gray-200' => ! $errors->has('enteredOtp'),
                                    ])
                                    @if ($i === 0)
                                        maxlength="6"
                                        autocomplete="one-time-code"
                                    @else
                                        maxlength="1"
                                        autocomplete="off"
                                    @endif
                                    wire:model.live="otp{{ $i }}"
                                    x-on:keydown="if ($event.key === 'Backspace' &amp;&amp; ! $el.value) { $event.preventDefault(); $el.previousElementSibling?.focus() }"
                                    x-on:input="if ($el.value &amp;&amp; $el.value.length === 1) { $el.nextElementSibling?.focus() }"
                                />
                            @endforeach
                        </div>
                        @error('enteredOtp')
                            <p class="mt-2 text-sm text-center text-red-500" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="button"
                        class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50"
                        wire:click="verifyAndRegister"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="verifyAndRegister">{{ __('Verify registration code') }}</span>
                        <span wire:loading wire:target="verifyAndRegister">{{ __('Verifying…') }}</span>
                    </button>
                </form>
            @endif

            <p class="mt-8 text-center text-sm text-gray-500">
                <a href="{{ route('login') }}" class="font-medium text-gray-900 hover:text-gray-600" wire:navigate>{{ __('Back to log in') }}</a>
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
                {{ __('Connect your campus with trusted student housing in one place.') }}
            </p>
            <p class="mt-2 text-sm text-gray-500">{{ config('app.name') }}</p>
        </div>
    </div>
</div>
