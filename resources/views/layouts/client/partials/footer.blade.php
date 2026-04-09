<footer class="border-t border-gray-200 bg-white pt-16 pb-8 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 mb-16">
        <div class="col-span-2 md:col-span-1">
            <a href="{{ route('client.home') }}" class="flex items-center gap-2 mb-6">
                <div class="w-6 h-6 bg-black rounded-md flex items-center justify-center">
                    <span class="text-white font-bold text-sm leading-none">S</span>
                </div>
                <span class="text-lg font-bold tracking-tight">{{ config('app.name') }}.</span>
            </a>
            <p class="text-gray-500 text-sm mb-6">
                Elevating the student living experience in London.
            </p>
        </div>

        <div>
            <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider">Explore</h4>
            <ul class="space-y-3 text-gray-500 text-sm">
                <li><a href="{{ route('client.explore') }}" class="hover:text-black transition-colors">Search London</a></li>
                <li><a href="{{ route('client.listing.show', ['slug' => 'example-studio']) }}" class="hover:text-black transition-colors">Example listing</a></li>
                <li><a href="{{ url('/register?role=student') }}" class="hover:text-black transition-colors">Student sign up</a></li>
                <li><a href="{{ route('client.home') }}" class="hover:text-black transition-colors">Home</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider">Client app</h4>
            <ul class="space-y-3 text-gray-500 text-sm">
                <li><a href="{{ route('client.student.dashboard') }}" class="hover:text-black transition-colors">Student</a></li>
                <li><a href="{{ route('client.landlord.dashboard') }}" class="hover:text-black transition-colors">Landlord</a></li>
                <li><a href="{{ route('client.institute.dashboard') }}" class="hover:text-black transition-colors">Institution</a></li>
                <li><a href="{{ route('client.agent.dashboard') }}" class="hover:text-black transition-colors">Agent</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider">Account</h4>
            <ul class="space-y-3 text-gray-500 text-sm">
                <li><a href="{{ route('login') }}" class="hover:text-black transition-colors">Log in</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-black transition-colors">Create account</a></li>
                <li><a href="{{ route('client.site_map') }}" class="hover:text-black transition-colors">Site map</a></li>
            </ul>
        </div>
    </div>

    <div class="max-w-7xl mx-auto pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <div class="flex flex-wrap gap-6 justify-center">
            <a href="{{ route('client.site_map') }}" class="font-medium text-gray-900 hover:underline">All pages</a>
            <a href="{{ route('client.privacy') }}" class="hover:text-black transition-colors">Privacy</a>
            <a href="{{ route('client.terms') }}" class="hover:text-black transition-colors">Terms</a>
        </div>
    </div>
</footer>
