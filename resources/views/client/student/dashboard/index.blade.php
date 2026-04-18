@extends('layouts.client.student-app')

@section('title', __('Student Dashboard').' | '.config('app.name'))

@php
    $institutionLabel = $user->institution?->name ?? __('Student');
    $avatarUrl = $user->hasMedia('avatar')
        ? $user->getFirstMediaUrl('avatar', 'profile_view')
        : null;
@endphp

@section('content')
    <div class="flex h-screen w-full min-h-0 min-w-0 overflow-hidden">
        @include('layouts.client.partials.student-sidebar-linked', [
            'user' => $user,
            'institutionLabel' => $institutionLabel,
            'avatarUrl' => $avatarUrl,
            'active' => 'applications',
        ])

        <main class="flex min-h-0 min-w-0 flex-1 flex-col bg-gray-50 pt-16 md:pt-0">
            <div class="hidden h-20 w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6 md:px-8 lg:px-10 md:flex sticky top-0 z-40">
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Applications') }}</h1>
                <div class="flex items-center gap-4">
                    <livewire:student.notification-bell />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
            <div class="w-full max-w-full px-4 py-4 sm:px-6 md:px-8 lg:px-10 xl:px-12 md:py-8 pb-24">

                @if ($user->account_status === \App\Models\User::ACCOUNT_STATUS_UNVERIFIED)
                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <i data-lucide="shield-alert" class="w-5 h-5 text-blue-600 mt-0.5 shrink-0"></i>
                            <div>
                                <h4 class="font-semibold text-blue-900 text-sm">{{ __('Verify your student status') }}</h4>
                                <p class="text-sm text-blue-700 mt-0.5">{{ __('Upload your university ID to fast-track your housing applications.') }}</p>
                            </div>
                        </div>
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap">
                            {{ __('Verify Now') }}
                        </button>
                    </div>
                @endif

                <livewire:student.student-applications-list />
            </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            if (window.location.hash === '#messages-tab') {
                window.location.replace(@json(route('client.student.messages')));
                return;
            }
            if (window.lucide && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        })();
    </script>
@endpush
