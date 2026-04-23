@php
    use App\Models\Application;
    use App\Models\User;
@endphp

<div class="flex min-h-0 flex-1 flex-col">
    @if ($conversations->isEmpty() && ! $activeApplication && ! $activeSupportStudent)
        <div class="flex flex-col items-center justify-center h-64 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="message-square" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Inbox empty') }}</h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                {{ __('Housing applications and support conversations with your students will appear here.') }}
            </p>
        </div>
    @else
        <div
            class="grid min-h-0 flex-1 grid-cols-1 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:grid-cols-3"
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
                    @foreach ($conversations as $c)
                        @php
                            $student = $c['student'];
                            $unread = (int) ($c['unread'] ?? 0);
                            $preview = $c['preview'] ?? null;
                            $avatarUrl = $student?->getFirstMediaUrl('avatar', 'thumb') ?: $student?->getFirstMediaUrl('avatar');
                            $avatarUrl = $avatarUrl !== '' ? $avatarUrl : null;
                            $isActive =
                                ($activeApplication !== null &&
                                    $c['type'] === 'application' &&
                                    (int) $c['id'] === (int) $activeApplication->id) ||
                                ($activeSupportStudent !== null &&
                                    $c['type'] === 'support' &&
                                    (int) $c['id'] === (int) $activeSupportStudent->id);
                        @endphp
                        <li>
                            <button
                                type="button"
                                wire:click="selectConversation({{ (int) $c['id'] }}, '{{ $c['type'] }}')"
                                wire:key="institute-conv-{{ $c['type'] }}-{{ $c['id'] }}"
                                @class([
                                    'flex w-full items-start gap-3 border-b border-gray-50 px-4 py-3 text-left transition hover:bg-gray-50',
                                    'bg-gray-50' => $isActive,
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
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <p class="truncate text-sm font-semibold text-gray-900">
                                                    {{ $student?->name ?: ($student?->email ?? __('Student')) }}
                                                </p>
                                                @if ($c['type'] === 'application')
                                                    <span
                                                        class="inline-flex max-w-full shrink-0 items-center truncate rounded-md border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700"
                                                        title="{{ $c['property_title'] ?? '' }}"
                                                    >
                                                        {{ __('Housing: :title', ['title' => \Illuminate\Support\Str::limit((string) ($c['property_title'] ?? ''), 28)]) }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex shrink-0 items-center rounded-md border border-amber-100 bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-800"
                                                    >
                                                        {{ __('Support / Verification') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($unread > 0)
                                            <span
                                                class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-600"
                                                title="{{ trans_choice(':count unread message|:count unread messages', $unread, ['count' => $unread]) }}"
                                            ></span>
                                        @endif
                                    </div>
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
                            <p class="mt-0.5 text-xs font-medium text-gray-600">
                                {{ __('Application: :status', ['status' => $statusLabel]) }}
                            </p>
                        </div>
                        <span
                            class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusBadge }}"
                        >
                            {{ $statusLabel }}
                        </span>
                    </header>

                    <div
                        id="institute-chat-scroll"
                        wire:poll.2s
                        class="custom-scrollbar flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto bg-gray-50/80 px-4 py-4"
                    >
                        @forelse ($activeMessages as $msg)
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
                            <label class="sr-only" for="institute-message-input">{{ __('Message') }}</label>
                            <input
                                id="institute-message-input"
                                type="text"
                                wire:key="institute-message-input-app-{{ $activeApplication->id }}-{{ $messageInputKey }}"
                                wire:model="messageBody"
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
                @elseif ($activeSupportStudent)
                    @php
                        /** @var User $supportStudent */
                        $supportStudent = $activeSupportStudent;
                        $acct = $supportStudent->account_status;
                        $verifyBadge = match ($acct) {
                            User::ACCOUNT_STATUS_ACTIVE => 'bg-emerald-100 text-emerald-800',
                            User::ACCOUNT_STATUS_REJECTED => 'bg-red-100 text-red-800',
                            default => 'bg-amber-100 text-amber-800',
                        };
                        $verifyLabel = match ($acct) {
                            User::ACCOUNT_STATUS_ACTIVE => __('Verified'),
                            User::ACCOUNT_STATUS_REJECTED => __('Rejected'),
                            default => __('Pending'),
                        };
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
                                {{ $supportStudent->name ?: ($supportStudent->email ?? __('Student')) }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                {{ __('Support / Verification') }}
                            </p>
                            <p class="mt-0.5 text-xs font-medium text-gray-600">
                                {{ __('Student status: :status', ['status' => $verifyLabel]) }}
                            </p>
                        </div>
                        <span
                            class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $verifyBadge }}"
                        >
                            {{ $verifyLabel }}
                        </span>
                    </header>

                    <div
                        id="institute-chat-scroll"
                        wire:poll.2s
                        class="custom-scrollbar flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto bg-gray-50/80 px-4 py-4"
                    >
                        @forelse ($activeMessages as $msg)
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
                            <label class="sr-only" for="institute-message-input">{{ __('Message') }}</label>
                            <input
                                id="institute-message-input"
                                type="text"
                                wire:key="institute-message-input-sup-{{ $activeSupportStudent->id }}-{{ $messageInputKey }}"
                                wire:model="messageBody"
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
