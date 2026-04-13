{{-- Alpine avatar dropdown — include only when @auth (caller may wrap with @auth) --}}
@php
    $user = auth()->user();
    $dashboardHref = $user->hasAdministrationAccess()
        ? route('administration.dashboard.index')
        : $user->clientPortalHomeUrl();
    $avatarInitials = strtoupper(
        mb_substr(trim((string) ($user->first_name ?? '')), 0, 1)
        . mb_substr(trim((string) ($user->last_name ?? '')), 0, 1)
    );
    if ($avatarInitials === '') {
        $parts = preg_split('/\s+/', trim($user->name), 2, PREG_SPLIT_NO_EMPTY);
        $avatarInitials = strtoupper(
            mb_substr($parts[0] ?? '?', 0, 1) . mb_substr($parts[1] ?? ($parts[0] ?? '?'), 0, 1)
        );
    }
@endphp

<div
    class="relative {{ $wrapperClass ?? '' }}"
    x-data="{ open: false }"
    @@click.outside="open = false"
>
    <button
        type="button"
        @@click="open = !open"
        class="flex items-center rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
        x-bind:aria-expanded="open"
        aria-haspopup="true"
    >
        <span class="sr-only">{{ __('Open account menu') }}</span>
        @if ($user->hasMedia('avatar'))
            <span class="block w-10 h-10 rounded-full overflow-hidden ring-1 ring-gray-200/80 bg-gray-100">
                <img
                    src="{{ $user->getFirstMediaUrl('avatar', 'thumb') }}"
                    alt=""
                    class="w-full h-full object-cover"
                    width="40"
                    height="40"
                >
            </span>
        @else
            <span class="flex w-10 h-10 items-center justify-center rounded-full bg-gray-800 text-white text-sm font-semibold ring-1 ring-gray-200/80">
                {{ $avatarInitials ?: '?' }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-[60]"
        role="menu"
    >
        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
            <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
        </div>
        <a
            href="{{ $dashboardHref }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
            role="menuitem"
        >
            <i data-lucide="layout-dashboard" class="w-4 h-4 text-gray-500 shrink-0"></i>
            {{ __('Dashboard') }}
        </a>
        <form method="POST" action="{{ route('logout') }}" class="block" role="none">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center gap-2 text-left px-4 py-2.5 text-sm text-red-600 hover:bg-gray-50 transition-colors"
                role="menuitem"
            >
                <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</div>
