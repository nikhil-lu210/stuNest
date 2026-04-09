@extends('layouts.client.app')

@section('title', $listing['title'].' | '.config('app.name'))

@section('body_class', 'bg-white font-sans text-gray-900 antialiased pb-24 lg:pb-0')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/lightbox.css') }}">
@endpush

@section('content')
    @include('layouts.client.partials.nav-explore', ['showFilters' => false])

    <main class="max-w-7xl mx-auto px-6 pt-8 pb-16">
        <div class="mb-6">
            <h1 class="text-3xl md:text-4xl font-semibold tracking-tight mb-2">{{ $listing['title'] }}</h1>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm font-medium">
                <div class="flex items-center gap-4 text-gray-900 flex-wrap">
                    <span class="flex items-center gap-1"><i data-lucide="star" class="w-4 h-4 fill-black"></i> {{ $listing['rating'] }} ({{ $listing['reviews'] }} reviews)</span>
                    <span class="text-gray-300 hidden sm:inline">•</span>
                    <span class="flex items-center gap-1"><i data-lucide="medal" class="w-4 h-4"></i> Superhost</span>
                    <span class="text-gray-300 hidden sm:inline">•</span>
                    <span class="underline cursor-pointer">{{ $listing['location'] }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <button type="button" class="flex items-center gap-2 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition-colors">
                        <i data-lucide="share" class="w-4 h-4"></i> Share
                    </button>
                    <button type="button" class="flex items-center gap-2 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition-colors">
                        <i data-lucide="heart" class="w-4 h-4"></i> Save
                    </button>
                </div>
            </div>
        </div>

        <div class="hidden md:grid grid-cols-4 grid-rows-2 gap-2 h-[60vh] min-h-[400px] max-h-[500px] rounded-2xl overflow-hidden relative group" data-lightbox-gallery="property-detail">
            <div class="col-span-2 row-span-2 relative cursor-pointer hover:opacity-90 transition-opacity">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Main living space" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/seed/pd-main/1200/900'">
            </div>
            <div class="col-span-1 row-span-1 relative cursor-pointer hover:opacity-90 transition-opacity">
                <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Kitchenette" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/seed/pd-kit/800/600'">
            </div>
            <div class="col-span-1 row-span-1 relative cursor-pointer hover:opacity-90 transition-opacity">
                <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Study Desk" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/seed/pd-desk/800/600'">
            </div>
            <div class="col-span-1 row-span-1 relative cursor-pointer hover:opacity-90 transition-opacity">
                <img src="https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Bathroom" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/seed/pd-bath/800/600'">
            </div>
            <div class="col-span-1 row-span-1 relative cursor-pointer hover:opacity-90 transition-opacity">
                <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Common area" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/seed/pd-common/800/600'">
            </div>
            <button type="button" data-lightbox-open="property-detail" data-lightbox-index="0" class="absolute bottom-6 right-6 bg-white border border-black text-black px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-gray-50 active:scale-95 transition-all shadow-sm z-20">
                <i data-lucide="grid" class="w-4 h-4"></i> Show all photos
            </button>
        </div>

        <div class="md:hidden relative aspect-[4/3] rounded-2xl overflow-hidden mt-4">
            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Main living space" class="w-full h-full object-cover cursor-zoom-in" data-lightbox-open="property-detail" data-lightbox-index="0" onerror="this.onerror=null;this.src='https://picsum.photos/seed/pd-mob/800/600'">
            <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-semibold tracking-wide">
                1 / 5
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 mt-12 relative">
            <div class="w-full lg:w-[65%]">
                <div class="flex items-start justify-between pb-8 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-semibold mb-1">Entire Studio managed by Chapter</h2>
                        <p class="text-gray-500 font-medium">1 Student • 1 Bed • 1 Private Bath • Kitchenette</p>
                    </div>
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden shrink-0">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Host Profile" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/seed/pd-host/150/150'">
                    </div>
                </div>

                <div class="py-8 border-b border-gray-200 space-y-6">
                    <div class="flex gap-4 items-start">
                        <i data-lucide="shield-check" class="w-6 h-6 text-gray-900 mt-0.5 shrink-0"></i>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ config('app.name') }} Verified</h3>
                            <p class="text-sm text-gray-500 mt-1">This property has been reviewed by our team before publication.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-gray-900 mt-0.5 shrink-0"></i>
                        <div>
                            <h3 class="font-semibold text-gray-900">Students Only</h3>
                            <p class="text-sm text-gray-500 mt-1">You must provide a valid university ID or acceptance letter to apply.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <i data-lucide="wifi" class="w-6 h-6 text-gray-900 mt-0.5 shrink-0"></i>
                        <div>
                            <h3 class="font-semibold text-gray-900">Ultra-fast WiFi</h3>
                            <p class="text-sm text-gray-500 mt-1">Free broadband included in your rent where stated by the landlord.</p>
                        </div>
                    </div>
                </div>

                <div class="py-8 border-b border-gray-200">
                    <h2 class="text-xl font-semibold mb-4">About this place</h2>
                    <div id="desc-text" class="text-gray-600 leading-relaxed line-clamp-3">
                        The Oxford Studio is a beautifully designed, self-contained living space created specifically for students who want a premium experience. Located just a 15-minute walk from UCL and Central Saint Martins, you'll be perfectly positioned for both study and London life.
                        <br><br>
                        Your studio comes fully furnished with a double bed, ample storage, a dedicated study desk, and a private en-suite bathroom. The modern kitchenette features a hob, microwave oven, and fridge-freezer.
                        <br><br>
                        Residents also get exclusive access to building amenities including a 24/7 gym, cinema room, and communal study areas. Confirm bills and tenancy details directly with the landlord after your application is accepted.
                    </div>
                    <button type="button" id="read-more-btn" class="font-semibold underline mt-4 text-gray-900 hover:text-gray-600 flex items-center gap-1">
                        Show more <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="py-8 border-b border-gray-200">
                    <h2 class="text-xl font-semibold mb-6">What this place offers</h2>
                    <div class="grid grid-cols-2 gap-y-5 gap-x-8">
                        <div class="flex items-center gap-4 text-gray-700"><i data-lucide="wifi" class="w-6 h-6 text-gray-400"></i> Free WiFi</div>
                        <div class="flex items-center gap-4 text-gray-700"><i data-lucide="flame" class="w-6 h-6 text-gray-400"></i> Heating</div>
                        <div class="flex items-center gap-4 text-gray-700"><i data-lucide="washing-machine" class="w-6 h-6 text-gray-400"></i> Laundry facilities</div>
                        <div class="flex items-center gap-4 text-gray-700"><i data-lucide="dumbbell" class="w-6 h-6 text-gray-400"></i> Building Gym</div>
                    </div>
                </div>

                <div class="py-8">
                    <h2 class="text-xl font-semibold mb-2">Where you'll be</h2>
                    <p class="text-gray-500 mb-6">{{ $listing['location'] }}</p>
                    <div class="w-full h-[400px] bg-gray-200 rounded-2xl overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Map — London" class="w-full h-full object-cover opacity-80 mix-blend-multiply grayscale" onerror="this.onerror=null;this.src='https://picsum.photos/1200/800'">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                            <div class="bg-black text-white p-3 rounded-full shadow-2xl flex items-center justify-center">
                                <i data-lucide="home" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Mapbox integration will replace this placeholder in a later release.</p>
                </div>
            </div>

            <div class="hidden lg:block w-[35%] relative">
                <div class="sticky top-28 bg-white border border-gray-200 rounded-2xl p-6 shadow-[0_12px_24px_-10px_rgba(0,0,0,0.1)]">
                    <div class="flex items-end justify-between mb-6">
                        <div>
                            <span class="text-2xl font-bold">£{{ $listing['price_week'] }}</span>
                            <span class="text-gray-500 font-medium"> / week</span>
                        </div>
                        <div class="text-sm font-medium flex items-center gap-1">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-black"></i> {{ $listing['rating'] }} <span class="text-gray-400 underline cursor-pointer ml-1">{{ $listing['reviews'] }} reviews</span>
                        </div>
                    </div>

                    <div class="border border-gray-300 rounded-xl mb-4 overflow-hidden">
                        <div class="flex border-b border-gray-300">
                            <div class="w-1/2 p-3 border-r border-gray-300 cursor-pointer hover:bg-gray-50 transition-colors">
                                <span class="block text-[10px] font-bold uppercase tracking-wide text-gray-900 mb-1">Move-in</span>
                                <span class="text-sm text-gray-500">14 Sep 2026</span>
                            </div>
                            <div class="w-1/2 p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                                <span class="block text-[10px] font-bold uppercase tracking-wide text-gray-900 mb-1">Contract</span>
                                <span class="text-sm text-gray-500">51 Weeks</span>
                            </div>
                        </div>
                        <div class="p-3 cursor-pointer hover:bg-gray-50 transition-colors flex justify-between items-center">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wide text-gray-900 mb-1">Guests</span>
                                <span class="text-sm text-gray-900">1 Student</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="block w-full text-center bg-black text-white py-3.5 rounded-xl font-semibold text-lg hover:bg-gray-800 transition-transform active:scale-95 mb-4">
                        Apply to book
                    </a>

                    <p class="text-center text-gray-500 text-sm mb-6">No charge on {{ config('app.name') }} — contract and deposit are agreed offline with the landlord.</p>

                    <div class="space-y-3 text-gray-600 mb-6 text-sm">
                        <div class="flex justify-between">
                            <span>£{{ $listing['price_week'] }} × 51 weeks</span>
                            <span>£14,535</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Utility bills</span>
                            <span class="text-green-600 font-medium">Included</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ config('app.name') }} service fee</span>
                            <span>£0</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-between font-semibold text-lg text-gray-900">
                        <span>Total contract value</span>
                        <span>£14,535</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="lg:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-4 px-6 z-50 flex items-center justify-between">
        <div>
            <div class="text-lg font-bold">£{{ $listing['price_week'] }} <span class="text-sm font-medium text-gray-500 font-normal">/ week</span></div>
            <div class="text-sm font-medium underline text-gray-900 cursor-pointer">14 Sep · 51 Weeks</div>
        </div>
        <a href="{{ route('login') }}" class="bg-black text-white px-8 py-3 rounded-xl font-semibold hover:bg-gray-800 active:scale-95 transition-all">
            Apply
        </a>
    </div>

    @include('layouts.client.partials.footer')
@endsection

@push('scripts')
    <script src="{{ asset('clients/js/lightbox.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var readMoreBtn = document.getElementById('read-more-btn');
            var descText = document.getElementById('desc-text');
            if (!readMoreBtn || !descText) return;
            readMoreBtn.addEventListener('click', function () {
                if (descText.classList.contains('line-clamp-3')) {
                    descText.classList.remove('line-clamp-3');
                    this.innerHTML = 'Show less <i data-lucide="chevron-up" class="w-4 h-4"></i>';
                } else {
                    descText.classList.add('line-clamp-3');
                    this.innerHTML = 'Show more <i data-lucide="chevron-right" class="w-4 h-4"></i>';
                }
                if (window.lucide) lucide.createIcons();
            });
        });
    </script>
@endpush
