@extends('layouts.landlord')

@section('title')
    {{ __('Notifications') }}
@endsection

@section('page_title')
    {{ __('Notifications') }}
@endsection

@section('content')
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('All notifications') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Updates about your listings, applications, and messages.') }}</p>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $body = is_array($data)
                        ? ($data['message'] ?? $data['body'] ?? $data['title'] ?? '')
                        : '';
                    if ($body === '') {
                        $body = __('You have a notification.');
                    }
                @endphp
                <li class="px-6 py-4 {{ $notification->read() ? 'bg-white' : 'bg-primary-50/50' }}">
                    <p class="text-sm text-gray-900">{{ $body }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                </li>
            @empty
                <li class="px-6 py-12 text-center text-sm text-gray-400">
                    {{ __('No notifications yet.') }}
                </li>
            @endforelse
        </ul>
        @if ($notifications->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                <div class="overflow-x-auto">
                    {{ $notifications->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
