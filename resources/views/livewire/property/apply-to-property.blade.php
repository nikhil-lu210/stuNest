@php
    $availableLabel = $property->availableFromForBooking()->format('j M Y');
@endphp

<div class="relative">
    @if ($isOpen)
        <div
            class="fixed inset-0 z-40 bg-gray-900/50"
            wire:click="closePanel"
            aria-hidden="true"
        ></div>

        <div
            class="fixed inset-y-0 right-0 z-50 flex w-full max-w-lg flex-col bg-white shadow-2xl transition-transform duration-300 ease-out translate-x-0"
            role="dialog"
            aria-modal="true"
            aria-labelledby="apply-panel-title"
        >
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-6 py-5">
                <h2 id="apply-panel-title" class="text-lg font-semibold tracking-tight text-gray-900">
                    {{ __('Apply for this Property') }}
                </h2>
                <button
                    type="button"
                    wire:click="closePanel"
                    class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900"
                    aria-label="{{ __('Close') }}"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="submit" class="flex flex-1 flex-col overflow-y-auto">
                <div class="flex-1 space-y-8 px-6 py-8">
                    @error('application')
                        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ $message }}
                        </div>
                    @enderror

                    <div>
                        <label for="proposed_move_in" class="block text-sm font-medium text-gray-900">{{ __('Move-in date') }}</label>
                        <input
                            id="proposed_move_in"
                            type="date"
                            wire:model="proposed_move_in"
                            class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                        />
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('Available from:') }} {{ $availableLabel }}
                        </p>
                        @error('proposed_move_in')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="proposed_duration" class="block text-sm font-medium text-gray-900">{{ __('Proposed Tenancy Duration') }}</label>
                        <select
                            id="proposed_duration"
                            wire:model.number="proposed_duration"
                            class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                        >
                            @foreach ($this->durationOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('Minimum required:') }} {{ $property->minContractLengthLabel() }}
                        </p>
                        @error('proposed_duration')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message_to_landlord" class="block text-sm font-medium text-gray-900">{{ __('Message to landlord') }}</label>
                        <textarea
                            id="message_to_landlord"
                            wire:model="message_to_landlord"
                            rows="5"
                            placeholder="{{ __('Introduce yourself to the landlord...') }}"
                            class="mt-2 block w-full resize-y rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                        ></textarea>
                        @error('message_to_landlord')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl bg-gray-50 px-5 py-4 text-sm text-gray-700">
                        <p class="font-medium text-gray-900">{{ __('Rent summary') }}</p>
                        <p class="mt-2 text-base">
                            {{ __('Rent:') }}
                            <span class="font-semibold text-gray-900">€{{ $property->rent_amount }}</span>
                            <span class="text-gray-500">/{{ $property->rent_duration }}</span>
                        </p>
                    </div>
                </div>

                <div class="shrink-0 border-t border-gray-100 bg-white px-6 py-6">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-gray-900 px-5 py-3.5 text-base font-semibold text-white shadow-sm transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="submit">{{ __('Submit Application') }}</span>
                        <span wire:loading wire:target="submit">{{ __('Submitting...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
