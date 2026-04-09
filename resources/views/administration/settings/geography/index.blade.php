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
                                    <td colspan="5" class="text-center text-muted">{{ __('No countries yet. Import JSON or run the Cyprus seeder.') }}</td>
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
