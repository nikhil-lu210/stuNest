@php
    use App\Models\Application;
    use App\Models\Property\Property;
    use App\Support\ListingPublicId;
@endphp

<div class="flex min-h-0 flex-1 flex-col">
    @if ($applications->isEmpty())
        <div class="flex flex-col items-center justify-center h-64 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="message-square" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Inbox empty') }}</h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                {{ __('Messages from prospective and current tenants will appear here.') }}
            </p>
        </div>
    @else
        <div
            class="grid h-[calc(100dvh-10.5rem)] max-h-[calc(100dvh-10.5rem)] min-h-[20rem] grid-cols-1 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:h-[calc(100dvh-7.5rem)] md:max-h-[calc(100dvh-7.5rem)] md:grid-cols-3"
        >
            {{-- Conversations list --}}
            <aside
                @class([
                    'flex min-h-0 max-h-full flex-col overflow-hidden border-gray-200 md:border-r',
                    'hidden md:flex' => $mobilePanel === 'chat',
                    'flex' => $mobilePanel === 'list',
                ])
            >
                <div class="border-b border-gray-100 px-4 py-3 md:hidden">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('Conversations') }}</p>
                </div>
                <ul class="custom-scrollbar min-h-0 flex-1 overflow-y-auto" role="list">
                    @foreach ($applications as $app)
                        @php
                            $student = $app->student;
                            $unread = (int) ($app->unread_from_student_count ?? 0);
                            $preview = $app->latestMessage?->body;
                            $avatarUrl = $student?->getFirstMediaUrl('avatar', 'thumb') ?: $student?->getFirstMediaUrl('avatar');
                            $avatarUrl = $avatarUrl !== '' ? $avatarUrl : null;
                        @endphp
                        <li>
                            <button
                                type="button"
                                wire:click="selectConversation({{ $app->id }})"
                                wire:key="landlord-conv-{{ $app->id }}"
                                @class([
                                    'flex w-full items-start gap-3 border-b border-gray-50 px-4 py-3 text-left transition hover:bg-gray-50',
                                    'bg-gray-50' => $activeApplication && $activeApplication->id === $app->id,
                                ])
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200 text-sm font-semibold text-gray-700"
                                >
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="" class="h-full w-full object-cover" />
                                    @else
                                        {{ strtoupper(mb_substr($student?->first_name ?? $student?->email ?? '?', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-gray-900">
                                            {{ $student?->name ?: ($student?->email ?? __('Applicant')) }}
                                        </p>
                                        @if ($unread > 0)
                                            <span
                                                class="mt-0.5 shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700"
                                                title="{{ trans_choice(':count unread message|:count unread messages', $unread, ['count' => $unread]) }}"
                                            >
                                                {{ $unread }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-gray-600">
                                        {{ $app->property?->display_title ?? __('Listing') }}
                                    </p>
                                    <p class="mt-0.5 truncate text-xs text-gray-500">
                                        {{ $preview ? \Illuminate\Support\Str::limit(strip_tags($preview), 56) : __('No messages yet') }}
                                    </p>
                                </div>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </aside>

            {{-- Chat pane --}}
            <section
                @class([
                    'flex min-h-0 max-h-full min-w-0 flex-col overflow-hidden border-gray-200 md:col-span-2',
                    'hidden md:flex' => $mobilePanel === 'list',
                    'flex' => $mobilePanel === 'chat',
                ])
            >
                @if ($activeApplication && $activeApplication->property)
                    @php
                        $student = $activeApplication->student;
                        $property = $activeApplication->property;
                        $status = $activeApplication->status;
                        $statusBadge = match ($status) {
                            Application::STATUS_PENDING => 'bg-amber-100 text-amber-800',
                            Application::STATUS_ACCEPTED => 'bg-emerald-100 text-emerald-800',
                            Application::STATUS_REJECTED => 'bg-red-100 text-red-800',
                            Application::STATUS_WITHDRAWN => 'bg-gray-100 text-gray-600',
                            default => 'bg-gray-100 text-gray-600',
                        };
                        $statusLabel = match ($status) {
                            Application::STATUS_PENDING => __('Pending'),
                            Application::STATUS_ACCEPTED => __('Accepted'),
                            Application::STATUS_REJECTED => __('Rejected'),
                            Application::STATUS_WITHDRAWN => __('Withdrawn'),
                            default => ucfirst((string) $status),
                        };
                        $canViewPublic = in_array($property->status, [Property::STATUS_PUBLISHED, Property::STATUS_LET_AGREED], true);
                    @endphp
                    <header class="flex shrink-0 flex-wrap items-center gap-2 border-b border-gray-100 bg-white px-4 py-3">
                        <button
                            type="button"
                            wire:click="backToList"
                            class="inline-flex items-center gap-1.5 rounded-lg px-2 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 md:hidden"
                            aria-label="{{ __('Back to conversations') }}"
                        >
                            <i data-lucide="arrow-left" class="h-5 w-5 shrink-0"></i>
                            {{ __('Back') }}
                        </button>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900">
                                {{ $student?->name ?: ($student?->email ?? __('Applicant')) }}
                            </p>
                            <p class="mt-0.5 truncate text-xs text-gray-500">
                                {{ $property->display_title }}
                            </p>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-medium">
                                <a
                                    href="{{ route('client.landlord.applications.index', ['application' => $activeApplication->id]) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-gray-700 underline decoration-gray-300 underline-offset-2 hover:text-gray-900 hover:decoration-gray-500"
                                >
                                    {{ __('View application') }}
                                </a>
                                @if ($canViewPublic)
                                    <span class="text-gray-300" aria-hidden="true">·</span>
                                    <a
                                        href="{{ route('client.listing.show', ['slug' => ListingPublicId::encode($property->id)]) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-gray-700 underline decoration-gray-300 underline-offset-2 hover:text-gray-900 hover:decoration-gray-500"
                                    >
                                        {{ __('View property') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <span
                            class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusBadge }}"
                        >
                            {{ $statusLabel }}
                        </span>
                    </header>

                    <div
                        id="landlord-chat-scroll"
                        @if ($activeApplication)
                            wire:poll.2s
                        @endif
                        class="custom-scrollbar flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto bg-gray-50/80 px-4 py-4"
                    >
                        @forelse ($activeApplication->messages as $msg)
                            @php $mine = $msg->sender_id === auth()->id(); @endphp
                            <div @class(['flex w-full', 'justify-end' => $mine, 'justify-start' => ! $mine])>
                                <div
                                    @class([
                                        'max-w-[85%] rounded-2xl px-3 py-2 text-sm shadow-sm',
                                        'bg-blue-600 text-white' => $mine,
                                        'border border-gray-200 bg-white text-gray-900' => ! $mine,
                                    ])
                                >
                                    <div class="break-words">{!! nl2br(e($msg->body)) !!}</div>
                                    <p
                                        @class([
                                            'mt-1 text-[10px]',
                                            'text-blue-100' => $mine,
                                            'text-gray-400' => ! $mine,
                                        ])
                                    >
                                        {{ $msg->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-gray-500">{{ __('No messages yet. Say hello below.') }}</p>
                        @endforelse
                    </div>

                    <div class="shrink-0 border-t border-gray-100 bg-white p-3">
                        <form wire:submit="sendMessage" class="flex items-center gap-2">
                            <label class="sr-only" for="landlord-message-input">{{ __('Message') }}</label>
                            <input
                                id="landlord-message-input"
                                type="text"
                                wire:key="landlord-message-input-{{ $activeApplication->id }}-{{ $messageInputKey }}"
                                wire:model.live="messageBody"
                                placeholder="{{ __('Type a message…') }}"
                                class="min-h-[44px] flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                            />
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="sendMessage"
                                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-white shadow-sm transition hover:bg-gray-800 disabled:opacity-50"
                                aria-label="{{ __('Send') }}"
                            >
                                <span wire:loading.remove wire:target="sendMessage">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M22 2L11 13" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M22 2l-7 20-4-9-9-4 20-7z" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span wire:loading wire:target="sendMessage" class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            </button>
                        </form>
                        @error('messageBody')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="flex flex-1 flex-col items-center justify-center p-8 text-center text-gray-500">
                        <p class="text-sm">{{ __('Select a conversation to view messages.') }}</p>
                    </div>
                @endif
            </section>
        </div>
    @endif
</div>
