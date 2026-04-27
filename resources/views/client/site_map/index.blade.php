@extends('layouts.client.app')

@section('title', 'All pages | '.config('app.name'))

@section('body_class', 'bg-gray-50 font-sans text-gray-900 antialiased min-h-screen')

@section('content')
    <header class="border-b border-gray-200 bg-white sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('client.home') }}" class="flex items-center">
                <x-stunest-logo class="h-8 w-auto max-w-[200px] object-left object-contain" />
            </a>
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Design index</span>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-12 pb-20">
        <div class="mb-10">
            <h1 class="text-3xl font-semibold tracking-tight">All pages</h1>
            <p class="text-gray-500 mt-2 max-w-2xl">
                Quick links to the public client area while we build the MVP.
            </p>
        </div>

        <div class="space-y-10">
            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Marketing &amp; browse</h2>
                <ul class="grid gap-3 sm:grid-cols-2">
                    <li>
                        <a href="{{ route('client.home') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="home" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Home</span>
                                <p class="text-sm text-gray-500 mt-1">Landing, hero search, featured listings.</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.explore') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Explore</span>
                                <p class="text-sm text-gray-500 mt-1">Split list + map, filters, property cards.</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.listing.show', ['slug' => 'example-studio']) }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="image" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Property detail</span>
                                <p class="text-sm text-gray-500 mt-1">Gallery, description, booking card.</p>
                            </div>
                        </a>
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Authentication</h2>
                <ul class="grid gap-3 sm:grid-cols-2">
                    <li>
                        <a href="{{ route('login') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="log-in" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Log in</span>
                                <p class="text-sm text-gray-500 mt-1">Shared login for students, landlords, and staff.</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="user-plus" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Register</span>
                                <p class="text-sm text-gray-500 mt-1">Currently redirects to login until sign-up is enabled.</p>
                            </div>
                        </a>
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Client module (auth)</h2>
                <p class="text-sm text-gray-500 mb-4">Marketplace users: students, landlords, institutions, and agents — all under <code class="text-gray-800">/client/…</code></p>
                <ul class="grid gap-3 sm:grid-cols-2">
                    <li>
                        <a href="{{ route('client.student.dashboard') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Student</span>
                                <p class="text-sm text-gray-500 mt-1"><code>/client/student/dashboard</code></p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.landlord.dashboard') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="building-2" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Landlord</span>
                                <p class="text-sm text-gray-500 mt-1"><code>/client/landlord/dashboard</code></p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.institute.dashboard') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="school" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Institution</span>
                                <p class="text-sm text-gray-500 mt-1">v2 · <code>/client/institute/dashboard</code></p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.agent.dashboard') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="briefcase" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Agent</span>
                                <p class="text-sm text-gray-500 mt-1">v2 · <code>/client/agent/dashboard</code></p>
                            </div>
                        </a>
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Staff (auth)</h2>
                <ul class="grid gap-3 sm:grid-cols-2">
                    <li>
                        <a href="{{ route('administration.dashboard.index') }}" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-gray-900 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="shield" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 group-hover:underline">Administration</span>
                                <p class="text-sm text-gray-500 mt-1">Staff only — <code>/administration/…</code></p>
                            </div>
                        </a>
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Legal</h2>
                <ul class="flex flex-wrap gap-3">
                    <a href="{{ route('client.terms') }}" class="text-sm font-medium text-gray-900 underline">Terms of Service</a>
                    <a href="{{ route('client.privacy') }}" class="text-sm font-medium text-gray-900 underline">Privacy Policy</a>
                </ul>
            </section>
        </div>
    </main>

    <footer class="border-t border-gray-200 bg-white py-8 text-center text-sm text-gray-500">
        <a href="{{ route('client.home') }}" class="hover:text-gray-900">← Back to home</a>
    </footer>
@endsection
