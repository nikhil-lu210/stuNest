@php
    $rightImage = file_exists(public_path('assets/images/register.gif'))
        ? 'assets/images/register.gif'
        : (file_exists(public_path('assets/img/animations/register.gif')) ? 'assets/img/animations/register.gif' : 'assets/img/animations/login.gif');
@endphp
<div class="min-h-screen flex flex-col lg:grid lg:grid-cols-2">
    <div class="flex flex-1 flex-col justify-center items-center w-full p-8 md:p-16 bg-white order-1">
        <div class="w-full max-w-lg">
            <a href="{{ url('/') }}" class="inline-block mb-8" wire:navigate>
                <x-stunest-logo class="h-9 w-auto max-w-[220px] object-left object-contain" />
            </a>

            @if ($step === 1)
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">{{ __('Create a student account') }}</h1>
                <p class="mt-2 text-sm text-gray-500">{{ __('Enter your details. We will email a code to your university address.') }}</p>

                <form class="mt-8 space-y-5" wire:submit="sendOtp">
                    <div
                        class="relative"
                        x-data
                        @click.away="$wire.set('institutePickerOpen', false)"
                    >
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('University / Institute') }} <span class="text-red-500">*</span></label>
                        <button
                            type="button"
                            wire:click="toggleInstitutePicker"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-left text-sm text-gray-900 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                        >
                            @if ($institute_id)
                                {{ data_get(collect($this->institutes)->firstWhere('id', (int) $institute_id), 'name', '') }}
                            @else
                                <span class="text-gray-400">{{ __('Select an institute') }}</span>
                            @endif
                        </button>
                        @if ($institutePickerOpen)
                            <div
                                class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 bg-white p-2 shadow-lg max-h-60 overflow-y-auto"
                                wire:click.stop
                            >
                                <input
                                    type="search"
                                    wire:model.live.debounce.100ms="instituteSearch"
                                    class="mb-2 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm"
                                    placeholder="{{ __('Search…') }}"
                                    autocomplete="off"
                                    wire:keydown.enter.prevent
                                />
                                <div
                                    class="min-h-0"
                                    wire:key="inst-pick-list-{{ md5(json_encode($this->filteredInstituteOptions)) }}"
                                >
                                    @forelse ($this->filteredInstituteOptions as $institute)
                                        <button
                                            type="button"
                                            class="w-full rounded-lg px-2 py-2 text-left text-sm text-gray-800 hover:bg-gray-50"
                                            wire:key="inst-pick-{{ $institute['id'] }}"
                                            wire:click="selectInstitute({{ (int) $institute['id'] }})"
                                        >
                                            {{ $institute['name'] }}
                                        </button>
                                    @empty
                                        <p class="px-2 py-2 text-sm text-gray-500">{{ __('No matches.') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                        @error('institute_id')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('University email') }} <span class="text-red-500">*</span></span>
                        <div class="flex rounded-xl border border-gray-200 bg-white overflow-hidden focus-within:ring-2 focus-within:ring-gray-900/10 focus-within:border-gray-900 @error('email_prefix') border-red-300 @enderror">
                            <input
                                type="text"
                                id="email_prefix"
                                wire:model.blur="email_prefix"
                                autocomplete="username"
                                placeholder="{{ __('you') }}"
                                class="min-w-0 flex-1 border-0 px-3.5 py-2.5 text-sm text-gray-900 focus:ring-0"
                            />
                            <div class="shrink-0 border-l border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-500 select-none">
                                @<span class="text-gray-700">{{ $institute_domain !== '' ? $institute_domain : __('select institute') }}</span>
                            </div>
                        </div>
                        @error('email_prefix')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('First name') }} <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                id="first_name"
                                wire:model.blur="first_name"
                                autocomplete="given-name"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('first_name') border-red-300 @enderror"
                            />
                            @error('first_name')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Last name') }} <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                id="last_name"
                                wire:model.blur="last_name"
                                autocomplete="family-name"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('last_name') border-red-300 @enderror"
                            />
                            @error('last_name')
                                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Password') }} <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            id="password"
                            wire:model="password"
                            autocomplete="new-password"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password') border-red-300 @enderror"
                        />
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm password') }} <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('password_confirmation') border-red-300 @enderror"
                        />
                        @error('password_confirmation')
                            <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="sendOtp">{{ __('Register & send code') }}</span>
                        <span wire:loading wire:target="sendOtp">{{ __('Sending…') }}</span>
                    </button>
                </form>
            @else
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">{{ __('Verify your university email') }}</h1>
                <p class="mt-2 text-sm text-gray-500">{{ __('Enter the 6-digit code we sent to your inbox.') }}</p>

                <form class="mt-8 space-y-6" wire:submit.prevent="verifyAndRegister">
                    <div>
                        <p id="reg-otp-label" class="sr-only">{{ __('Enter the 6-digit verification code') }}</p>
                        <div
                            class="flex flex-wrap justify-center gap-2"
                            x-data
                            role="group"
                            aria-labelledby="reg-otp-label"
                        >
                            @foreach (range(0, 5) as $i)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    id="reg-otp-{{ $i }}"
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
                        class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
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
            <p class="mt-8 text-lg font-medium text-gray-800 max-w-sm leading-snug">{{ __('Find verified student housing in one place.') }}</p>
            <p class="mt-2 text-sm text-gray-500">StuNest</p>
        </div>
    </div>
</div>
