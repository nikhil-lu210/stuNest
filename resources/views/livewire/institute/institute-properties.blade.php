@php
    use App\Models\Property\Property;
    use App\Support\ListingPublicId;
@endphp

<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ route('client.institute.create-listing') }}"
            wire:navigate
            class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-black text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-800 shadow-sm transition-colors"
        >
            <i data-lucide="plus" class="w-4 h-4"></i>
            {{ __('New Advertise') }}
        </a>
    </div>

    @if ($properties->isEmpty())
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="building-2" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('No properties yet') }}</h3>
                <p class="text-gray-500 mt-1 max-w-sm text-sm">{{ __('Create a listing to offer accommodation exclusively to your students.') }}</p>
                <a
                    href="{{ route('client.institute.create-listing') }}"
                    wire:navigate
                    class="mt-6 inline-flex items-center justify-center gap-2 bg-black text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-800 shadow-sm transition-colors"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    {{ __('New Advertise') }}
                </a>
            </div>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach ($properties as $property)
                @php
                    $thumbUrl = $property->getFirstMediaUrl('property_gallery', 'thumb');
                    $thumbUrl = $thumbUrl !== '' ? $thumbUrl : null;
                    $status = (string) $property->status;
                    $appsCount = $property->applications->count();

                    $isRented = $status === Property::STATUS_LET_AGREED || $status === 'rented';
                    $isLive = $status === Property::STATUS_PUBLISHED;

                    $canToggle = ! $isRented && in_array($status, [
                        Property::STATUS_PUBLISHED,
                        Property::STATUS_DRAFT,
                        Property::STATUS_ARCHIVED,
                    ], true);

                    $canViewPublic = in_array($status, [Property::STATUS_PUBLISHED, Property::STATUS_LET_AGREED], true);
                    $publicListingUrl = route('client.listing.show', ['slug' => ListingPublicId::encode($property->id)]);
                @endphp
                <div
                    wire:key="institute-property-{{ $property->id }}"
                    class="flex flex-col md:flex-row bg-white rounded-xl border border-gray-200 p-4 md:p-5 gap-4 md:gap-6 items-start md:items-center shadow-sm"
                >
                    {{-- Left: thumbnail --}}
                    <div class="w-full md:w-40 shrink-0">
                        @if ($thumbUrl)
                            <img
                                src="{{ $thumbUrl }}"
                                alt=""
                                class="w-full md:w-40 h-48 md:h-28 object-cover rounded-lg"
                                loading="lazy"
                            />
                        @else
                            <div class="w-full md:w-40 h-48 md:h-28 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i data-lucide="image" class="w-8 h-8 text-gray-300"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Middle: details & badges --}}
                    <div class="flex-1 flex flex-col gap-2 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">
                            {{ $property->display_title }}
                        </h3>
                        <p class="text-base text-gray-900">
                            €{{ $property->weekly_rent_display }}<span class="text-sm text-gray-500">/{{ $property->rent_duration }}</span>
                        </p>
                        @if (filled($property->marketing_area_line))
                            <p class="text-sm text-gray-500">
                                {{ $property->marketing_area_line }}
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-2">
                            @if ($isLive)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 text-green-700 border border-green-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                    {{ __('Live') }}
                                </span>
                            @elseif ($isRented)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 text-primary-700 border border-primary-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                    {{ __('Rented') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                    {{ __('Paused') }}
                                </span>
                            @endif
                            <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 border border-gray-200 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider">
                                {{ $appsCount }} {{ __('Active Applications') }}
                            </span>
                        </div>
                    </div>

                    {{-- Right: actions --}}
                    <div class="w-full md:w-auto flex flex-col items-end gap-3 shrink-0">
                        <a
                            href="#"
                            class="w-full md:min-w-[15rem] text-center rounded-xl bg-black px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800"
                        >
                            {{ __('View Applications') }} ({{ $appsCount }})
                        </a>

                        <div class="flex w-full items-center justify-end gap-2">
                            @if ($canViewPublic)
                                <a
                                    href="{{ $publicListingUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 transition-colors hover:bg-gray-50"
                                    title="{{ __('View on site') }}"
                                    aria-label="{{ __('View on site') }}"
                                >
                                    <i data-lucide="eye" class="h-5 w-5"></i>
                                </a>
                            @else
                                <span
                                    class="inline-flex cursor-not-allowed rounded-xl border border-gray-100 bg-gray-50 p-2.5 text-gray-300"
                                    title="{{ __('Listing is not public yet') }}"
                                    aria-label="{{ __('View on site (unavailable)') }}"
                                >
                                    <i data-lucide="eye" class="h-5 w-5"></i>
                                </span>
                            @endif

                            <a
                                href="{{ route('client.institute.listings.edit', $property) }}"
                                wire:navigate
                                class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 transition-colors hover:bg-gray-50"
                                title="{{ __('Edit') }}"
                                aria-label="{{ __('Edit') }}"
                            >
                                <i data-lucide="pencil" class="h-5 w-5"></i>
                            </a>

                            @if ($canToggle)
                                <button
                                    type="button"
                                    wire:click="toggleStatus({{ $property->id }})"
                                    wire:loading.attr="disabled"
                                    class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-50"
                                    title="{{ $isLive ? __('Pause') : __('Play') }}"
                                    aria-label="{{ $isLive ? __('Pause') : __('Play') }}"
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
                                class="inline-flex rounded-xl border border-gray-200 bg-white p-2.5 text-red-500 transition-colors hover:bg-red-50 hover:border-red-200"
                                title="{{ __('Delete') }}"
                                aria-label="{{ __('Delete') }}"
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
