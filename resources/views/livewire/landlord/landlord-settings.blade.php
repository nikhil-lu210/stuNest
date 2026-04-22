<div class="w-full max-w-full min-w-0 space-y-6">
    {{-- Card 1: Profile — matches landlord_dashboard white cards: border, rounded-2xl, shadow-sm --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Profile Information') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Update your photo, contact details, and company name.') }}</p>
        </div>
        <div class="space-y-6 px-6 py-6">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                <div class="shrink-0">
                    <p class="mb-2 text-sm font-medium text-gray-700">{{ __('Profile photo') }}</p>
                    <div class="relative h-24 w-24 overflow-hidden rounded-full bg-gray-100 ring-1 ring-gray-200">
                        @if ($newAvatar)
                            <img src="{{ $newAvatar->temporaryUrl() }}" alt="" class="h-full w-full object-cover">
                        @elseif ($user->hasMedia('avatar'))
                            <img src="{{ $user->getFirstMediaUrl('avatar', 'thumb') ?: $user->getFirstMediaUrl('avatar') }}" alt="" class="h-full w-full object-cover">
                        @else
                            @php
                                $ini = strtoupper(mb_substr($user->first_name ?? '', 0, 1) . mb_substr($user->last_name ?? '', 0, 1));
                            @endphp
                            <span class="flex h-full w-full items-center justify-center text-xl font-semibold text-gray-600">{{ $ini ?: '?' }}</span>
                        @endif
                    </div>
                    <label for="landlord-settings-avatar" class="mt-3 inline-flex cursor-pointer">
                        <span class="text-sm font-semibold text-gray-900 hover:text-black">{{ __('Change photo') }}</span>
                        <input
                            id="landlord-settings-avatar"
                            type="file"
                            wire:model="newAvatar"
                            accept="image/jpeg,image/png"
                            class="sr-only"
                        >
                    </label>
                    <div wire:loading wire:target="newAvatar" class="mt-1 text-xs text-gray-500">{{ __('Uploading…') }}</div>
                    @error('newAvatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid w-full flex-1 grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="landlord_first_name" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('First name') }}</label>
                        <input
                            id="landlord_first_name"
                            type="text"
                            wire:model.blur="first_name"
                            autocomplete="given-name"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors outline-none focus:border-black focus:ring-1 focus:ring-black"
                        >
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="landlord_last_name" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Last name') }}</label>
                        <input
                            id="landlord_last_name"
                            type="text"
                            wire:model.blur="last_name"
                            autocomplete="family-name"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors outline-none focus:border-black focus:ring-1 focus:ring-black"
                        >
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="landlord_phone" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Phone number') }}</label>
                        <input
                            id="landlord_phone"
                            type="text"
                            wire:model.blur="phone"
                            autocomplete="tel"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors outline-none focus:border-black focus:ring-1 focus:ring-black"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="landlord_company_name" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Company name') }} <span class="font-normal text-gray-400">({{ __('optional') }})</span></label>
                        <input
                            id="landlord_company_name"
                            type="text"
                            wire:model.blur="company_name"
                            autocomplete="organization"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors outline-none focus:border-black focus:ring-1 focus:ring-black"
                        >
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="landlord_email_readonly" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Email address') }}</label>
                        <input
                            id="landlord_email_readonly"
                            type="email"
                            value="{{ $user->email }}"
                            readonly
                            disabled
                            class="w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500"
                        >
                        <p class="mt-1.5 text-xs text-gray-400">{{ __('Email is tied to your account and cannot be changed here.') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-gray-50 pt-2 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    wire:click="updateProfile"
                    wire:loading.attr="disabled"
                    wire:target="updateProfile"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-black px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 disabled:opacity-60 sm:w-auto"
                >
                    <span wire:loading.remove wire:target="updateProfile">{{ __('Save Profile') }}</span>
                    <span wire:loading wire:target="updateProfile">{{ __('Saving…') }}</span>
                </button>
            </div>
        </div>
    </section>

    {{-- Card 2: Security --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Security & Password') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Use a strong password you do not reuse on other sites.') }}</p>
        </div>
        <div class="space-y-5 px-6 py-6">
            <div>
                <label for="landlord_current_password" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Current password') }}</label>
                <input
                    id="landlord_current_password"
                    type="password"
                    wire:model.blur="current_password"
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors outline-none focus:border-black focus:ring-1 focus:ring-black"
                >
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="landlord_new_password" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('New password') }}</label>
                <input
                    id="landlord_new_password"
                    type="password"
                    wire:model.blur="new_password"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors outline-none focus:border-black focus:ring-1 focus:ring-black"
                >
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="landlord_new_password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Confirm new password') }}</label>
                <input
                    id="landlord_new_password_confirmation"
                    type="password"
                    wire:model.blur="new_password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 transition-colors outline-none focus:border-black focus:ring-1 focus:ring-black"
                >
                @error('new_password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-gray-50 pt-2 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    wire:click="updatePassword"
                    wire:loading.attr="disabled"
                    wire:target="updatePassword"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-black px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 disabled:opacity-60 sm:w-auto"
                >
                    <span wire:loading.remove wire:target="updatePassword">{{ __('Update Password') }}</span>
                    <span wire:loading wire:target="updatePassword">{{ __('Saving…') }}</span>
                </button>
            </div>
        </div>
    </section>

    {{-- Card 3: Notifications --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Notification Preferences') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Choose which emails we send you.') }}</p>
        </div>
        <div class="space-y-6 px-6 py-6">
            <div class="flex flex-col gap-3 py-1 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                <span class="text-sm text-gray-800 sm:pr-2">{{ __('Email me when I receive a new application.') }}</span>
                <label class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-full bg-gray-200 transition-colors has-[:checked]:bg-black focus-within:ring-2 focus-within:ring-gray-900 focus-within:ring-offset-2">
                    <input type="checkbox" wire:model.live="notify_new_application" class="peer sr-only">
                    <span class="pointer-events-none absolute top-0.5 left-0.5 h-6 w-6 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                </label>
            </div>
            <div class="flex flex-col gap-3 py-1 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                <span class="text-sm text-gray-800 sm:pr-2">{{ __('Email me when a student sends a message.') }}</span>
                <label class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-full bg-gray-200 transition-colors has-[:checked]:bg-black focus-within:ring-2 focus-within:ring-gray-900 focus-within:ring-offset-2">
                    <input type="checkbox" wire:model.live="notify_student_message" class="peer sr-only">
                    <span class="pointer-events-none absolute top-0.5 left-0.5 h-6 w-6 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                </label>
            </div>
            <div class="flex flex-col gap-3 py-1 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                <span class="text-sm text-gray-800 sm:pr-2">{{ __('Email me with platform updates and tips.') }}</span>
                <label class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-full bg-gray-200 transition-colors has-[:checked]:bg-black focus-within:ring-2 focus-within:ring-gray-900 focus-within:ring-offset-2">
                    <input type="checkbox" wire:model.live="notify_platform_updates" class="peer sr-only">
                    <span class="pointer-events-none absolute top-0.5 left-0.5 h-6 w-6 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                </label>
            </div>
            @error('notify_new_application')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('notify_student_message')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('notify_platform_updates')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex flex-col-reverse gap-2 border-t border-gray-50 pt-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    wire:click="savePreferences"
                    wire:loading.attr="disabled"
                    wire:target="savePreferences"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition-colors hover:bg-gray-50 disabled:opacity-60 sm:w-auto"
                >
                    <span wire:loading.remove wire:target="savePreferences">{{ __('Save Preferences') }}</span>
                    <span wire:loading wire:target="savePreferences">{{ __('Saving…') }}</span>
                </button>
            </div>
        </div>
    </section>
</div>
