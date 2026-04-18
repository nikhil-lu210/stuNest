@extends('layouts.client.app')

@section('title', $listing['title'].' | '.config('app.name'))

@section('body_class', 'bg-white font-sans text-gray-900 antialiased pb-24 lg:pb-0')

@php
    $hasProperty = isset($property) && $property !== null;
    $placeholderPhotos = [
        'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    ];
    $gallery = ($hasProperty && count($galleryUrls ?? []) > 0) ? $galleryUrls : $placeholderPhotos;
    $photoCount = count($gallery);
    $rd = $listing['rent_duration'] ?? 'week';
    $rentAmount = (int) ($listing['rent_amount'] ?? $listing['price_week'] ?? 0);
    $rentPeriodLabel = match ($rd) {
        'day' => __('day'),
        'month' => __('month'),
        default => __('week'),
    };
    $loginApplyUrl = route('login').'?redirect='.urlencode(request()->fullUrl());
    $weeksForTotal = match ($listing['min_contract_length'] ?? '') {
        '1_month' => 4,
        '3_months' => 13,
        '6_months' => 26,
        '1_year' => 52,
        default => 51,
    };
    $totalHint = ($rd === 'week') ? $rentAmount * $weeksForTotal : null;

    $googleMapsUrl = null;
    if ($hasProperty) {
        if ($property->latitude !== null && $property->latitude !== '' && $property->longitude !== null && $property->longitude !== '') {
            $googleMapsUrl = 'https://www.google.com/maps?q='.urlencode((float) $property->latitude.','.(float) $property->longitude);
        } elseif (! empty($property->map_link)) {
            $ml = (string) $property->map_link;
            if (str_contains($ml, 'google.') || str_contains($ml, 'goo.gl') || str_contains($ml, 'maps.app.goo.gl')) {
                $googleMapsUrl = $ml;
            } elseif (! empty($listing['location'])) {
                $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode((string) $listing['location']);
            }
        } elseif (! empty($listing['location'])) {
            $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode((string) $listing['location']);
        }
    } elseif (! empty($listing['location'])) {
        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode((string) $listing['location']);
    }
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/lightbox.css') }}">
    @if ($hasProperty && $property->latitude && $property->longitude)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endif
@endpush

@section('content')
    @include('layouts.client.partials.nav-explore', [
        'filters' => ['q' => '', 'move_in' => '', 'guests' => 1],
        'filterState' => [],
        'countriesForFilter' => collect(),
        'citiesForFilter' => collect(),
        'areasForFilter' => collect(),
        'showFilters' => false,
    ])

    <main
        class="max-w-7xl mx-auto px-6 pt-8 pb-16"
        @if ($hasProperty)
            data-explore-favorite-prefix="{{ url('/explore/favorites') }}/"
        @endif
    >
        <div class="mb-6">
            <h1 class="text-3xl md:text-4xl font-semibold tracking-tight mb-2">{{ $listing['title'] }}</h1>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm font-medium">
                <div class="flex items-center gap-4 text-gray-900 flex-wrap">
                    <span class="flex items-center gap-1"><i data-lucide="star" class="w-4 h-4 fill-black"></i> {{ $listing['rating'] }} ({{ $listing['reviews'] }} {{ __('reviews') }})</span>
                    <span class="text-gray-300 hidden sm:inline">•</span>
                    @if ($hasProperty && $property->status === \App\Models\Property\Property::STATUS_PUBLISHED)
                        <span class="flex items-center gap-1 text-emerald-700"><i data-lucide="check-circle" class="w-4 h-4"></i> {{ __('Published') }}</span>
                        <span class="text-gray-300 hidden sm:inline">•</span>
                    @endif
                    <span class="text-gray-700">{{ $listing['location'] }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="listing-share-btn" class="flex items-center gap-2 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition-colors border border-transparent hover:border-gray-200">
                        <i data-lucide="share" class="w-4 h-4"></i> {{ __('Share') }}
                    </button>
                    @if ($hasProperty)
                        <button
                            type="button"
                            class="explore-heart-btn flex items-center gap-2 px-3 py-1.5 rounded-lg transition-colors border border-transparent hover:border-gray-200 {{ $isSaved ? 'text-red-500' : 'text-gray-900' }}"
                            data-property-id="{{ $property->id }}"
                            data-saved="{{ $isSaved ? '1' : '0' }}"
                            aria-label="{{ __('Save property') }}"
                        >
                            <i data-lucide="heart" class="w-4 h-4 {{ $isSaved ? 'fill-red-500 text-red-500' : '' }}"></i>
                            {{ __('Save') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="hidden md:grid grid-cols-4 grid-rows-2 gap-2 h-[60vh] min-h-[400px] max-h-[500px] rounded-2xl overflow-hidden relative group" data-lightbox-gallery="property-detail">
            @foreach ($gallery as $i => $src)
                @if ($i === 0)
                    <div class="col-span-2 row-span-2 relative cursor-pointer hover:opacity-90 transition-opacity">
                        <img src="{{ $src }}" alt="" class="w-full h-full object-cover bg-gray-100" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" onerror="this.onerror=null;this.src='https://picsum.photos/seed/lg{{ $i }}/1200/900'">
                    </div>
                @elseif ($i <= 4)
                    <div class="col-span-1 row-span-1 relative cursor-pointer hover:opacity-90 transition-opacity">
                        <img src="{{ $src }}" alt="" class="w-full h-full object-cover bg-gray-100" loading="lazy" onerror="this.onerror=null;this.src='https://picsum.photos/seed/sm{{ $i }}/800/600'">
                    </div>
                @endif
            @endforeach
            @if ($photoCount > 0)
                <button type="button" data-lightbox-open="property-detail" data-lightbox-index="0" class="absolute bottom-6 right-6 bg-white border border-black text-black px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-gray-50 active:scale-95 transition-all shadow-sm z-20">
                    <i data-lucide="grid" class="w-4 h-4"></i> {{ __('Show all photos') }}
                </button>
            @endif
        </div>

        <div class="md:hidden relative aspect-[4/3] rounded-2xl overflow-hidden mt-4 bg-gray-100">
            <img src="{{ $gallery[0] ?? $placeholderPhotos[0] }}" alt="" class="w-full h-full object-cover cursor-zoom-in" data-lightbox-open="property-detail" data-lightbox-index="0" loading="eager" onerror="this.onerror=null;this.src='https://picsum.photos/seed/mob/800/600'">
            <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-semibold tracking-wide">
                1 / {{ $photoCount }}
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 mt-12 relative">
            <div class="w-full lg:w-[65%]">
                <div class="flex items-start justify-between pb-8 border-b border-gray-200 gap-4">
                    <div class="min-w-0">
                        <h2 class="text-2xl font-semibold mb-1">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) ($listing['listing_category'] ?? 'stay'))) }}</h2>
                        <p class="text-gray-500 font-medium">
                            {{ $listing['capacity'] ?? 1 }} {{ __('guests') }} • {{ $listing['bedrooms'] ?? '—' }} {{ __('beds') }} • {{ $listing['bathrooms'] ?? '—' }} {{ __('baths') }}
                            @if (!empty($listing['is_furnished']))
                                • {{ __('Furnished') }}
                            @endif
                        </p>
                        @if (!empty($listing['distance_campus']))
                            <p class="text-sm text-gray-600 mt-2 flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4 shrink-0"></i> {{ $listing['distance_campus'] }}
                            </p>
                        @endif
                        @if (!empty($listing['distance_transit']))
                            <p class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                                <i data-lucide="bus" class="w-4 h-4 shrink-0"></i> {{ $listing['distance_transit'] }}
                            </p>
                        @endif
                    </div>
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden shrink-0">
                        @if (!empty($listing['host_avatar_url']))
                            <img src="{{ $listing['host_avatar_url'] }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="text-lg font-semibold text-gray-600">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($listing['host_name'] ?? 'H', 0, 1)) }}</span>
                        @endif
                    </div>
                </div>

                <div class="py-8 border-b border-gray-200 space-y-6">
                    <div class="flex gap-4 items-start">
                        <i data-lucide="shield-check" class="w-6 h-6 text-gray-900 mt-0.5 shrink-0"></i>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ config('app.name') }} {{ __('Verified') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('This property has been reviewed by our team before publication.') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-gray-900 mt-0.5 shrink-0"></i>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ __('Students & tenancy') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Listing preference: :for. Minimum stay: :min. Deposit: :dep.', ['for' => $listing['rent_for'] ?? '—', 'min' => $listing['min_contract_label'] ?? '—', 'dep' => $listing['deposit_label'] ?? '—']) }}</p>
                        </div>
                    </div>
                    @if ($hasProperty && is_array($property->amenities) && in_array('wifi', $property->amenities, true))
                        <div class="flex gap-4 items-start">
                            <i data-lucide="wifi" class="w-6 h-6 text-gray-900 mt-0.5 shrink-0"></i>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ __('Wi‑Fi') }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Wi‑Fi is listed as an amenity for this property.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="py-8 border-b border-gray-200">
                    <h2 class="text-xl font-semibold mb-4">{{ __('About this place') }}</h2>
                    <div id="desc-text" class="text-gray-600 leading-relaxed whitespace-pre-line line-clamp-3">{{ $aboutText }}</div>
                    <button
                        type="button"
                        id="read-more-btn"
                        class="font-semibold underline mt-4 text-gray-900 hover:text-gray-600 flex items-center gap-1"
                        data-label-more="{{ __('Show more') }}"
                        data-label-less="{{ __('Show less') }}"
                    >
                        {{ __('Show more') }} <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="py-8 border-b border-gray-200">
                    <h2 class="text-xl font-semibold mb-6">{{ __('What this place offers') }}</h2>
                    @if (count($amenityRows ?? []) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
                            @foreach ($amenityRows as $row)
                                <div class="flex items-center gap-4 text-gray-700">
                                    <i data-lucide="{{ $row['icon'] }}" class="w-6 h-6 text-gray-400 shrink-0"></i> {{ $row['label'] }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('Amenity details will appear here when provided.') }}</p>
                    @endif
                </div>

                <div class="py-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4 mb-2">
                        <h2 class="text-xl font-semibold">{{ __("Where you'll be") }}</h2>
                        @if (! empty($googleMapsUrl))
                            <a
                                href="{{ $googleMapsUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition hover:border-gray-900 hover:bg-gray-50 shrink-0"
                            >
                                <i data-lucide="map-pin" class="h-4 w-4 shrink-0"></i>
                                {{ __('Open in Google Map') }}
                            </a>
                        @endif
                    </div>
                    <p class="text-gray-500 mb-6">{{ $listing['location'] }}</p>
                    @if ($hasProperty && $property->latitude && $property->longitude)
                        <div id="listing-map" class="w-full h-[400px] bg-gray-100 rounded-2xl overflow-hidden z-0"></div>
                    @elseif ($hasProperty && $property->map_link)
                        <div class="w-full h-[280px] bg-gray-100 rounded-2xl flex items-center justify-center text-gray-500 text-sm px-4 text-center">
                            {{ __('Embedded map preview is not available. Use Open in Google Map above.') }}
                        </div>
                    @else
                        <div class="w-full h-[280px] bg-gray-100 rounded-2xl flex items-center justify-center text-gray-500 text-sm">
                            {{ __('Map preview unavailable for this listing.') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="hidden lg:block w-[35%] relative">
                <div class="sticky top-28 bg-white border border-gray-200 rounded-2xl p-6 shadow-[0_12px_24px_-10px_rgba(0,0,0,0.1)]">
                    <div class="flex items-end justify-between mb-6 gap-2">
                        <div>
                            <span class="text-2xl font-bold">€{{ $rentAmount }}</span>
                            <span class="text-gray-500 font-medium"> / {{ $rentPeriodLabel }}</span>
                        </div>
                        <div class="text-sm font-medium flex items-center gap-1 shrink-0">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-black"></i> {{ $listing['rating'] }}
                        </div>
                    </div>

                    <div class="border border-gray-300 rounded-xl mb-4 overflow-hidden divide-y divide-gray-200">
                        <div class="p-3">
                            <span class="block text-[10px] font-bold uppercase tracking-wide text-gray-900 mb-1">{{ __('Minimum contract') }}</span>
                            <span class="text-sm text-gray-700">{{ $listing['min_contract_label'] ?? '—' }}</span>
                        </div>
                        <div class="p-3">
                            <span class="block text-[10px] font-bold uppercase tracking-wide text-gray-900 mb-1">{{ __('Deposit') }}</span>
                            <span class="text-sm text-gray-700">{{ $listing['deposit_label'] ?? '—' }}</span>
                        </div>
                        <div class="p-3 flex justify-between items-center">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wide text-gray-900 mb-1">{{ __('Capacity') }}</span>
                                <span class="text-sm text-gray-900">{{ $listing['capacity'] ?? 1 }} {{ __('guests') }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ $loginApplyUrl }}" class="block w-full text-center bg-black text-white py-3.5 rounded-xl font-semibold text-lg hover:bg-gray-800 transition-transform active:scale-95 mb-4">
                        {{ __('Apply to book') }}
                    </a>

                    <p class="text-center text-gray-500 text-sm mb-6">{{ __('No charge on :app — contract and deposit are agreed with the landlord.', ['app' => config('app.name')]) }}</p>

                    @if ($totalHint !== null)
                        <div class="space-y-3 text-gray-600 mb-6 text-sm">
                            <div class="flex justify-between gap-4">
                                <span>€{{ $rentAmount }} × {{ $weeksForTotal }} {{ __('weeks') }} ({{ __('estimate') }})</span>
                                <span>€{{ number_format($totalHint, 0, '.', ',') }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>{{ __('Utility bills') }}</span>
                                <span class="font-medium {{ ($listing['bills_included'] ?? '') === 'all' ? 'text-emerald-600' : 'text-gray-700' }}">
                                    {{ match ($listing['bills_included'] ?? '') {
                                        'all' => __('Included'),
                                        'some' => __('Some included'),
                                        'none' => __('Not included'),
                                        default => \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) ($listing['bills_included'] ?? '—'))),
                                    } }}
                                </span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>{{ config('app.name') }} {{ __('service fee') }}</span>
                                <span>€0</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4 flex justify-between font-semibold text-lg text-gray-900 gap-4">
                            <span>{{ __('Total (indicative)') }}</span>
                            <span>€{{ number_format($totalHint, 0, '.', ',') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <div class="lg:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-4 px-6 z-50 flex items-center justify-between gap-4">
        <div class="min-w-0">
            <div class="text-lg font-bold">€{{ $rentAmount }} <span class="text-sm font-medium text-gray-500 font-normal">/ {{ $rentPeriodLabel }}</span></div>
            <div class="text-sm text-gray-600 truncate">{{ $listing['min_contract_label'] ?? '' }}</div>
        </div>
        <a href="{{ $loginApplyUrl }}" class="shrink-0 bg-black text-white px-6 py-3 rounded-xl font-semibold hover:bg-gray-800 active:scale-95 transition-all">
            {{ __('Apply') }}
        </a>
    </div>

    @include('layouts.client.partials.footer')
@endsection

@push('scripts')
    <script src="{{ asset('clients/js/lightbox.js') }}"></script>
    <script src="{{ asset('clients/js/pages/listing-page.js') }}"></script>
    @if ($hasProperty && $property->latitude && $property->longitude)
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('listing-map');
                if (!el || typeof L === 'undefined') return;
                var lat = {{ (float) $property->latitude }};
                var lng = {{ (float) $property->longitude }};
                var map = L.map(el).setView([lat, lng], 14);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 20
                }).addTo(map);
                L.marker([lat, lng]).addTo(map);
                setTimeout(function () { map.invalidateSize(); }, 200);
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var readMoreBtn = document.getElementById('read-more-btn');
            var descText = document.getElementById('desc-text');
            if (readMoreBtn && descText) {
                var more = readMoreBtn.getAttribute('data-label-more') || 'Show more';
                var less = readMoreBtn.getAttribute('data-label-less') || 'Show less';
                readMoreBtn.addEventListener('click', function () {
                    if (descText.classList.contains('line-clamp-3')) {
                        descText.classList.remove('line-clamp-3');
                        this.innerHTML = less + ' <i data-lucide="chevron-up" class="w-4 h-4"></i>';
                    } else {
                        descText.classList.add('line-clamp-3');
                        this.innerHTML = more + ' <i data-lucide="chevron-right" class="w-4 h-4"></i>';
                    }
                    if (window.lucide) lucide.createIcons();
                });
            }
            var shareBtn = document.getElementById('listing-share-btn');
            if (shareBtn) {
                shareBtn.addEventListener('click', function () {
                    var url = window.location.href;
                    if (navigator.share) {
                        navigator.share({ title: document.title, url: url }).catch(function () {});
                    } else if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(url).then(function () {
                            alert(@json(__('Link copied')));
                        });
                    }
                });
            }
        });
    </script>
@endpush
