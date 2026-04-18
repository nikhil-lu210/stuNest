{{-- Explore filter modal — aligned with listing multiform (rounded-2xl, section labels) --}}
@php
    /** @var string $fi @var string $sel @var string $rpOn @var string $rpOff */
@endphp

<div class="space-y-8 mb-6">
    {{-- Location --}}
    <div class="space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Location') }}</h3>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Country') }}</label>
            <div class="relative">
                <select x-model="countryId" @change="loadCities()" class="{{ $sel }}">
                    <option value="">{{ __('All countries') }}</option>
                    @foreach ($countriesForFilter as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('City') }}</label>
            <div class="relative">
                <select x-model="cityId" @change="loadAreas()" class="{{ $sel }}" :disabled="!countryId">
                    <option value="">{{ __('Any city') }}</option>
                    <template x-for="c in cityOptions" :key="c.id">
                        <option :value="String(c.id)" x-text="c.name"></option>
                    </template>
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Area') }}</label>
            <div class="relative">
                <select x-model="areaId" class="{{ $sel }}" :disabled="!cityId">
                    <option value="">{{ __('Any area') }}</option>
                    <template x-for="a in areaOptions" :key="a.id">
                        <option :value="String(a.id)" x-text="a.name"></option>
                    </template>
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>

    {{-- Rent --}}
    <div class="space-y-4 border-t border-gray-100 pt-6">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Rent & contract') }}</h3>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Rent period') }}</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="rentPeriod = 'day'" :class="rentPeriod === 'day' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-3 py-3 text-center text-sm font-medium transition">{{ __('Daily') }}</button>
                <button type="button" @click="rentPeriod = 'week'" :class="rentPeriod === 'week' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-3 py-3 text-center text-sm font-medium transition">{{ __('Weekly') }}</button>
                <button type="button" @click="rentPeriod = 'month'" :class="rentPeriod === 'month' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-3 py-3 text-center text-sm font-medium transition">{{ __('Monthly') }}</button>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Min rent') }} (€)</label>
                <input type="number" min="0" x-model="priceMin" class="{{ $fi }}" placeholder="0">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Max rent') }} (€)</label>
                <input type="number" min="0" x-model="priceMax" class="{{ $fi }}" placeholder="—">
            </div>
        </div>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Bills') }}</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="billsIncluded = billsIncluded === 'all' ? '' : 'all'" :class="billsIncluded === 'all' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition sm:text-sm">{{ __('All inc.') }}</button>
                <button type="button" @click="billsIncluded = billsIncluded === 'some' ? '' : 'some'" :class="billsIncluded === 'some' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition sm:text-sm">{{ __('Some') }}</button>
                <button type="button" @click="billsIncluded = billsIncluded === 'none' ? '' : 'none'" :class="billsIncluded === 'none' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition sm:text-sm">{{ __('None') }}</button>
            </div>
        </div>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Minimum contract') }}</p>
            <div class="relative">
                <select x-model="minContractLength" class="{{ $sel }}">
                    <option value="">{{ __('Any') }}</option>
                    <option value="1_month">{{ __('1 month') }}</option>
                    <option value="3_months">{{ __('3 months') }}</option>
                    <option value="6_months">{{ __('6 months') }}</option>
                    <option value="1_year">{{ __('1 year') }}</option>
                    <option value="flexible">{{ __('Flexible') }}</option>
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Deposit') }}</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="depositRequired = depositRequired === 'none' ? '' : 'none'" :class="depositRequired === 'none' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('No deposit') }}</button>
                <button type="button" @click="depositRequired = depositRequired === '1_month' ? '' : '1_month'" :class="depositRequired === '1_month' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('1 month') }}</button>
                <button type="button" @click="depositRequired = depositRequired === '5_weeks' ? '' : '5_weeks'" :class="depositRequired === '5_weeks' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('5 weeks') }}</button>
            </div>
        </div>
        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-gray-200 px-4 py-3">
            <span class="text-sm font-medium text-gray-900">{{ __('Written agreement') }}</span>
            <input type="checkbox" x-model="providesAgreement" class="h-5 w-5 rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
        </label>
    </div>

    {{-- Property --}}
    <div class="space-y-4 border-t border-gray-100 pt-6">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Property') }}</h3>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Listing') }}</label>
            <div class="relative">
                <select x-model="listingCategory" class="{{ $sel }}">
                    <option value="">{{ __('Any listing') }}</option>
                    <option value="entire_place">{{ __('Entire place') }}</option>
                    <option value="shared_room">{{ __('Shared room') }}</option>
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Property type') }}</label>
            <div class="relative">
                <select x-model="propertyType" class="{{ $sel }}">
                    <option value="">{{ __('Any type') }}</option>
                    <option value="studio">{{ __('Studio') }}</option>
                    <option value="apartment">{{ __('Apartment') }}</option>
                    <option value="house">{{ __('House') }}</option>
                    <option value="student_seat">{{ __('Student hall / seat') }}</option>
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Bedrooms (min)') }}</label>
                <input type="number" min="1" max="6" x-model="bedroomsMin" class="{{ $fi }}" placeholder="—">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Bedrooms (max)') }}</label>
                <input type="number" min="1" max="6" x-model="bedroomsMax" class="{{ $fi }}" placeholder="—">
            </div>
        </div>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Bathrooms') }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach ([1, 2, 3] as $n)
                    <button type="button" @click="bathrooms = '{{ $n }}'" :class="bathrooms === '{{ $n }}' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="h-10 min-w-[2.5rem] rounded-2xl border px-3 text-sm font-medium transition">{{ $n === 3 ? '3+' : $n }}</button>
                @endforeach
                <button type="button" @click="bathrooms = ''" :class="bathrooms === '' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="h-10 rounded-2xl border px-3 text-xs font-medium transition">{{ __('Any') }}</button>
            </div>
        </div>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Bed type') }}</p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="bedType = bedType === 'single' ? '' : 'single'" :class="bedType === 'single' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-3 py-3 text-left text-sm font-medium transition">{{ __('Single bed') }}</button>
                <button type="button" @click="bedType = bedType === 'shared_double' ? '' : 'shared_double'" :class="bedType === 'shared_double' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-3 py-3 text-left text-sm font-medium transition">{{ __('Shared double') }}</button>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Bathroom type') }}</label>
            <div class="relative">
                <select x-model="bathroomType" class="{{ $sel }}">
                    <option value="">{{ __('Any') }}</option>
                    <option value="private_ensuite">{{ __('Private / ensuite') }}</option>
                    <option value="shared">{{ __('Shared bathroom') }}</option>
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-gray-200 px-4 py-3">
            <span class="text-sm font-medium text-gray-900">{{ __('Furnished') }}</span>
            <input type="checkbox" x-model="furnished" class="h-5 w-5 rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
        </label>
    </div>

    {{-- Distances --}}
    <div class="space-y-4 border-t border-gray-100 pt-6">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Distances') }}</h3>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Max distance to campus (km)') }}</label>
            <input type="number" min="0" step="0.1" x-model="distanceMax" class="{{ $fi }}" placeholder="{{ __('No limit') }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Max distance to transit (km)') }}</label>
            <input type="number" min="0" step="0.1" x-model="distanceTransitMax" class="{{ $fi }}" placeholder="{{ __('No limit') }}">
        </div>
    </div>

    {{-- Match --}}
    <div class="space-y-4 border-t border-gray-100 pt-6">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Match') }}</h3>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Rent is for') }}</p>
            <div class="relative">
                <select x-model="rentFor" class="{{ $sel }}">
                    <option value="">{{ __('Any') }}</option>
                    <option value="only_boys">{{ __('Only boys') }}</option>
                    <option value="only_girls">{{ __('Only girls') }}</option>
                    <option value="couples">{{ __('Couples') }}</option>
                    <option value="anyone">{{ __('Anyone') }}</option>
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Suitable for') }}</p>
            <div class="grid gap-2 sm:grid-cols-3">
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50 has-[:checked]:ring-2 has-[:checked]:ring-gray-900/10">
                    <input type="checkbox" value="undergraduates" x-model="suitableFor" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                    {{ __('Undergraduates') }}
                </label>
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50 has-[:checked]:ring-2 has-[:checked]:ring-gray-900/10">
                    <input type="checkbox" value="postgraduates" x-model="suitableFor" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                    {{ __('Postgraduates') }}
                </label>
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50 has-[:checked]:ring-2 has-[:checked]:ring-gray-900/10">
                    <input type="checkbox" value="couples" x-model="suitableFor" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                    {{ __('Couples') }}
                </label>
            </div>
        </div>
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">{{ __('Flatmate vibe') }}</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="flatmateVibe = flatmateVibe === 'all_male' ? '' : 'all_male'" :class="flatmateVibe === 'all_male' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('All male') }}</button>
                <button type="button" @click="flatmateVibe = flatmateVibe === 'all_female' ? '' : 'all_female'" :class="flatmateVibe === 'all_female' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('All female') }}</button>
                <button type="button" @click="flatmateVibe = flatmateVibe === 'mixed' ? '' : 'mixed'" :class="flatmateVibe === 'mixed' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('Mixed') }}</button>
            </div>
        </div>
    </div>

    {{-- House rules --}}
    <div class="space-y-4 border-t border-gray-100 pt-6">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('House rules') }}</h3>
        <div class="grid gap-2 sm:grid-cols-3">
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                <input type="checkbox" value="pet_friendly" x-model="houseRules" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                {{ __('Pet friendly') }}
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                <input type="checkbox" value="smoking_allowed" x-model="houseRules" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                {{ __('Smoking allowed') }}
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                <input type="checkbox" value="quiet_house" x-model="houseRules" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                {{ __('Quiet house') }}
            </label>
        </div>
    </div>

    {{-- Amenities --}}
    <div class="space-y-4 border-t border-gray-100 pt-6">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Amenities') }}</h3>
        <div class="grid gap-2 sm:grid-cols-2">
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                <input type="checkbox" x-model="wifi" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                {{ __('Wi‑Fi') }}
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                <input type="checkbox" x-model="gym" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                {{ __('Building gym') }}
            </label>
            @foreach ([
                'washing_machine' => __('Washing machine'),
                'tumble_dryer' => __('Tumble dryer'),
                'dishwasher' => __('Dishwasher'),
                'balcony_garden' => __('Balcony / garden'),
                'desk_in_room' => __('Desk in room'),
                'bike_storage' => __('Bike storage'),
            ] as $amKey => $amLabel)
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                    <input type="checkbox" value="{{ $amKey }}" x-model="amenities" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/20">
                    {{ $amLabel }}
                </label>
            @endforeach
        </div>
        <p class="text-xs text-gray-500">{{ __('Tip: use the row of quick filters for one-tap toggles; this panel mirrors the listing form.') }}</p>
    </div>
</div>
