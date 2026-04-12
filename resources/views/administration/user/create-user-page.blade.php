@extends('layouts.administration.app')

@section('meta_tags')
    @livewireStyles
@endsection

@section('page_title', __('Create administration user'))

@section('css_links')
@endsection

@section('custom_css')
@endsection

@section('page_name')
    <b class="text-uppercase">{{ __('Create administration user') }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User Management') }}</li>
    <li class="breadcrumb-item active">{{ __('Create administration user') }}</li>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <i class="ti ti-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif
    <livewire:administration.user.create-user />
@endsection

@section('custom_script')
    @livewireScripts
@endsection
