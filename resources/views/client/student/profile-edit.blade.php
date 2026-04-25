@extends('layouts.client.student-app')

@section('title', __('Complete profile').' | '.config('app.name'))

@php
    $user->loadMissing('institution');
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
            'active' => 'settings',
        ])

        <main class="flex min-h-0 min-w-0 flex-1 flex-col bg-gray-50 pt-16 md:pt-0">
            <div class="hidden h-20 w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-8 md:flex sticky top-0 z-40">
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Complete your profile') }}</h1>
                <div class="flex items-center gap-4">
                    <livewire:student.notification-bell />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="mx-auto w-full max-w-5xl p-4 pb-24 md:p-8">
                    <livewire:student.profile-completion :key="'profile-'.$user->id" />
                </div>
            </div>
        </main>
    </div>
@endsection
