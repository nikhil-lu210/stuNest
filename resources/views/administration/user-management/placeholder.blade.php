@extends('layouts.administration.app')

@section('page_title', $title)

@section('page_name')
    <b class="text-uppercase">{{ $title }}</b>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ $title }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h1 class="mb-2">{{ $title }}</h1>
            <p class="text-muted mb-0">{{ __('This page is scaffolded; listing and actions will be wired here.') }}</p>
        </div>
    </div>
@endsection
