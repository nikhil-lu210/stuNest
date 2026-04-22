@php
    use App\Models\Application;
    use App\Support\ListingPublicId;
@endphp

<div>
    @if ($pendingApplications->isEmpty() && $processedApplications->isEmpty())
        <div class="flex flex-col items-center justify-center h-64 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('No applications yet') }}</h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                {{ __('When students apply to your listings, their requests will appear here for you to review.') }}
            </p>
        </div>
    @else
        @if ($pendingApplications->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Pending review') }}</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($pendingApplications as $application)
                        @php
                            $student = $application->student;
                            $property = $application->property;
                            $avatarUrl = $student?->getFirstMediaUrl('avatar', 'thumb') ?: $student?->getFirstMediaUrl('avatar');
                            $avatarUrl = $avatarUrl !== '' ? $avatarUrl : null;
                            $initials = strtoupper(mb_substr((string) ($student?->first_name ?: $student?->email ?? '?'), 0, 1) . mb_substr((string) ($student?->last_name ?? ''), 0, 1));
                            $intro = $application->message_to_landlord;
                            if ($intro === null || trim((string) $intro) === '') {
                                $intro = $application->latestMessage?->body;
                            }
                            $weekly = (int) $property->rent_amount;
                            $weeks = (int) $application->proposed_duration_weeks;
                            $totalRent = $weekly * $weeks;
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
                            $canViewPublic = $property && in_array($property->status, [\App\Models\Property\Property::STATUS_PUBLISHED, \App\Models\Property\Property::STATUS_LET_AGREED], true);
                        @endphp
                        <div
                            wire:key="landlord-app-pending-{{ $application->id }}"
                            class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm"
                        >
                            <div class="flex justify-between items-start mb-6 gap-3">
                                <div class="flex gap-4 items-center min-w-0">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="" class="w-12 h-12 rounded-full object-cover border border-gray-100 shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-500 text-sm border border-gray-100 shrink-0">
                                            {{ $initials !== '' ? $initials : '?' }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-lg leading-tight truncate">{{ $student?->name ?: $student?->email }}</h3>
                                        <p class="text-sm text-gray-500 mt-1 truncate">{{ $student?->email }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="text-xs font-bold text-gray-400 uppercase">{{ $application->created_at?->diffForHumans() }}</span>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $statusBadgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 mb-4 border border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Applying for') }}</p>
                                <p class="font-semibold text-gray-900">{{ $property?->display_title }}</p>
                                @if ($canViewPublic)
                                    <a
                                        href="{{ route('client.listing.show', ['slug' => ListingPublicId::encode($property->id)]) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-medium text-gray-600 hover:text-black mt-1 inline-flex items-center gap-1"
                                    >
                                        {{ __('View listing') }}
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                @endif
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3">
                                    <span>
                                        {{ $application->proposed_move_in?->format('j M Y') }}
                                        @if ($weeks > 0)
                                            — {{ trans_choice(':count week|:count weeks', $weeks, ['count' => $weeks]) }}
                                        @endif
                                    </span>
                                    <span class="font-semibold text-gray-900">
                                        €{{ number_format($weekly) }}/{{ $property?->rent_duration }}
                                        @if ($weeks > 0 && $totalRent > 0)
                                            <span class="text-gray-500 font-normal">· €{{ number_format($totalRent) }} {{ __('est. total') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if ($intro)
                                <div class="bg-gray-50 p-3 rounded-lg text-sm italic text-gray-700 mb-6">
                                    {{ $intro }}
                                </div>
                            @endif

                            <div class="flex gap-3">
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
                                    class="flex-1 bg-white border border-gray-200 text-gray-900 py-2.5 rounded-xl font-semibold text-sm hover:bg-gray-50 transition-colors"
                                >
                                    {{ __('Decline') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($processedApplications->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Processed') }}</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($processedApplications as $application)
                        @php
                            $student = $application->student;
                            $property = $application->property;
                            $avatarUrl = $student?->getFirstMediaUrl('avatar', 'thumb') ?: $student?->getFirstMediaUrl('avatar');
                            $avatarUrl = $avatarUrl !== '' ? $avatarUrl : null;
                            $initials = strtoupper(mb_substr((string) ($student?->first_name ?: $student?->email ?? '?'), 0, 1) . mb_substr((string) ($student?->last_name ?? ''), 0, 1));
                            $intro = $application->message_to_landlord;
                            if ($intro === null || trim((string) $intro) === '') {
                                $intro = $application->latestMessage?->body;
                            }
                            $weekly = (int) $property->rent_amount;
                            $weeks = (int) $application->proposed_duration_weeks;
                            $totalRent = $weekly * $weeks;
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
                            $canViewPublic = $property && in_array($property->status, [\App\Models\Property\Property::STATUS_PUBLISHED, \App\Models\Property\Property::STATUS_LET_AGREED], true);
                        @endphp
                        <div
                            wire:key="landlord-app-processed-{{ $application->id }}"
                            class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm"
                        >
                            <div class="flex justify-between items-start mb-6 gap-3">
                                <div class="flex gap-4 items-center min-w-0">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="" class="w-12 h-12 rounded-full object-cover border border-gray-100 shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-500 text-sm border border-gray-100 shrink-0">
                                            {{ $initials !== '' ? $initials : '?' }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-lg leading-tight truncate">{{ $student?->name ?: $student?->email }}</h3>
                                        <p class="text-sm text-gray-500 mt-1 truncate">{{ $student?->email }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="text-xs font-bold text-gray-400 uppercase">{{ $application->created_at?->diffForHumans() }}</span>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $statusBadgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 mb-4 border border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Applying for') }}</p>
                                <p class="font-semibold text-gray-900">{{ $property?->display_title }}</p>
                                @if ($canViewPublic)
                                    <a
                                        href="{{ route('client.listing.show', ['slug' => ListingPublicId::encode($property->id)]) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-medium text-gray-600 hover:text-black mt-1 inline-flex items-center gap-1"
                                    >
                                        {{ __('View listing') }}
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                @endif
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3">
                                    <span>
                                        {{ $application->proposed_move_in?->format('j M Y') }}
                                        @if ($weeks > 0)
                                            — {{ trans_choice(':count week|:count weeks', $weeks, ['count' => $weeks]) }}
                                        @endif
                                    </span>
                                    <span class="font-semibold text-gray-900">
                                        €{{ number_format($weekly) }}/{{ $property?->rent_duration }}
                                        @if ($weeks > 0 && $totalRent > 0)
                                            <span class="text-gray-500 font-normal">· €{{ number_format($totalRent) }} {{ __('est. total') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if ($intro)
                                <div class="bg-gray-50 p-3 rounded-lg text-sm italic text-gray-700">
                                    {{ $intro }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
