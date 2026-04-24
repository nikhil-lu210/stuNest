@php
    use App\Models\Application;
@endphp

<div>
    <h1 class="text-xl font-semibold tracking-tight text-gray-900 mb-6 md:hidden">{{ __('Applications for Our Properties') }}</h1>

    @if ($applications->isEmpty())
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('No applications yet') }}</h3>
                <p class="text-gray-500 mt-1 max-w-sm text-sm">
                    {{ __('When students apply to your institute listings, their requests will appear here for you to review.') }}
                </p>
            </div>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach ($applications as $application)
                @php
                    $student = $application->student;
                    $property = $application->property;
                    $avatarUrl = $student?->getFirstMediaUrl('avatar', 'thumb') ?: $student?->getFirstMediaUrl('avatar');
                    $avatarUrl = $avatarUrl !== '' ? $avatarUrl : null;
                    $initials = strtoupper(mb_substr((string) ($student?->first_name ?: $student?->email ?? '?'), 0, 1) . mb_substr((string) ($student?->last_name ?? ''), 0, 1));
                    $weeks = (int) $application->proposed_duration_weeks;
                    $status = (string) $application->status;
                    $statusBadgeClass = match ($status) {
                        Application::STATUS_PENDING => 'bg-amber-100 text-amber-700',
                        Application::STATUS_ACCEPTED => 'bg-green-100 text-green-700',
                        Application::STATUS_REJECTED => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = match ($status) {
                        Application::STATUS_PENDING => __('Pending'),
                        Application::STATUS_ACCEPTED => __('Accepted'),
                        Application::STATUS_REJECTED => __('Rejected'),
                        Application::STATUS_WITHDRAWN => __('Withdrawn'),
                        default => \Illuminate\Support\Str::headline($status),
                    };
                    $message = $application->message_to_landlord;
                @endphp
                <div
                    wire:key="institute-our-app-{{ $application->id }}"
                    class="flex flex-col bg-white rounded-xl border border-gray-200 p-4 md:p-5 gap-4 shadow-sm"
                >
                    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 lg:items-start">
                        <div class="flex gap-4 items-start min-w-0 flex-1">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="" class="w-12 h-12 rounded-full object-cover border border-gray-100 shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-sm border border-gray-100 shrink-0">
                                    {{ $initials !== '' ? $initials : '?' }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1 space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">
                                        {{ $student?->name ?: $student?->email }}
                                    </h3>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $statusBadgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">{{ $student?->email }}</p>
                                <p class="text-sm text-gray-800">
                                    <span class="font-semibold text-gray-900">{{ __('Property') }}:</span>
                                    {{ $property?->display_title ?? '—' }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold text-gray-900">{{ __('Move-in') }}:</span>
                                    {{ $application->proposed_move_in?->format('j M Y') ?? '—' }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold text-gray-900">{{ __('Duration') }}:</span>
                                    @if ($weeks > 0)
                                        {{ trans_choice(':count week|:count weeks', $weeks, ['count' => $weeks]) }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if (filled($message))
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 text-sm italic text-gray-600">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-3 pt-1">
                        @if ($status === Application::STATUS_PENDING)
                            <button
                                type="button"
                                wire:click="acceptApplication({{ $application->id }})"
                                wire:confirm="{{ __('Accept this application? The listing will be marked as let agreed.') }}"
                                class="flex-1 bg-green-600 text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-green-700 transition-colors shadow-sm"
                            >
                                {{ __('Accept') }}
                            </button>
                            <button
                                type="button"
                                wire:click="rejectApplication({{ $application->id }})"
                                wire:confirm="{{ __('Decline this application?') }}"
                                class="flex-1 bg-gray-50 border border-red-200 text-red-700 py-2.5 rounded-xl font-semibold text-sm hover:bg-red-50 transition-colors"
                            >
                                {{ __('Decline') }}
                            </button>
                        @endif
                        <a
                            href="{{ route('client.institute.applications.show', ['applicationId' => $application->id]) }}"
                            wire:navigate
                            class="inline-flex flex-1 items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 transition-colors hover:bg-gray-50 {{ $status === Application::STATUS_PENDING ? '' : 'sm:flex-none sm:min-w-[10rem]' }}"
                        >
                            {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
