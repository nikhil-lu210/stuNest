@php
    $exploreFilters = $filters ?? ['q' => '', 'move_in' => '', 'guests' => 1];
    $filterState = $filterState ?? [
        'price_min' => '',
        'price_max' => '',
        'distance_max' => '',
        'country_id' => '',
        'property_type' => '',
        'listing_category' => '',
        'ensuite' => false,
        'gym' => false,
        'bills' => false,
        'furnished' => false,
        'wifi' => false,
    ];
    $countriesForFilter = $countriesForFilter ?? collect();
    $citiesForFilter = $citiesForFilter ?? collect();
    $areasForFilter = $areasForFilter ?? collect();
    $toggleExploreFilter = function (string $key): string {
        $q = request()->except('page');
        if (request()->boolean($key)) {
            unset($q[$key]);
        } else {
            $q[$key] = 1;
        }

        return route('client.explore').'?'.http_build_query(array_filter($q, fn ($v) => $v !== null && $v !== ''));
    };
    $togglePropertyType = function (string $type): string {
        $q = request()->except('page');
        unset($q['studio']);
        if (($q['property_type'] ?? '') === $type) {
            unset($q['property_type']);
        } else {
            $q['property_type'] = $type;
        }

        return route('client.explore').'?'.http_build_query(array_filter($q, fn ($v) => $v !== null && $v !== ''));
    };
@endphp
{{-- Explore header: primary nav + auth (filters live in the row below; no location/date/guest pill) --}}
<header
    class="border-b border-gray-100 bg-white z-50 shrink-0"
    x-data="{ exploreNavOpen: false }"
    x-on:keydown.escape.window="exploreNavOpen = false"
    x-on:click.outside="exploreNavOpen = false"
