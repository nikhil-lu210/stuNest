@extends('layouts.administration.app')

@section('page_title', __('Geography data'))

@section('page_name')
    <b class="text-uppercase">{{ __('Geography data') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
    <li class="breadcrumb-item active">{{ __('Geography') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Import country / city / area data') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ __('Upload a JSON file following the structure below. Existing countries match by :code; cities and areas match by name within their parent.', ['code' => 'ISO code']) }}</p>
                <p>
                    <a href="{{ route('administration.settings.geography.import.sample') }}" class="btn btn-sm btn-label-secondary">
                        <i class="ti ti-download me-1"></i>{{ __('Download sample JSON') }}
                    </a>
                </p>
                @can('Geography Create')
                    <form action="{{ route('administration.settings.geography.import') }}" method="post" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <label class="form-label">{{ __('JSON file') }}</label>
                                <input type="file" name="file" class="form-control" accept=".json,application/json" required />
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Upload & import') }}</button>
                            </div>
                        </div>
                    </form>
                @else
                    <p class="text-muted mb-0"><em>{{ __('You do not have permission to import geography data.') }}</em></p>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Countries') }}</h5>
                @can('Geography Create')
                    <div class="card-header-elements ms-auto">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                            <i class="ti ti-plus me-1"></i>{{ __('Add country') }}
                        </button>
                    </div>
                @endcan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('ISO') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Cities') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($countries as $country)
                                <tr>
                                    <td><code>{{ $country->iso_code }}</code></td>
                                    <td>{{ $country->name }}</td>
                                    <td>{{ $country->cities_count }}</td>
                                    <td>
                                        @if ($country->is_active)
                                            <span class="badge bg-label-success">{{ __('Enabled') }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ __('Disabled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('administration.settings.geography.countries.show', $country) }}" class="btn btn-sm btn-label-primary">
                                            {{ __('Manage cities') }}
                                        </a>
                                        @can('Geography Update')
                                            <form action="{{ route('administration.settings.geography.countries.toggle', $country) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-label-{{ $country->is_active ? 'warning' : 'success' }}">
                                                    {{ $country->is_active ? __('Disable') : __('Enable') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">{{ __('No countries yet. Add one, import JSON, or run the Cyprus seeder.') }}</td>
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
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCountryModalLabel">{{ __('Add country') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form method="post" action="{{ route('administration.settings.geography.countries.store') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="_form" value="country">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="country_iso">{{ __('ISO code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="iso_code" id="country_iso" value="{{ old('iso_code') }}" class="form-control @error('iso_code') is-invalid @enderror" maxlength="2" required placeholder="{{ __('e.g. CY') }}" style="text-transform: uppercase;">
                        @error('iso_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="country_name">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="country_name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="country_is_active" @checked(old('is_active', '1') == '1')>
                        <label class="form-check-label" for="country_is_active">{{ __('Enabled') }}</label>
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
    @if ($errors->any() && old('_form') === 'country')
    var el = document.getElementById('addCountryModal');
    if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        new bootstrap.Modal(el).show();
    }
    @endif
});
</script>
@endcan
@endsection
