<div>
    <h1 class="text-xl font-semibold tracking-tight text-gray-900 mb-6 md:hidden">{{ __('Dashboard') }}</h1>

    <div class="mb-6 p-4 border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50 rounded-2xl">
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto sm:items-center">
            <label class="sr-only" for="institute-dashboard-date-range">{{ __('Date range') }}</label>
            <select
                id="institute-dashboard-date-range"
                class="w-full sm:w-auto min-w-[12rem] pl-3 pr-8 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all"
                wire:model.live="dateRange"
            >
                <option value="this_month">{{ __('This month') }}</option>
                <option value="last_6_months">{{ __('Last 6 months') }}</option>
                <option value="this_year">{{ __('This year') }}</option>
                <option value="all_time">{{ __('All time') }}</option>
            </select>
        </div>
        <button
            type="button"
            wire:click="exportReport"
            class="w-full sm:w-auto bg-white border border-gray-200 text-gray-900 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 shadow-sm"
        >
            <i data-lucide="download" class="w-4 h-4"></i> {{ __('Export CSV') }}
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Total Students') }}</h3>
                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-4 h-4 text-primary-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 mb-1">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalStudents) }}</span>
            </div>
            <p class="text-sm text-gray-500 font-medium">{{ __('Registrations in selected period') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Verified Students') }}</h3>
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-4 h-4 text-green-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 mb-1">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($verifiedStudents) }}</span>
            </div>
            <p class="text-sm text-gray-500 font-medium">{{ __('Active accounts in cohort') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-primary-500"></div>
            <div class="flex items-center justify-between mb-4 pl-2">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Total Applications') }}</h3>
                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-primary-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 mb-1 pl-2">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalApplications) }}</span>
            </div>
            <p class="text-sm text-gray-500 font-medium pl-2">{{ __('Submitted in selected period') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Application success rate') }}</h3>
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="home" class="w-4 h-4 text-green-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 mb-1">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">
                    {{ $successRatePercent !== null ? $successRatePercent.'%' : '—' }}
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                <div
                    class="bg-green-500 h-1.5 rounded-full transition-all duration-300"
                    style="width: {{ $successRatePercent !== null ? min(100, max(0, $successRatePercent)) : 0 }}%"
                ></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Applications by month') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Volume of housing applications from your students') }}</p>
            </div>
            <div class="p-6">
                @if (count($applicationByMonth) === 0)
                    <p class="text-sm text-gray-500 text-center py-8">{{ __('No applications in this period.') }}</p>
                @else
                    <div class="flex items-end justify-between gap-1 sm:gap-2 min-h-[11rem]" role="img" aria-label="{{ __('Applications by month chart') }}">
                        @foreach ($applicationByMonth as $bar)
                            <div class="flex-1 min-w-0 flex flex-col items-center gap-2 justify-end group">
                                <span class="text-[10px] font-semibold text-gray-600 tabular-nums">{{ $bar['count'] }}</span>
                                <div class="relative h-28 w-full max-w-[2.5rem] mx-auto bg-gray-100 rounded-t-lg overflow-hidden">
                                    <div
                                        class="absolute bottom-0 left-0 right-0 bg-primary-500 rounded-t transition-all duration-300"
                                        style="height: {{ $bar['count'] === 0 ? 0 : max(8, (float) $bar['height_percent']) }}%"
                                    ></div>
                                </div>
                                <span class="text-[10px] font-medium text-gray-500 truncate w-full text-center">{{ $bar['label'] }}</span>
                                <span class="text-[9px] text-gray-400 -mt-1">{{ $bar['sub_label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Student status mix') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Verified vs pending vs rejected (registrations in period)') }}</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="w-full h-3 rounded-full overflow-hidden flex bg-gray-100">
                    @foreach ($statusSegments as $seg)
                        @if ($seg['percent'] > 0)
                            <div
                                class="{{ $seg['class'] }} h-full transition-all duration-300"
                                style="width: {{ $seg['percent'] }}%"
                                title="{{ $seg['label'] }}: {{ $seg['percent'] }}%"
                            ></div>
                        @endif
                    @endforeach
                </div>
                <ul class="flex flex-wrap gap-4 text-xs text-gray-600">
                    @foreach ($statusSegments as $seg)
                        <li class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $seg['class'] }}"></span>
                            {{ $seg['label'] }}
                            <span class="font-semibold text-gray-900">{{ $seg['percent'] }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Recent successful applications') }}</h2>
        </div>
        @if ($recentAcceptedApplications->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="clipboard-list" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('No accepted applications yet') }}</h3>
                <p class="text-gray-500 mt-1 max-w-sm text-sm">{{ __('Accepted applications from your students in this period will appear here.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">{{ __('Student') }}</th>
                            <th class="px-6 py-4">{{ __('Property') }}</th>
                            <th class="px-6 py-4">{{ __('Accepted') }}</th>
                            <th class="px-6 py-4">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-900">
                        @foreach ($recentAcceptedApplications as $application)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold">{{ $application->student?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $application->property?->display_title ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $application->accepted_at?->timezone(config('app.timezone'))->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                        <i data-lucide="check" class="w-3 h-3"></i> {{ __('Accepted') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
