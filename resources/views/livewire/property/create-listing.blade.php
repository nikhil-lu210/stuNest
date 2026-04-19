<div>
    <header class="mb-10 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('StuNest') }}</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
            @if ($editingPropertyId)
                {{ __('Edit listing') }}
            @else
                {{ $this->isStudent() ? __('List a Room/Seat') : __('Create a listing') }}
            @endif
        </h1>
        <p class="mt-2 text-sm text-zinc-500">{{ __('Answer a few steps — no long descriptions needed.') }}</p>
    </header>

    {{-- Progress --}}
    <ol class="mb-10 flex items-center justify-between gap-2">
        @foreach (range(1, 5) as $n)
            <li class="flex flex-1 flex-col items-center gap-2">
                <span @class([
                    'flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold transition',
                    'bg-indigo-600 text-white shadow' => $currentStep >= $n,
                    'bg-zinc-200 text-zinc-500' => $currentStep < $n,
                ])>{{ $n }}</span>
                <span class="hidden text-center text-[11px] font-medium text-zinc-500 sm:block">
                    @switch($n)
                        @case(1) {{ __('Location') }} @break
                        @case(2) {{ __('Specs') }} @break
                        @case(3) {{ __('Rent') }} @break
                        @case(4) {{ __('Match') }} @break
                        @case(5) {{ __('Amenities, photos & publish') }} @break
                    @endswitch
                </span>
            </li>
        @endforeach
    </ol>

    <div class="rounded-3xl border border-zinc-200/80 bg-white p-6 shadow-sm sm:p-8">
        {{-- Step 1: Category + location + distances --}}
        @if ($currentStep === 1)
            <div class="space-y-8">
                @if (! $this->isStudent())
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold text-zinc-900">{{ __('What are you listing?') }}</h2>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="button" wire:click="$set('listing_category', 'entire_place')"
                                @class([
                                    'group flex flex-col rounded-2xl border p-5 text-left transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $listing_category === 'entire_place',
                                    'border-zinc-200 bg-white hover:border-zinc-300' => $listing_category !== 'entire_place',
                                ])>
                                <span class="text-base font-semibold text-zinc-900">{{ __('Entire place') }}</span>
                                <span class="mt-1 text-sm text-zinc-500">{{ __('A private home or flat for one household.') }}</span>
                            </button>
                            <button type="button" wire:click="$set('listing_category', 'shared_room')"
                                @class([
                                    'group flex flex-col rounded-2xl border p-5 text-left transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $listing_category === 'shared_room',
                                    'border-zinc-200 bg-white hover:border-zinc-300' => $listing_category !== 'shared_room',
                                ])>
                                <span class="text-base font-semibold text-zinc-900">{{ __('A shared room') }}</span>
                                <span class="mt-1 text-sm text-zinc-500">{{ __('A room in a shared flat or house.') }}</span>
                            </button>
                        </div>
                        @error('listing_category')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div @class([
                    'border-t border-zinc-100 pt-6' => ! $this->isStudent(),
                    'pt-0' => $this->isStudent(),
                ])>
                    <h3 class="text-base font-semibold text-zinc-900">{{ __('Location') }}</h3>
                    <p class="mt-1 text-sm text-zinc-500">{{ __('Choose country, city, and area — lists update as you go.') }}</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('Country') }}</label>
                            <select wire:model.live="country_id" id="location-country"
                                data-placeholder="{{ __('Select…') }}"
                                class="location-select block w-full rounded-2xl border-zinc-200 py-2.5 px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select…') }}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('City') }}</label>
                            <select wire:model.live="city_id" id="location-city"
                                data-placeholder="{{ __('Select…') }}"
                                class="location-select block w-full rounded-2xl border-zinc-200 py-2.5 px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @disabled(! $country_id)>
                                <option value="">{{ __('Select…') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('Area') }}</label>
                            <select wire:model.live="area_id" id="location-area"
                                data-placeholder="{{ __('Select…') }}"
                                class="location-select block w-full rounded-2xl border-zinc-200 py-2.5 px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @disabled(! $city_id)>
                                <option value="">{{ __('Select…') }}</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-4" x-data="{ showMapHelp: false }">
                    <div class="flex flex-wrap items-end justify-between gap-2">
                        <label class="block text-sm font-medium text-zinc-700">{{ __('Map link') }} <span class="text-red-500">*</span></label>
                        <button type="button" @click="showMapHelp = true"
                            class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                            {{ __('How to get a map link') }}
                        </button>
                    </div>
                    <input type="url" wire:model.live="map_link" placeholder="https://maps.google.com/… or https://maps.app.goo.gl/…"
                        class="block w-full rounded-2xl border-zinc-200 py-3 px-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('map_link')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div x-show="showMapHelp" x-cloak
                        class="fixed inset-0 z-50 flex items-end justify-center px-4 py-8 sm:items-center sm:p-4"
                        aria-modal="true" role="dialog">
                        <div class="absolute inset-0 bg-zinc-900/50" @click="showMapHelp = false"></div>
                        <div class="relative z-10 max-h-[85vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-zinc-200 bg-white p-6 shadow-xl">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="text-lg font-semibold text-zinc-900">{{ __('Getting a Google Maps link') }}</h3>
                                <button type="button" @click="showMapHelp = false"
                                    class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700"
                                    aria-label="{{ __('Close') }}">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-4 text-zinc-600">
                                @include('partials.map-link-help-content')
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="button" @click="showMapHelp = false"
                                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                    {{ __('Close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">{{ __('Latitude') }} <span class="text-xs font-normal text-zinc-400">({{ __('optional') }})</span></label>
                        <input type="text" inputmode="decimal" wire:model.live="latitude" placeholder="51.5074"
                            class="block w-full rounded-2xl border-zinc-200 py-3 px-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('latitude')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">{{ __('Longitude') }} <span class="text-xs font-normal text-zinc-400">({{ __('optional') }})</span></label>
                        <input type="text" inputmode="decimal" wire:model.live="longitude" placeholder="-0.1278"
                            class="block w-full rounded-2xl border-zinc-200 py-3 px-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('longitude')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">{{ __('Distance to university (km)') }} <span class="text-red-500">*</span></label>
                        <input type="number" wire:model.live="distance_university_km" min="0" max="999.99" step="0.01"
                            class="block w-full rounded-2xl border-zinc-200 py-3 px-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('distance_university_km')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">{{ __('Distance to nearest bus/train (km)') }} <span class="text-red-500">*</span></label>
                        <input type="number" wire:model.live="distance_transit_km" min="0" max="999.99" step="0.01"
                            class="block w-full rounded-2xl border-zinc-200 py-3 px-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('distance_transit_km')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 2 --}}
        @if ($currentStep === 2)
            <div class="space-y-8">
                <h2 class="text-lg font-semibold text-zinc-900">{{ __('Property details') }}</h2>

                @if ($listing_category === 'shared_room')
                    <div>
                        <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Bed type') }}</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="button" wire:click="$set('bed_type', 'single')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-left text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $bed_type === 'single',
                                    'border-zinc-200 hover:border-zinc-300' => $bed_type !== 'single',
                                ])>
                                <span class="block font-semibold text-zinc-900">{{ __('Single bed') }}</span>
                                <span class="mt-0.5 block text-xs text-zinc-500">{{ __('One single bed in the room') }}</span>
                            </button>
                            <button type="button" wire:click="$set('bed_type', 'shared_double')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-left text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $bed_type === 'shared_double',
                                    'border-zinc-200 hover:border-zinc-300' => $bed_type !== 'shared_double',
                                ])>
                                <span class="block font-semibold text-zinc-900">{{ __('Shared double') }}</span>
                                <span class="mt-0.5 block text-xs text-zinc-500">{{ __('Sharing a double bed') }}</span>
                            </button>
                        </div>
                        @error('bed_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Property type') }}</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach (['studio' => __('Studio'), 'apartment' => __('Apartment'), 'house' => __('House'), 'student_seat' => __('Student hall / seat')] as $val => $label)
                            <button type="button" wire:click="$set('property_type', '{{ $val }}')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-left text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $property_type === $val,
                                    'border-zinc-200 hover:border-zinc-300' => $property_type !== $val,
                                ])>{{ $label }}</button>
                        @endforeach
                    </div>
                    @error('property_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-8 sm:grid-cols-2">
                    <div>
                        <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Bedrooms') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ([1, 2, 3, 4, 5, 6] as $n)
                                <button type="button" wire:click="$set('bedrooms', {{ $n }})"
                                    @class([
                                        'h-10 min-w-[2.5rem] rounded-xl border px-3 text-sm font-medium transition',
                                        'border-indigo-500 bg-indigo-50 text-indigo-900' => $bedrooms === $n,
                                        'border-zinc-200 hover:border-zinc-300' => $bedrooms !== $n,
                                    ])>{{ $n === 6 ? '6+' : $n }}</button>
                            @endforeach
                        </div>
                        @error('bedrooms')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Bathrooms') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ([1, 2, 3] as $n)
                                <button type="button" wire:click="$set('bathrooms', {{ $n }})"
                                    @class([
                                        'h-10 min-w-[2.5rem] rounded-xl border px-3 text-sm font-medium transition',
                                        'border-indigo-500 bg-indigo-50 text-indigo-900' => $bathrooms === $n,
                                        'border-zinc-200 hover:border-zinc-300' => $bathrooms !== $n,
                                    ])>{{ $n === 3 ? '3+' : $n }}</button>
                            @endforeach
                        </div>
                        @error('bathrooms')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Bathroom type') }}</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach (['private_ensuite' => __('Private / ensuite'), 'shared' => __('Shared bathroom')] as $val => $label)
                            <button type="button" wire:click="$set('bathroom_type', '{{ $val }}')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-left text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $bathroom_type === $val,
                                    'border-zinc-200 hover:border-zinc-300' => $bathroom_type !== $val,
                                ])>{{ $label }}</button>
                        @endforeach
                    </div>
                    @error('bathroom_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between rounded-2xl border border-zinc-200 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-900">{{ __('Furnished') }}</p>
                        <p class="text-xs text-zinc-500">{{ __('Includes essential furniture') }}</p>
                    </div>
                    <button type="button" wire:click="$toggle('is_furnished')"
                        @class([
                            'relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200',
                            'bg-indigo-600' => $is_furnished,
                            'bg-zinc-200' => ! $is_furnished,
                        ]) role="switch" aria-checked="{{ $is_furnished ? 'true' : 'false' }}">
                        <span @class([
                            'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200',
                            'translate-x-5' => $is_furnished,
                            'translate-x-0.5' => ! $is_furnished,
                        ])></span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Step 3 --}}
        @if ($currentStep === 3)
            <div class="space-y-8">
                <h2 class="text-lg font-semibold text-zinc-900">{{ __('Rent & contract') }}</h2>

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Rent period') }}</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach (['day' => __('Per day'), 'week' => __('Per week'), 'month' => __('Per month')] as $val => $label)
                            <button type="button" wire:click="$set('rent_duration', '{{ $val }}')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-center text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $rent_duration === $val,
                                    'border-zinc-200 hover:border-zinc-300' => $rent_duration !== $val,
                                ])>{{ $label }}</button>
                        @endforeach
                    </div>
                    @error('rent_duration')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-700">{{ __('Rent amount') }}</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-zinc-400">€</span>
                        <input type="number" wire:model.live="rent_amount" min="1" step="1"
                            class="block w-full rounded-2xl border-zinc-200 py-3 pl-9 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="0">
                    </div>
                    @error('rent_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Bills') }}</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach (['all' => __('All included'), 'some' => __('Some included'), 'none' => __('Not included')] as $val => $label)
                            <button type="button" wire:click="$set('bills_included', '{{ $val }}')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-center text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $bills_included === $val,
                                    'border-zinc-200 hover:border-zinc-300' => $bills_included !== $val,
                                ])>{{ $label }}</button>
                        @endforeach
                    </div>
                    @error('bills_included')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($bills_included === 'some')
                    <div>
                        <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Which bills are included?') }}</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach (['wifi' => __('Wi‑Fi'), 'water' => __('Water'), 'electricity' => __('Electricity'), 'gas' => __('Gas')] as $val => $label)
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-500/20"
                                    wire:key="bill-{{ $val }}">
                                    <input type="checkbox" wire:model="included_bills" value="{{ $val }}"
                                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-zinc-800">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('included_bills')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Minimum contract') }}</p>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            '1_month' => __('1 month'),
                            '3_months' => __('3 months'),
                            '6_months' => __('6 months'),
                            '1_year' => __('1 year'),
                            'flexible' => __('Flexible'),
                        ] as $val => $label)
                            <button type="button" wire:click="$set('min_contract_length', '{{ $val }}')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-center text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $min_contract_length === $val,
                                    'border-zinc-200 hover:border-zinc-300' => $min_contract_length !== $val,
                                ])>{{ $label }}</button>
                        @endforeach
                    </div>
                    @error('min_contract_length')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between rounded-2xl border border-zinc-200 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-900">{{ __('Provides written agreement') }}</p>
                        <p class="text-xs text-zinc-500">{{ __('Tenancy agreement or similar') }}</p>
                    </div>
                    <button type="button" wire:click="$toggle('provides_agreement')"
                        @class([
                            'relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200',
                            'bg-indigo-600' => $provides_agreement,
                            'bg-zinc-200' => ! $provides_agreement,
                        ]) role="switch">
                        <span @class([
                            'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200',
                            'translate-x-5' => $provides_agreement,
                            'translate-x-0.5' => ! $provides_agreement,
                        ])></span>
                    </button>
                </div>

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Deposit') }}</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach (['none' => __('No deposit'), '1_month' => __('1 month rent'), '5_weeks' => __('5 weeks rent')] as $val => $label)
                            <button type="button" wire:click="$set('deposit_required', '{{ $val }}')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-center text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $deposit_required === $val,
                                    'border-zinc-200 hover:border-zinc-300' => $deposit_required !== $val,
                                ])>{{ $label }}</button>
                        @endforeach
                    </div>
                    @error('deposit_required')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Rent is for') }}</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ([
                            'only_boys' => __('Only boys'),
                            'only_girls' => __('Only girls'),
                            'couples' => __('Couples'),
                            'anyone' => __('Anyone'),
                        ] as $val => $label)
                            <button type="button" wire:click="$set('rent_for', '{{ $val }}')"
                                @class([
                                    'rounded-2xl border px-4 py-3 text-left text-sm font-medium transition',
                                    'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $rent_for === $val,
                                    'border-zinc-200 hover:border-zinc-300' => $rent_for !== $val,
                                ])>{{ $label }}</button>
                        @endforeach
                    </div>
                    @error('rent_for')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        {{-- Step 4 --}}
        @if ($currentStep === 4)
            <div class="space-y-8">
                <h2 class="text-lg font-semibold text-zinc-900">{{ __('Vibe & house rules') }}</h2>

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Suitable for') }}</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach (['undergraduates' => __('Undergraduates'), 'postgraduates' => __('Postgraduates'), 'couples' => __('Couples')] as $val => $label)
                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-500/20">
                                <input type="checkbox" wire:model="suitable_for" value="{{ $val }}"
                                    class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-medium text-zinc-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('suitable_for')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($listing_category === 'shared_room')
                    <div>
                        <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('Flatmate vibe') }}</p>
                        <div class="grid gap-3 sm:grid-cols-3">
                            @foreach (['all_male' => __('All male'), 'all_female' => __('All female'), 'mixed' => __('Mixed')] as $val => $label)
                                <button type="button" wire:click="$set('flatmate_vibe', '{{ $val }}')"
                                    @class([
                                        'rounded-2xl border px-4 py-3 text-center text-sm font-medium transition',
                                        'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20' => $flatmate_vibe === $val,
                                        'border-zinc-200 hover:border-zinc-300' => $flatmate_vibe !== $val,
                                    ])>{{ $label }}</button>
                            @endforeach
                        </div>
                        @error('flatmate_vibe')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <p class="mb-3 text-sm font-medium text-zinc-700">{{ __('House rules') }}</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach (['pet_friendly' => __('Pet friendly'), 'smoking_allowed' => __('Smoking allowed'), 'quiet_house' => __('Quiet house')] as $val => $label)
                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-500/20">
                                <input type="checkbox" wire:model="house_rules" value="{{ $val }}"
                                    class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-medium text-zinc-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('house_rules')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        {{-- Step 5 --}}
        @if ($currentStep === 5)
            <div class="space-y-10">
                <div class="space-y-8">
                    <h2 class="text-lg font-semibold text-zinc-900">{{ __('Amenities') }}</h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ([
                            'wifi' => __('Wi‑Fi'),
                            'washing_machine' => __('Washing machine'),
                            'tumble_dryer' => __('Tumble dryer'),
                            'dishwasher' => __('Dishwasher'),
                            'balcony_garden' => __('Balcony / garden'),
                            'desk_in_room' => __('Desk in room'),
                            'building_gym' => __('Building gym'),
                            'bike_storage' => __('Bike storage'),
                        ] as $val => $label)
                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-500/20">
                                <input type="checkbox" wire:model="amenities" value="{{ $val }}"
                                    class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-medium text-zinc-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('amenities')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-zinc-100 pt-8"
                    x-data="{
                        existing: @js($existingGalleryCount),
                        photoCount: @entangle('photosCount').live,
                        dragging: false,
                        uploadFromDrop(e) {
                            this.dragging = false;
                            const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                            const maxAdd = Math.max(0, 10 - this.existing - this.photoCount);
                            const batch = files.slice(0, maxAdd);
                            if (!batch.length) return;
                            $wire.uploadMultiple('photos', batch, function () {}, function () {}, function () {}, function () {}, this.photoCount > 0);
                        }
                    }">
                    <h2 class="text-lg font-semibold text-zinc-900">{{ __('Property photos') }}</h2>
                    @if ($editingPropertyId)
                        <p class="mt-1 text-sm text-zinc-500">
                            {{ __('You already have :count saved photo(s). Add more if you like — you need 3–10 photos in total. New uploads only: combined size must not exceed 10 MB. JPG, PNG, or WebP.', ['count' => $existingGalleryCount]) }}
                        </p>
                    @else
                        <p class="mt-1 text-sm text-zinc-500">{{ __('Upload between 3 and 10 images. Combined file size must not exceed 10 MB. JPG, PNG, or WebP.') }}</p>
                    @endif
                    <p class="mt-1 text-xs font-medium text-zinc-600">
                        <span>{{ __('Photos (saved + new)') }}:</span>
                        <span x-text="existing + photoCount"></span> / 10
                    </p>

                    @if ($editingPropertyId && count($existingPhotoUrls) > 0)
                        <div class="mt-4">
                            <p class="mb-2 text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('Current photos') }}</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach ($existingPhotoUrls as $url)
                                    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100 shadow-sm">
                                        <img src="{{ $url }}" alt="" class="h-36 w-full object-cover" loading="lazy" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Hidden: UI is the dashed zone only (no native "Choose file" row) --}}
                    <input type="file" id="property-photos-input" wire:model="photos" multiple
                        class="hidden" tabindex="-1" accept="image/*" aria-hidden="true">

                    <div class="relative mt-4">
                        <button type="button"
                            x-on:click="document.getElementById('property-photos-input').click()"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragenter.prevent="dragging = true"
                            x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="uploadFromDrop($event)"
                            :class="dragging ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/20' : 'border-zinc-200 bg-white hover:border-zinc-300 hover:bg-zinc-50/80'"
                            class="flex w-full cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed px-6 py-12 text-center transition">
                            <div wire:loading.remove wire:target="photos" class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3A1.5 1.5 0 001.5 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="text-sm font-semibold text-zinc-900">{{ __('Click to upload') }}</span>
                                <span class="text-xs text-zinc-500">{{ __('or drag and drop images here') }}</span>
                            </div>
                            <div wire:loading wire:target="photos" class="flex flex-col items-center gap-2" role="status">
                                <svg class="h-8 w-8 shrink-0 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm font-medium text-zinc-700">{{ __('Preparing previews…') }}</span>
                            </div>
                        </button>
                    </div>

                    @error('photos')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @foreach ($errors->messages() as $key => $messages)
                        @if (str_starts_with($key, 'photos.') && $key !== 'photos')
                            @foreach ($messages as $message)
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @endforeach
                        @endif
                    @endforeach

                    @if ($photos)
                        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($photos as $photo)
                                <div class="group relative rounded-xl border border-zinc-200 bg-zinc-100 shadow-sm"
                                    wire:key="photo-{{ $photo->getFilename() }}">
                                    {{-- overflow-hidden only on the image stack so the remove button is not clipped --}}
                                    <div class="relative h-36 w-full overflow-hidden rounded-t-xl">
                                        <img src="{{ $photo->temporaryUrl() }}" alt=""
                                            class="h-full w-full object-cover">
                                        <div class="pointer-events-none absolute inset-0 bg-zinc-900/0 transition group-hover:bg-zinc-900/30"></div>
                                    </div>
                                    <button type="button"
                                        wire:click="$removeUpload('photos', @js($photo->getFilename()))"
                                        wire:loading.attr="disabled"
                                        aria-label="{{ __('Remove image') }}"
                                        class="absolute right-2 top-2 z-10 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-zinc-200/80 bg-white text-zinc-800 shadow-md transition hover:border-red-500 hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="border-t border-zinc-100 pt-8">
                    <h2 class="text-lg font-semibold text-zinc-900">{{ __('Publish or save as draft') }}</h2>
                    <p class="mt-1 text-sm text-zinc-500">
                        @if ($editingPropertyId)
                            {{ __('Save your changes as a draft or publish so seekers see the latest version.') }}
                        @else
                            {{ __('Publishing makes the listing live for seekers. Draft keeps it in your account until you publish it later.') }}
                        @endif
                    </p>
                </div>
            </div>
        @endif

        {{-- Nav --}}
        <div class="mt-10 flex flex-col-reverse gap-3 border-t border-zinc-100 pt-8 sm:flex-row sm:justify-between">
            <button type="button" wire:click="previousStep"
                class="inline-flex justify-center rounded-full border border-zinc-200 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 disabled:cursor-not-allowed disabled:opacity-40"
                @if ($currentStep === 1) disabled @endif>
                {{ __('Back') }}
            </button>

            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center sm:justify-end">
                @if ($currentStep < 5)
                    <button type="button" wire:click="nextStep"
                        class="inline-flex justify-center rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                        {{ __('Continue') }}
                    </button>
                @else
                    <button type="button" wire:click="submitDraft"
                        wire:loading.attr="disabled"
                        class="inline-flex justify-center rounded-full border border-zinc-300 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-800 shadow-sm transition hover:bg-zinc-50 disabled:opacity-60">
                        <span wire:loading.remove wire:target="submitDraft">{{ __('Save as draft') }}</span>
                        <span wire:loading wire:target="submitDraft">{{ __('Saving…') }}</span>
                    </button>
                    <button type="button" wire:click="submitPublished"
                        wire:loading.attr="disabled"
                        class="inline-flex justify-center rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-60">
                        <span wire:loading.remove wire:target="submitPublished">
                            {{ $editingPropertyId ? __('Update & publish') : __('Publish') }}
                        </span>
                        <span wire:loading wire:target="submitPublished">{{ __('Publishing…') }}</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
