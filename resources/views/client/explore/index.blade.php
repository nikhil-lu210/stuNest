@extends('layouts.client.app')

@section('title', 'Explore London | '.config('app.name'))

@section('body_class', 'bg-white font-sans text-gray-900 antialiased min-h-screen flex flex-col')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('clients/css/lightbox.css') }}">
@endpush

@section('content')
    @include('layouts.client.partials.nav-explore')

    <main class="flex-1 flex overflow-hidden min-h-[60vh] lg:min-h-[calc(100vh-12rem)]">
        <section class="w-full lg:w-[55%] xl:w-[60%] flex-col overflow-y-auto custom-scrollbar-wide px-6 py-6 pb-24 lg:pb-6">
            <p class="text-sm font-medium text-gray-500 mb-2">320+ places to stay in London</p>
            <h1 class="text-3xl font-semibold tracking-tight mb-6">Student accommodations</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 xl:gap-8">
                @php
                    $cards = [
                        ['title' => 'The Oxford Studio', 'price' => '285', 'line1' => 'Premium Studio • Islington', 'line2' => '1.2 miles to UCL', 'img' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'slug' => 'the-oxford-studio', 'star' => '4.9'],
                        ['title' => 'Nova Premium En-suite', 'price' => '320', 'line1' => 'En-suite Room • Camden Town', 'line2' => "0.8 miles to King's College", 'img' => 'https://images.unsplash.com/photo-1502672260266-1c1c294036f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'slug' => 'nova-premium-ensuite', 'star' => '4.8'],
                        ['title' => 'Apex Student Living', 'price' => '210', 'line1' => 'Shared Apartment • Wembley', 'line2' => '2.5 miles to Imperial College', 'img' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'slug' => 'apex-student-living', 'star' => '4.7'],
                        ['title' => 'The Chapter Loft', 'price' => '395', 'line1' => 'Luxury Studio • Spitalfields', 'line2' => '0.5 miles to LSE', 'img' => 'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'slug' => 'the-chapter-loft', 'star' => '5.0', 'rare' => true],
                        ['title' => 'Battersea Residence', 'price' => '260', 'line1' => 'Standard Studio • Battersea', 'line2' => "1.8 miles to King's College", 'img' => 'https://images.unsplash.com/photo-1499955085172-a104c9463ece?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'slug' => 'battersea-residence', 'star' => '4.6'],
                        ['title' => 'Stratford Quarters', 'price' => '230', 'line1' => 'En-suite Room • Stratford', 'line2' => 'Next to Queen Mary Uni', 'img' => 'https://images.unsplash.com/photo-1540518614846-7eded433c457?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'slug' => 'stratford-quarters', 'star' => '4.5'],
                    ];
                @endphp
                @foreach ($cards as $i => $c)
                    <a href="{{ route('client.listing.show', ['slug' => $c['slug']]) }}" class="group block">
                        <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-gray-100 mb-4" data-lightbox-gallery="sr-{{ $i + 1 }}">
                            <img src="{{ $c['img'] }}" alt="{{ $c['title'] }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null;this.src='https://picsum.photos/seed/sr{{ $i }}/800/600'">
                            <span class="heart-btn absolute top-3 right-3 p-2 bg-white/70 backdrop-blur-md rounded-full text-gray-900 hover:bg-white hover:scale-110 transition-all z-10">
                                <i data-lucide="heart" class="w-4 h-4"></i>
                            </span>
                            @if (!empty($c['rare']))
                                <div class="absolute top-3 left-3 px-2 py-1 bg-black text-white rounded-lg text-[10px] font-bold tracking-wider uppercase z-10">Rare Find</div>
                            @endif
                            <div class="absolute bottom-3 left-3 px-2 py-1 bg-white/90 backdrop-blur-md rounded-lg text-xs font-semibold z-10 flex items-center gap-1">
                                <i data-lucide="star" class="w-3 h-3 fill-black text-black"></i> {{ $c['star'] }}
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-semibold text-gray-900 truncate pr-4 text-base">{{ $c['title'] }}</h3>
                                <p class="font-semibold text-base shrink-0">£{{ $c['price'] }}<span class="text-gray-500 font-normal text-sm">/pw</span></p>
                            </div>
                            <p class="text-gray-500 text-sm mb-1 truncate">{{ $c['line1'] }}</p>
                            <p class="text-gray-400 text-sm">{{ $c['line2'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 text-center pb-12">
                <p class="text-sm text-gray-500 mb-4">Continue exploring London</p>
                <button type="button" class="bg-gray-900 text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-black transition-colors">Show more</button>
            </div>
        </section>

        <section class="hidden lg:block lg:w-[45%] xl:w-[40%] bg-gray-200 relative border-l border-gray-200 min-h-[50vh]">
            <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Map View" class="w-full h-full min-h-[420px] object-cover opacity-60 mix-blend-multiply grayscale" onerror="this.onerror=null;this.src='https://picsum.photos/1200/800'">
            <div class="absolute inset-0 p-4">
                <div class="absolute top-[25%] left-[30%] group cursor-pointer transition-transform hover:scale-105 hover:z-20">
                    <div class="bg-white px-3 py-1.5 rounded-full shadow-lg font-bold text-sm border border-gray-200 group-hover:bg-black group-hover:text-white transition-colors">£285</div>
                </div>
                <div class="absolute top-[45%] left-[50%] group cursor-pointer transition-transform hover:scale-105 hover:z-10">
                    <div class="bg-black text-white px-3 py-1.5 rounded-full shadow-lg font-bold text-sm border border-black scale-110">£320</div>
                </div>
                <div class="absolute top-[60%] left-[20%] group cursor-pointer transition-transform hover:scale-105 hover:z-20">
                    <div class="bg-white px-3 py-1.5 rounded-full shadow-lg font-bold text-sm border border-gray-200 group-hover:bg-black group-hover:text-white transition-colors">£210</div>
                </div>
                <div class="absolute top-[35%] right-[25%] group cursor-pointer transition-transform hover:scale-105 hover:z-20">
                    <div class="bg-white px-3 py-1.5 rounded-full shadow-lg font-bold text-sm border border-gray-200 group-hover:bg-black group-hover:text-white transition-colors">£395</div>
                </div>
            </div>
            <div class="absolute right-6 bottom-8 flex flex-col gap-2">
                <button type="button" class="w-10 h-10 bg-white rounded-lg shadow-md flex items-center justify-center hover:bg-gray-50 active:scale-95 transition-all text-gray-700" aria-label="Zoom in">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                </button>
                <button type="button" class="w-10 h-10 bg-white rounded-lg shadow-md flex items-center justify-center hover:bg-gray-50 active:scale-95 transition-all text-gray-700" aria-label="Zoom out">
                    <i data-lucide="minus" class="w-5 h-5"></i>
                </button>
            </div>
        </section>

        <div class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40">
            <button type="button" class="bg-gray-900 text-white px-5 py-3 rounded-full font-medium text-sm shadow-xl flex items-center gap-2 hover:bg-black hover:scale-105 transition-all active:scale-95">
                Map <i data-lucide="map" class="w-4 h-4"></i>
            </button>
        </div>
    </main>

    @include('layouts.client.partials.footer')
@endsection

@push('scripts')
    <script src="{{ asset('clients/js/lightbox.js') }}"></script>
    <script src="{{ asset('clients/js/pages/search-result.js') }}"></script>
@endpush
