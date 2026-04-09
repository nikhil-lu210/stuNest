@extends('layouts.administration.app')

@section('page_title', $city->name)

@section('page_name')
    <b class="text-uppercase">{{ $city->name }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('administration.settings.geography.index') }}">{{ __('Geography') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('administration.settings.geography.countries.show', $city->country) }}">{{ $city->country->name }}</a></li>
    <li class="breadcrumb-item active">{{ $city->name }}</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('administration.settings.geography.countries.show', $city->country) }}" class="btn btn-sm btn-label-secondary">
            <i class="ti ti-arrow-left me-1"></i>{{ __('Back to cities') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Areas in') }} {{ $city->name }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Area') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($areas as $area)
                                <tr>
                                    <td>{{ $area->name }}</td>
                                    <td>
                                        @if ($area->is_active)
                                            <span class="badge bg-label-success">{{ __('Enabled') }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ __('Disabled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('Geography Update')
                                            <form action="{{ route('administration.settings.geography.areas.toggle', $area) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-label-{{ $area->is_active ? 'warning' : 'success' }}">
                                                    {{ $area->is_active ? __('Disable') : __('Enable') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">{{ __('No areas for this city.') }}</td>
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
