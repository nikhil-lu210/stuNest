<div>
    @if ($savedProperties->isEmpty())
        {{-- Empty state aligned with student dashboard shell --}}
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center shadow-sm sm:p-14">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 sm:h-16 sm:w-16">
                <i data-lucide="heart" class="h-7 w-7 text-gray-300 sm:h-8 sm:w-8"></i>
            </div>
            <p class="text-lg font-semibold text-gray-900">{{ __('You haven\'t saved any properties yet.') }}</p>
            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                {{ __('Save listings you like while browsing to build a shortlist and compare them here.') }}
            </p>
            <a
                href="{{ route('client.explore') }}"
                class="mt-6 inline-flex items-center justify-center rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
            >
                {{ __('Go browse listings') }}
            </a>
        </div>
    @else
        {{-- TAB 2: SAVED PROPERTIES — project_documents/clients_theme/student_dashboard.html --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($savedProperties as $property)
                <article
                    wire:key="saved-property-{{ $property->id }}"
                    class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >
                    <a
                        href="{{ route('client.listing.show', ['slug' => \App\Support\ListingPublicId::encode($property->id)]) }}"
                        class="block"
                    >
                        <div class="relative aspect-[4/3] bg-gray-100">
                            <img
                                src="{{ $property->thumbnail_url ?? 'https://picsum.photos/seed/sr'.$property->id.'/800/600' }}"
                                alt="{{ $property->display_title }}"
                                class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.02]"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='https://picsum.photos/seed/sr{{ $property->id }}/800/600'"
                            >
                        </div>
                        <div class="p-4">
                            <div class="mb-1 flex items-start justify-between gap-2">
                                <h3 class="min-w-0 flex-1 truncate font-semibold text-gray-900">{{ $property->display_title }}</h3>
                                <p class="shrink-0 font-semibold">
                                    €{{ $property->weekly_rent_display }}<span class="text-sm font-normal text-gray-500">/{{ $property->rent_duration }}</span>
                                </p>
                            </div>
                            <p class="truncate text-sm text-gray-500">
                                {{ $property->marketing_uni_line }} · {{ $property->marketing_area_line }}
                            </p>
                        </div>
                    </a>

                    <button
                        type="button"
                        wire:click="unsaveProperty({{ $property->id }})"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60"
                        class="absolute right-3 top-3 z-10 rounded-full bg-white p-2 text-red-500 shadow-sm transition hover:scale-110"
                        aria-label="{{ __('Remove from saved') }}"
                    >
                        <i data-lucide="heart" class="h-4 w-4 fill-red-500"></i>
                    </button>
                </article>
            @endforeach
        </div>
    @endif
</div>
