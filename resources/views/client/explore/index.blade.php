@extends('layouts.client.app')

@section('title', $explorePageTitle)

@section('body_class', 'bg-white font-sans text-gray-900 antialiased min-h-screen flex flex-col')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('clients/css/lightbox.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
    @include('layouts.client.partials.nav-explore', [
        'filters' => $filters,
        'filterState' => $filterState,
        'countriesForFilter' => $countriesForFilter,
        'citiesForFilter' => $citiesForFilter,
        'areasForFilter' => $areasForFilter,
    ])

    <main
        x-data="{ mapSheet: false }"
        class="flex-1 flex overflow-hidden min-h-[60vh] lg:min-h-[calc(100vh-12rem)] relative"
        data-explore-favorite-prefix="{{ url('/explore/favorites') }}/"
    >
        <section class="w-full lg:w-[55%] xl:w-[60%] flex flex-col overflow-y-auto custom-scrollbar-wide px-6 py-6 pb-24 lg:pb-6">
            <p class="text-sm font-medium text-gray-500 mb-2">
                @if ($properties->total() === 0)
                    {{ __('No places match your filters yet.') }}
                @else
                    @if ($properties->total() === 1)
                        {{ __('1 place to stay') }}
                    @else
                        {{ __(':count places to stay', ['count' => $properties->total()]) }}
                    @endif
                    @if (($placesContextLine ?? '') !== '')
                        {{ $placesContextLine }}
                    @endif
                @endif
            </p>
            <h1 class="text-3xl font-semibold tracking-tight mb-6">{{ __('Student accommodations') }}</h1>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 xl:gap-8">
                @forelse ($properties as $i => $property)
                    @php
                        $isSaved = in_array($property->id, $savedIds ?? [], true);
                    @endphp
                    <article class="relative rounded-2xl transition-shadow" data-explore-property="{{ $property->id }}">
                        <a href="{{ route('client.listing.show', ['slug' => $property->id]) }}" class="group block">
                            <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-gray-100 mb-4" data-lightbox-gallery="sr-{{ $property->id }}">
                                <img
                                    src="{{ $property->thumbnail_url ?? 'https://picsum.photos/seed/sr'.$property->id.'/800/600' }}"
                                    alt="{{ $property->display_title }}"
                                    class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700 ease-out"
                                    onerror="this.onerror=null;this.src='https://picsum.photos/seed/sr{{ $property->id }}/800/600'"
                                >
                                <div class="absolute bottom-3 left-3 px-2 py-1 bg-white/90 backdrop-blur-md rounded-lg text-xs font-semibold z-[1] flex items-center gap-1">
                                    <i data-lucide="star" class="w-3 h-3 fill-black text-black"></i> {{ $property->public_star_rating }}
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-start mb-1 gap-2">
                                    <h3 class="font-semibold text-gray-900 truncate text-base min-w-0">{{ $property->display_title }}</h3>
                                    <p class="font-semibold text-base shrink-0">€{{ $property->weekly_rent_display }}<span class="text-gray-500 font-normal text-sm">/pw</span></p>
                                </div>
                                <p class="text-gray-500 text-sm mb-1 truncate">{{ $property->marketing_uni_line }}</p>
                                <p class="text-gray-400 text-sm">{{ $property->marketing_area_line }}</p>
                            </div>
                        </a>
                        <button
                            type="button"
                            class="explore-heart-btn absolute top-3 right-3 p-2 bg-white/70 backdrop-blur-md rounded-full hover:bg-white hover:scale-110 transition-all z-10 {{ $isSaved ? 'text-red-500' : 'text-gray-900' }}"
                            data-property-id="{{ $property->id }}"
                            data-saved="{{ $isSaved ? '1' : '0' }}"
                            aria-label="{{ __('Save property') }}"
                        >
                            <i data-lucide="heart" class="w-4 h-4 {{ $isSaved ? 'fill-red-500 text-red-500' : '' }}"></i>
                        </button>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center">
                        <p class="text-gray-600">{{ __('Try widening your search or clearing filters.') }}</p>
                        <a href="{{ route('client.explore') }}" class="mt-4 inline-block text-sm font-semibold text-gray-900 underline">{{ __('View all published stays') }}</a>
                    </div>
                @endforelse
            </div>

            @if ($properties->hasPages())
                <div class="mt-10 pb-12">
                    {{ $properties->links() }}
                </div>
            @endif
        </section>

        <section class="hidden lg:flex lg:w-[45%] xl:w-[40%] bg-gray-100 relative border-l border-gray-200 min-h-[50vh] lg:min-h-0 lg:self-stretch">
            <div id="explore-map" class="absolute inset-0 z-0 min-h-[420px]"></div>
        </section>

        <div
            x-show="mapSheet"
            x-cloak
            x-transition
            class="fixed inset-0 z-[90] flex flex-col bg-white lg:hidden"
        >
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-gray-100 shrink-0">
                <button
                    type="button"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                    @click="mapSheet = false"
                >
                    {{ __('Close') }}
                </button>
                <span class="text-sm font-semibold text-gray-900">{{ __('Map') }}</span>
                <span class="w-12"></span>
            </div>
            <div id="explore-map-mobile" class="flex-1 min-h-0 w-full"></div>
        </div>

        <div class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40" x-show="!mapSheet" x-cloak>
            <button
                type="button"
                class="bg-gray-900 text-white px-5 py-3 rounded-full font-medium text-sm shadow-xl flex items-center gap-2 hover:bg-black hover:scale-105 transition-all active:scale-95"
                @click="mapSheet = true; $nextTick(() => { if (window.initExploreMapMobile) window.initExploreMapMobile(); if (window.lucide) lucide.createIcons(); })"
            >
                {{ __('Map') }} <i data-lucide="map" class="w-4 h-4"></i>
            </button>
        </div>
    </main>

    @include('layouts.client.partials.footer')
@endsection

@push('scripts')
    <script>
        window.__EXPLORE_MARKERS__ = @json($mapMarkers ?? []);
    </script>
    <script src="{{ asset('clients/js/lightbox.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('clients/js/pages/explore-page.js') }}"></script>
@endpush
