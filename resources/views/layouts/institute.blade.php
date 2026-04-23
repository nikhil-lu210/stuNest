<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? $__env->yieldContent('title', __('Institute Portal')) }} | {{ config('app.name') }}</title>

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
</head>
@php
    $instituteUser = auth()->user();
    $instituteInitial = 'U';
    if ($instituteUser) {
        $f = trim((string) ($instituteUser->first_name ?? ''));
        $l = trim((string) ($instituteUser->last_name ?? ''));
        if ($f !== '' && $l !== '') {
            $instituteInitial = strtoupper(mb_substr($f, 0, 1).mb_substr($l, 0, 1));
        } elseif ($f !== '') {
            $instituteInitial = strtoupper(mb_substr($f, 0, 2));
        } else {
            $instituteInitial = strtoupper(mb_substr((string) ($instituteUser->email ?? 'U'), 0, 2));
        }
    }

    $instituteStudentsSectionActive = request()->routeIs(
        'client.institute.students.index',
        'client.institute.students.unverified',
        'client.institute.students.create',
    );
    $instituteStudentsAllActive = request()->routeIs('client.institute.students.index');
    $instituteStudentsUnverifiedActive = request()->routeIs('client.institute.students.unverified');
    $instituteStudentsCreateActive = request()->routeIs('client.institute.students.create');
