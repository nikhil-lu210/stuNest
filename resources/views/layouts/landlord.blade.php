<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? $__env->yieldContent('title', __('Landlord Dashboard')) }} | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('clients/js/tailwind-config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('clients/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('clients/css/dashboard.css') }}">

    <script src="https://unpkg.com/lucide@latest"></script>
    <style>[x-cloak]{display:none !important;}</style>
    @stack('styles')
    @stack('head')

    @livewireStyles
    {{-- Alpine ships with Livewire; loading alpinejs CDN as well runs Alpine.start() twice and breaks UI (e.g. notification bell). --}}
</head>
@php
    $landlordUser = auth()->user();
    $landlordAvatarUrl = $landlordUser?->getFirstMediaUrl('avatar', 'thumb') ?: $landlordUser?->getFirstMediaUrl('avatar');
    $landlordInitial = strtoupper(mb_substr((string) ($landlordUser?->first_name ?: $landlordUser?->email ?? 'H'), 0, 1));
@endphp
<body class="bg-gray-50 font-sans text-gray-900 antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <x-toast />

    <div
        class="md:hidden fixed inset-0 z-[55] bg-gray-900/40 transition-opacity"
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    <!-- --- SIDEBAR (Desktop + mobile drawer) --- -->
    <aside
        class="flex flex-col w-64 bg-white border-r border-gray-200 h-full shrink-0 fixed md:relative inset-y-0 left-0 z-[60] transition-transform duration-200 ease-out md:!translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Logo -->
        <a href="{{ route('client.landlord.dashboard') }}" class="h-20 flex items-center px-6 border-b border-gray-100 cursor-pointer" @click="sidebarOpen = false">
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center mr-2">
                <span class="text-white font-bold text-xl leading-none tracking-tighter">S</span>
            </div>
            <span class="text-xl font-bold tracking-tight">StuNest.</span>
            <span class="ml-2 text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full uppercase tracking-wider">Host</span>
        </a>

        <div class="md:hidden flex justify-end px-2 py-2 border-b border-gray-100">
            <button type="button" class="p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50" @click="sidebarOpen = false" aria-label="{{ __('Close menu') }}">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a
                href="{{ route('client.landlord.dashboard') }}"
                @click="sidebarOpen = false"
                @class([
                    'nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors',
                    'bg-gray-50 text-gray-900 font-semibold' => request()->routeIs('client.landlord.dashboard'),
                    'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.landlord.dashboard'),
                ])
            >
                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                {{ __('Dashboard Overview') }}
            </a>
            <a
                href="{{ route('client.landlord.properties.index') }}"
                @click="sidebarOpen = false"
                @class([
                    'nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors',
                    'bg-gray-50 text-gray-900 font-semibold' => request()->routeIs('client.landlord.properties.*'),
                    'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.landlord.properties.*'),
                ])
            >
                <i data-lucide="building" class="w-5 h-5"></i>
                {{ __('My Properties') }}
            </a>
            <a
                href="{{ route('client.landlord.applications.index') }}"
                @click="sidebarOpen = false"
                @class([
                    'nav-btn w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-colors',
                    'bg-gray-50 text-gray-900 font-semibold' => request()->routeIs('client.landlord.applications.*'),
                    'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.landlord.applications.*'),
                ])
            >
                <div class="flex items-center gap-3">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    {{ __('Applications') }}
                </div>
                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">3 New</span>
            </a>
            <a
                href="{{ route('client.landlord.messages.index') }}"
                @click="sidebarOpen = false"
                @class([
                    'nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors',
                    'bg-gray-50 text-gray-900 font-semibold' => request()->routeIs('client.landlord.messages.*'),
                    'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.landlord.messages.*'),
                ])
            >
                <i data-lucide="message-square" class="w-5 h-5"></i>
                {{ __('Messages') }}
            </a>
            <div class="pt-4 mt-4 border-t border-gray-100">
                <a
                    href="{{ route('client.landlord.settings.index') }}"
                    @click="sidebarOpen = false"
                    @class([
                        'nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors',
                        'bg-gray-50 text-gray-900 font-semibold' => request()->routeIs('client.landlord.settings.*'),
                        'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.landlord.settings.*'),
                    ])
                >
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    {{ __('Settings') }}
                </a>
            </div>
        </nav>

        <!-- User Profile (Bottom Sidebar) -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden shrink-0">
                    @if ($landlordAvatarUrl)
                        <img src="{{ $landlordAvatarUrl }}" alt="{{ __('Host profile') }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-sm font-bold text-gray-600">{{ $landlordInitial }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $landlordUser?->first_name }} {{ $landlordUser?->last_name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ __('Host') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-black p-1" aria-label="{{ __('Log out') }}">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- --- MOBILE HEADER --- -->
    <header class="md:hidden fixed top-0 w-full h-16 bg-white border-b border-gray-200 z-50 flex items-center justify-between px-4">
        <div class="flex items-center gap-1 min-w-0">
            <button type="button" class="p-2 -ml-2 text-gray-600 hover:text-black shrink-0 rounded-lg" @click="sidebarOpen = true" aria-label="{{ __('Open menu') }}">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <a href="{{ route('client.landlord.dashboard') }}" class="flex items-center gap-2 cursor-pointer min-w-0">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-lg leading-none tracking-tighter">S</span>
                </div>
                <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0">Host</span>
            </a>
        </div>
        <div class="relative shrink-0" x-data="{ open: false }">
            <button type="button" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden" @click="open = ! open" :aria-expanded="open">
                @if ($landlordAvatarUrl)
                    <img src="{{ $landlordAvatarUrl }}" alt="{{ __('Host profile') }}" class="w-full h-full object-cover">
                @else
                    <span class="text-xs font-bold text-gray-600">{{ $landlordInitial }}</span>
                @endif
            </button>
            <div
                class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-[70] origin-top-right"
                x-show="open"
                x-cloak
                x-transition
                @click.outside="open = false"
            >
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $landlordUser?->first_name }} {{ $landlordUser?->last_name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $landlordUser?->email }}</p>
                </div>
                <a href="{{ route('client.landlord.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" @click="open = false">{{ __('Settings') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>
    </header>

    <!-- --- MAIN CONTENT AREA --- -->
    <main
        @class([
            'flex h-full min-h-0 min-w-0 flex-1 flex-col pt-16 md:pt-0',
            'overflow-hidden' => request()->routeIs('client.landlord.messages.*'),
            'overflow-y-auto' => ! request()->routeIs('client.landlord.messages.*'),
        ])
    >

        <!-- Top Bar Area (Desktop) -->
        <div class="hidden h-20 w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-8 md:flex md:sticky md:top-0 md:z-40">
            <h1 class="text-xl font-semibold tracking-tight" id="page-title">{{ $pageTitle ?? $__env->yieldContent('page_title', __('Dashboard Overview')) }}</h1>
            <div class="flex items-center gap-4">
                <a href="{{ route('client.landlord.create-listing') }}" class="bg-white border border-gray-200 text-gray-900 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> {{ __('Add New Property') }}
                </a>
                <div class="w-px h-6 bg-gray-200"></div>
                <div class="flex shrink-0 items-center">
                    <livewire:landlord.notification-bell />
                </div>
                <div class="w-px h-6 bg-gray-200"></div>
                <div class="relative" x-data="{ open: false }">
                    <button type="button" class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white pl-1 pr-2 py-1 hover:bg-gray-50 transition-colors" @click="open = ! open" :aria-expanded="open">
                        <span class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                            @if ($landlordAvatarUrl)
                                <img src="{{ $landlordAvatarUrl }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-bold text-gray-600">{{ $landlordInitial }}</span>
                            @endif
                        </span>
                        <span class="text-sm font-semibold text-gray-900 max-w-[8rem] truncate hidden lg:inline">{{ $landlordUser?->first_name }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-50 origin-top-right"
                        x-show="open"
                        x-cloak
                        x-transition
                        @click.outside="open = false"
                    >
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $landlordUser?->first_name }} {{ $landlordUser?->last_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $landlordUser?->email }}</p>
                        </div>
                        <a href="{{ route('client.landlord.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" @click="open = false">{{ __('Settings') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Log out') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div
            @class([
                'mx-auto w-full max-w-6xl p-4 pb-24 md:p-8',
                'flex min-h-0 flex-1 flex-col overflow-hidden' => request()->routeIs('client.landlord.messages.*'),
            ])
        >
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot }}
            @endif
        </div>
    </main>

    <!-- --- MOBILE BOTTOM NAV --- -->
    <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 z-50 px-6 py-3 flex justify-between items-center pb-safe">
        <a
            href="{{ route('client.landlord.dashboard') }}"
            @class([
                'flex flex-col items-center gap-1 nav-btn-mobile',
                'text-black' => request()->routeIs('client.landlord.dashboard'),
                'text-gray-400 hover:text-black' => ! request()->routeIs('client.landlord.dashboard'),
            ])
        >
            <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
            <span class="text-[10px] font-semibold">{{ __('Home') }}</span>
        </a>
        <a
            href="{{ route('client.landlord.properties.index') }}"
            @class([
                'flex flex-col items-center gap-1 nav-btn-mobile',
                'text-black' => request()->routeIs('client.landlord.properties.*'),
                'text-gray-400 hover:text-black' => ! request()->routeIs('client.landlord.properties.*'),
            ])
        >
            <i data-lucide="building" class="w-6 h-6"></i>
            <span class="text-[10px] font-semibold">{{ __('Listings') }}</span>
        </a>
        <a
            href="{{ route('client.landlord.applications.index') }}"
            @class([
                'flex flex-col items-center gap-1 relative nav-btn-mobile',
                'text-black' => request()->routeIs('client.landlord.applications.*'),
                'text-gray-400 hover:text-black' => ! request()->routeIs('client.landlord.applications.*'),
            ])
        >
            <i data-lucide="users" class="w-6 h-6"></i>
            <span class="absolute top-0 right-1 w-2 h-2 bg-amber-500 rounded-full border border-white"></span>
            <span class="text-[10px] font-semibold">{{ __('Apps') }}</span>
        </a>
        <a
            href="{{ route('client.landlord.messages.index') }}"
            @class([
                'flex flex-col items-center gap-1 nav-btn-mobile',
                'text-black' => request()->routeIs('client.landlord.messages.*'),
                'text-gray-400 hover:text-black' => ! request()->routeIs('client.landlord.messages.*'),
            ])
        >
            <i data-lucide="message-square" class="w-6 h-6"></i>
            <span class="text-[10px] font-semibold">{{ __('Inbox') }}</span>
        </a>
    </nav>

    @stack('scripts')
    @livewireScripts
    <script>
        (function () {
            var registered = false;
            function stunestRegisterNotifyBridge() {
                if (registered || typeof Livewire === 'undefined' || typeof Livewire.on !== 'function') {
                    return;
                }
                registered = true;
                Livewire.on('notify', function (event) {
                    var raw = event && event.detail !== undefined ? event.detail : event;
                    var payload = Array.isArray(raw) ? raw[0] : raw;
                    if (!payload || typeof payload !== 'object') {
                        payload = { message: 'Action successful', type: 'success' };
                    }
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: [{ message: payload.message ?? '', type: payload.type || 'success' }],
                    }));
                });
            }
            document.addEventListener('livewire:init', stunestRegisterNotifyBridge);
            document.addEventListener('livewire:initialized', stunestRegisterNotifyBridge);
            stunestRegisterNotifyBridge();
        })();
    </script>
    <script>
        function stunestRefreshLucide() {
            if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        }
        stunestRefreshLucide();
        document.addEventListener('livewire:navigated', stunestRefreshLucide);
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                stunestRefreshLucide();
            });
        });
    </script>
</body>
</html>
