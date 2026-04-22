@extends('layouts.landlord')

@section('title')
    {{ $pageTitle ?? __('Landlord Dashboard') }}
@endsection

@section('page_title')
    {{ $pageTitle ?? __('Dashboard Overview') }}
@endsection

@section('content')
    <p class="text-sm text-gray-500 mb-0">
        {{ __('Landlord module placeholder for') }} <span class="font-mono text-gray-700">{{ url()->current() }}</span>
    </p>
@endsection