@endphp
<body class="bg-gray-50 font-sans text-gray-900 antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <div
        class="md:hidden fixed inset-0 z-[55] bg-gray-900/40 transition-opacity"
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    <!-- --- SIDEBAR (Desktop + mobile drawer; matches institute_dashboard.html structure) --- -->
    <aside
        class="flex flex-col w-64 bg-white border-r border-gray-200 h-full shrink-0 fixed md:relative inset-y-0 left-0 z-[60] transition-transform duration-200 ease-out md:!translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Logo -->
        <a href="{{ route('client.institute.dashboard') }}" class="h-20 flex items-center px-6 border-b border-gray-100 cursor-pointer" @click="sidebarOpen = false">
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center mr-2">
                <span class="text-white font-bold text-xl leading-none tracking-tighter">S</span>
            </div>
            <span class="text-xl font-bold tracking-tight">StuNest.</span>
            <span class="ml-2 text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full uppercase tracking-wider border border-indigo-100">Uni</span>
        </a>

        <div class="md:hidden flex justify-end px-2 py-2 border-b border-gray-100">
            <button type="button" class="p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50" @click="sidebarOpen = false" aria-label="{{ __('Close menu') }}">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            <a
                href="{{ route('client.institute.dashboard') }}"
                @click="sidebarOpen = false"
                @class([
                    'nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors',
                    'font-semibold bg-gray-50 text-gray-900' => request()->routeIs('client.institute.dashboard'),
                    'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.institute.dashboard'),
                ])
            >
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                {{ __('Overview') }}
            </a>
            <div
                class="space-y-0.5"
                x-data="{ studentsOpen: {{ $instituteStudentsSectionActive ? 'true' : 'false' }} }"
            >
                <button
                    type="button"
                    @click="studentsOpen = ! studentsOpen"
                    :aria-expanded="studentsOpen"
                    @class([
                        'nav-btn w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors text-left',
                        'text-gray-900 font-semibold bg-gray-50' => $instituteStudentsSectionActive,
                        'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! $instituteStudentsSectionActive,
                    ])
                >
                    <span class="flex items-center gap-3 min-w-0">
                        <i data-lucide="users" class="w-5 h-5 shrink-0"></i>
                        <span class="truncate">{{ __('Students') }}</span>
                    </span>
                    <span
                        class="inline-flex shrink-0 text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-180': ! studentsOpen }"
                    >
                        <i data-lucide="chevron-up" class="w-4 h-4"></i>
                    </span>
                </button>
                <div
                    x-show="studentsOpen"
                    x-cloak
                    class="mt-0.5 ml-2 pl-3 border-l border-gray-200 space-y-0.5 py-0.5"
                >
                    <a
                        href="{{ route('client.institute.students.index') }}"
                        @click="sidebarOpen = false"
                        @class([
                            'nav-btn w-full flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors',
                            'bg-gray-50 text-gray-900 font-semibold' => $instituteStudentsAllActive,
                            'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! $instituteStudentsAllActive,
                        ])
                    >
                        {{ __('All Students') }}
                    </a>
                    <a
                        href="{{ route('client.institute.students.unverified') }}"
                        @click="sidebarOpen = false"
                        @class([
                            'nav-btn w-full flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors',
                            'bg-gray-50 text-gray-900 font-semibold' => $instituteStudentsUnverifiedActive,
                            'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! $instituteStudentsUnverifiedActive,
                        ])
                    >
                        {{ __('Unverified Students') }}
                    </a>
                    <a
                        href="{{ route('client.institute.students.create') }}"
                        @click="sidebarOpen = false"
                        @class([
                            'nav-btn w-full flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors',
                            'bg-gray-50 text-gray-900 font-semibold' => $instituteStudentsCreateActive,
                            'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! $instituteStudentsCreateActive,
                        ])
                    >
                        {{ __('Create Account') }}
                    </a>
                </div>
            </div>
            <a
                href="#"
                @click.prevent="sidebarOpen = false"
                @class([
                    'nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors',
                    'font-semibold bg-gray-50 text-gray-900' => request()->routeIs('client.institute.partners.*'),
                    'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.institute.partners.*'),
                ])
            >
                <i data-lucide="building-2" class="w-5 h-5"></i>
                {{ __('Approved Partners') }}
            </a>
            <a
                href="#"
                @click.prevent="sidebarOpen = false"
                @class([
                    'nav-btn w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-colors',
                    'font-semibold bg-gray-50 text-gray-900' => request()->routeIs('client.institute.welfare.*'),
                    'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.institute.welfare.*'),
                ])
            >
                <div class="flex items-center gap-3">
                    <i data-lucide="life-buoy" class="w-5 h-5"></i>
                    {{ __('Welfare Alerts') }}
                </div>
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
            </a>
            <div class="pt-4 mt-4 border-t border-gray-100">
                <a
                    href="{{ route('client.institute.settings') }}"
                    @click="sidebarOpen = false"
                    @class([
                        'nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors',
                        'font-semibold bg-gray-50 text-gray-900' => request()->routeIs('client.institute.settings'),
                        'font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50' => ! request()->routeIs('client.institute.settings'),
                    ])
                >
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    {{ __('Institution Settings') }}
                </a>
            </div>
        </nav>

        <!-- Profile (Bottom Sidebar) -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-10 h-10 bg-indigo-900 text-white rounded-full flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden">
                    {{ $instituteInitial }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $instituteOrgName ?? __('Institution') }}</p>
                    <p class="text-xs text-indigo-600 font-medium truncate">{{ $instituteUser?->first_name }} {{ $instituteUser?->last_name }}</p>
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
        <div class="flex items-center gap-2 min-w-0">
            <button type="button" class="p-2 -ml-2 text-gray-600 hover:text-black shrink-0 rounded-lg" @click="sidebarOpen = true" aria-label="{{ __('Open menu') }}">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <a href="{{ route('client.institute.dashboard') }}" class="flex items-center gap-2 cursor-pointer min-w-0">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-lg leading-none tracking-tighter">S</span>
                </div>
                <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full uppercase tracking-wider border border-indigo-100 shrink-0">Uni</span>
            </a>
        </div>
        <div class="relative shrink-0 ml-2" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
            <button type="button" class="w-8 h-8 bg-indigo-900 text-white rounded-full flex items-center justify-center font-bold text-[10px] overflow-hidden" @click="profileOpen = ! profileOpen" :aria-expanded="profileOpen">
                {{ $instituteInitial }}
            </button>
            <div
                x-show="profileOpen"
                x-transition
                x-cloak
                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-[70] overflow-hidden"
            >
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $instituteUser?->first_name }} {{ $instituteUser?->last_name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $instituteUser?->email }}</p>
                </div>
                <a href="{{ route('client.institute.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" @click="profileOpen = false">{{ __('Institution Settings') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>
    </header>

    <!-- --- MAIN CONTENT AREA --- -->
    <main class="flex-1 h-full overflow-y-auto pt-16 md:pt-0 custom-scrollbar">

        <!-- Top Bar Area (Desktop) -->
        <div class="hidden md:flex h-20 items-center justify-between px-8 bg-white border-b border-gray-200 sticky top-0 z-40 overflow-visible">
            <div>
                <h1 class="text-xl font-semibold tracking-tight" id="page-title">{{ $pageTitle ?? $__env->yieldContent('page_title', __('University Overview')) }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">{{ $pageSubtitle ?? $__env->yieldContent('page_subtitle', __('University College London (Academic Year 26/27)')) }}</p>
            </div>

            <div class="flex items-center justify-end gap-4 shrink-0 min-w-0">
                <button type="button" class="bg-white border border-gray-200 text-gray-900 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> {{ __('Download Census') }}
                </button>
                <div class="w-px h-6 bg-gray-200 shrink-0"></div>
                <div class="relative shrink-0" x-data="{ notificationOpen: false }" @click.outside="notificationOpen = false">
                    <button
                        type="button"
                        class="p-2 text-gray-400 hover:text-black transition-colors relative"
                        @click="notificationOpen = ! notificationOpen"
                        :aria-expanded="notificationOpen"
                    >
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    </button>
                    <div
                        x-show="notificationOpen"
                        x-transition
                        x-cloak
                        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden"
                    >
                        <div class="px-4 py-8 text-center text-sm text-gray-500 border-b border-gray-100">
                            {{ __('No notifications yet.') }}
                        </div>
                    </div>
                </div>
                <div class="w-px h-6 bg-gray-200 shrink-0"></div>
                <div class="relative ml-4 shrink-0" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
                    <button type="button" class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white pl-1 pr-2 py-1 hover:bg-gray-50 transition-colors" @click="profileOpen = ! profileOpen" :aria-expanded="profileOpen">
                        <span class="w-9 h-9 rounded-lg bg-indigo-900 text-white flex items-center justify-center overflow-hidden shrink-0 text-xs font-bold">
                            {{ $instituteInitial }}
                        </span>
                        <span class="text-sm font-semibold text-gray-900 max-w-[8rem] truncate hidden lg:inline">{{ $instituteUser?->first_name }}</span>
                        <span class="inline-flex shrink-0 text-gray-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </span>
                    </button>
                    <div
                        x-show="profileOpen"
                        x-transition
                        x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden"
                    >
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $instituteUser?->first_name }} {{ $instituteUser?->last_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $instituteUser?->email }}</p>
                        </div>
                        <a href="{{ route('client.institute.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" @click="profileOpen = false">{{ __('Institution Settings') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Log out') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 md:p-8 max-w-7xl mx-auto pb-24">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot }}
            @endif
        </div>
    </main>

    <!-- --- MOBILE BOTTOM NAV --- -->
    <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 z-50 px-4 py-3 flex justify-between items-center pb-safe">
        <a
            href="{{ route('client.institute.dashboard') }}"
            @class([
                'flex flex-col items-center gap-1 nav-btn-mobile',
                'text-black' => request()->routeIs('client.institute.dashboard'),
                'text-gray-400 hover:text-black' => ! request()->routeIs('client.institute.dashboard'),
            ])
        >
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="text-[10px] font-semibold">{{ __('Home') }}</span>
        </a>
        <a
            href="{{ route('client.institute.students.index') }}"
            @class([
                'flex flex-col items-center gap-1 relative nav-btn-mobile',
                'text-black' => $instituteStudentsSectionActive,
                'text-gray-400 hover:text-black' => ! $instituteStudentsSectionActive,
            ])
        >
            <i data-lucide="user-check" class="w-5 h-5"></i>
            <span class="absolute top-0 right-1 w-2 h-2 bg-indigo-600 rounded-full border border-white"></span>
            <span class="text-[10px] font-semibold">{{ __('Roster') }}</span>
        </a>
        <a
            href="#"
            @class([
                'flex flex-col items-center gap-1 nav-btn-mobile',
                'text-black' => request()->routeIs('client.institute.partners.*'),
                'text-gray-400 hover:text-black' => ! request()->routeIs('client.institute.partners.*'),
            ])
        >
            <i data-lucide="building-2" class="w-5 h-5"></i>
            <span class="text-[10px] font-semibold">{{ __('Partners') }}</span>
        </a>
        <a
            href="#"
            @click.prevent
            @class([
                'flex flex-col items-center gap-1 relative nav-btn-mobile',
                'text-black' => request()->routeIs('client.institute.welfare.*'),
                'text-gray-400 hover:text-black' => ! request()->routeIs('client.institute.welfare.*'),
            ])
        >
            <i data-lucide="life-buoy" class="w-5 h-5"></i>
            <span class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
            <span class="text-[10px] font-semibold">{{ __('Alerts') }}</span>
        </a>
    </nav>

    <x-toast />

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
