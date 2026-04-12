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
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Areas in') }} {{ $city->name }}</h5>
                @can('Geography Create')
                    <div class="card-header-elements ms-auto">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAreaModal">
                            <i class="ti ti-plus me-1"></i>{{ __('Add area') }}
                        </button>
                    </div>
                @endcan
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
                                    <td colspan="3" class="text-center text-muted">{{ __('No areas for this city. Add one or import JSON.') }}</td>
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
<div class="modal fade" id="addAreaModal" tabindex="-1" aria-labelledby="addAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAreaModalLabel">{{ __('Add area') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form method="post" action="{{ route('administration.settings.geography.cities.areas.store', $city) }}" autocomplete="off">
                @csrf
                <input type="hidden" name="_form" value="area">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="area_name">{{ __('Area name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="area_name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="area_is_active" @checked(old('is_active', '1') == '1')>
                        <label class="form-check-label" for="area_is_active">{{ __('Enabled') }}</label>
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
    @if ($errors->any() && old('_form') === 'area')
    var el = document.getElementById('addAreaModal');
    if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        new bootstrap.Modal(el).show();
    }
    @endif
});
</script>
@endcan
@endsection
