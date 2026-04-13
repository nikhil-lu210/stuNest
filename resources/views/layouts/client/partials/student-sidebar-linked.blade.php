{{--
  Linked navigation for student portal (dashboard + settings).
  @var \App\Models\User $user
  @var string $institutionLabel
  @var string|null $avatarUrl
  @var string $active  applications|settings (saved/messages use hash on dashboard)
--}}
@php
    $navBase = 'flex w-full items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors';
    $activeCls = 'bg-gray-50 text-gray-900 font-semibold';
    $idleCls = 'text-gray-500 font-medium hover:text-gray-900 hover:bg-gray-50';
@endphp

<aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-200 h-full shrink-0">
    <a href="{{ route('client.home') }}" class="h-20 flex items-center px-6 border-b border-gray-100">
        <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center mr-2">
            <span class="text-white font-bold text-xl leading-none tracking-tighter">S</span>
        </div>
        <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}</span>
    </a>

    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
        <a href="{{ route('client.student.dashboard') }}" class="{{ $navBase }} {{ $active === 'applications' ? $activeCls : $idleCls }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            {{ __('Applications') }}
        </a>
        <a href="{{ route('client.student.dashboard') }}#saved-tab" class="{{ $navBase }} {{ $idleCls }}">
            <i data-lucide="heart" class="w-5 h-5"></i>
            {{ __('Saved Properties') }}
        </a>
        <a href="{{ route('client.student.dashboard') }}#messages-tab" class="{{ $navBase }} {{ $idleCls }}">
            <i data-lucide="message-square" class="w-5 h-5"></i>
            {{ __('Messages') }}
            <span class="ml-auto bg-black text-white text-[10px] font-bold px-2 py-0.5 rounded-full">2</span>
        </a>
        <div class="pt-4 mt-4 border-t border-gray-100">
            <a href="{{ route('client.student.settings') }}" class="{{ $navBase }} {{ $active === 'settings' ? $activeCls : $idleCls }}">
                <i data-lucide="settings" class="w-5 h-5"></i>
                {{ __('Settings') }}
            </a>
        </div>
    </nav>

    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden shrink-0">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-sm font-semibold text-gray-600">{{ strtoupper(mb_substr($user->first_name ?? '?', 0, 1)) }}</span>
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
            <span class="text-xs font-semibold text-gray-600">{{ strtoupper(mb_substr($user->first_name ?? '?', 0, 1)) }}</span>
        @endif
    </div>
</header>

<nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 z-50 px-6 py-3 flex justify-between items-center pb-safe" aria-label="{{ __('Primary') }}">
    <a href="{{ route('client.student.dashboard') }}" class="flex flex-col items-center gap-1 {{ $active === 'applications' ? 'text-black' : 'text-gray-400' }}">
        <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
        <span class="text-[10px] font-semibold">{{ __('Apps') }}</span>
    </a>
    <a href="{{ route('client.student.dashboard') }}#saved-tab" class="flex flex-col items-center gap-1 text-gray-400 hover:text-black">
        <i data-lucide="heart" class="w-6 h-6"></i>
        <span class="text-[10px] font-semibold">{{ __('Saved') }}</span>
    </a>
    <a href="{{ route('client.student.dashboard') }}#messages-tab" class="flex flex-col items-center gap-1 text-gray-400 hover:text-black relative">
        <i data-lucide="message-square" class="w-6 h-6"></i>
        <span class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
        <span class="text-[10px] font-semibold">{{ __('Inbox') }}</span>
    </a>
    <a href="{{ route('client.student.settings') }}" class="flex flex-col items-center gap-1 {{ $active === 'settings' ? 'text-black' : 'text-gray-400' }}">
        <i data-lucide="settings" class="w-6 h-6"></i>
        <span class="text-[10px] font-semibold">{{ __('Settings') }}</span>
    </a>
</nav>
