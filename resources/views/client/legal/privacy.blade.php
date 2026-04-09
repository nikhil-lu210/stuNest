@extends('layouts.client.app')

@section('title', 'Privacy Policy | '.config('app.name'))

@section('body_class', 'bg-gray-50 font-sans text-gray-900 antialiased min-h-screen')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-16">
        <a href="{{ route('client.home') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 mb-8 inline-block">← Home</a>
        <h1 class="text-3xl font-semibold tracking-tight mb-2">Privacy Policy</h1>
        <p class="text-sm text-gray-500 mb-10">Last updated: {{ date('F j, Y') }}</p>

        <div class="prose prose-gray max-w-none space-y-6 text-gray-600 leading-relaxed">
            <p>
                This placeholder describes how {{ config('app.name') }} may collect and use personal data. Replace with a full policy aligned to UK GDPR and your actual data practices before production.
            </p>
            <p>
                We may process account details, contact information you provide, and usage data to operate the service, improve the product, and communicate with you about your account.
            </p>
        </div>
    </div>
@endsection