>
    <div class="px-6 h-20 flex w-full items-center justify-between gap-3 md:gap-4">
        <div class="flex min-w-0 flex-1 items-center gap-4 lg:gap-8">
            <a href="{{ route('client.home') }}" class="flex shrink-0 items-center gap-2" @click="exploreNavOpen = false">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl leading-none tracking-tighter">S</span>
                </div>
                <span class="text-xl font-bold tracking-tight hidden lg:inline">{{ config('app.name') }}.</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-medium" aria-label="{{ __('Primary') }}">
                <a href="{{ route('client.explore') }}" class="text-gray-900 transition-colors hover:text-gray-600 whitespace-nowrap">{{ __('Explore') }}</a>
                <a href="{{ route('client.home') }}#how-it-works" class="text-gray-900 transition-colors hover:text-gray-600 whitespace-nowrap">{{ __('How it works') }}</a>
            </nav>
        </div>

        <div class="flex shrink-0 items-center justify-end gap-1 sm:gap-2">
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-900 transition-colors hover:bg-gray-100 md:hidden"
                x-bind:aria-expanded="exploreNavOpen"
                aria-controls="explore-nav-mobile-menu"
                aria-label="{{ __('Open menu') }}"
                @click="exploreNavOpen = !exploreNavOpen"
            >
                <i data-lucide="menu" class="h-6 w-6" x-show="!exploreNavOpen" x-cloak></i>
                <i data-lucide="x" class="h-6 w-6" x-show="exploreNavOpen" x-cloak></i>
            </button>
            @guest
                <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-gray-600 hover:text-gray-900">{{ __('Log in') }}</a>
                <a
                    href="{{ route('register') }}"
                    class="inline-flex items-center justify-center sm:hidden h-10 w-10 rounded-full bg-black text-sm font-medium text-white transition-colors hover:bg-gray-800"
                    aria-label="{{ __('Student sign up') }}"
                >
                    <i data-lucide="user" class="h-4 w-4"></i>
                </a>
                <div
                    x-data="{ open: false }"
                    @click.outside="open = false"
                    class="relative hidden text-left sm:inline-block"
                >
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex items-center justify-center gap-1.5 rounded-full bg-black px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800"
                        x-bind:aria-expanded="open"
                        aria-haspopup="true"
                    >
                        {{ __('Sign up') }}
                        <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" aria-hidden="true"></i>
                    </button>
                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="translate-y-1 opacity-0"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="translate-y-0 opacity-100"
                        x-transition:leave-end="translate-y-1 opacity-0"
                        class="absolute right-0 z-50 mt-2 w-56 origin-top-right overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg"
                        role="menu"
                    >
                        @include('layouts.client.partials.nav-public-signup-links', [
                            'isMobileMenu' => false,
                            'closeDesktopDropdown' => true,
                        ])
                    </div>
                </div>
            @else
                @include('layouts.client.partials.nav-user-menu')
            @endguest
        </div>
    </div>

    {{-- Mobile: same links as marketing nav (without List a property) --}}
    <div
        id="explore-nav-mobile-menu"
        x-show="exploreNavOpen"
        x-cloak
        x-transition
        class="md:hidden border-t border-gray-100 bg-white px-6 py-3"
    >
        <div class="flex flex-col gap-1">
            <a
                href="{{ route('client.explore') }}"
                class="rounded-xl px-4 py-3 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50"
                @click="exploreNavOpen = false"
            >
                {{ __('Explore') }}
            </a>
            <a
                href="{{ route('client.home') }}#how-it-works"
                class="rounded-xl px-4 py-3 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50"
                @click="exploreNavOpen = false"
            >
                {{ __('How it works') }}
            </a>
            @guest
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <a
                        href="{{ route('login') }}"
                        class="block rounded-xl px-4 py-3 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50"
                        @click="exploreNavOpen = false"
                    >
                        {{ __('Log in') }}
                    </a>
                    <p class="mt-1 px-4 pt-2 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sign up') }}</p>
                    <div class="mt-1 flex flex-col gap-0.5">
                        @include('layouts.client.partials.nav-public-signup-links', [
                            'isMobileMenu' => true,
                            'onClickClose' => 'exploreNavOpen = false',
                        ])
                    </div>
                </div>
            @endguest
        </div>
    </div>

    @if ($showFilters ?? true)
    @php
        $pillOn = 'border-gray-900 bg-gray-900 text-white hover:bg-gray-800';
        $pillOff = 'border-gray-200 bg-white text-gray-900 hover:border-black';
        $priceFilterActive = ($exploreFilters['price_min'] ?? '') !== '' || ($exploreFilters['price_max'] ?? '') !== '' || ($exploreFilters['rent_period'] ?? '') !== '';
        $distanceFilterActive = ($exploreFilters['distance_max'] ?? '') !== '' || ($exploreFilters['distance_transit_max'] ?? '') !== '';
        $studioPillActive = request('property_type') === 'studio' || request()->boolean('studio');
        $fi = 'block w-full rounded-2xl border border-gray-200 bg-white py-3 px-4 text-sm font-medium text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10';
        $sel = 'block w-full cursor-pointer appearance-none rounded-2xl border border-gray-200 bg-white py-3 px-4 pr-10 text-sm font-medium text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10';
        $rpOn = 'border-gray-900 bg-gray-50 ring-2 ring-gray-900/10';
        $rpOff = 'border-gray-200 bg-white hover:border-gray-300';
        $geoCities = $citiesForFilter->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values();
        $geoAreas = $areasForFilter->map(fn ($a) => ['id' => $a->id, 'name' => $a->name])->values();
    @endphp
    <div
        x-data="exploreFilterUi(@js($filterState), { citiesUrlPrefix: '{{ url('/explore/cities') }}/', areasUrlPrefix: '{{ url('/explore/areas') }}/', initialCities: @json($geoCities), initialAreas: @json($geoAreas) })"
        class="px-6 py-4 border-t border-gray-50 relative z-40"
    >
        <div class="flex items-center gap-3 overflow-x-auto no-scrollbar">
            <button
                type="button"
                class="flex items-center gap-2 border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0 bg-white"
                @click="filtersOpen = true; syncFromUrl(); $nextTick(() => { if (window.lucide) lucide.createIcons(); })"
            >
                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                {{ __('Filters') }}
            </button>
            <div class="w-px h-6 bg-gray-200 shrink-0 mx-2"></div>

            <div class="relative shrink-0">
                <button
                    type="button"
                    class="border rounded-full px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap"
                    :class="(priceOpen || {{ $priceFilterActive ? 'true' : 'false' }}) ? '{{ $pillOn }}' : '{{ $pillOff }}'"
                    @click="distanceOpen = false; priceOpen = !priceOpen; if (priceOpen && !rentPeriod && (priceMin || priceMax)) { rentPeriod = 'week' }"
                >
                    {{ __('Price') }}
                </button>
                <div
                    x-show="priceOpen"
                    x-cloak
                    x-transition
                    @click.outside="priceOpen = false"
                    class="absolute left-0 top-full mt-2 w-[min(100vw-2rem,22rem)] rounded-2xl border border-gray-200 bg-white p-4 shadow-xl z-50"
                >
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">{{ __('Rent amount') }}</p>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <button type="button" @click="rentPeriod = 'day'" :class="rentPeriod === 'day' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('Daily') }}</button>
                        <button type="button" @click="rentPeriod = 'week'" :class="rentPeriod === 'week' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('Weekly') }}</button>
                        <button type="button" @click="rentPeriod = 'month'" :class="rentPeriod === 'month' ? '{{ $rpOn }}' : '{{ $rpOff }}'" class="rounded-2xl border px-2 py-2.5 text-xs font-semibold transition">{{ __('Monthly') }}</button>
                    </div>
                    <div class="flex gap-3 mb-4">
                        <label class="flex-1 text-xs font-medium text-gray-600">{{ __('Min') }} (€)
                            <input type="number" min="0" x-model="priceMin" class="mt-1.5 {{ $fi }}" placeholder="0">
                        </label>
                        <label class="flex-1 text-xs font-medium text-gray-600">{{ __('Max') }} (€)
                            <input type="number" min="0" x-model="priceMax" class="mt-1.5 {{ $fi }}" placeholder="—">
                        </label>
                    </div>
                    <button type="button" class="w-full rounded-full bg-gray-900 text-white text-sm font-medium py-2.5 hover:bg-black" @click="applyPrice(); priceOpen = false">
                        {{ __('Apply') }}
                    </button>
                </div>
            </div>

            <a href="{{ $toggleExploreFilter('ensuite') }}" class="border rounded-full px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap shrink-0 {{ request()->boolean('ensuite') ? $pillOn : $pillOff }}">
                {{ __('En-suite') }}
            </a>
            <a href="{{ $togglePropertyType('studio') }}" class="border rounded-full px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap shrink-0 {{ $studioPillActive ? $pillOn : $pillOff }}">
                {{ __('Studio') }}
            </a>
            <a href="{{ $toggleExploreFilter('gym') }}" class="border rounded-full px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap shrink-0 {{ request()->boolean('gym') ? $pillOn : $pillOff }}">
                {{ __('Gym') }}
            </a>
            <a href="{{ $toggleExploreFilter('bills') }}" class="border rounded-full px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap shrink-0 {{ request()->boolean('bills') ? $pillOn : $pillOff }}">
                {{ __('Bills Included') }}
            </a>

            <div class="relative shrink-0">
                <button
                    type="button"
                    class="border rounded-full px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap"
                    :class="(distanceOpen || {{ $distanceFilterActive ? 'true' : 'false' }}) ? '{{ $pillOn }}' : '{{ $pillOff }}'"
                    @click="distanceOpen = !distanceOpen; priceOpen = false"
                >
                    {{ __('Distance to Uni') }}
                </button>
                <div
                    x-show="distanceOpen"
                    x-cloak
                    x-transition
                    @click.outside="distanceOpen = false"
                    class="absolute left-0 top-full mt-2 w-72 rounded-2xl border border-gray-200 bg-white p-4 shadow-xl z-50"
                >
                    <p class="text-xs font-medium text-gray-500 mb-2">{{ __('Max distance from campus') }}</p>
                    <label class="block text-xs font-medium text-gray-600">{{ __('Kilometres') }}
                        <input type="number" min="0" step="0.1" x-model="distanceMax" class="mt-1.5 {{ $fi }}" placeholder="{{ __('e.g. 5') }}">
                    </label>
                    <label class="mt-3 block text-xs font-medium text-gray-600">{{ __('Max distance to transit') }}
                        <input type="number" min="0" step="0.1" x-model="distanceTransitMax" class="mt-1.5 {{ $fi }}" placeholder="{{ __('e.g. 0.5') }}">
                    </label>
                    <button type="button" class="mt-4 w-full rounded-full bg-gray-900 text-white text-sm font-medium py-2.5 hover:bg-black" @click="applyDistance(); distanceOpen = false">
                        {{ __('Apply') }}
                    </button>
                </div>
            </div>
        </div>

        <div
            x-show="filtersOpen"
            x-cloak
            x-transition
            class="fixed inset-0 z-[100] flex items-end md:items-center justify-center p-0 md:p-4"
        >
            <div class="absolute inset-0 bg-black/40" @click="filtersOpen = false"></div>
            <div
                class="relative w-full md:max-w-2xl lg:max-w-3xl rounded-t-3xl md:rounded-2xl bg-white shadow-2xl max-h-[90vh] overflow-y-auto p-6 md:p-8 z-10"
                @click.stop
            >
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold tracking-tight text-gray-900">{{ __('All filters') }}</h2>
                    <button type="button" class="p-2 rounded-full hover:bg-gray-100" @click="filtersOpen = false" aria-label="{{ __('Close') }}">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                @include('client.explore.partials.filter-modal', [
                    'countriesForFilter' => $countriesForFilter,
                    'fi' => $fi,
                    'sel' => $sel,
                    'rpOn' => $rpOn,
                    'rpOff' => $rpOff,
                ])
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" class="flex-1 rounded-full border border-gray-200 py-3 text-sm font-medium hover:bg-gray-50" @click="clearFilterParams()">
                        {{ __('Clear filters') }}
                    </button>
                    <button type="button" class="flex-1 rounded-full bg-gray-900 text-white py-3 text-sm font-medium hover:bg-black" @click="applyAllFromPanel()">
                        {{ __('Apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</header>
