@extends('layouts.administration.app')

@section('page_title', $institute->name)

@section('page_name')
    <b class="text-uppercase">{{ $institute->name }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institute') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('administration.institute.index') }}">{{ __('All Institutes') }}</a></li>
    <li class="breadcrumb-item active">{{ $institute->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('Institute details') }}</h5>
                <div class="card-header-elements ms-auto">
                    @can('Institute Update')
                        <a href="{{ route('administration.institute.edit', $institute) }}" class="btn btn-sm btn-primary me-1">
                            <i class="ti ti-pencil me-1"></i>{{ __('Edit') }}
                        </a>
                    @endcan
                    @can('Institute Update')
                        <a href="{{ route('administration.institute.representatives.create', $institute) }}" class="btn btn-sm btn-label-primary">
                            <i class="ti ti-user-plus me-1"></i>{{ __('Add representative') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">{{ __('Name') }}</dt>
                    <dd class="col-sm-9">{{ $institute->name }}</dd>
                    <dt class="col-sm-3">{{ __('Email code') }}</dt>
                    <dd class="col-sm-9"><code>{{ $institute->email_code }}</code></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Branches') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Branch') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Area') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th>{{ __('Postcode') }}</th>
                                <th>{{ __('Primary') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($institute->locations as $loc)
                                <tr>
                                    <td>{{ $loc->name }}</td>
                                    <td>{{ $loc->country?->name ?? '—' }}</td>
                                    <td>{{ $loc->city?->name ?? '—' }}</td>
                                    <td>{{ $loc->area?->name ?? '—' }}</td>
                                    <td>{{ $loc->address_line_1 ?? '—' }}</td>
                                    <td>{{ $loc->postcode ?? '—' }}</td>
                                    <td>
                                        @if ($loc->is_primary)
                                            <span class="badge bg-label-primary">{{ __('Yes') }}</span>
                                        @else
                                            <span class="text-muted">{{ __('No') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">{{ __('No branches recorded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Representatives by branch') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Branch') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($institute->representatives as $rep)
                                <tr>
                                    <td>{{ $rep->location?->name ?? '—' }}</td>
                                    <td>{{ $rep->user?->name }}</td>
                                    <td>{{ $rep->user?->email }}</td>
                                    <td>
                                        @can('Institute Update')
                                            <a href="{{ route('administration.institute.representatives.destroy', [$institute, $rep]) }}" class="btn btn-sm btn-label-danger confirm-danger">
                                                {{ __('Remove') }}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('No representatives yet.') }}</td>
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
