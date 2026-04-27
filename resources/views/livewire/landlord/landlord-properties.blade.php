@php
    use App\Models\Property\Property;
    use App\Support\ListingPublicId;
@endphp

<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-auto">
            <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search properties…') }}"
                class="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm focus:border-black focus:outline-none focus:ring-1 focus:ring-black sm:w-64"
            />
        </div>
        <div class="flex items-center justify-end gap-2">
            <a
                href="{{ route('client.landlord.create-listing') }}"
                class="hidden rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-gray-800 md:inline-flex md:items-center md:gap-2"
            >
                <i data-lucide="plus" class="h-4 w-4"></i>
                {{ __('Add New Property') }}
            </a>
            <a href="{{ route('client.landlord.create-listing') }}" class="rounded-lg bg-black p-2 text-white md:hidden" aria-label="{{ __('Add New Property') }}">
                <i data-lucide="plus" class="h-5 w-5"></i>
            </a>
        </div>
    </div>

    @if ($properties->isEmpty())
        <div class="flex h-64 flex-col items-center justify-center text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                <i data-lucide="building" class="h-8 w-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('No properties yet') }}</h3>
            <p class="mt-1 max-w-sm text-gray-500">
                {{ __('Create your first listing to appear in search and receive applications from students.') }}
            </p>
            <a
                href="{{ route('client.landlord.create-listing') }}"
                class="mt-6 inline-flex items-center justify-center rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-gray-800"
            >
                {{ __('Add New Property') }}
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($properties as $property)
                @php
                    $thumbUrl = $property->getFirstMediaUrl('property_gallery', 'thumb');
                    $thumbUrl = $thumbUrl !== '' ? $thumbUrl : null;
                    $status = (string) $property->status;
                    $appsCount = $property->applications->count();

                    $isRented = $status === Property::STATUS_LET_AGREED || $status === 'rented';
                    $isLive = $status === Property::STATUS_PUBLISHED;

                    if ($isLive) {
                        $statusBadgeClass = 'bg-green-100 text-green-700';
                        $statusLabel = __('Live');
                    } elseif ($isRented) {
                        $statusBadgeClass = 'bg-primary-100 text-primary-700';
                        $statusLabel = __('Rented');
                    } else {
                        $statusBadgeClass = 'bg-amber-50 text-amber-800 border border-amber-100';
                        $statusLabel = __('Paused');
                    }

                    $canToggle = ! $isRented && in_array($status, [
                        Property::STATUS_PUBLISHED,
                        Property::STATUS_DRAFT,
                        Property::STATUS_ARCHIVED,
                    ], true);

                    $canViewPublic = in_array($status, [Property::STATUS_PUBLISHED, Property::STATUS_LET_AGREED], true);
                    $publicListingUrl = route('client.listing.show', ['slug' => ListingPublicId::encode($property->id)]);
                    $applicationsUrl = route('client.landlord.applications.index', ['property' => $property->id]);
                @endphp
                <div
                    wire:key="landlord-property-{{ $property->id }}"
                    class="flex flex-col gap-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:flex-row md:items-stretch md:gap-8"
                >
                    {{-- Left: thumbnail --}}
                    <div class="shrink-0 md:w-36">
                        @if ($thumbUrl)
                            <img src="{{ $thumbUrl }}" alt="" class="h-40 w-full rounded-xl object-cover md:h-36 md:w-36" loading="lazy" />
                        @else
                            <div class="flex h-40 w-full items-center justify-center rounded-xl bg-gray-100 md:h-36 md:w-36">
                                <i data-lucide="image" class="h-8 w-8 text-gray-300"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Middle: details & badges --}}
                    <div class="min-w-0 flex-1">
                        <h3 class="text-lg font-semibold tracking-tight text-gray-900">
                            {{ $property->display_title }}
                        </h3>
                        <p class="mt-1 text-base font-semibold text-gray-900">
                            €{{ $property->weekly_rent_display }}<span class="text-sm font-normal text-gray-500">/{{ $property->rent_duration }}</span>
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $property->marketing_area_line }}
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $statusBadgeClass }}">
                                {{ $statusLabel }}
                            </span>
                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-gray-600">
                                {{ $appsCount }} {{ __('Active Applications') }}
                            </span>
                        </div>
                    </div>

                    {{-- Right: actions --}}
                    <div class="flex w-full flex-col gap-3 md:w-auto md:shrink-0 md:items-end">
                        <a
                            href="{{ $applicationsUrl }}"
                            class="w-full rounded-xl bg-black px-4 py-2.5 text-center text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800 md:min-w-[15rem]"
                        >
                            {{ __('View Applications') }} ({{ $appsCount }})
                        </a>

                        <div class="flex flex-wrap justify-end gap-2 md:justify-end">
                            @if ($canViewPublic)
                                <a
                                    href="{{ $publicListingUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 transition-colors hover:bg-gray-50"
                                    aria-label="{{ __('View on site') }}"
                                    title="{{ __('View on site') }}"
                                >
                                    <i data-lucide="external-link" class="h-5 w-5"></i>
                                </a>
                            @else
                                <span
                                    class="inline-flex cursor-not-allowed rounded-xl border border-gray-100 bg-gray-50 p-2.5 text-gray-300"
                                    title="{{ __('Listing is not public yet') }}"
                                    aria-label="{{ __('View on site (unavailable)') }}"
                                >
                                    <i data-lucide="external-link" class="h-5 w-5"></i>
                                </span>
                            @endif

                            <a
                                href="{{ route('client.landlord.listings.edit', $property) }}"
                                class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 transition-colors hover:bg-gray-50"
                                aria-label="{{ __('Edit') }}"
                                title="{{ __('Edit') }}"
                            >
                                <i data-lucide="pencil" class="h-5 w-5"></i>
                            </a>

                            @if ($canToggle)
                                <button
                                    type="button"
                                    wire:click="toggleStatus({{ $property->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-50"
                                    aria-label="{{ $isLive ? __('Take off market') : __('Put on market') }}"
                                    title="{{ $isLive ? __('Take off market') : __('Put on market') }}"
                                >
                                    <span wire:loading.remove wire:target="toggleStatus({{ $property->id }})">
                                        @if ($isLive)
                                            <i data-lucide="pause" class="h-5 w-5"></i>
                                        @else
                                            <i data-lucide="play" class="h-5 w-5"></i>
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="toggleStatus({{ $property->id }})" class="inline-flex h-5 w-5 items-center justify-center">
                                        <i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i>
                                    </span>
                                </button>
                            @endif

                            <button
                                type="button"
                                wire:click="deleteProperty({{ $property->id }})"
                                wire:confirm="{{ __('Delete this listing? This cannot be undone.') }}"
                                class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-gray-500 transition-colors hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                aria-label="{{ __('Delete') }}"
                                title="{{ __('Delete') }}"
                            >
                                <i data-lucide="trash-2" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
