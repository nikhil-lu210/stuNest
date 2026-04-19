@php
    use App\Models\Application;
@endphp

<div>
    @if ($applications->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">
            <i data-lucide="inbox" class="mx-auto mb-3 h-10 w-10 text-gray-300"></i>
            <p class="font-medium text-gray-900">{{ __('No applications yet') }}</p>
            <p class="mx-auto mt-1 max-w-md text-sm">{{ __('When you apply for a property, your progress and actions will show here.') }}</p>
            <a href="{{ route('client.home') }}" class="mt-4 inline-block text-sm font-semibold text-black hover:underline">{{ __('Browse listings') }}</a>
        </div>
    @else
        <div class="flex flex-col gap-5">
            @foreach ($applications as $application)
                @php
                    $property = $application->property;
                    $thumbUrl = $property?->getFirstMediaUrl('property_gallery', 'thumb');
                    $thumbUrl = $thumbUrl !== '' ? $thumbUrl : null;
                    $status = $application->status;
                    $badgeClass = match ($status) {
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
                <article
                    wire:key="application-{{ $application->id }}"
                    class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                >
                    @if ($status === Application::STATUS_PENDING)
                        <div class="absolute left-0 top-0 h-full w-1.5 bg-amber-500" aria-hidden="true"></div>
                    @endif

                    {{-- Layout: project_documents/clients_theme/student_dashboard.html — stack on mobile/tablet, row from lg --}}
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-stretch">
                        {{-- Thumbnail --}}
                        <div class="mx-auto aspect-[16/10] w-full shrink-0 overflow-hidden rounded-xl bg-gray-100 lg:mx-0 lg:w-48 lg:aspect-square">
                            @if ($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" />
                            @else
                                <div class="flex h-full w-full items-center justify-center text-gray-300">
                                    <i data-lucide="image" class="h-8 w-8"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Main --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 sm:text-xl">
                                        {{ $property?->display_title ?? __('Listing unavailable') }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $property?->marketing_area_line ?? '—' }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-700">
                                        <span class="font-medium text-gray-900">{{ __('Tenancy') }}:</span>
                                        {{ $this->proposedTenancyLabel($application) }}
                                        @if ($application->proposed_move_in)
                                            <span class="text-gray-400">·</span>
                                            {{ __('From :date', ['date' => $application->proposed_move_in->translatedFormat('M j, Y')]) }}
                                        @endif
                                    </p>
                                    @if ($property)
                                        <p class="mt-2 text-xl font-bold text-gray-900 sm:hidden">
                                            €{{ $property->rent_amount }}<span class="text-sm font-normal text-gray-500">/{{ $property->rent_duration }}</span>
                                        </p>
                                    @endif
                                </div>

                                <div class="hidden shrink-0 flex-col gap-3 text-right sm:flex sm:items-end">
                                    @if ($property)
                                        <div class="text-xl font-bold text-gray-900">
                                            €{{ $property->rent_amount }}<span class="text-sm font-normal text-gray-500">/{{ $property->rent_duration }}</span>
                                        </div>
                                    @endif
                                    <p class="text-xs text-gray-400">
                                        {{ __('Applied on :date', ['date' => $application->created_at->translatedFormat('M j, Y')]) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Actions: full-width on mobile (theme application cards) --}}
                            <div class="mt-4 flex flex-col gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4">
                                @if ($property)
                                    <a
                                        href="{{ route('client.listing.show', ['slug' => \App\Support\ListingPublicId::encode($property->id)]) }}"
                                        class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-gray-50 sm:inline-flex sm:w-auto sm:justify-start sm:border-0 sm:bg-transparent sm:px-0 sm:py-0 sm:shadow-none sm:underline sm:underline-offset-2"
                                    >
                                        {{ __('View listing') }}
                                    </a>
                                @endif
                                @if ($application->status === Application::STATUS_PENDING)
                                    <button
                                        type="button"
                                        wire:click="withdrawApplication({{ $application->id }})"
                                        wire:confirm="{{ __('Are you sure?') }}"
                                        class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100 sm:ml-auto sm:w-auto"
                                    >
                                        {{ __('Withdraw application') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
