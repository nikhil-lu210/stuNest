<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Total Students') }}</h3>
                <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-4 h-4 text-indigo-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 mb-1">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalStudents) }}</span>
            </div>
            <p class="text-sm text-gray-500 font-medium">{{ __('Registered with your institution on StuNest') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Active Applications') }}</h3>
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-green-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 mb-1">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($activeApplications) }}</span>
            </div>
            <p class="text-sm text-gray-500 font-medium">{{ __('Pending or accepted housing applications') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
            <div class="flex items-center justify-between mb-4 pl-2">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Pending Verification') }}</h3>
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 mb-1 pl-2">
                <span class="text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($pendingVerification) }}</span>
            </div>
            <p class="text-sm text-amber-600 font-medium pl-2">{{ __('Students awaiting account verification') }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Recent Student Registrations') }}</h2>
        </div>

        @if ($recentSignups->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="user-plus" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('No registrations yet') }}</h3>
                <p class="text-gray-500 mt-1 max-w-sm text-sm">{{ __('When students from your institution join StuNest, they will appear here.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">{{ __('Student Name') }}</th>
                            <th class="px-6 py-4">{{ __('Student ID') }}</th>
                            <th class="px-6 py-4">{{ __('Registration Date') }}</th>
                            <th class="px-6 py-4">{{ __('Account Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-900">
                        @foreach ($recentSignups as $signup)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold">{{ $signup->name }}</td>
                                <td class="px-6 py-4 text-gray-500">
                                    @if (filled($signup->student_id_number))
                                        {{ $signup->student_id_number }}
                                    @elseif (filled($signup->userid))
                                        {{ $signup->userid }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $signup->created_at?->timezone(config('app.timezone'))->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @switch($signup->account_status)
                                        @case(\App\Models\User::ACCOUNT_STATUS_ACTIVE)
                                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                                <i data-lucide="check" class="w-3 h-3"></i> {{ __('Active') }}
                                            </span>
                                            @break
                                        @case(\App\Models\User::ACCOUNT_STATUS_REJECTED)
                                            <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                                {{ __('Rejected') }}
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> {{ __('Pending Verification') }}
                                            </span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
