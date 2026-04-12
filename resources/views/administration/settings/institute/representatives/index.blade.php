@extends('layouts.administration.app')

@section('meta_tags')
@endsection

@section('page_title', __('All Representatives'))

@section('css_links')
    <link href="{{ asset('assets/css/custom_css/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom_css/datatables/datatable.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page_name')
    <b class="text-uppercase">{{ __('All Representatives') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institute') }}</li>
    <li class="breadcrumb-item active">{{ __('All Representatives') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('All Representatives') }}</h5>
                <div class="card-header-elements ms-auto">
                    @can('Institute Update')
                        <a href="{{ route('administration.institute.representatives.create.entry') }}" class="btn btn-sm btn-primary">
                            <span class="tf-icon ti ti-plus ti-xs me-1"></span>
                            {{ __('Create New Representative') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table class="table data-table table-bordered table-responsive" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('Sl.') }}</th>
                            <th>{{ __('Institute') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($representatives as $key => $rep)
                            <tr>
                                <th>#{{ serial($representatives, $key) }}</th>
                                <td>{{ $rep->institute?->name ?? '—' }}</td>
                                <td>{{ $rep->location?->name ?? '—' }}</td>
                                <td>{{ $rep->user?->name ?? '—' }}</td>
                                <td>{{ $rep->user?->email ?? '—' }}</td>
                                <td>
                                    @if ($rep->institute)
                                        <a href="{{ route('administration.institute.show', $rep->institute) }}" class="btn btn-sm btn-icon item-edit" data-bs-toggle="tooltip" title="{{ __('View institute') }}">
                                            <i class="text-primary ti ti-info-hexagon"></i>
                                        </a>
                                    @endif
                                    @can('Institute Update')
                                        @if ($rep->institute)
                                            <a href="{{ route('administration.institute.representatives.destroy', [$rep->institute, $rep]) }}" class="btn btn-sm btn-label-danger confirm-danger">
                                                {{ __('Remove') }}
                                            </a>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script_links')
    <script src="{{ asset('assets/js/custom_js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom_js/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom_js/datatables/datatable.js') }}"></script>
@endsection

@section('custom_script')
    <script></script>
@endsection
