@php
    use App\Models\Application;
    use App\Support\ListingPublicId;
@endphp

<div class="flex min-h-0 flex-col">
    @if ($applications->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center shadow-sm sm:p-12">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
                <i data-lucide="message-square" class="h-7 w-7 text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('No conversations yet') }}</h3>
            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                {{ __('When you apply for a property or exchange messages with a landlord, your threads will appear here.') }}
            </p>
        </div>
    @else
        <div
            class="grid min-h-[24rem] grid-cols-1 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:min-h-[28rem] md:grid-cols-3"
        >
            {{-- Conversations list --}}
            <aside
                @class([
                    'flex min-h-0 flex-col border-gray-200 md:border-r',
                    'hidden md:flex' => $mobilePanel === 'chat',
                    'flex' => $mobilePanel === 'list',
                ])
            >
                <div class="border-b border-gray-100 px-4 py-3 md:hidden">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('Conversations') }}</p>
                </div>
                <ul class="custom-scrollbar min-h-0 flex-1 overflow-y-auto md:max-h-[min(28rem,calc(100vh-12rem))]" role="list">
                    @foreach ($applications as $app)
                        @php
                            $landlord = $app->property?->creator;
                            $unread = (int) ($app->unread_from_landlord_count ?? 0);
                            $preview = $app->latestMessage?->body;
                        @endphp
                        <li>
                            <button
                                type="button"
                                wire:click="selectConversation({{ $app->id }})"
                                wire:key="conv-{{ $app->id }}"
                                @class([
                                    'flex w-full items-start gap-3 border-b border-gray-50 px-4 py-3 text-left transition hover:bg-gray-50',
                                    'bg-gray-50' => $activeApplication && $activeApplication->id === $app->id,
                                ])
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200 text-sm font-semibold text-gray-700"
                                >
                                    @if ($landlord?->hasMedia('avatar'))
                                        <img
                                            src="{{ $landlord->getFirstMediaUrl('avatar', 'thumb') }}"
                                            alt=""
                                            class="h-full w-full object-cover"
                                        />
                                    @else
                                        {{ strtoupper(mb_substr($landlord?->first_name ?? '?', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-gray-900">
                                            {{ $app->property?->display_title ?? __('Listing') }}
                                        </p>
                                        @if ($unread > 0)
                                            <span
                                                class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-primary-600"
                                                title="{{ __('Unread') }}"
                                            ></span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-gray-500">
                                        {{ $preview ? \Illuminate\Support\Str::limit($preview, 56) : __('No messages yet') }}
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
                    'flex min-h-0 min-w-0 flex-col border-gray-200 md:col-span-2',
                    'hidden md:flex' => $mobilePanel === 'list',
                    'flex' => $mobilePanel === 'chat',
                ])
            >
                @if ($activeApplication && $activeApplication->property)
                    @php
                        $landlord = $activeApplication->property->creator;
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
                    <header class="flex shrink-0 items-center gap-2 border-b border-gray-100 bg-white px-4 py-3">
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
                                {{ $activeApplication->property->display_title }}
                            </p>
                            <p class="mt-0.5 truncate text-xs text-gray-500">
                                {{ $landlord?->name ?? __('Landlord') }}
                                <span class="text-gray-300" aria-hidden="true">·</span>
                                <a
                                    href="{{ route('client.listing.show', ['slug' => ListingPublicId::encode($activeApplication->property->id)]) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-medium text-gray-700 underline decoration-gray-300 underline-offset-2 hover:text-gray-900 hover:decoration-gray-500"
                                >
                                    {{ __('View listing') }}
                                </a>
                            </p>
                        </div>
                        <span
                            class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusBadge }}"
                        >
                            {{ $statusLabel }}
                        </span>
                    </header>

                    <div
                        id="student-chat-scroll"
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
                                        'bg-primary-600 text-white' => $mine,
                                        'border border-gray-200 bg-white text-gray-900' => ! $mine,
                                    ])
                                >
                                    <p class="whitespace-pre-wrap break-words">{{ $msg->body }}</p>
                                    <p
                                        @class([
                                            'mt-1 text-[10px]',
                                            'text-primary-100' => $mine,
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
                        <form wire:submit="sendMessage" class="flex items-end gap-2">
                            <label class="sr-only" for="student-message-input">{{ __('Message') }}</label>
                            <textarea
                                id="student-message-input"
                                wire:key="student-message-input-{{ $activeApplication->id }}-{{ $messageInputKey }}"
                                wire:model.live="messageBody"
                                wire:keydown.enter="$event.shiftKey || ($event.preventDefault(), $wire.sendMessage())"
                                rows="1"
                                placeholder="{{ __('Type a message…') }}"
                                class="custom-scrollbar max-h-32 min-h-[44px] flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                            ></textarea>
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
