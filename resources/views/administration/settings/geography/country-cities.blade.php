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
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Cities in') }} {{ $country->name }}</h5>
                @can('Geography Create')
                    <div class="card-header-elements ms-auto">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">
                            <i class="ti ti-plus me-1"></i>{{ __('Add city') }}
                        </button>
                    </div>
                @endcan
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
                                    <td colspan="4" class="text-center text-muted">{{ __('No cities for this country. Add one or import JSON.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@can('Geography Create')
<div class="modal fade" id="addCityModal" tabindex="-1" aria-labelledby="addCityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCityModalLabel">{{ __('Add city') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form method="post" action="{{ route('administration.settings.geography.countries.cities.store', $country) }}" autocomplete="off">
                @csrf
                <input type="hidden" name="_form" value="city">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="city_name">{{ __('City name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="city_name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="city_is_active" @checked(old('is_active', '1') == '1')>
                        <label class="form-check-label" for="city_is_active">{{ __('Enabled') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('custom_script')
@can('Geography Create')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->any() && old('_form') === 'city')
    var el = document.getElementById('addCityModal');
    if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        new bootstrap.Modal(el).show();
    }
    @endif
});
</script>
@endcan
@endsection
