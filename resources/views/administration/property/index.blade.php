@extends('layouts.administration.app')

@section('page_title', $pageTitle)

@section('page_name')
    <b class="text-uppercase">{{ $pageTitle }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Property listings') }}</li>
    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ $pageTitle }}</h5>
                <div class="card-header-elements ms-auto">
                    @can('create', \App\Models\Property\Property::class)
                        <a href="{{ route('administration.properties.create') }}" class="btn btn-sm btn-primary">
                            <span class="tf-icon ti ti-plus ti-xs me-1"></span>
                            {{ __('Create listing') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                @if ($properties->isEmpty())
                    <p class="text-muted mb-0">{{ __('No properties found for this view.') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 4rem;">#</th>
                                    <th>{{ __('Listing') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Bedrooms') }}</th>
                                    <th>{{ __('Rent') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Listed by') }}</th>
                                    <th>{{ __('Created') }}</th>
                                    <th class="text-nowrap">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($properties as $key => $property)
                                    <tr>
                                        <td>{{ $properties->firstItem() + $key }}</td>
                                        <td>
                                            <span class="fw-medium">{{ str_replace('_', ' ', ucfirst($property->listing_category)) }}</span>
                                        </td>
                                        <td>{{ str_replace('_', ' ', ucfirst($property->property_type)) }}</td>
                                        <td>{{ $property->bedrooms }}{{ $property->bedrooms >= 6 ? '+' : '' }}</td>
                                        <td>
                                            £{{ number_format($property->rent_amount) }}
                                            <small class="text-muted">/ {{ $property->rent_duration }}</small>
                                        </td>
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
                                                    <span class="badge bg-label-secondary">{{ str_replace('_', ' ', $property->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if ($property->creator)
                                                <span class="text-truncate d-inline-block" style="max-width: 12rem;">{{ $property->creator->name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $property->created_at?->format('M j, Y') }}</small></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @can('view', $property)
                                                    <a href="{{ route('administration.properties.show', $property) }}" class="btn btn-sm btn-text-secondary">
                                                        <i class="ti ti-eye ti-xs me-1"></i>{{ __('Show') }}
                                                    </a>
                                                @endcan
                                                @can('update', $property)
                                                    <a href="{{ route('administration.properties.edit', $property) }}" class="btn btn-sm btn-text-primary">
                                                        <i class="ti ti-pencil ti-xs me-1"></i>{{ __('Edit') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $properties->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
