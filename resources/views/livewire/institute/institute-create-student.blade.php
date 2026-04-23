<div>
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Create student account') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('New accounts are created as unverified until your team approves them in Unverified Students.') }}</p>
        </div>

        <form wire:submit="save" class="p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="student_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Student name') }} <span class="text-red-600">*</span></label>
                    <input
                        id="student_name"
                        type="text"
                        wire:model="student_name"
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all @error('student_name') border-red-300 @enderror"
                        placeholder="{{ __('First and last name') }}"
                        autocomplete="name"
                    >
                    @error('student_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Contact no.') }} <span class="text-red-600">*</span></label>
                    <input
                        id="phone"
                        type="text"
                        wire:model="phone"
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all @error('phone') border-red-300 @enderror"
                        autocomplete="tel"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">{{ __('WhatsApp no.') }} <span class="text-gray-400 font-normal">({{ __('optional') }})</span></label>
                    <input
                        id="whatsapp"
                        type="text"
                        wire:model="whatsapp"
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all @error('whatsapp') border-red-300 @enderror"
                        autocomplete="tel"
                    >
                    @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="country_code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Student country') }} <span class="text-red-600">*</span></label>
                    <select
                        id="country_code"
                        wire:model="country_code"
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all @error('country_code') border-red-300 @enderror"
                    >
                        <option value="">{{ __('Select country') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('The country or region the student is from (nationality / home country).') }}</p>
                    @error('country_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institute_location_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Branch / campus') }} <span class="text-red-600">*</span></label>
                    <select
                        id="institute_location_id"
                        wire:model="institute_location_id"
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all @error('institute_location_id') border-red-300 @enderror"
                        @if ($locations->isEmpty()) disabled @endif
                    >
                        <option value="">{{ __('Select branch') }}</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    @if ($locations->isEmpty())
                        <p class="mt-1 text-sm text-amber-600">{{ __('No branches are configured for your institution. Contact support.') }}</p>
                    @endif
                    @error('institute_location_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="email_prefix" class="block text-sm font-medium text-gray-700 mb-1">{{ __('University email') }} <span class="text-red-600">*</span></label>
                    <div class="flex rounded-lg border border-gray-200 overflow-hidden focus-within:ring-1 focus-within:ring-black focus-within:border-black transition-all @error('email_prefix') border-red-300 @enderror @error('email') border-red-300 @enderror">
                        <input
                            id="email_prefix"
                            type="text"
                            wire:model="email_prefix"
                            class="min-w-0 flex-1 px-4 py-2 bg-white text-sm border-0 focus:ring-0 focus:outline-none"
                            placeholder="{{ __('username') }}"
                            autocomplete="off"
                            inputmode="email"
                        >
                        <span class="inline-flex items-center px-3 py-2 bg-gray-50 text-sm text-gray-600 border-l border-gray-200 shrink-0">{{ $emailSuffix }}</span>
                    </div>
                    @error('email_prefix')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }} <span class="text-red-600">*</span></label>
                    <input
                        id="password"
                        type="password"
                        wire:model="password"
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all @error('password') border-red-300 @enderror"
                        autocomplete="new-password"
                        minlength="8"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm password') }} <span class="text-red-600">*</span></label>
                    <input
                        id="password_confirmation"
                        type="password"
                        wire:model="password_confirmation"
                        class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all"
                        autocomplete="new-password"
                        minlength="8"
                    >
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-end pt-2 border-t border-gray-100">
                <a
                    href="{{ route('client.institute.students.index') }}"
                    wire:navigate
                    class="inline-flex justify-center px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 bg-white text-gray-900 hover:bg-gray-50 transition-colors"
                >
                    {{ __('Cancel') }}
                </a>
                <button
                    type="submit"
                    class="inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-black text-white hover:bg-gray-800 transition-colors shadow-sm disabled:opacity-60"
                    wire:loading.attr="disabled"
                    @if ($locations->isEmpty()) disabled @endif
                >
                    <span wire:loading.remove wire:target="save">{{ __('Create account') }}</span>
                    <span wire:loading wire:target="save">{{ __('Creating…') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
