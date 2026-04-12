@extends('layouts.administration.app')

@section('page_title', __('Property #:id', ['id' => $property->id]))

@section('page_name')
    <b class="text-uppercase">{{ __('Property details') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('administration.properties.index') }}">{{ __('Property listings') }}</a></li>
    <li class="breadcrumb-item active">#{{ $property->id }}</li>
@endsection

@section('content')
@php
    $fmt = fn (?string $v) => $v ? str_replace('_', ' ', ucfirst($v)) : '—';
@endphp
<div class="row">
    <div class="col-lg-10 col-xl-8">
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">{{ __('Listing #') }}{{ $property->id }}</h5>
                <div class="d-flex flex-wrap gap-2">
                    @can('update', $property)
                        <a href="{{ route('administration.properties.edit', $property) }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-pencil me-1"></i>{{ __('Edit') }}
                        </a>
                    @endcan
                    <a href="{{ route('administration.properties.index') }}" class="btn btn-sm btn-label-secondary">
                        {{ __('Back to list') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th class="w-25 text-muted">{{ __('Status') }}</th>
                            <td>
                                @switch($property->status)
                                    @case(\App\Models\Property\Property::STATUS_DRAFT)
                                        <span class="badge bg-label-secondary">{{ __('Draft') }}</span>
                                        @break
                                    @case(\App\Models\Property\Property::STATUS_PENDING)
                                        <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                        @break
                                    @case(\App\Models\Property\Property::STATUS_PUBLISHED)
                                        <span class="badge bg-label-success">{{ __('Published') }}</span>
                                        @break
                                    @case(\App\Models\Property\Property::STATUS_REJECTED)
                                        <span class="badge bg-label-danger">{{ __('Rejected') }}</span>
                                        @break
                                    @case(\App\Models\Property\Property::STATUS_LET_AGREED)
                                        <span class="badge bg-label-info">{{ __('Let agreed') }}</span>
                                        @break
                                    @case(\App\Models\Property\Property::STATUS_ARCHIVED)
                                        <span class="badge bg-label-secondary">{{ __('Archived') }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-label-secondary">{{ $fmt($property->status) }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Capacity') }}</th>
                            <td>{{ $property->capacity ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Available beds') }}</th>
                            <td>{{ $property->available_beds ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Listing category') }}</th>
                            <td>{{ $fmt($property->listing_category) }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Country') }}</th>
                            <td>{{ $property->country?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('City') }}</th>
                            <td>{{ $property->city?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Area') }}</th>
                            <td>{{ $property->area?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Map link') }}</th>
                            <td>
                                @if ($property->map_link)
                                    <a href="{{ $property->map_link }}" target="_blank" rel="noopener noreferrer">{{ __('Open map') }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Coordinates') }}</th>
                            <td>
                                @if ($property->latitude !== null && $property->longitude !== null)
                                    {{ $property->latitude }}, {{ $property->longitude }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Distance to university') }}</th>
                            <td>{{ $property->distance_university_km !== null ? $property->distance_university_km.' km' : '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Distance to nearest bus/train') }}</th>
                            <td>{{ $property->distance_transit_km !== null ? $property->distance_transit_km.' km' : '—' }}</td>
                        </tr>
                        @if ($property->listing_category === 'shared_room' && $property->bed_type)
                            <tr>
                                <th class="text-muted">{{ __('Bed type') }}</th>
                                <td>{{ ucwords(str_replace('_', ' ', $property->bed_type)) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-muted">{{ __('Property type') }}</th>
                            <td>{{ $fmt($property->property_type) }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Bedrooms') }}</th>
                            <td>{{ $property->bedrooms }}{{ $property->bedrooms >= 6 ? '+' : '' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Bathrooms') }}</th>
                            <td>{{ $property->bathrooms }}{{ $property->bathrooms >= 3 ? '+' : '' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Bathroom type') }}</th>
                            <td>{{ $fmt($property->bathroom_type) }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Furnished') }}</th>
                            <td>{{ $property->is_furnished ? __('Yes') : __('No') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Rent') }}</th>
                            <td>£{{ number_format($property->rent_amount) }} / {{ $property->rent_duration }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Bills') }}</th>
                            <td>{{ $fmt($property->bills_included) }}</td>
                        </tr>
                        @if ($property->bills_included === 'some' && is_array($property->included_bills) && count($property->included_bills))
                            <tr>
                                <th class="text-muted">{{ __('Included bills') }}</th>
                                <td>{{ collect($property->included_bills)->map(fn ($b) => $fmt($b))->implode(', ') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-muted">{{ __('Min. contract') }}</th>
                            <td>{{ $fmt($property->min_contract_length) }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Written agreement') }}</th>
                            <td>{{ $property->provides_agreement ? __('Yes') : __('No') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Deposit') }}</th>
                            <td>{{ $fmt($property->deposit_required) }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Rent for') }}</th>
                            <td>{{ $fmt($property->rent_for) }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Suitable for') }}</th>
                            <td>{{ is_array($property->suitable_for) ? collect($property->suitable_for)->map(fn ($s) => $fmt($s))->implode(', ') : '—' }}</td>
                        </tr>
                        @if ($property->listing_category === 'shared_room' && $property->flatmate_vibe)
                            <tr>
                                <th class="text-muted">{{ __('Flatmate vibe') }}</th>
                                <td>{{ $fmt($property->flatmate_vibe) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-muted">{{ __('House rules') }}</th>
                            <td>{{ is_array($property->house_rules) && count($property->house_rules) ? collect($property->house_rules)->map(fn ($x) => $fmt($x))->implode(', ') : __('None selected') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Amenities') }}</th>
                            <td>{{ is_array($property->amenities) ? collect($property->amenities)->map(fn ($a) => $fmt($a))->implode(', ') : '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Listed by') }}</th>
                            <td>{{ $property->creator?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Created') }}</th>
                            <td>{{ $property->created_at?->format('M j, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('Updated') }}</th>
                            <td>{{ $property->updated_at?->format('M j, Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
