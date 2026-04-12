@extends('layouts.administration.app')

@section('meta_tags')
@endsection

@section('page_title', $pageTitleMeta)

@section('css_links')
    <link href="{{ asset('assets/css/custom_css/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom_css/datatables/datatable.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('custom_css')
    <style>
    </style>
@endsection

@section('page_name')
    <b class="text-uppercase">{{ $pageHeading }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ $breadcrumbParent }}</li>
    <li class="breadcrumb-item active">{{ $breadcrumbCurrent }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h5 class="mb-0">{{ $cardTitle }}</h5>

                <div class="card-header-elements ms-auto">
                    @isset($createRoute)
                        <a href="{{ $createRoute }}" class="btn btn-sm btn-primary">
                            <span class="tf-icon ti ti-plus ti-xs me-1"></span>
                            {{ $createLabel }}
                        </a>
                    @endisset
                </div>
            </div>
            <div class="card-body">
                <table class="table data-table table-bordered table-responsive" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $user)
                            @php
                                $accountStatus = $user->account_status ?? \App\Models\User::ACCOUNT_STATUS_ACTIVE;
                            @endphp
                            <tr>
                                <th>#{{ serial($users, $key) }}</th>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                @if ($user->hasMedia('avatar'))
                                                    <img src="{{ $user->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ $user->name }} Avatar" class="rounded-circle">
                                                @else
                                                    <img src="{{ file_exists(public_path('assets/img/no_image.svg')) ? asset('assets/img/no_image.svg') : 'https://placehold.co/400?text=No+Image' }}" alt="{{ $user->name }} No Avatar" class="rounded-circle">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('administration.settings.user.show.profile', ['user' => $user]) }}" class="emp_name text-truncate">{{ $user->name }}</a>
                                            <small class="emp_post text-truncate text-muted">{{ $user->roles->first()?->name ?? '—' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @switch($accountStatus)
                                        @case(\App\Models\User::ACCOUNT_STATUS_ACTIVE)
                                            <span class="badge bg-label-success">{{ __('Active') }}</span>
                                            @break
                                        @case(\App\Models\User::ACCOUNT_STATUS_PENDING)
                                            <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                                            @break
                                        @case(\App\Models\User::ACCOUNT_STATUS_REJECTED)
                                            <span class="badge bg-label-danger">{{ __('Rejected') }}</span>
                                            @break
                                        @case(\App\Models\User::ACCOUNT_STATUS_UNVERIFIED)
                                            <span class="badge bg-label-info">{{ __('Unverified') }}</span>
                                            @break
                                        @default
                                            <span class="badge bg-label-secondary">{{ ucfirst($accountStatus) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="d-inline-block">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="text-primary ti ti-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <a href="{{ route('administration.settings.user.edit', ['user' => $user]) }}" class="dropdown-item">
                                                <i class="text-primary ti ti-pencil"></i>
                                                {{ __('Edit') }}
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a href="{{ route('administration.settings.user.destroy', ['user' => $user]) }}" class="dropdown-item text-danger delete-record confirm-danger">
                                                <i class="ti ti-trash"></i>
                                                {{ __('Delete') }}
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{ route('administration.settings.user.show.profile', ['user' => $user]) }}" class="btn btn-sm btn-icon item-edit" data-bs-toggle="tooltip" title="{{ __('Show Details') }}">
                                        <i class="text-primary ti ti-info-hexagon"></i>
                                    </a>
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
