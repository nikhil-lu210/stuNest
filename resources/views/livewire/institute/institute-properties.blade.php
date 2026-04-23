@php
    use App\Models\Application;
    use App\Models\Property\Property;
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
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4 w-20">{{ __('Photo') }}</th>
                            <th class="px-6 py-4">{{ __('Property') }}</th>
                            <th class="px-6 py-4">{{ __('Rent') }}</th>
                            <th class="px-6 py-4">{{ __('Status') }}</th>
                            <th class="px-6 py-4">{{ __('Applications') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-900">
                        @foreach ($properties as $property)
                            @php
                                $thumbUrl = $property->getFirstMediaUrl('property_gallery', 'thumb');
                                $thumbUrl = $thumbUrl !== '' ? $thumbUrl : null;
                                $status = (string) $property->status;
                                $activeAppsCount = $property->applications
                                    ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_ACCEPTED])
                                    ->count();

                                $isRented = $status === Property::STATUS_LET_AGREED || $status === 'rented';
                                $isLive = $status === Property::STATUS_PUBLISHED;

                                $canToggle = ! $isRented && in_array($status, [
                                    Property::STATUS_PUBLISHED,
                                    Property::STATUS_DRAFT,
                                    Property::STATUS_ARCHIVED,
                                ], true);
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors" wire:key="institute-property-{{ $property->id }}">
                                <td class="px-6 py-4">
                                    @if ($thumbUrl)
                                        <img src="{{ $thumbUrl }}" alt="" class="w-14 h-14 rounded-lg object-cover bg-gray-100" loading="lazy">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <i data-lucide="image" class="w-6 h-6 text-gray-300"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 max-w-xs truncate">{{ $property->display_title }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5 max-w-xs truncate">{{ $property->marketing_area_line }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    €{{ $property->weekly_rent_display }}<span class="text-gray-400">/{{ $property->rent_duration }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($isLive)
                                        <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                            <i data-lucide="check" class="w-3 h-3"></i> {{ __('Live') }}
                                        </span>
                                    @elseif ($isRented)
                                        <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                            {{ __('Rented') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> {{ __('Paused') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-100 px-2.5 py-1 rounded-md text-xs font-medium">
                                        {{ $activeAppsCount }} {{ __('active') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex flex-wrap justify-end gap-2">
                                        <a
                                            href="{{ route('client.institute.listings.edit', $property) }}"
                                            wire:navigate
                                            class="bg-white border border-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:border-black transition-colors"
                                        >
                                            {{ __('Edit') }}
                                        </a>
                                        @if ($canToggle)
                                            <button
                                                type="button"
                                                wire:click="toggleStatus({{ $property->id }})"
                                                wire:loading.attr="disabled"
                                                class="bg-white border border-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:border-black transition-colors disabled:opacity-50 inline-flex items-center gap-1"
                                            >
                                                <span wire:loading.remove wire:target="toggleStatus({{ $property->id }})">
                                                    @if ($isLive)
                                                        <i data-lucide="pause" class="w-3.5 h-3.5"></i> {{ __('Pause') }}
                                                    @else
                                                        <i data-lucide="play" class="w-3.5 h-3.5"></i> {{ __('Play') }}
                                                    @endif
                                                </span>
                                                <span wire:loading wire:target="toggleStatus({{ $property->id }})">{{ __('…') }}</span>
                                            </button>
                                        @endif
                                        <button
                                            type="button"
                                            wire:click="deleteProperty({{ $property->id }})"
                                            wire:confirm="{{ __('Delete this listing? This cannot be undone.') }}"
                                            class="bg-white border border-gray-200 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-50 transition-colors"
                                        >
                                            {{ __('Delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
