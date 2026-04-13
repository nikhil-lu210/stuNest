@extends('layouts.administration.app')

@section('page_title', __('Landlord profile'))

@section('css_links')
    <link href="{{ asset('assets/css/custom_css/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom_css/datatables/datatable.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
@endsection

@section('custom_css')
    <style></style>
@endsection

@section('page_name')
    <b class="text-uppercase">{{ __('Landlord profile') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User Management') }}</li>
    <li class="breadcrumb-item">{{ __('Landlord') }}</li>
    <li class="breadcrumb-item">
        <a href="{{ route('administration.landlords.index') }}">{{ __('All Landlords') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('Profile') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-12 mt-4">
            <div class="card mb-4">
                <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        @if ($user->hasMedia('avatar'))
                            <img src="{{ $user->getFirstMediaUrl('avatar', 'profile_view') }}" alt="{{ $user->name }} Avatar" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
                        @else
                            <img src="{{ file_exists(public_path('assets/img/no_image.svg')) ? asset('assets/img/no_image.svg') : 'https://placehold.co/400?text=No+Image' }}" alt="{{ $user->name }} No Avatar" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
                        @endif
                    </div>
                    <div class="flex-grow-1 mt-3 mt-sm-5">
                        <div class="d-flex align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                            <div class="user-profile-info">
                                <h4 class="mb-0">{{ $user->name }}</h4>
                                <p class="fw-bold text-dark mb-1">{{ __('ID') }}: <span class="text-primary">{{ $user->userid }}</span></p>
                                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                    <li class="list-inline-item d-flex gap-1">
                                        <i class="ti ti-crown"></i>
                                        {{ $user->roles->first()?->name ?? '—' }}
                                    </li>
                                    <li class="list-inline-item d-flex gap-1">
                                        <i class="ti ti-calendar"></i>
                                        {{ show_date($user->created_at) }}
                                    </li>
                                </ul>
                            </div>
                            @can('User Update')
                                <a href="{{ route('administration.landlords.edit', ['user' => $user]) }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="ti ti-pencil me-1"></i>
                                    {{ __('Edit landlord') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('administration.landlords.show') ? 'active' : '' }}" href="{{ route('administration.landlords.show', ['user' => $user]) }}">
                        <i class="ti-xs ti ti-user-check me-1"></i>
                        {{ __('Profile') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('administration.landlords.show.applications') ? 'active' : '' }}" href="{{ route('administration.landlords.show.applications', ['user' => $user]) }}">
                        <i class="ti-xs ti ti-file-text me-1"></i>
                        {{ __('Applications') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('administration.landlords.show.favorites') ? 'active' : '' }}" href="{{ route('administration.landlords.show.favorites', ['user' => $user]) }}">
                        <i class="ti-xs ti ti-heart me-1"></i>
                        {{ __('Favorites') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    @yield('profile_content')
@endsection

@section('script_links')
    <script src="{{ asset('assets/js/custom_js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom_js/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom_js/datatables/datatable.js') }}"></script>
    <script src="{{ asset('assets/js/pages-profile.js') }}"></script>
@endsection

@section('custom_script')
    <script></script>
@endsection
