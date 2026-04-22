<div class="space-y-8">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Total properties') }}</h3>
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-50">
                    <i data-lucide="layers" class="h-4 w-4 text-gray-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ $totalProperties }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Active listings') }}</h3>
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-50">
                    <i data-lucide="building" class="h-4 w-4 text-gray-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ $activeListings }}</span>
                <span class="text-sm font-medium text-green-600">{{ __('Published') }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Pending applications') }}</h3>
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-50">
                    <i data-lucide="users" class="h-4 w-4 text-amber-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ $pendingApplications }}</span>
                @if ($pendingApplications > 0)
                    <span class="text-sm font-medium text-amber-600">{{ __('Requires review') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Action Required: Recent Applications') }}</h2>
            @if ($recentApplications->isNotEmpty())
                <a href="{{ route('client.landlord.applications.index') }}" class="text-sm font-medium text-gray-500 hover:text-black">
                    {{ __('View all') }}
                </a>
            @endif
        </div>

        @if ($recentApplications->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                    <i data-lucide="inbox" class="h-8 w-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('No pending applications right now') }}</h3>
                <p class="mt-1 max-w-sm text-sm text-gray-500">
                    {{ __('When students apply to your listings, they will appear here for you to review.') }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 text-xs font-bold uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-3">{{ __('Applicant') }}</th>
                            <th class="px-6 py-3">{{ __('Property') }}</th>
                            <th class="px-6 py-3">{{ __('Proposed dates') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($recentApplications as $application)
                            @php
                                $student = $application->student;
                                $property = $application->property;
                                $applicantName = $student
                                    ? trim($student->first_name.' '.$student->last_name)
                                    : __('Unknown');
                                $moveIn = $application->proposed_move_in;
                                $weeks = $application->proposed_duration_weeks;
                                $datesLabel = $moveIn
                                    ? $moveIn->translatedFormat('j M Y').($weeks ? ' · '.$weeks.' '.__('weeks') : '')
                                    : __('—');
                            @endphp
                            <tr class="transition-colors hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">
                                    {{ $applicantName }}
                                </td>
                                <td class="max-w-xs truncate px-6 py-4 text-gray-700">
                                    {{ $property?->display_title ?? __('Listing #').$application->property_id }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                    {{ $datesLabel }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a
                                        href="{{ route('client.landlord.applications.index') }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 transition-colors hover:border-black"
                                    >
                                        {{ __('Review') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
