@php
    use App\Models\Application;
    use App\Support\ListingPublicId;

    $application = $this->application;
    $student = $application->user;
    $property = $application->property;
    $landlord = $property?->owner;
    $isOwner = $this->isOwner;

    $avatarUrl = $student?->getFirstMediaUrl('avatar', 'thumb') ?: $student?->getFirstMediaUrl('avatar');
    $avatarUrl = $avatarUrl !== '' ? $avatarUrl : null;
    $initials = strtoupper(mb_substr((string) ($student?->first_name ?: $student?->email ?? '?'), 0, 1) . mb_substr((string) ($student?->last_name ?? ''), 0, 1));

    $accountStatus = (string) ($student?->account_status ?? '');
    $verificationLabel = match ($accountStatus) {
        \App\Models\User::ACCOUNT_STATUS_ACTIVE => __('Verified'),
        \App\Models\User::ACCOUNT_STATUS_PENDING, \App\Models\User::ACCOUNT_STATUS_UNVERIFIED => __('Pending verification'),
        \App\Models\User::ACCOUNT_STATUS_REJECTED => __('Rejected'),
        default => \Illuminate\Support\Str::headline($accountStatus !== '' ? $accountStatus : __('Unknown')),
    };
    $verificationBadgeClass = match ($accountStatus) {
        \App\Models\User::ACCOUNT_STATUS_ACTIVE => 'bg-green-100 text-green-800 border-green-200',
        \App\Models\User::ACCOUNT_STATUS_PENDING, \App\Models\User::ACCOUNT_STATUS_UNVERIFIED => 'bg-amber-100 text-amber-800 border-amber-200',
        \App\Models\User::ACCOUNT_STATUS_REJECTED => 'bg-red-100 text-red-800 border-red-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };

    $status = (string) $application->status;
    $statusBadgeClass = match ($status) {
        Application::STATUS_PENDING => 'bg-amber-100 text-amber-700 ring-amber-200',
        Application::STATUS_ACCEPTED => 'bg-green-100 text-green-700 ring-green-200',
        Application::STATUS_REJECTED => 'bg-red-100 text-red-700 ring-red-200',
        Application::STATUS_WITHDRAWN => 'bg-gray-100 text-gray-700 ring-gray-200',
        default => 'bg-gray-100 text-gray-600 ring-gray-200',
    };
    $statusLabel = match ($status) {
        Application::STATUS_PENDING => __('Pending'),
        Application::STATUS_ACCEPTED => __('Accepted'),
        Application::STATUS_REJECTED => __('Rejected'),
        Application::STATUS_WITHDRAWN => __('Withdrawn'),
        default => \Illuminate\Support\Str::headline($status),
    };

    $weeks = (int) $application->proposed_duration_weeks;
    $thumbUrl = $property?->getFirstMediaUrl('property_gallery', 'thumb');
    $thumbUrl = $thumbUrl !== '' ? $thumbUrl : null;
    $canViewPublic = $property && in_array($property->status, [\App\Models\Property\Property::STATUS_PUBLISHED, \App\Models\Property\Property::STATUS_LET_AGREED], true);

    $backHref = $isOwner
        ? route('client.institute.applications.our')
        : route('client.institute.applications.all');

    $landlordFirst = trim((string) ($landlord?->first_name ?? ''));
    $landlordLast = trim((string) ($landlord?->last_name ?? ''));
    $landlordDisplayName = trim($landlordFirst.' '.$landlordLast) !== '' ? trim($landlordFirst.' '.$landlordLast) : ($landlord?->name ?? $landlord?->email ?? '—');

    $supportMailto = 'mailto:support@stunest.com?subject='.rawurlencode('Escalation: Application #'.$application->id);
@endphp

<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a
                href="{{ $backHref }}"
                wire:navigate
                class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50"
            >
                <i data-lucide="arrow-left" class="mr-1.5 h-4 w-4"></i>
                {{ __('Back') }}
            </a>
            <h1 class="text-xl font-semibold tracking-tight text-gray-900">{{ __('Application Details') }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main column --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Student profile --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Student') }}</h2>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="" class="h-20 w-20 shrink-0 rounded-full border border-gray-100 object-cover">
                    @else
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full border border-gray-100 bg-gray-100 text-xl font-bold text-gray-600">
                            {{ $initials !== '' ? $initials : '?' }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1 space-y-4">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('First name') }}</p>
                                <p class="text-base font-semibold text-gray-900">{{ $student?->first_name ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('Last name') }}</p>
                                <p class="text-base font-semibold text-gray-900">{{ $student?->last_name ?: '—' }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Contact') }}</p>
                            <div class="flex items-start gap-2 text-sm">
                                <i data-lucide="mail" class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"></i>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-500">{{ __('Email') }}</p>
                                    <a href="mailto:{{ $student?->email }}" class="font-medium text-gray-900 break-all hover:underline">{{ $student?->email ?? '—' }}</a>
                                </div>
                            </div>
                            @if (filled($student?->phone))
                                <div class="flex items-start gap-2 text-sm">
                                    <i data-lucide="phone" class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"></i>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-gray-500">{{ __('Phone') }}</p>
                                        <a href="tel:{{ preg_replace('/\s+/', '', (string) $student->phone) }}" class="font-medium text-gray-900 hover:underline">{{ $student->phone }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <a
                            href="{{ route('client.institute.messages', ['user_id' => $application->user_id]) }}"
                            wire:navigate
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-black px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 sm:w-auto"
                        >
                            <i data-lucide="message-square" class="h-4 w-4"></i>
                            {{ __('Message Student') }}
                        </a>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Verification') }}</p>
                            <span class="mt-1 inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $verificationBadgeClass }}">
                                {{ $verificationLabel }}
                            </span>
                        </div>
                        <dl class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                            @if (filled($student?->student_id_number))
                                <div>
                                    <dt class="font-medium text-gray-500">{{ __('Student ID') }}</dt>
                                    <dd class="text-gray-900">{{ $student->student_id_number }}</dd>
                                </div>
                            @endif
                            @if (filled($student?->course_level))
                                <div>
                                    <dt class="font-medium text-gray-500">{{ __('Course level') }}</dt>
                                    <dd class="text-gray-900">{{ $student->course_level }}</dd>
                                </div>
                            @endif
                        </dl>

                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Proposed tenancy') }}</p>
                            <p class="mt-2 text-sm text-gray-800">
                                <span class="font-semibold text-gray-900">{{ __('Move-in') }}:</span>
                                {{ $application->proposed_move_in?->format('j M Y') ?? '—' }}
                            </p>
                            <p class="mt-1 text-sm text-gray-800">
                                <span class="font-semibold text-gray-900">{{ __('Duration') }}:</span>
                                @if ($weeks > 0)
                                    {{ trans_choice(':count week|:count weeks', $weeks, ['count' => $weeks]) }}
                                @else
                                    —
                                @endif
                            </p>
                            @if (filled($application->message_to_landlord))
                                <div class="mt-4 border-t border-gray-200 pt-4">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Message to landlord') }}</p>
                                    <div class="mt-2 rounded-lg border border-gray-100 bg-white p-3 text-sm italic text-gray-600">
                                        {{ $application->message_to_landlord }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Property --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Property') }}</h2>
                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                    <div class="w-full shrink-0 md:w-40">
                        @if ($thumbUrl)
                            <img src="{{ $thumbUrl }}" alt="" class="h-40 w-full rounded-lg object-cover md:h-28" loading="lazy">
                        @else
                            <div class="flex h-40 w-full items-center justify-center rounded-lg bg-gray-100 md:h-28">
                                <i data-lucide="image" class="h-8 w-8 text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1 space-y-3">
                        <p class="text-lg font-bold text-gray-900">{{ $property?->display_title ?? '—' }}</p>
                        @if (filled($property?->marketing_area_line))
                            <p class="text-sm text-gray-500">{{ $property->marketing_area_line }}</p>
                        @endif
                        @if ($property)
                            <p class="text-base text-gray-900">
                                €{{ $property->weekly_rent_display }}<span class="text-sm text-gray-500">/{{ $property->rent_duration }}</span>
                            </p>
                        @endif
                        @if ($canViewPublic)
                            <a
                                href="{{ route('client.listing.show', ['slug' => ListingPublicId::encode($property->id)]) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-black"
                            >
                                {{ __('View listing') }}
                                <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                            </a>
                        @endif
                    </div>
                </div>

                @if ($landlord)
                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <h3 class="mb-3 text-sm font-semibold text-gray-900">{{ __('Landlord details') }}</h3>
                        <div class="space-y-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <div class="flex items-start gap-2 text-sm">
                                <i data-lucide="user" class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"></i>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-500">{{ __('Name') }}</p>
                                    <p class="font-medium text-gray-900">{{ $landlordFirst !== '' || $landlordLast !== '' ? trim($landlordFirst.' '.$landlordLast) : $landlordDisplayName }}</p>
                                </div>
                            </div>
                            @if (filled($landlord->email))
                                <div class="flex items-start gap-2 text-sm">
                                    <i data-lucide="mail" class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"></i>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-gray-500">{{ __('Email') }}</p>
                                        <a href="mailto:{{ $landlord->email }}" class="font-medium text-gray-900 break-all hover:underline">{{ $landlord->email }}</a>
                                    </div>
                                </div>
                            @endif
                            @if (filled($landlord->phone))
                                <div class="flex items-start gap-2 text-sm">
                                    <i data-lucide="phone" class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"></i>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-gray-500">{{ __('Phone') }}</p>
                                        <a href="tel:{{ preg_replace('/\s+/', '', (string) $landlord->phone) }}" class="font-medium text-gray-900 hover:underline">{{ $landlord->phone }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (! $isOwner)
                            <a
                                href="{{ route('client.institute.messages', ['application_id' => $application->id]) }}"
                                wire:navigate
                                class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition-colors hover:bg-gray-50 sm:w-auto"
                            >
                                <i data-lucide="message-square" class="h-4 w-4"></i>
                                {{ __('Message Landlord') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6 lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-gray-900">{{ __('Status') }}</h2>
                <div class="flex flex-col items-center justify-center gap-2 py-4 text-center">
                    <span class="inline-flex rounded-2xl px-5 py-2 text-sm font-bold uppercase tracking-wider ring-2 ring-inset {{ $statusBadgeClass }}">
                        {{ $statusLabel }}
                    </span>
                    <p class="text-xs text-gray-500">{{ __('Submitted') }} {{ $application->created_at?->diffForHumans() }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-gray-900">{{ __('Actions') }}</h2>

                @if ($isOwner && $status === Application::STATUS_PENDING)
                    <div class="flex flex-col gap-3">
                        <button
                            type="button"
                            wire:click="acceptApplication"
                            wire:confirm="{{ __('Accept this application? The listing will be marked as let agreed.') }}"
                            class="w-full rounded-xl bg-green-600 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-green-700"
                        >
                            {{ __('Accept') }}
                        </button>
                        <button
                            type="button"
                            wire:click="rejectApplication"
                            wire:confirm="{{ __('Decline this application?') }}"
                            class="w-full rounded-xl border border-red-200 bg-gray-50 py-2.5 text-sm font-semibold text-red-700 transition-colors hover:bg-red-50"
                        >
                            {{ __('Decline') }}
                        </button>
                    </div>
                @elseif (! $isOwner)
                    <p class="text-sm text-gray-600">
                        {{ __('This application is managed by :name.', ['name' => $landlord?->name ?? $landlord?->email ?? __('the landlord')]) }}
                    </p>
                @else
                    <p class="text-sm text-gray-500">{{ __('No further actions are available for this application.') }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm relative overflow-hidden">
                <div class="absolute left-0 top-0 h-full w-1.5 bg-indigo-500" aria-hidden="true"></div>
                <div class="pl-3">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('System support') }}</h2>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Need help resolving an issue with this application or user?') }}
                    </p>
                    <a
                        href="{{ $supportMailto }}"
                        class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition-colors hover:bg-gray-50"
                    >
                        <i data-lucide="life-buoy" class="h-4 w-4"></i>
                        {{ __('Contact system administrator') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
