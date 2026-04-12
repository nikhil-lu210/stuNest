@extends('layouts.administration.app')

@section('meta_tags')
@endsection

@section('page_title', $title)

@section('css_links')
@endsection

@section('custom_css')
@endsection

@section('page_name')
    <b class="text-uppercase">{{ $title }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User Management') }}</li>
    <li class="breadcrumb-item active">{{ $title }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $title }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary mb-4" role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        {{ __('This registration flow will live on the client portal (separate from the Vuexy administration area).') }}
                    </div>
                    <p class="mb-2">{{ __('Portal type: :type', ['type' => $portalLabel]) }}</p>
                    <p class="text-muted small mb-4">
                        {{ __('Dashboard theme reference (static HTML):') }}
                        <code class="user-select-all">project_documents/clients_theme/{{ $themeFile }}</code>
                    </p>
                    <p class="text-muted small mb-4">
                        {{ __('Spatie roles for this user type use the :guard auth guard (see config/auth.php).', ['guard' => $guardName]) }}
                    </p>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-label-secondary">{{ __('Back') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
