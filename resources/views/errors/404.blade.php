@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-4">404</h1>
    <p class="lead">{{ __('Page not found.') }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary">{{ __('Home') }}</a>
</div>
@endsection
