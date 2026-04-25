<div class="relative" wire:click.outside="closeMenu">
    <button
        type="button"
        wire:click="toggleMenu"
        class="relative rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-50 hover:text-black focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2"
        aria-label="{{ __('Notifications') }}"
        @if ($menuOpen) aria-expanded="true" @else aria-expanded="false" @endif
    >
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        @if ($this->unreadCount > 0)
            <span class="absolute right-1.5 top-1.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-500 px-0.5 text-[10px] font-bold leading-none text-white ring-2 ring-white">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    @if ($menuOpen)
        <div
            class="absolute right-0 z-[100] mt-2 w-80 origin-top-right overflow-hidden rounded-xl border border-gray-100 bg-white shadow-lg transition-[opacity,transform] duration-150 ease-out"
            wire:key="student-notification-panel"
        >
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Notifications') }}</h2>
                @if ($this->unreadCount > 0)
                    <button
                        type="button"
                        wire:click="markAllAsRead"
                        wire:loading.attr="disabled"
                        class="text-xs font-semibold text-gray-500 transition-colors hover:text-gray-900 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="markAllAsRead">{{ __('Mark all as read') }}</span>
                        <span wire:loading wire:target="markAllAsRead">{{ __('…') }}</span>
                    </button>
                @endif
            </div>

            <ul class="max-h-80 divide-y divide-gray-50 overflow-y-auto custom-scrollbar">
                @forelse ($this->recentUnreadNotifications as $notification)
                    @php
                        $data = $notification->data;
                        $body = is_array($data)
                            ? ($data['message'] ?? $data['body'] ?? $data['title'] ?? '')
                            : '';
                        if ($body === '') {
                            $body = __('You have a new notification.');
                        }
                        $typeKey = class_basename($notification->type);
                    @endphp
                    <li wire:key="notification-{{ $notification->id }}">
                        <button
                            type="button"
                            wire:click="markAsRead('{{ $notification->id }}')"
                            class="flex w-full gap-3 bg-blue-50/80 px-4 py-3 text-left transition-colors hover:bg-gray-50/80"
                        >
                            <span class="mt-0.5 shrink-0 text-gray-500" aria-hidden="true">
                                @if (str_contains(strtolower($typeKey), 'message'))
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
                                @elseif (str_contains(strtolower($typeKey), 'application'))
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                                @endif
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm leading-snug text-gray-900">{{ $body }}</span>
                                <span class="mt-1 block text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                        </button>
                    </li>
                @empty
                    <li class="px-4 py-10 text-center text-sm text-gray-400">
                        {{ __('You have no new notifications.') }}
                    </li>
                @endforelse
            </ul>

            <div class="border-t border-gray-100 bg-gray-50/50 px-4 py-2.5 text-center">
                <a
                    href="{{ route('client.student.notifications') }}"
                    wire:click="closeMenu"
                    class="text-xs font-semibold text-gray-700 transition-colors hover:text-black"
                >
                    {{ __('View all') }}
                </a>
            </div>
        </div>
    @endif
</div>
