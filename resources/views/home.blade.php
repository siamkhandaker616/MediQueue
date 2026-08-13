<x-guest-layout>
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">MediQueue</h1>
        <p class="mt-2 text-gray-600">
            Outpatient medical management — book an appointment, pay online, and receive a digital queue token with real-time visit tracking.
        </p>

        <div class="mt-6">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md bg-brand-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-brand-600">
                    Go to dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-brand-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-brand-600">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="ml-2 inline-flex items-center rounded-md border border-brand-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-brand-700 transition hover:bg-brand-50">
                    Register
                </a>
            @endauth
        </div>
    </div>
</x-guest-layout>
