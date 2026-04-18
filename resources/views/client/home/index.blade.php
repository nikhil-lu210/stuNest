@extends('layouts.client.app')

@section('title', config('app.name').' | Premium Student Housing')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/lightbox.css') }}">
    <style>
        input.home-search-date::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.55;
        }
        input.home-search-date::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    @include('layouts.client.partials.nav-marketing')

    <main class="mx-auto max-w-7xl px-6 pb-20 pt-28 sm:pt-32">
        <div class="max-w-4xl mx-auto text-center mt-12 md:mt-20">
            <h1 class="text-5xl md:text-7xl font-semibold tracking-tight text-gray-900 leading-[1.1]">
                Find your perfect <br class="hidden md:block" />
                <span class="text-gray-400">student home.</span>
            </h1>
            <p class="mt-6 text-lg md:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                Premium student accommodation near your university. Verified properties, simple booking, and zero hidden fees.
            </p>
        </div>

        @php
            $searchQ = request('q', '');
            $searchMoveIn = request('move_in', '');
            $searchGuests = (int) request('guests', 1);
            $searchGuests = max(1, min(10, $searchGuests ?: 1));
            $searchFormStrings = [
                'addDates' => __('Add dates'),
                'selectDate' => __('Select date'),
                'clear' => __('Clear'),
                'guestsHeading' => __('Guests'),
                'student' => __('student'),
                'students' => __('students'),
                'whoTravelling' => __('Who is travelling?'),
            ];
        @endphp

        <div class="mt-12 md:mt-16 max-w-4xl mx-auto">
            <form
                method="GET"
                action="{{ route('client.explore') }}"
                id="search-container"
                class="hidden md:flex items-center overflow-visible bg-white rounded-full border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 p-2"
                x-data="searchForm({ moveIn: @js($searchMoveIn), guests: {{ $searchGuests }}, strings: @js($searchFormStrings) })"
            >
                <div class="flex-1 flex items-center px-6 border-r border-gray-100">
                    <i data-lucide="map-pin" class="text-gray-400 mr-3 w-5 h-5 shrink-0"></i>
                    <div class="flex-1 min-w-0">
                        <label for="location-input" class="text-xs font-semibold text-gray-900 tracking-wide uppercase">{{ __('Location') }}</label>
                        <input
                            type="text"
                            name="q"
                            id="location-input"
                            value="{{ old('q', $searchQ) }}"
                            placeholder="{{ __('University or city') }}"
                            autocomplete="off"
                            class="w-full bg-transparent outline-none text-gray-900 placeholder-gray-400 font-medium text-base mt-0.5"
                        >
                    </div>
                </div>

                <div class="relative flex flex-1 items-center border-r border-gray-100 px-6">
                    <i data-lucide="calendar" class="pointer-events-none mr-3 h-5 w-5 shrink-0 text-gray-400"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-900">{{ __('Move in') }}</p>
                        <button
                            type="button"
                            class="mt-0.5 flex w-full items-center justify-between gap-2 text-left text-base font-medium text-gray-900 outline-none"
                            @click.stop="toggleDate()"
                            aria-haspopup="dialog"
                            x-bind:aria-expanded="dateOpen"
                        >
                            <span x-text="moveInLabel()" class="min-w-0 truncate"></span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200" x-bind:class="{ 'rotate-180': dateOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <div
                            x-show="dateOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            @click.outside="dateOpen = false"
                            x-cloak
                            class="absolute left-4 right-4 top-full z-[70] mt-2 sm:left-1 sm:right-auto sm:w-72"
                        >
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                                <label class="mb-2 block text-xs font-medium text-gray-500" x-text="strings.selectDate"></label>
                                <input
                                    type="date"
                                    name="move_in"
                                    x-model="moveIn"
                                    class="home-search-date w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900 outline-none transition-colors focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                >
                                <button type="button" class="mt-3 text-xs font-semibold text-gray-500 hover:text-gray-900" @click="clearMoveIn(); dateOpen = false" x-show="moveIn" x-text="strings.clear"></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex flex-1 items-center px-6">
                    <i data-lucide="users" class="pointer-events-none mr-3 h-5 w-5 shrink-0 text-gray-400"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-900">{{ __('Guests') }}</p>
                        <button
                            type="button"
                            class="mt-0.5 flex w-full items-center justify-between gap-2 text-left text-base font-medium text-gray-900 outline-none"
                            @click.stop="toggleGuests()"
                            aria-haspopup="dialog"
                            x-bind:aria-expanded="guestsOpen"
                        >
                            <span x-text="guestsLabel()" class="min-w-0 truncate"></span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200" x-bind:class="{ 'rotate-180': guestsOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <input type="hidden" name="guests" x-bind:value="guests">
                        <div
                            x-show="guestsOpen"
                            x-transition
                            @click.outside="guestsOpen = false"
                            x-cloak
                            class="absolute left-4 right-4 top-full z-[70] mt-2 sm:left-1 sm:right-auto sm:w-72"
                        >
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                                <p class="mb-4 text-xs font-medium text-gray-500" x-text="strings.whoTravelling"></p>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm font-medium text-gray-900" x-text="strings.guestsHeading"></span>
                                    <div class="flex items-center gap-4">
                                        <button
                                            type="button"
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-gray-200 text-lg font-medium text-gray-700 transition-colors hover:border-gray-900 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                            @click="adjustGuests(-1)"
                                            x-bind:disabled="guests <= 1"
                                            aria-label="{{ __('Decrease') }}"
                                        >−</button>
                                        <span class="min-w-[1.5rem] text-center text-base font-semibold tabular-nums" x-text="guests"></span>
                                        <button
                                            type="button"
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-gray-200 text-lg font-medium text-gray-700 transition-colors hover:border-gray-900 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                            @click="adjustGuests(1)"
                                            x-bind:disabled="guests >= 10"
                                            aria-label="{{ __('Increase') }}"
                                        >+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-black text-white h-14 w-14 rounded-full flex items-center justify-center hover:bg-gray-800 transition-transform active:scale-95 flex-shrink-0" aria-label="{{ __('Search') }}">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>
            </form>

            <form
                method="GET"
                action="{{ route('client.explore') }}"
                class="md:hidden space-y-3 rounded-3xl border border-gray-200 bg-white p-4 shadow-sm"
                x-data="searchForm({ moveIn: @js($searchMoveIn), guests: {{ $searchGuests }}, strings: @js($searchFormStrings) })"
            >
                <div class="flex items-center gap-3 rounded-2xl bg-gray-50 px-4 py-3">
                    <i data-lucide="map-pin" class="h-5 w-5 shrink-0 text-gray-500"></i>
                    <input
                        type="text"
                        name="q"
                        value="{{ old('q', $searchQ) }}"
                        placeholder="{{ __('University or city') }}"
                        class="w-full bg-transparent text-base font-medium text-gray-900 placeholder-gray-400 outline-none"
                    >
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="relative rounded-2xl border border-gray-100 px-3 py-3">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">{{ __('Move in') }}</p>
                        <button
                            type="button"
                            class="mt-1 flex w-full items-center justify-between gap-2 text-left text-sm font-medium text-gray-900"
                            @click.stop="toggleDate()"
                            x-bind:aria-expanded="dateOpen"
                        >
                            <span x-text="moveInLabel()" class="truncate"></span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400" x-bind:class="{ 'rotate-180': dateOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </button>
                        <div
                            x-show="dateOpen"
                            x-transition
                            @click.outside="dateOpen = false"
                            x-cloak
                            class="absolute left-0 right-0 top-full z-[100] mt-2 px-1"
                        >
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                                <label class="mb-2 block text-xs font-medium text-gray-500" x-text="strings.selectDate"></label>
                                <input
                                    type="date"
                                    name="move_in"
                                    x-model="moveIn"
                                    class="home-search-date w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900 outline-none focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                >
                                <button type="button" class="mt-3 text-xs font-semibold text-gray-500 hover:text-gray-900" @click="clearMoveIn(); dateOpen = false" x-show="moveIn" x-text="strings.clear"></button>
                            </div>
                        </div>
                    </div>
                    <div class="relative rounded-2xl border border-gray-100 px-3 py-3">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">{{ __('Guests') }}</p>
                        <button
                            type="button"
                            class="mt-1 flex w-full items-center justify-between gap-2 text-left text-sm font-medium text-gray-900"
                            @click.stop="toggleGuests()"
                            x-bind:aria-expanded="guestsOpen"
                        >
                            <span x-text="guestsLabel()" class="truncate"></span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400" x-bind:class="{ 'rotate-180': guestsOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </button>
                        <input type="hidden" name="guests" x-bind:value="guests">
                        <div
                            x-show="guestsOpen"
                            x-transition
                            @click.outside="guestsOpen = false"
                            x-cloak
                            class="absolute left-0 right-0 top-full z-[100] mt-2 px-1"
                        >
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                                <p class="mb-4 text-xs font-medium text-gray-500" x-text="strings.whoTravelling"></p>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm font-medium text-gray-900" x-text="strings.guestsHeading"></span>
                                    <div class="flex items-center gap-4">
                                        <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-gray-200 text-lg font-medium text-gray-700 transition-colors hover:border-gray-900 hover:bg-gray-50 disabled:opacity-40" @click="adjustGuests(-1)" x-bind:disabled="guests <= 1">−</button>
                                        <span class="min-w-[1.5rem] text-center text-base font-semibold tabular-nums" x-text="guests"></span>
                                        <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-gray-200 text-lg font-medium text-gray-700 transition-colors hover:border-gray-900 hover:bg-gray-50 disabled:opacity-40" @click="adjustGuests(1)" x-bind:disabled="guests >= 10">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-black py-3.5 text-sm font-semibold text-white transition-colors hover:bg-gray-800">
                    <i data-lucide="search" class="h-6 w-6"></i>
                    {{ __('Search stays') }}
                </button>
            </form>
        </div>
    </main>

    <section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
        <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-3xl font-semibold tracking-tight text-gray-900">{{ __('Premium Studios') }}</h2>
                <p class="mt-2 font-medium text-gray-500">{{ __('Top-rated living spaces near campus.') }}</p>
            </div>
            <a
                href="{{ route('client.explore') }}"
                class="inline-flex shrink-0 items-center text-sm font-semibold text-gray-900 transition-colors hover:text-gray-500"
            >
                {{ __('View all') }}
                <i data-lucide="chevron-right" class="ml-1 h-4 w-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            @forelse ($featuredProperties as $index => $property)
                <a href="{{ route('client.listing.show', ['slug' => \App\Support\ListingPublicId::encode($property->id)]) }}" class="group block">
                    <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-100 mb-4" data-lightbox-gallery="home-listing-{{ $property->id }}">
                        <img
                            src="{{ $property->thumbnail_url ?? 'https://picsum.photos/seed/stunest-'.$property->id.'/800/800' }}"
                            alt="{{ $property->display_title }}"
                            class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700 ease-out"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='https://picsum.photos/seed/stunest-{{ $property->id }}/800/800'"
                        >
                        <div class="absolute top-4 right-4 p-2.5 bg-white/70 backdrop-blur-md rounded-full text-gray-900 z-10 pointer-events-none">
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        </div>
                        <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-xs font-bold tracking-wide uppercase z-10 shadow-sm">{{ __('Verified') }}</div>
                    </div>
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 flex-1 pr-2">
                            <h3 class="font-semibold text-lg text-gray-900 truncate">{{ $property->display_title }}</h3>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $property->marketing_uni_line }}</p>
                            <p class="text-gray-400 text-sm">{{ $property->marketing_area_line }}</p>
                        </div>
                        <div class="flex items-center gap-1 text-sm font-medium shrink-0">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-black"></i>
                            <span>{{ $property->public_star_rating }}</span>
                        </div>
                    </div>
                    <div class="mt-2 border-t border-gray-100 pt-2">
                        <p class="font-semibold text-lg">€{{ $property->weekly_rent_display }} <span class="text-gray-500 text-sm font-normal">/pw</span></p>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-gray-50/50 px-6 py-16 text-center">
                    <p class="text-gray-500">{{ __('Published listings will appear here.') }}</p>
                    <a href="{{ route('client.explore') }}" class="mt-4 inline-block text-sm font-semibold text-gray-900 underline">{{ __('Browse all stays') }}</a>
                </div>
            @endforelse
        </div>
    </section>

    <section id="how-it-works" class="mt-8 bg-gray-50 px-6 py-24 md:py-32">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-3xl md:text-4xl font-semibold tracking-tight">Booking made beautifully simple.</h2>
                <p class="text-gray-500 mt-4 text-lg">Your journey to the perfect room in 3 transparent steps.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="search" class="w-7 h-7 text-black"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">1. Discover</h3>
                    <p class="text-gray-500 leading-relaxed max-w-xs mx-auto">
                        Search verified properties near your campus. Filter by price, amenities, and room type.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="shield-check" class="w-7 h-7 text-black"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">2. Verify &amp; Apply</h3>
                    <p class="text-gray-500 leading-relaxed max-w-xs mx-auto">
                        Review verified photos and student reviews. Submit your application instantly online.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="home" class="w-7 h-7 text-black"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">3. Move In</h3>
                    <p class="text-gray-500 leading-relaxed max-w-xs mx-auto">
                        Connect with your landlord after acceptance to arrange contract and key handover offline.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.client.partials.footer')
@endsection

@push('scripts')
    <script src="{{ asset('clients/js/lightbox.js') }}"></script>
    <script src="{{ asset('clients/js/navbar-home.js') }}"></script>
    <script src="{{ asset('clients/js/page-home.js') }}"></script>
@endpush
