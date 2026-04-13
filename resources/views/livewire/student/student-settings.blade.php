<div class="w-full max-w-full min-w-0 space-y-8">
    {{-- Section 1: Profile --}}
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Profile Information') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Update your photo and contact details.') }}</p>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-start gap-6">
                <div class="shrink-0">
                    <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Profile photo') }}</p>
                    <div class="relative h-20 w-20 rounded-full overflow-hidden bg-gray-100 ring-1 ring-gray-200">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" alt="" class="h-full w-full object-cover">
                        @elseif ($user->hasMedia('avatar'))
                            <img src="{{ $user->getFirstMediaUrl('avatar', 'profile_view') }}" alt="" class="h-full w-full object-cover">
                        @else
                            @php
                                $ini = strtoupper(mb_substr($user->first_name ?? '', 0, 1) . mb_substr($user->last_name ?? '', 0, 1));
                            @endphp
                            <span class="flex h-full w-full items-center justify-center text-lg font-semibold text-gray-600">{{ $ini ?: '?' }}</span>
                        @endif
                    </div>
                    <label for="student-settings-avatar" class="mt-3 inline-flex cursor-pointer">
                        <span class="text-sm font-semibold text-gray-900 hover:text-black">{{ __('Change photo') }}</span>
                        <input
                            id="student-settings-avatar"
                            type="file"
                            wire:model="avatar"
                            accept="image/jpeg,image/png"
                            class="sr-only"
                        >
                    </label>
                    <div wire:loading wire:target="avatar" class="text-xs text-gray-500 mt-1">{{ __('Uploading…') }}</div>
                    @error('avatar')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('First name') }}</label>
                        <input
                            wire:model.blur="first_name"
                            type="text"
                            id="first_name"
                            autocomplete="given-name"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors"
                        >
                        @error('first_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Last name') }}</label>
                        <input
                            wire:model.blur="last_name"
                            type="text"
                            id="last_name"
                            autocomplete="family-name"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors"
                        >
                        @error('last_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="email_readonly" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email address') }}</label>
                        <input
                            type="email"
                            id="email_readonly"
                            value="{{ $user->email }}"
                            readonly
                            disabled
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-500 cursor-not-allowed"
                        >
                        <p class="text-xs text-gray-400 mt-1.5">{{ __('Your verified institutional email cannot be changed here.') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Phone number') }}</label>
                        <input
                            wire:model.blur="phone"
                            type="text"
                            id="phone"
                            autocomplete="tel"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors"
                        >
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-50">
                <button
                    type="button"
                    wire:click="updateProfile"
                    wire:loading.attr="disabled"
                    wire:target="updateProfile"
                    class="inline-flex items-center justify-center rounded-xl bg-black px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 disabled:opacity-60 transition-colors"
                >
                    <span wire:loading.remove wire:target="updateProfile">{{ __('Save Profile') }}</span>
                    <span wire:loading wire:target="updateProfile">{{ __('Saving…') }}</span>
                </button>
            </div>
        </div>
    </section>

    {{-- Section 2: Password --}}
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Update Password') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Use a strong password you do not use elsewhere.') }}</p>
        </div>
        <div class="px-6 py-6 space-y-5">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Current password') }}</label>
                <input
                    wire:model.blur="current_password"
                    type="password"
                    id="current_password"
                    autocomplete="current-password"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors"
                >
                @error('current_password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('New password') }}</label>
                <input
                    wire:model.blur="new_password"
                    type="password"
                    id="new_password"
                    autocomplete="new-password"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors"
                >
                @error('new_password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm new password') }}</label>
                <input
                    wire:model.blur="new_password_confirmation"
                    type="password"
                    id="new_password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors"
                >
                @error('new_password_confirmation')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end pt-2 border-t border-gray-50">
                <button
                    type="button"
                    wire:click="updatePassword"
                    wire:loading.attr="disabled"
                    wire:target="updatePassword"
                    class="inline-flex items-center justify-center rounded-xl bg-black px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 disabled:opacity-60 transition-colors"
                >
                    <span wire:loading.remove wire:target="updatePassword">{{ __('Update Password') }}</span>
                    <span wire:loading wire:target="updatePassword">{{ __('Saving…') }}</span>
                </button>
            </div>
        </div>
    </section>

    {{-- Section 3: Notifications --}}
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Notification Preferences') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Choose which emails we send you.') }}</p>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div class="flex items-center justify-between gap-4 py-1">
                <span class="text-sm text-gray-800 pr-2">{{ __('Email me when my application status changes.') }}</span>
                <label class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-full bg-gray-200 transition-colors has-[:checked]:bg-black focus-within:ring-2 focus-within:ring-gray-900 focus-within:ring-offset-2">
                    <input type="checkbox" wire:model.live="notify_application_status" class="peer sr-only">
                    <span class="pointer-events-none absolute left-0.5 top-0.5 h-6 w-6 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                </label>
            </div>
            <div class="flex items-center justify-between gap-4 py-1">
                <span class="text-sm text-gray-800 pr-2">{{ __('Email me when a landlord sends a message.') }}</span>
                <label class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-full bg-gray-200 transition-colors has-[:checked]:bg-black focus-within:ring-2 focus-within:ring-gray-900 focus-within:ring-offset-2">
                    <input type="checkbox" wire:model.live="notify_landlord_message" class="peer sr-only">
                    <span class="pointer-events-none absolute left-0.5 top-0.5 h-6 w-6 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                </label>
            </div>
            @error('notify_application_status')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('notify_landlord_message')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex justify-end pt-4 border-t border-gray-50">
                <button
                    type="button"
                    wire:click="savePreferences"
                    wire:loading.attr="disabled"
                    wire:target="savePreferences"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 disabled:opacity-60 transition-colors"
                >
                    <span wire:loading.remove wire:target="savePreferences">{{ __('Save Preferences') }}</span>
                    <span wire:loading wire:target="savePreferences">{{ __('Saving…') }}</span>
                </button>
            </div>
        </div>
    </section>
</div>
