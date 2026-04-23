{{--
  Linked navigation for student portal — responsive shell from project_documents/clients_theme/student_dashboard.html:
  - Desktop: aside `hidden md:flex` + scrollable nav
  - Mobile: fixed top bar (logo + profile) + fixed bottom nav (`md:hidden` … `pb-safe`)
  - No hamburger/off-canvas: primary mobile nav is the bottom bar, matching the theme.
  @var \App\Models\User $user
  @var string $institutionLabel
  @var string|null $avatarUrl
  @var string $active  applications|listings|create-listing|saved|settings|messages
--}}
@php
    $navBase = 'flex w-full items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors';
    $activeCls = 'bg-gray-50 text-gray-900 font-semibold';
    $idleCls = 'text-gray-500 font-medium hover:text-gray-900 hover:bg-gray-50';
    $unreadMessagesCount = $user->unreadApplicationMessagesFromLandlordsCount();
    $studentDashboardActive = request()->routeIs('client.student.dashboard');
    $studentApplicationsNavActive = request()->routeIs('client.student.applications.*');
    $propertiesBottomActive = $active === 'listings'
        || request()->routeIs('client.student.create-listing')
        || request()->routeIs('client.student.listings.edit');
    $profileBottomActive = $active === 'settings';
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

        <div
            class="space-y-0"
            x-data="{ open: @json(request()->routeIs('client.student.listings.*') || request()->routeIs('client.student.create-listing')) }"
        >
            <button
                type="button"
                @click="open = !open"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm transition-colors {{ in_array($active, ['listings', 'create-listing'], true) ? 'bg-gray-50 font-semibold text-gray-900' : 'font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                x-bind:aria-expanded="open"
            >
                <i data-lucide="home" class="h-5 w-5 shrink-0"></i>
                <span class="min-w-0 flex-1">{{ __('Properties / Rooms') }}</span>
                <i
                    data-lucide="chevron-down"
                    class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200"
                    x-bind:class="{ 'rotate-180': open }"
                ></i>
            </button>
            <div x-show="open" x-collapse class="space-y-0.5 pb-1 pt-0.5">
                <a
                    href="{{ route('client.student.listings.index') }}"
                    class="flex w-full items-center rounded-lg py-2 pl-11 pr-3 text-sm transition-colors {{ $active === 'listings' ? 'bg-gray-50 font-semibold text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    {{ __('All Listings') }}
                </a>
                <a
                    href="{{ route('client.student.create-listing') }}"
                    class="flex w-full items-center rounded-lg py-2 pl-11 pr-3 text-sm transition-colors {{ request()->routeIs('client.student.create-listing') || request()->routeIs('client.student.listings.edit') ? 'bg-gray-50 font-semibold text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    {{ __('New Advertise') }}
                </a>
            </div>
        </div>

        <a href="{{ route('client.student.saved') }}" class="{{ $navBase }} {{ $active === 'saved' ? $activeCls : $idleCls }}">
            <i data-lucide="heart" class="w-5 h-5"></i>
            {{ __('Saved Properties') }}
        </a>
        <a href="{{ route('client.student.messages') }}" class="{{ $navBase }} {{ $active === 'messages' ? $activeCls : $idleCls }}">
            <i data-lucide="message-square" class="w-5 h-5"></i>
            {{ __('Messages') }}
            @if ($unreadMessagesCount > 0)
                <span class="ml-auto min-w-[1.25rem] rounded-full bg-black px-2 py-0.5 text-center text-[10px] font-bold text-white">
                    {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                </span>
            @endif
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

<header class="md:hidden fixed top-0 z-50 flex h-16 w-full items-center justify-between border-b border-gray-200 bg-white px-4 overflow-visible">
    <a href="{{ route('client.home') }}" class="flex items-center gap-2">
        <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-lg leading-none tracking-tighter">S</span>
        </div>
    </a>
    <div class="relative shrink-0" x-data="{ openProfile: false }">
        <button
            type="button"
            class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200 ring-2 ring-transparent transition-shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
            @click="openProfile = !openProfile"
            aria-haspopup="true"
            x-bind:aria-expanded="openProfile"
            aria-label="{{ __('Account menu') }}"
        >
            @if ($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="" class="h-full w-full object-cover">
            @else
                <span class="text-xs font-semibold text-gray-600">{{ strtoupper(mb_substr($user->first_name ?? '?', 0, 1)) }}</span>
            @endif
        </button>
        <div
            x-show="openProfile"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            @click.outside="openProfile = false"
            x-cloak
            class="absolute right-4 mt-2 w-48 rounded-xl border border-gray-100 bg-white shadow-xl z-[100]"
        >
            <div class="border-b border-gray-100 px-3 py-2.5">
                <p class="truncate text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="mt-0.5 truncate text-xs text-gray-500">{{ $user->email }}</p>
            </div>
            <a
                href="{{ route('client.student.settings') }}"
                class="block px-3 py-2.5 text-sm font-medium text-gray-800 transition-colors hover:bg-gray-50"
                @click="openProfile = false"
            >
                {{ __('Settings') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                @csrf
                <button
                    type="submit"
                    class="w-full px-3 py-2.5 text-left text-sm font-medium text-gray-800 transition-colors hover:bg-gray-50"
                >
                    {{ __('Log out') }}
                </button>
            </form>
        </div>
    </div>
</header>

{{-- Mobile bottom nav — outer wrapper overflow-visible so Profile drop-up is not clipped --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 z-[60] overflow-visible pointer-events-none">
    <nav class="pointer-events-auto w-full overflow-visible border-t border-gray-200 bg-white px-1 py-3 pb-safe" aria-label="{{ __('Primary') }}">
        <div class="grid grid-cols-6 items-end justify-items-center gap-0.5">
            <a href="{{ route('client.student.dashboard') }}" class="flex max-w-[4.5rem] flex-col items-center gap-1 {{ $studentDashboardActive ? 'text-black' : 'text-gray-400 hover:text-black' }}">
                <i data-lucide="home" class="h-6 w-6 shrink-0"></i>
                <span class="text-center text-[10px] font-semibold leading-tight">{{ __('Home') }}</span>
            </a>
            <a href="{{ route('client.student.applications.index') }}" class="flex max-w-[4.5rem] flex-col items-center gap-1 {{ $studentApplicationsNavActive ? 'text-black' : 'text-gray-400 hover:text-black' }}">
                <i data-lucide="file-text" class="h-6 w-6 shrink-0"></i>
                <span class="text-center text-[10px] font-semibold leading-tight">{{ __('Applications') }}</span>
            </a>
            <a href="{{ route('client.student.messages') }}" class="relative flex max-w-[4.5rem] flex-col items-center gap-1 {{ $active === 'messages' ? 'text-black' : 'text-gray-400 hover:text-black' }}">
                <i data-lucide="message-square" class="h-6 w-6 shrink-0"></i>
                @if ($unreadMessagesCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-black px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white">
                        {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                    </span>
                @endif
                <span class="text-center text-[10px] font-semibold leading-tight">{{ __('Messages') }}</span>
            </a>
            <a href="{{ route('client.student.saved') }}" class="flex max-w-[4.5rem] flex-col items-center gap-1 {{ $active === 'saved' ? 'text-black' : 'text-gray-400 hover:text-black' }}">
                <i data-lucide="heart" class="h-6 w-6 shrink-0"></i>
                <span class="text-center text-[10px] font-semibold leading-tight">{{ __('Saved') }}</span>
            </a>
            <a href="{{ route('client.student.listings.index') }}" class="flex max-w-[4.5rem] flex-col items-center gap-1 {{ $propertiesBottomActive ? 'text-black' : 'text-gray-400 hover:text-black' }}">
                <i data-lucide="building-2" class="h-6 w-6 shrink-0"></i>
                <span class="text-center text-[9px] font-semibold leading-tight">{{ __('Properties / Rooms') }}</span>
            </a>
            <div class="relative z-[1] flex max-w-[4.5rem] flex-col items-center" x-data="{ mobileProfileOpen: false }">
                <button
                    type="button"
                    @click="mobileProfileOpen = !mobileProfileOpen"
                    class="flex flex-col items-center gap-1 {{ $profileBottomActive ? 'text-black' : 'text-gray-400 hover:text-black' }}"
                    aria-haspopup="true"
                    x-bind:aria-expanded="mobileProfileOpen"
                    aria-label="{{ __('Profile menu') }}"
                >
                    <i data-lucide="user" class="h-6 w-6 shrink-0"></i>
                    <span class="text-center text-[10px] font-semibold leading-tight">{{ __('Profile') }}</span>
                </button>
                <div
                    x-show="mobileProfileOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    @click.outside="mobileProfileOpen = false"
                    x-cloak
                    class="absolute bottom-full right-2 mb-4 w-48 rounded-xl border border-gray-100 bg-white shadow-2xl z-[70]"
                >
            <div class="border-b border-gray-100 px-3 py-2.5">
                <p class="truncate text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="mt-0.5 truncate text-xs text-gray-500">{{ $user->email }}</p>
            </div>
            <a
                href="{{ route('client.student.settings') }}"
                class="block px-3 py-2.5 text-sm font-medium text-gray-800 transition-colors hover:bg-gray-50"
                @click="mobileProfileOpen = false"
            >
                {{ __('Settings') }}
            </a>
            <a
                href="{{ route('client.student.create-listing') }}"
                class="block px-3 py-2.5 text-sm font-medium text-gray-800 transition-colors hover:bg-gray-50"
                @click="mobileProfileOpen = false"
            >
                {{ __('List a Room/Seat') }}
            </a>
            <a
                href="{{ route('client.student.listings.index') }}"
                class="block px-3 py-2.5 text-sm font-medium text-gray-800 transition-colors hover:bg-gray-50"
                @click="mobileProfileOpen = false"
            >
                {{ __('All Listings') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                @csrf
                <button
                    type="submit"
                    class="w-full px-3 py-2.5 text-left text-sm font-medium text-gray-800 transition-colors hover:bg-gray-50"
                >
                    {{ __('Log out') }}
                </button>
            </form>
                </div>
            </div>
        </div>
    </nav>
</div>
