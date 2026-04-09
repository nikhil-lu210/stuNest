@extends('layouts.administration.app')

@section('meta_tags')
@endsection

@section('page_title', __('Institutes'))

@section('css_links')
    <link href="{{ asset('assets/css/custom_css/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom_css/datatables/datatable.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('custom_css')
    <style>
    </style>
@endsection

@section('page_name')
    <b class="text-uppercase">{{ __('All Institutes') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Institutions') }}</li>
    <li class="breadcrumb-item active">{{ __('All Institutes') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ __('All Institutes') }}</h5>
                <div class="card-header-elements ms-auto">
                    @can('Institute Create')
                        <a href="{{ route('administration.settings.institute.create') }}" class="btn btn-sm btn-primary">
                            <span class="tf-icon ti ti-plus ti-xs me-1"></span>
                            {{ __('Register Institute') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table class="table data-table table-bordered table-responsive" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('Sl.') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email code') }}</th>
                            <th>{{ __('Branches') }}</th>
                            <th>{{ __('Representatives') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($institutes as $key => $institute)
                            <tr>
                                <th>#{{ serial($institutes, $key) }}</th>
                                <td>{{ $institute->name }}</td>
                                <td><code>{{ $institute->email_code }}</code></td>
                                <td>{{ $institute->locations_count }}</td>
                                <td>{{ $institute->representatives_count }}</td>
                                <td>
                                    <a href="{{ route('administration.settings.institute.show', $institute) }}" class="btn btn-sm btn-icon item-edit" data-bs-toggle="tooltip" title="{{ __('View') }}">
                                        <i class="text-primary ti ti-info-hexagon"></i>
                                    </a>
                                    @can('Institute Update')
                                        <a href="{{ route('administration.settings.institute.edit', $institute) }}" class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                            <i class="text-primary ti ti-pencil"></i>
                                        </a>
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
