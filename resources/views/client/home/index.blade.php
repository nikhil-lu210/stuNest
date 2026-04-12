@extends('layouts.client.app')

@section('title', config('app.name').' | Premium Student Housing')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/lightbox.css') }}">
@endpush

@section('content')
    @include('layouts.client.partials.nav-marketing')

    <main class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
        <div class="max-w-4xl mx-auto text-center mt-12 md:mt-20">
            <h1 class="text-5xl md:text-7xl font-semibold tracking-tight text-gray-900 leading-[1.1]">
                Find your perfect <br class="hidden md:block" />
                <span class="text-gray-400">student home.</span>
            </h1>
            <p class="mt-6 text-lg md:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                Premium student accommodation near your university. Verified properties, simple booking, and zero hidden fees.
            </p>
        </div>

        <div class="mt-12 md:mt-16 max-w-4xl mx-auto">
            <div id="search-container" class="hidden md:flex items-center bg-white rounded-full border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 p-2">
                <div class="flex-1 flex items-center px-6 border-r border-gray-100">
                    <i data-lucide="map-pin" class="text-gray-400 mr-3 w-5 h-5"></i>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-900 tracking-wide uppercase">Location</p>
                        <input
                            type="text"
                            id="location-input"
                            placeholder="University or City"
                            class="w-full bg-transparent outline-none text-gray-900 placeholder-gray-400 font-medium text-base mt-0.5"
                        >
                    </div>
                </div>

                <div class="flex-1 flex items-center px-6 border-r border-gray-100 cursor-pointer group">
                    <i data-lucide="calendar" class="text-gray-400 mr-3 w-5 h-5 group-hover:text-black transition-colors"></i>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 tracking-wide uppercase">Move in</p>
                        <p class="text-gray-400 font-medium text-base mt-0.5 group-hover:text-gray-900 transition-colors">Add dates</p>
                    </div>
                </div>

                <div class="flex-1 flex items-center px-6 cursor-pointer group">
                    <i data-lucide="users" class="text-gray-400 mr-3 w-5 h-5 group-hover:text-black transition-colors"></i>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 tracking-wide uppercase">Guests</p>
                        <p class="text-gray-400 font-medium text-base mt-0.5 group-hover:text-gray-900 transition-colors">1 Student</p>
                    </div>
                </div>

                <a href="{{ route('client.explore') }}" class="bg-black text-white h-14 w-14 rounded-full flex items-center justify-center hover:bg-gray-800 transition-transform active:scale-95 flex-shrink-0" aria-label="Search">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </a>
            </div>

            <a href="{{ route('client.explore') }}" class="md:hidden block bg-white rounded-3xl border border-gray-200 shadow-sm p-4">
                <div class="flex items-center bg-gray-50 rounded-2xl p-4">
                    <i data-lucide="search" class="text-gray-900 mr-3 w-5 h-5"></i>
                    <span class="w-full text-left text-gray-500 font-medium">Where are you studying?</span>
                </div>
            </a>
        </div>
    </main>

    <section class="py-20 px-6 max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-semibold tracking-tight">Premium Studios</h2>
                <p class="text-gray-500 mt-2 font-medium">Top-rated living spaces near campus.</p>
            </div>
            <a href="{{ route('client.explore') }}" class="hidden md:flex items-center text-sm font-semibold hover:text-gray-500 transition-colors">
                View all <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            @foreach ($featuredListings as $index => $item)
                <a href="{{ route('client.listing.show', ['slug' => $item['slug']]) }}" class="group block">
                    <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-100 mb-4" data-lightbox-gallery="home-listing-{{ $index + 1 }}">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700 ease-out" loading="lazy" onerror="this.onerror=null;this.src='https://picsum.photos/seed/stunest-{{ $index }}/800/800'">
                        <div class="absolute top-4 right-4 p-2.5 bg-white/70 backdrop-blur-md rounded-full text-gray-900 z-10 pointer-events-none">
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        </div>
                        <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-xs font-bold tracking-wide uppercase z-10 shadow-sm">Verified</div>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900 truncate">{{ $item['title'] }}</h3>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $item['uni'] }}</p>
                            <p class="text-gray-400 text-sm">{{ $item['area'] }}</p>
                        </div>
                        <div class="flex items-center gap-1 text-sm font-medium">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-black"></i>
                            <span>{{ $item['rating'] }}</span>
                        </div>
                    </div>
                    <div class="mt-2 border-t border-gray-100 pt-2">
                        <p class="font-semibold text-lg">€{{ $item['price'] }} <span class="text-gray-500 text-sm font-normal">/pw</span></p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section id="how-it-works" class="bg-gray-50 py-32 px-6 mt-12">
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
