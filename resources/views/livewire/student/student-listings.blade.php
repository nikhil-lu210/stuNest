@php
    use App\Models\Property\Property;
@endphp

<div>
    @if ($properties->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center shadow-sm sm:p-14">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100">
                <i data-lucide="home" class="h-8 w-8 text-gray-300"></i>
            </div>
            <p class="text-lg font-semibold text-gray-900">{{ __('No listings yet') }}</p>
            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                {{ __('Create your first shared room advert to help another student find a spare seat.') }}
            </p>
            <a
                href="{{ route('client.student.create-listing') }}"
                class="mt-6 inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
            >
                {{ __('Create your first ad') }}
            </a>
        </div>
    @else
        <div class="flex flex-col gap-5">
            @foreach ($properties as $property)
                @php
                    $thumbUrl = $property->getFirstMediaUrl('property_gallery', 'thumb');
                    $thumbUrl = $thumbUrl !== '' ? $thumbUrl : null;
                    $status = $property->status;
                    $badgeClass = match ($status) {
                        Property::STATUS_PUBLISHED => 'bg-emerald-100 text-emerald-800',
                        Property::STATUS_DRAFT => 'bg-gray-100 text-gray-700',
                        Property::STATUS_ARCHIVED => 'bg-gray-100 text-gray-600',
                        Property::STATUS_PENDING => 'bg-amber-100 text-amber-800',
                        Property::STATUS_REJECTED => 'bg-red-100 text-red-800',
                        Property::STATUS_LET_AGREED => 'bg-blue-100 text-blue-800',
                        default => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = match ($status) {
                        Property::STATUS_PUBLISHED => __('Published'),
                        Property::STATUS_DRAFT => __('Draft'),
                        Property::STATUS_ARCHIVED => __('Archived'),
                        Property::STATUS_PENDING => __('Pending review'),
                        Property::STATUS_REJECTED => __('Rejected'),
                        Property::STATUS_LET_AGREED => __('Let agreed'),
                        default => ucfirst((string) $status),
                    };
                    $canToggle = in_array($status, [Property::STATUS_PUBLISHED, Property::STATUS_DRAFT, Property::STATUS_ARCHIVED], true);
                    $isLive = $status === Property::STATUS_PUBLISHED;
                @endphp
                <article
                    wire:key="student-listing-{{ $property->id }}"
                    class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                >
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-stretch">
                        <div class="mx-auto aspect-[16/10] w-full shrink-0 overflow-hidden rounded-xl bg-gray-100 lg:mx-0 lg:w-48 lg:aspect-square">
                            @if ($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" />
                            @else
                                <div class="flex h-full w-full items-center justify-center text-gray-300">
                                    <i data-lucide="image" class="h-8 w-8"></i>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 sm:text-xl">
                                        {{ $property->display_title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $property->marketing_area_line }}
                                    </p>
                                    <p class="mt-2 text-base font-semibold text-gray-900">
                                        €{{ $property->weekly_rent_display }}<span class="text-sm font-normal text-gray-500">/{{ $property->rent_duration }}</span>
                                    </p>
                                </div>

                                <div class="flex w-full shrink-0 flex-col gap-2 lg:w-auto lg:items-end">
                                    @if ($canToggle)
                                        <button
                                            type="button"
                                            wire:click="toggleStatus({{ $property->id }})"
                                            wire:loading.attr="disabled"
                                            role="switch"
                                            aria-checked="{{ $isLive ? 'true' : 'false' }}"
                                            class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 shadow-sm transition hover:bg-gray-50 disabled:opacity-50 lg:w-auto lg:justify-start"
                                        >
                                            <span class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition-colors {{ $isLive ? 'bg-emerald-500' : 'bg-gray-200' }}">
                                                <span
                                                    class="pointer-events-none inline-block h-5 w-5 translate-y-0.5 rounded-full bg-white shadow transition {{ $isLive ? 'translate-x-5' : 'translate-x-0.5' }}"
                                                ></span>
                                            </span>
                                            <span wire:loading.remove class="inline">{{ $isLive ? __('Live') : __('Paused') }}</span>
                                            <span wire:loading class="inline text-gray-500">{{ __('Saving…') }}</span>
                                        </button>
                                    @endif

                                    <div class="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
                                        <a
                                            href="{{ route('client.student.listings.edit', $property) }}"
                                            class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-900 transition hover:bg-gray-50 sm:w-auto"
                                        >
                                            {{ __('Edit') }}
                                        </a>
                                        @if ($property->status === Property::STATUS_PUBLISHED)
                                            <a
                                                href="{{ route('client.listing.show', ['slug' => \App\Support\ListingPublicId::encode($property->id)]) }}"
                                                class="inline-flex w-full items-center justify-center rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-gray-800 sm:w-auto"
                                            >
                                                {{ __('View live') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
