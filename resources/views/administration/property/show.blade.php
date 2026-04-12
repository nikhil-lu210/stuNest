@extends('layouts.administration.app')

@section('page_title', __('Property #:id', ['id' => $property->id]))

@section('page_name')
    <b class="text-uppercase">{{ __('Property #:id', ['id' => $property->id]) }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('administration.properties.index') }}">{{ __('Property listings') }}</a></li>
    <li class="breadcrumb-item active">#{{ $property->id }}</li>
@endsection

@section('content')
@php
    $fmt = fn (?string $v) => $v ? str_replace('_', ' ', ucfirst($v)) : '—';
    $mediaThumb = function ($media) {
        return $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl();
    };
    $mediaFull = function ($media) {
        return $media->hasGeneratedConversion('optimized') ? $media->getUrl('optimized') : $media->getUrl();
    };
@endphp

<div class="row">
    <div class="col-12">
        {{-- Summary --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-lg-between gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <h4 class="mb-0">{{ __('Listing #:id', ['id' => $property->id]) }}</h4>
                            @switch($property->status)
                                @case(\App\Models\Property\Property::STATUS_DRAFT)
                                    <span class="badge bg-label-secondary">{{ __('Draft') }}</span>
                                    @break
                                @case(\App\Models\Property\Property::STATUS_PENDING)
                                    <span class="badge bg-label-warning">{{ __('Pending review') }}</span>
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
                        </div>
                        <p class="text-muted mb-0">
                            <i class="ti ti-map-pin me-1"></i>
                            {{ $property->area?->name ?? '—' }}{{ $property->city ? ', '.$property->city->name : '' }}{{ $property->country ? ', '.$property->country->name : '' }}
                        </p>
                    </div>
                    <div class="text-lg-end">
                        <div class="h5 mb-1 text-primary">
                            €{{ number_format($property->rent_amount) }}
                            <span class="text-muted fs-6 fw-normal">/ {{ $fmt($property->rent_duration) }}</span>
                        </div>
                        <span class="text-muted small">{{ $fmt($property->listing_category) }} · {{ $fmt($property->property_type) }}</span>
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex flex-wrap gap-2">
                    @can('update', $property)
                        <a href="{{ route('administration.properties.edit', $property) }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-pencil me-1"></i>{{ __('Edit listing') }}
                        </a>
                    @endcan
                    <a href="{{ route('administration.properties.index') }}" class="btn btn-sm btn-label-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{{ __('Back to list') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Gallery --}}
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">
                    <i class="ti ti-photo me-1"></i>{{ __('Photos') }}
                </h5>
                <span class="badge bg-label-primary">{{ $gallery->count() }} {{ __('images') }}</span>
            </div>
            <div class="card-body">
                @if ($gallery->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-photo-off ti-lg mb-2 d-block opacity-50"></i>
                        <p class="mb-0">{{ __('No photos have been uploaded for this listing yet.') }}</p>
                    </div>
                @else
                    @php $first = $gallery->first(); @endphp
                    <div class="row g-4">
                        <div class="col-12 {{ $gallery->count() > 1 ? 'col-lg-7' : '' }}">
                            <a href="{{ $mediaFull($first) }}" target="_blank" rel="noopener noreferrer" class="d-block rounded overflow-hidden border bg-label-secondary bg-opacity-10">
                                <img src="{{ $mediaThumb($first) }}" alt="{{ $first->name }}"
                                    class="w-100 object-fit-cover"
                                    style="max-height: 420px; min-height: 280px;">
                            </a>
                            <p class="small text-muted mt-2 mb-0">{{ __('Click image to open full size') }}</p>
                        </div>
                        @if ($gallery->count() > 1)
                            <div class="col-12 col-lg-5">
                                <p class="small text-uppercase text-muted mb-2">{{ __('More photos') }}</p>
                                <div class="row g-2">
                                    @foreach ($gallery->skip(1) as $thumb)
                                        <div class="col-4">
                                            <a href="{{ $mediaFull($thumb) }}" target="_blank" rel="noopener noreferrer" class="d-block rounded overflow-hidden border">
                                                <img src="{{ $mediaThumb($thumb) }}" alt=""
                                                    class="w-100 object-fit-cover" style="height: 96px;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @if ($gallery->count() > 1)
                        <hr class="my-4">
                        <h6 class="text-muted text-uppercase small mb-3">{{ __('All images') }}</h6>
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
                            @foreach ($gallery as $m)
                                <div class="col">
                                    <a href="{{ $mediaFull($m) }}" target="_blank" rel="noopener noreferrer" class="d-block rounded overflow-hidden border shadow-sm">
                                        <img src="{{ $mediaThumb($m) }}" alt=""
                                            class="w-100 object-fit-cover" style="height: 120px;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-map-2 me-1"></i>{{ __('Location') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted small">{{ __('Country') }}</dt>
                            <dd class="col-sm-8">{{ $property->country?->name ?? '—' }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('City') }}</dt>
                            <dd class="col-sm-8">{{ $property->city?->name ?? '—' }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Area') }}</dt>
                            <dd class="col-sm-8">{{ $property->area?->name ?? '—' }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Map') }}</dt>
                            <dd class="col-sm-8">
                                @if ($property->map_link)
                                    <a href="{{ $property->map_link }}" target="_blank" rel="noopener noreferrer" class="text-break">{{ __('Open in Google Maps') }}</a>
                                @else
                                    —
                                @endif
                            </dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Coordinates') }}</dt>
                            <dd class="col-sm-8">
                                @if ($property->latitude !== null && $property->longitude !== null)
                                    <code class="user-select-all">{{ $property->latitude }}, {{ $property->longitude }}</code>
                                @else
                                    —
                                @endif
                            </dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Distance to university') }}</dt>
                            <dd class="col-sm-8">{{ $property->distance_university_km !== null ? $property->distance_university_km.' km' : '—' }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Distance to transit') }}</dt>
                            <dd class="col-sm-8">{{ $property->distance_transit_km !== null ? $property->distance_transit_km.' km' : '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-home-2 me-1"></i>{{ __('Property') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted small">{{ __('Category') }}</dt>
                            <dd class="col-sm-8">{{ $fmt($property->listing_category) }}</dd>
                            @if ($property->listing_category === 'shared_room' && $property->bed_type)
                                <dt class="col-sm-4 text-muted small">{{ __('Bed type') }}</dt>
                                <dd class="col-sm-8">{{ ucwords(str_replace('_', ' ', $property->bed_type)) }}</dd>
                            @endif
                            <dt class="col-sm-4 text-muted small">{{ __('Type') }}</dt>
                            <dd class="col-sm-8">{{ $fmt($property->property_type) }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Bedrooms') }}</dt>
                            <dd class="col-sm-8">{{ $property->bedrooms }}{{ $property->bedrooms >= 6 ? '+' : '' }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Bathrooms') }}</dt>
                            <dd class="col-sm-8">{{ $property->bathrooms }}{{ $property->bathrooms >= 3 ? '+' : '' }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Bathroom type') }}</dt>
                            <dd class="col-sm-8">{{ $fmt($property->bathroom_type) }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Furnished') }}</dt>
                            <dd class="col-sm-8">{{ $property->is_furnished ? __('Yes') : __('No') }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Capacity / beds') }}</dt>
                            <dd class="col-sm-8">{{ $property->capacity ?? '—' }} / {{ $property->available_beds ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-0 mt-lg-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-currency-euro me-1"></i>{{ __('Rent & contract') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted small">{{ __('Rent') }}</dt>
                            <dd class="col-sm-8">€{{ number_format($property->rent_amount) }} / {{ $property->rent_duration }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Bills') }}</dt>
                            <dd class="col-sm-8">{{ $fmt($property->bills_included) }}</dd>
                            @if ($property->bills_included === 'some' && is_array($property->included_bills) && count($property->included_bills))
                                <dt class="col-sm-4 text-muted small">{{ __('Included bills') }}</dt>
                                <dd class="col-sm-8">{{ collect($property->included_bills)->map(fn ($b) => $fmt($b))->implode(', ') }}</dd>
                            @endif
                            <dt class="col-sm-4 text-muted small">{{ __('Min. contract') }}</dt>
                            <dd class="col-sm-8">{{ $fmt($property->min_contract_length) }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Written agreement') }}</dt>
                            <dd class="col-sm-8">{{ $property->provides_agreement ? __('Yes') : __('No') }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Deposit') }}</dt>
                            <dd class="col-sm-8">{{ $fmt($property->deposit_required) }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Rent for') }}</dt>
                            <dd class="col-sm-8">{{ $fmt($property->rent_for) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-users me-1"></i>{{ __('Household & amenities') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted small">{{ __('Suitable for') }}</dt>
                            <dd class="col-sm-8">{{ is_array($property->suitable_for) ? collect($property->suitable_for)->map(fn ($s) => $fmt($s))->implode(', ') : '—' }}</dd>
                            @if ($property->listing_category === 'shared_room' && $property->flatmate_vibe)
                                <dt class="col-sm-4 text-muted small">{{ __('Flatmate vibe') }}</dt>
                                <dd class="col-sm-8">{{ $fmt($property->flatmate_vibe) }}</dd>
                            @endif
                            <dt class="col-sm-4 text-muted small">{{ __('House rules') }}</dt>
                            <dd class="col-sm-8">{{ is_array($property->house_rules) && count($property->house_rules) ? collect($property->house_rules)->map(fn ($x) => $fmt($x))->implode(', ') : __('None selected') }}</dd>
                            <dt class="col-sm-4 text-muted small">{{ __('Amenities') }}</dt>
                            <dd class="col-sm-8">{{ is_array($property->amenities) ? collect($property->amenities)->map(fn ($a) => $fmt($a))->implode(', ') : '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-info-circle me-1"></i>{{ __('Record') }}</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 col-md-2 text-muted small">{{ __('Listed by') }}</dt>
                    <dd class="col-sm-9 col-md-10">{{ $property->creator?->name ?? '—' }}</dd>
                    <dt class="col-sm-3 col-md-2 text-muted small">{{ __('Created') }}</dt>
                    <dd class="col-sm-9 col-md-10">{{ $property->created_at?->format('M j, Y H:i') }}</dd>
                    <dt class="col-sm-3 col-md-2 text-muted small">{{ __('Updated') }}</dt>
                    <dd class="col-sm-9 col-md-10">{{ $property->updated_at?->format('M j, Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
