@php
    use App\Models\Application;
@endphp

<div>
    <h1 class="text-xl font-semibold tracking-tight text-gray-900 mb-6 md:hidden">{{ __('Student Applications Overview') }}</h1>

    @if ($applications->isEmpty())
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('No applications yet') }}</h3>
                <p class="text-gray-500 mt-1 max-w-sm text-sm">
                    {{ __('When your students apply for listings, their requests will appear here.') }}
                </p>
            </div>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach ($applications as $application)
                @php
                    $student = $application->student;
                    $property = $application->property;
                    $landlord = $property?->creator;
                    $avatarUrl = $student?->getFirstMediaUrl('avatar', 'thumb') ?: $student?->getFirstMediaUrl('avatar');
                    $avatarUrl = $avatarUrl !== '' ? $avatarUrl : null;
                    $initials = strtoupper(mb_substr((string) ($student?->first_name ?: $student?->email ?? '?'), 0, 1) . mb_substr((string) ($student?->last_name ?? ''), 0, 1));
                    $status = (string) $application->status;
                    $statusBadgeClass = match ($status) {
                        Application::STATUS_PENDING => 'bg-amber-100 text-amber-700',
                        Application::STATUS_ACCEPTED => 'bg-green-100 text-green-700',
                        Application::STATUS_REJECTED => 'bg-red-100 text-red-700',
                        Application::STATUS_WITHDRAWN => 'bg-gray-100 text-gray-600',
                        default => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = match ($status) {
                        Application::STATUS_PENDING => __('Pending'),
                        Application::STATUS_ACCEPTED => __('Accepted'),
                        Application::STATUS_REJECTED => __('Rejected'),
                        Application::STATUS_WITHDRAWN => __('Withdrawn'),
                        default => \Illuminate\Support\Str::headline($status),
                    };
                @endphp
                <div
                    wire:key="institute-all-app-{{ $application->id }}"
                    class="flex flex-col md:flex-row bg-white rounded-xl border border-gray-200 p-4 md:p-5 gap-4 md:gap-6 items-start md:items-center shadow-sm"
                >
                    <div class="flex gap-4 items-center min-w-0 flex-1 w-full md:w-auto">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="" class="w-12 h-12 rounded-full object-cover border border-gray-100 shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-sm border border-gray-100 shrink-0">
                                {{ $initials !== '' ? $initials : '?' }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight truncate">
                                {{ $student?->name ?: $student?->email }}
                            </h3>
                            <p class="text-sm text-gray-500 truncate">{{ $student?->email }}</p>
                            <p class="text-sm text-gray-700 mt-1">
                                <span class="font-medium text-gray-900">{{ __('Property') }}:</span>
                                {{ $property?->display_title ?? '—' }}
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">
                                <span class="font-medium text-gray-900">{{ __('Landlord') }}:</span>
                                {{ $landlord?->name ?? $landlord?->email ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row w-full md:w-auto items-stretch sm:items-center gap-3 shrink-0 md:justify-end">
                        <span class="inline-flex self-start sm:self-center rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $statusBadgeClass }}">
                            {{ $statusLabel }}
                        </span>
                        <a
                            href="{{ route('client.institute.applications.show', ['applicationId' => $application->id]) }}"
                            wire:navigate
                            class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-black px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800"
                        >
                            {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
