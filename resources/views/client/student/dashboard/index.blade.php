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
                <h1 class="text-xl font-semibold tracking-tight" id="page-title">{{ __('Applications') }}</h1>
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

                <div id="applications-tab" class="tab-content active">
                    <livewire:student.student-applications-list />
                </div>

                <div id="messages-tab" class="tab-content">
                    <div class="flex flex-col items-center justify-center h-64 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="message-square" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('No messages yet') }}</h3>
                        <p class="text-gray-500 mt-1 max-w-sm">{{ __('When you contact a landlord or they send you an update, your messages will appear here.') }}</p>
                    </div>
                </div>
            </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const tabContents = document.querySelectorAll('.tab-content');
            const pageTitle = document.getElementById('page-title');

            const titles = {
                'applications-tab': @json(__('Applications')),
                'messages-tab': @json(__('Messages')),
            };

            const hashToTab = {
                '': 'applications-tab',
                '#messages-tab': 'messages-tab',
            };

            function refreshIcons() {
                if (window.lucide && typeof lucide.createIcons === 'function') {
                    lucide.createIcons();
                }
            }

            function activateTab(targetId) {
                if (!targetId || !titles[targetId]) {
                    targetId = 'applications-tab';
                }
                tabContents.forEach(function (content) {
                    content.classList.remove('active');
                });
                const targetEl = document.getElementById(targetId);
                if (targetEl) {
                    targetEl.classList.add('active');
                }
                if (pageTitle && titles[targetId]) {
                    pageTitle.textContent = titles[targetId];
                }
                refreshIcons();
            }

            function syncFromHash() {
                const h = window.location.hash;
                const id = hashToTab[h] || 'applications-tab';
                activateTab(id);
            }

            document.addEventListener('DOMContentLoaded', syncFromHash);
            window.addEventListener('hashchange', syncFromHash);
            refreshIcons();
        })();
    </script>
@endpush
