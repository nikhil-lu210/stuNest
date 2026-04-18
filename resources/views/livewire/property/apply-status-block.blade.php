@php
    use App\Models\Application;
    use App\Models\Property\Property;
@endphp

<div class="apply-status-block w-full">
@if ($variant === 'mobile')
    <div class="mb-3 min-w-0">
        <div class="text-lg font-bold">€{{ $rentAmount }} <span class="text-sm font-medium text-gray-500 font-normal">/ {{ $rentPeriodLabel }}</span></div>
        <div class="text-sm text-gray-600 truncate">{{ $listingMinContractLabel }}</div>
    </div>
@endif

@if ($property->status === Property::STATUS_LET_AGREED && (! $existing || $existing->status !== Application::STATUS_ACCEPTED))
    <div class="{{ $variant === 'sidebar' ? 'mb-4' : 'mb-0 w-full' }}">
        <button
            type="button"
            disabled
            class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-3.5 text-center text-base font-semibold text-gray-500"
        >
            <span aria-hidden="true">🔒</span>
            <span>{{ __('Property no longer available') }}</span>
        </button>
    </div>
@elseif ($existing && $existing->status === Application::STATUS_PENDING)
    <div class="{{ $variant === 'sidebar' ? 'mb-4' : 'mb-0 w-full' }}">
        <button
            type="button"
            disabled
            class="inline-flex w-full cursor-default items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-center text-base font-semibold text-amber-700"
        >
            <i data-lucide="clock" class="h-5 w-5 shrink-0" wire:key="icon-pending-{{ $existing->id }}"></i>
            <span>{{ __('Application Pending') }}</span>
        </button>
        <span class="mt-1 block text-center text-xs text-gray-500">
            {{ __('Applied on :date', ['date' => $existing->created_at->format('jS M, Y')]) }}
        </span>
    </div>
@elseif ($existing && $existing->status === Application::STATUS_ACCEPTED)
    <div class="{{ $variant === 'sidebar' ? 'mb-4' : 'mb-0 w-full' }}">
        <button
            type="button"
            disabled
            class="inline-flex w-full cursor-default items-center justify-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3.5 text-center text-base font-semibold text-green-700"
        >
            <i data-lucide="check" class="h-5 w-5 shrink-0" wire:key="icon-accepted-{{ $existing->id }}"></i>
            <span>🎉 {{ __('Application Accepted!') }}</span>
        </button>
        <span class="mt-1 block text-center text-xs text-gray-500">
            {{ __('Confirmed on :date', ['date' => ($existing->accepted_at ?? $existing->updated_at)->format('jS M, Y')]) }}
        </span>
    </div>
@elseif ($existing && $existing->status === Application::STATUS_REJECTED)
    <div class="{{ $variant === 'sidebar' ? 'mb-4' : 'mb-0 w-full' }}">
        <button
            type="button"
            disabled
            class="inline-flex w-full cursor-default items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3.5 text-center text-base font-semibold text-red-700"
        >
            <span>{{ __('Application Declined') }}</span>
        </button>
    </div>
@elseif (auth()->check() && auth()->user()->hasStudentRole() && $property->status === Property::STATUS_PUBLISHED && (! $existing || $existing->status === Application::STATUS_WITHDRAWN))
    <div class="{{ $variant === 'sidebar' ? 'mb-4' : 'w-full' }}">
        @livewire('property.apply-to-property-trigger', [
            'buttonClass' => $variant === 'sidebar'
                ? 'inline-flex w-full items-center justify-center gap-2 rounded-xl bg-black px-4 py-3.5 text-center text-lg font-semibold text-white transition-transform hover:bg-gray-800 active:scale-95'
                : 'inline-flex w-full items-center justify-center gap-2 rounded-xl bg-black px-4 py-3 text-center text-base font-semibold text-white transition-all hover:bg-gray-800 active:scale-95',
            'label' => $variant === 'sidebar' ? __('Apply to book') : __('Apply'),
        ], key('apply-trigger-'.$variant.'-'.$property->id))
    </div>
@else
    <div class="{{ $variant === 'sidebar' ? 'mb-4' : 'w-full' }}">
        <a
            href="{{ $loginApplyUrl }}"
            class="{{ $variant === 'sidebar' ? 'block w-full text-center text-lg' : 'inline-flex w-full items-center justify-center text-base' }} rounded-xl bg-black px-4 py-3.5 font-semibold text-white transition-transform hover:bg-gray-800 active:scale-95"
        >
            {{ $variant === 'sidebar' ? __('Apply to book') : __('Apply') }}
        </a>
    </div>
@endif
</div>
