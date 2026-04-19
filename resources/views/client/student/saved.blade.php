@extends('layouts.client.student-app')

@section('title', __('Saved Properties').' | '.config('app.name'))

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
            'active' => 'saved',
        ])

        <main class="flex min-h-0 min-w-0 flex-1 flex-col bg-gray-50 pt-16 md:pt-0">
            <div class="hidden h-20 w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-8 md:flex sticky top-0 z-40">
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Saved Properties') }}</h1>
                <div class="flex items-center gap-4">
                    <livewire:student.notification-bell />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                {{-- Matches project_documents/clients_theme/student_dashboard.html main content width --}}
                <div class="mx-auto w-full max-w-5xl p-4 pb-24 md:p-8">
                    @if ($user->account_status === \App\Models\User::ACCOUNT_STATUS_UNVERIFIED)
                        <div class="mb-8 flex flex-col items-start justify-between gap-4 rounded-2xl border border-blue-100 bg-blue-50 p-4 sm:flex-row sm:items-center">
                            <div class="flex items-start gap-3">
                                <i data-lucide="shield-alert" class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900">{{ __('Verify your student status') }}</h4>
                                    <p class="mt-0.5 text-sm text-blue-700">{{ __('Upload your university ID to fast-track your housing applications.') }}</p>
                                </div>
                            </div>
                            <button type="button" class="whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                                {{ __('Verify Now') }}
                            </button>
                        </div>
                    @endif

                    <livewire:student.student-saved-properties />
                </div>
            </div>
        </main>
    </div>
@endsection
