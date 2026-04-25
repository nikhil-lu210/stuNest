{{--
  Desktop top bar: notification dropdown (Livewire) + profile dropdown (Alpine), aligned with landlord / institute portals.
  @var \App\Models\User $user
--}}
@php
    $u = isset($user) && $user instanceof \App\Models\User ? $user : auth()->user();
    if (! $u instanceof \App\Models\User) {
        $u = null;
    }
    $topAvatarUrl = $u?->hasMedia('avatar') ? $u->getFirstMediaUrl('avatar', 'profile_view') : null;
    $topInitials = strtoupper(mb_substr((string) ($u?->first_name ?? ''), 0, 1).mb_substr((string) ($u?->last_name ?? ''), 0, 1));
    if (trim($topInitials) === '' && $u) {
        $parts = preg_split('/\s+/', trim((string) $u->name), -1, PREG_SPLIT_NO_EMPTY);
        $topInitials = $parts === []
            ? '?'
            : strtoupper(mb_substr($parts[0], 0, 1).(isset($parts[1]) ? mb_substr($parts[1], 0, 1) : ''));
    }
    if (trim($topInitials) === '') {
        $topInitials = '?';
    }
@endphp
@if ($u)
    <div class="flex items-center gap-4 shrink-0 min-w-0">
        <div class="w-px h-6 bg-gray-200 shrink-0" aria-hidden="true"></div>
        <div class="flex shrink-0 items-center">
            <livewire:student.notification-bell />
        </div>
        <div class="w-px h-6 bg-gray-200 shrink-0" aria-hidden="true"></div>
        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
            <button
                type="button"
                class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white pl-1 pr-2 py-1 hover:bg-gray-50 transition-colors"
                @click="open = ! open"
                :aria-expanded="open"
                aria-haspopup="menu"
            >
                <span class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                    @if ($topAvatarUrl)
                        <img src="{{ $topAvatarUrl }}" alt="" class="w-full h-full object-cover">
                    @else
                        <span class="text-[10px] font-bold text-gray-600 leading-none">{{ $topInitials }}</span>
                    @endif
                </span>
                <span class="text-sm font-semibold text-gray-900 max-w-[8rem] truncate hidden lg:inline">{{ $u->first_name }}</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 shrink-0"></i>
            </button>
            <div
                class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-[100] origin-top-right"
                x-show="open"
                x-cloak
                x-transition
            >
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $u->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $u->email }}</p>
                </div>
                <a
                    href="{{ route('client.student.settings') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    @click="open = false"
                >{{ __('Account Settings') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>
    </div>
@endif
