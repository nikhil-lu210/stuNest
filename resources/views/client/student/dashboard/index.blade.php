@extends('layouts.client.student-app')

@section('title', __('Student Dashboard').' | '.config('app.name'))

@php
    $institutionLabel = $user->institution?->name ?? __('Student');
    $avatarUrl = $user->hasMedia('avatar')
        ? $user->getFirstMediaUrl('avatar', 'profile_view')
        : null;
@endphp

@section('content')
    {{-- Sidebar (desktop) --}}
    <aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-200 h-full shrink-0">
        <a href="{{ route('client.home') }}" class="h-20 flex items-center px-6 border-b border-gray-100">
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center mr-2">
                <span class="text-white font-bold text-xl leading-none tracking-tighter">S</span>
            </div>
            <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}</span>
        </a>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            <button type="button" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-lg bg-gray-50 text-gray-900 transition-colors" data-target="applications-tab">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                {{ __('Applications') }}
            </button>
            <button type="button" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors" data-target="saved-tab">
                <i data-lucide="heart" class="w-5 h-5"></i>
                {{ __('Saved Properties') }}
            </button>
            <button type="button" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors" data-target="messages-tab">
                <i data-lucide="message-square" class="w-5 h-5"></i>
                {{ __('Messages') }}
                <span class="ml-auto bg-black text-white text-[10px] font-bold px-2 py-0.5 rounded-full">2</span>
            </button>
            <div class="pt-4 mt-4 border-t border-gray-100">
                <button type="button" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors" data-target="settings-tab">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    {{ __('Settings') }}
                </button>
            </div>
        </nav>

        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden shrink-0">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="" class="w-full h-full object-cover">
                    @else
                        <span class="text-sm font-semibold text-gray-600">{{ strtoupper(substr($user->first_name ?? '?', 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $institutionLabel }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline shrink-0">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-black p-1" aria-label="{{ __('Log out') }}">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile header --}}
    <header class="md:hidden fixed top-0 w-full h-16 bg-white border-b border-gray-200 z-50 flex items-center justify-between px-4">
        <a href="{{ route('client.home') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg leading-none tracking-tighter">S</span>
            </div>
        </a>
        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
            @if ($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="" class="w-full h-full object-cover">
            @else
                <span class="text-xs font-semibold text-gray-600">{{ strtoupper(substr($user->first_name ?? '?', 0, 1)) }}</span>
            @endif
        </div>
    </header>

    <main class="flex-1 h-full overflow-y-auto pt-16 md:pt-0">
        <div class="hidden md:flex h-20 items-center justify-between px-8 bg-white border-b border-gray-200 sticky top-0 z-40">
            <h1 class="text-xl font-semibold tracking-tight" id="page-title">{{ __('Applications') }}</h1>
            <div class="flex items-center gap-4">
                <button type="button" class="p-2 text-gray-400 hover:text-black transition-colors relative" aria-label="{{ __('Notifications') }}">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>
            </div>
        </div>

        <div class="p-4 md:p-8 max-w-5xl mx-auto pb-24">

            @if ($user->account_status === \App\Models\User::ACCOUNT_STATUS_UNVERIFIED)
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-blue-600 mt-0.5 shrink-0"></i>
                        <div>
                            <h4 class="font-semibold text-blue-900 text-sm">{{ __('Verify your student status') }}</h4>
                            <p class="text-sm text-blue-700 mt-0.5">{{ __('Upload your university ID to fast-track your housing applications.') }}</p>
                        </div>
                    </div>
                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap">
                        {{ __('Verify Now') }}
                    </button>
                </div>
            @endif

            <div id="applications-tab" class="tab-content active">
                <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-medium text-gray-900">{{ __('No applications yet') }}</p>
                    <p class="text-sm mt-1 max-w-md mx-auto">{{ __('When you apply for a property, your progress and actions will show here.') }}</p>
                    <a href="{{ route('client.home') }}" class="inline-block mt-4 text-sm font-semibold text-black hover:underline">{{ __('Browse listings') }}</a>
                </div>
            </div>

            <div id="saved-tab" class="tab-content">
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50/50 p-12 text-center text-gray-500">
                    <i data-lucide="heart" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-medium text-gray-900">{{ __('No saved properties') }}</p>
                    <p class="text-sm mt-1">{{ __('Save listings you like from search to see them here.') }}</p>
                </div>
            </div>

            <div id="settings-tab" class="tab-content max-w-2xl">
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold">{{ __('Account') }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Your profile details are managed from your account settings.') }}</p>
                    </div>
                    <div class="p-6 space-y-4 text-sm text-gray-600">
                        <p><span class="font-medium text-gray-900">{{ __('Email') }}:</span> {{ $user->email }}</p>
                        @if ($user->phone)
                            <p><span class="font-medium text-gray-900">{{ __('Phone') }}:</span> {{ $user->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div id="messages-tab" class="tab-content">
                <div class="flex flex-col items-center justify-center h-64 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="message-square" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('No messages yet') }}</h3>
                    <p class="text-gray-500 mt-1 max-w-sm">{{ __('When you contact a landlord or they send you an update, your messages will appear here.') }}</p>
                </div>
            </div>
        </div>
    </main>

    <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 z-50 px-6 py-3 flex justify-between items-center pb-safe" aria-label="{{ __('Primary') }}">
        <button type="button" class="flex flex-col items-center gap-1 text-black nav-btn-mobile" data-target="applications-tab">
            <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
            <span class="text-[10px] font-semibold">{{ __('Apps') }}</span>
        </button>
        <button type="button" class="flex flex-col items-center gap-1 text-gray-400 hover:text-black nav-btn-mobile" data-target="saved-tab">
            <i data-lucide="heart" class="w-6 h-6"></i>
            <span class="text-[10px] font-semibold">{{ __('Saved') }}</span>
        </button>
        <button type="button" class="flex flex-col items-center gap-1 text-gray-400 hover:text-black relative nav-btn-mobile" data-target="messages-tab">
            <i data-lucide="message-square" class="w-6 h-6"></i>
            <span class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
            <span class="text-[10px] font-semibold">{{ __('Inbox') }}</span>
        </button>
        <button type="button" class="flex flex-col items-center gap-1 text-gray-400 hover:text-black nav-btn-mobile" data-target="settings-tab">
            <i data-lucide="settings" class="w-6 h-6"></i>
            <span class="text-[10px] font-semibold">{{ __('Settings') }}</span>
        </button>
    </nav>
@endsection

@push('scripts')
    <script>
        (function () {
            const navBtns = document.querySelectorAll('.nav-btn, .nav-btn-mobile');
            const tabContents = document.querySelectorAll('.tab-content');
            const pageTitle = document.getElementById('page-title');

            const titles = {
                'applications-tab': @json(__('Applications')),
                'saved-tab': @json(__('Saved Properties')),
                'messages-tab': @json(__('Messages')),
                'settings-tab': @json(__('Account Settings'))
            };

            function refreshIcons() {
                if (window.lucide && typeof lucide.createIcons === 'function') {
                    lucide.createIcons();
                }
            }

            navBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const targetId = btn.getAttribute('data-target');
                    if (!targetId) return;

                    tabContents.forEach(function (content) {
                        content.classList.remove('active');
                    });

                    document.querySelectorAll('.nav-btn').forEach(function (b) {
                        b.classList.remove('bg-gray-50', 'text-gray-900', 'font-semibold');
                        b.classList.add('text-gray-500', 'font-medium');
                    });

                    document.querySelectorAll('.nav-btn-mobile').forEach(function (b) {
                        b.classList.remove('text-black');
                        b.classList.add('text-gray-400');
                    });

                    const desktopBtn = document.querySelector('.nav-btn[data-target="' + targetId + '"]');
                    if (desktopBtn) {
                        desktopBtn.classList.remove('text-gray-500', 'font-medium');
                        desktopBtn.classList.add('bg-gray-50', 'text-gray-900', 'font-semibold');
                    }

                    const mobileBtn = document.querySelector('.nav-btn-mobile[data-target="' + targetId + '"]');
                    if (mobileBtn) {
                        mobileBtn.classList.remove('text-gray-400');
                        mobileBtn.classList.add('text-black');
                    }

                    const targetEl = document.getElementById(targetId);
                    if (targetEl) {
                        targetEl.classList.add('active');
                    }

                    if (pageTitle && titles[targetId]) {
                        pageTitle.textContent = titles[targetId];
                    }

                    refreshIcons();
                });
            });

            refreshIcons();
        })();
    </script>
@endpush
