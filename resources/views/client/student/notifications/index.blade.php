@extends('layouts.client.student-app')

@section('title', __('Notifications').' | '.config('app.name'))

@php
    $institutionLabel = $user->institution?->name ?? __('Student');
    $avatarUrl = $user->hasMedia('avatar')
        ? $user->getFirstMediaUrl('avatar', 'profile_view')
        : null;
@endphp

@section('content')
    <div class="flex h-screen w-full min-h-0 min-w-0 max-md:overflow-visible md:overflow-hidden">
        @include('layouts.client.partials.student-sidebar-linked', [
            'user' => $user,
            'institutionLabel' => $institutionLabel,
            'avatarUrl' => $avatarUrl,
            'active' => 'applications',
        ])

        <main class="flex min-h-0 min-w-0 flex-1 flex-col bg-gray-50 pt-16 md:pt-0">
            <div class="hidden h-20 w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-8 md:flex sticky top-0 z-40">
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Notifications') }}</h1>
                <div class="flex items-center gap-4">
                    <livewire:student.notification-bell />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="mx-auto w-full max-w-5xl p-4 pb-24 md:p-8">
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-6 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('All notifications') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Updates about your applications and messages.') }}</p>
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
                                <li class="px-6 py-4 {{ $notification->read() ? 'bg-white' : 'bg-blue-50/50' }}">
                                    <p class="text-sm text-gray-900">{{ $body }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
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
                </div>
            </div>
        </main>
    </div>
@endsection
