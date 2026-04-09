@extends('layouts.administration.app')

@section('page_title', $country->name)

@section('page_name')
    <b class="text-uppercase">{{ $country->name }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('administration.settings.geography.index') }}">{{ __('Geography') }}</a></li>
    <li class="breadcrumb-item active">{{ $country->name }}</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('administration.settings.geography.index') }}" class="btn btn-sm btn-label-secondary">
            <i class="ti ti-arrow-left me-1"></i>{{ __('Back to countries') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Cities in') }} {{ $country->name }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Areas') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cities as $city)
                                <tr>
                                    <td>{{ $city->name }}</td>
                                    <td>{{ $city->areas_count }}</td>
                                    <td>
                                        @if ($city->is_active)
                                            <span class="badge bg-label-success">{{ __('Enabled') }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ __('Disabled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('administration.settings.geography.cities.show', $city) }}" class="btn btn-sm btn-label-primary">
                                            {{ __('Manage areas') }}
                                        </a>
                                        @can('Geography Update')
                                            <form action="{{ route('administration.settings.geography.cities.toggle', $city) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-label-{{ $city->is_active ? 'warning' : 'success' }}">
                                                    {{ $city->is_active ? __('Disable') : __('Enable') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('No cities for this country.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
