@php
    $exploreFilters = $filters ?? ['q' => '', 'move_in' => '', 'guests' => 1];
    $exploreGuestCount = (int) ($exploreFilters['guests'] ?? 1);
    $exploreGuestCount = max(1, min(10, $exploreGuestCount ?: 1));
    $exploreLocationLabel = ($exploreFilters['q'] ?? '') !== '' ? $exploreFilters['q'] : __('London');
    $exploreMoveIn = $exploreFilters['move_in'] ?? '';
    try {
        $exploreMoveInLabel = $exploreMoveIn !== '' ? \Illuminate\Support\Carbon::parse($exploreMoveIn)->format('M Y') : __('Add dates');
    } catch (\Throwable $e) {
        $exploreMoveInLabel = __('Add dates');
    }
    $exploreGuestLabel = $exploreGuestCount === 1 ? '1 '.__('student') : $exploreGuestCount.' '.__('students');
@endphp
{{-- Explore / listing header with search bar + filters --}}
<header class="border-b border-gray-100 bg-white z-50 shrink-0">
    <div class="px-6 h-20 flex items-center justify-between gap-4">
        <a href="{{ route('client.home') }}" class="flex items-center gap-2 w-48 shrink-0">
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl leading-none tracking-tighter">S</span>
            </div>
            <span class="text-xl font-bold tracking-tight hidden lg:block">{{ config('app.name') }}.</span>
        </a>

        <a href="{{ route('client.explore', array_filter(['q' => $exploreFilters['q'] ?? null, 'move_in' => $exploreMoveIn ?: null, 'guests' => $exploreGuestCount])) }}" class="hidden md:flex items-center bg-white border border-gray-200 rounded-full shadow-sm hover:shadow-md transition-shadow duration-300 py-2 px-4 cursor-pointer max-w-xl min-w-0 flex-1 justify-center">
            <span class="text-sm font-semibold px-4 border-r border-gray-200 truncate">{{ $exploreLocationLabel }}</span>
            <span class="text-sm font-medium text-gray-500 px-4 border-r border-gray-200 shrink-0">{{ $exploreMoveInLabel }}</span>
            <span class="text-sm font-medium text-gray-500 px-4 shrink-0 truncate">{{ $exploreGuestLabel }}</span>
            <div class="bg-black text-white p-2 rounded-full ml-2 shrink-0">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
        </a>

        <div class="md:hidden flex-1 flex justify-center min-w-0">
            <a href="{{ route('client.explore', array_filter(['q' => $exploreFilters['q'] ?? null, 'move_in' => $exploreMoveIn ?: null, 'guests' => $exploreGuestCount])) }}" class="flex items-center gap-2 bg-gray-50 rounded-full py-2 px-4 border border-gray-100 w-full max-w-sm min-w-0">
                <i data-lucide="search" class="w-4 h-4 text-gray-500 shrink-0"></i>
                <span class="text-sm font-medium truncate">{{ $exploreLocationLabel }}</span>
            </a>
        </div>

        <div class="flex items-center justify-end gap-3 w-48 shrink-0">
            <a href="{{ url('/register?role=landlord') }}" class="hidden lg:block text-sm font-medium hover:text-gray-600 transition-colors whitespace-nowrap">
                List your property
            </a>
            @guest
                <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-gray-600 hover:text-gray-900">Log in</a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-10 h-10 sm:w-auto sm:h-auto sm:px-4 sm:py-2 bg-black text-white text-sm font-medium rounded-full hover:bg-gray-800 transition-colors">
                    <span class="hidden sm:inline">Sign up</span>
                    <i data-lucide="user" class="w-4 h-4 sm:hidden"></i>
                </a>
            @else
                @include('layouts.client.partials.nav-user-menu')
            @endguest
        </div>
    </div>

    @if ($showFilters ?? true)
    <div class="px-6 py-4 flex items-center gap-3 overflow-x-auto no-scrollbar border-t border-gray-50">
        <button type="button" class="flex items-center gap-2 border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
            Filters
        </button>
        <div class="w-px h-6 bg-gray-200 shrink-0 mx-2"></div>
        <button type="button" class="filter-btn border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0">Price</button>
        <button type="button" class="filter-btn border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0">En-suite</button>
        <button type="button" class="filter-btn border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0">Studio</button>
        <button type="button" class="filter-btn border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0">Gym</button>
        <button type="button" class="filter-btn border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0">Bills Included</button>
        <button type="button" class="filter-btn border border-gray-200 rounded-full px-4 py-2 text-sm font-medium hover:border-black transition-colors whitespace-nowrap shrink-0">Distance to Uni</button>
    </div>
    @endif
</header>
