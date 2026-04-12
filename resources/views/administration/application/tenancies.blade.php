@extends('layouts.administration.app')

@section('page_title', __('Tenancies & applications'))

@section('page_name')
    <b class="text-uppercase">{{ __('Accepted tenancies') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Tenancies & applications') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Accepted applications') }}</h5>
            </div>
            <div class="card-body">
                @if ($applications->isEmpty())
                    <p class="text-muted mb-0">{{ __('No accepted applications yet.') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Property') }}</th>
                                    <th>{{ __('Accepted at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $application)
                                    <tr>
                                        <td>{{ $application->id }}</td>
                                        <td>
                                            <div class="fw-medium">{{ $application->student?->name ?? '—' }}</div>
                                            <small class="text-muted">{{ $application->student?->email }}</small>
                                        </td>
                                        <td>
                                            @if ($application->property)
                                                <a href="{{ route('administration.properties.show', $application->property) }}">
                                                    #{{ $application->property->id }} — {{ str_replace('_', ' ', ucfirst($application->property->listing_category)) }}
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td><small>{{ $application->accepted_at?->format('M j, Y H:i') }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
