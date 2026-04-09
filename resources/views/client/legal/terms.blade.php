@extends('layouts.client.app')

@section('title', 'Terms of Service | '.config('app.name'))

@section('body_class', 'bg-gray-50 font-sans text-gray-900 antialiased min-h-screen')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-16">
        <a href="{{ route('client.home') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 mb-8 inline-block">← Home</a>
        <h1 class="text-3xl font-semibold tracking-tight mb-2">Terms of Service</h1>
        <p class="text-sm text-gray-500 mb-10">Last updated: {{ date('F j, Y') }}</p>

        <div class="prose prose-gray max-w-none space-y-6 text-gray-600 leading-relaxed">
            <p>
                {{ config('app.name') }} provides a platform to help students discover accommodation and connect with landlords. These terms are a placeholder for your pilot — replace with counsel-reviewed copy before launch.
            </p>
            <p>
                <strong class="text-gray-900">Landlord responsibility.</strong> Landlords are solely responsible for compliance with UK private rented sector rules, Right to Rent checks, tenancy agreements, and deposit protection where applicable. {{ config('app.name') }} does not hold deposits or act as letting agent unless separately agreed in writing.
            </p>
            <p>
                <strong class="text-gray-900">No legal advice.</strong> Nothing on this site constitutes legal, financial, or immigration advice.
            </p>
        </div>
    </div>
@endsection
