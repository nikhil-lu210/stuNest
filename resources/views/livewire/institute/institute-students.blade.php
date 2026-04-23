<div>
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input
                    type="text"
                    placeholder="{{ __('Search student ID, name or course...') }}"
                    class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all"
                    wire:model.live.debounce.300ms="search"
                >
            </div>
            <div class="flex gap-2 w-full sm:w-auto items-center">
                <label class="sr-only" for="institute-student-status-filter">{{ __('Status') }}</label>
                <select
                    id="institute-student-status-filter"
                    class="w-full sm:w-auto min-w-[10rem] pl-3 pr-8 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all"
                    wire:model.live="statusFilter"
                >
                    <option value="pending">{{ __('Pending verification') }}</option>
                    <option value="verified">{{ __('Verified') }}</option>
                    <option value="all">{{ __('All students') }}</option>
                </select>
            </div>
        </div>

        @if ($students->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="user-x" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('No students found') }}</h3>
                <p class="text-gray-500 mt-1 max-w-sm text-sm">{{ __('No students found matching your criteria.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">{{ __('Student') }}</th>
                            <th class="px-6 py-4">{{ __('Student ID') }}</th>
                            <th class="px-6 py-4">{{ __('University email') }}</th>
                            <th class="px-6 py-4">{{ __('Verification status') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-900">
                        @foreach ($students as $student)
                            @php
                                $initials = strtoupper(mb_substr((string) ($student->first_name ?: ''), 0, 1).mb_substr((string) ($student->last_name ?: ''), 0, 1)) ?: '?';
                                $avatarUrl = $student->getFirstMediaUrl('avatar', 'thumb') ?: $student->getFirstMediaUrl('avatar');
                                $isPending = in_array($student->account_status, [\App\Models\User::ACCOUNT_STATUS_PENDING, \App\Models\User::ACCOUNT_STATUS_UNVERIFIED], true);
                            @endphp
                            <tr @class([
                                'hover:bg-gray-50/50 transition-colors',
                                'bg-gray-50/30' => ! $isPending && $student->account_status === \App\Models\User::ACCOUNT_STATUS_ACTIVE,
                            ])>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if ($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="" class="w-10 h-10 rounded-full object-cover shrink-0 bg-gray-100">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-600 shrink-0">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-semibold truncate @if(! $isPending && $student->account_status === \App\Models\User::ACCOUNT_STATUS_ACTIVE) text-gray-500 @endif">
                                                <span class="inline">{{ $student->first_name }}</span>
                                                <span class="inline">{{ $student->last_name }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    @if (filled($student->student_id_number))
                                        {{ $student->student_id_number }}
                                    @elseif (filled($student->userid))
                                        {{ $student->userid }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    <div class="text-xs">{{ $student->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @switch($student->account_status)
                                        @case(\App\Models\User::ACCOUNT_STATUS_ACTIVE)
                                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                                <i data-lucide="check" class="w-3 h-3"></i> {{ __('Verified') }}
                                            </span>
                                            @break
                                        @case(\App\Models\User::ACCOUNT_STATUS_REJECTED)
                                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 border border-gray-200 px-2.5 py-1 rounded-md text-xs font-medium">
                                                {{ __('Rejected') }}
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> {{ __('Pending') }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex justify-end gap-2">
                                        <button
                                            type="button"
                                            wire:click="messageStudent({{ $student->id }})"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors"
                                            title="{{ __('Message student') }}"
                                        >
                                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                                        </button>
                                        @if ($isPending)
                                            <button
                                                type="button"
                                                wire:click="verifyStudent({{ $student->id }})"
                                                wire:confirm="{{ __('Verify this student account?') }}"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors"
                                                title="{{ __('Verify') }}"
                                            >
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="rejectStudent({{ $student->id }})"
                                                wire:confirm="{{ __('Reject this student application?') }}"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-white border border-gray-200 text-red-600 hover:bg-red-50 transition-colors"
                                                title="{{ __('Reject') }}"
                                            >
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-white">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
