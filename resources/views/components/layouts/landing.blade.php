<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription ?? 'MediQueue — book appointments with top-rated doctors, manage your queue position, and access digital prescriptions and receipts at MediQueue Medical Center.' }}">

        <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'MediQueue') }}</title>

        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name', 'MediQueue') }}">
        <meta property="og:title" content="{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'MediQueue') }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'Book appointments, manage your queue, power digital prescriptions and receipts at MediQueue Medical Center.' }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ menuOpen: false }">
        <div class="min-h-screen bg-canvas">

            <!-- Header -->
            <header class="sticky top-0 z-40 border-b border-brand-100 bg-surface/85 backdrop-blur">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </span>
                        <span class="text-lg font-bold tracking-tight text-brand-700">MediQueue</span>
                    </a>

                    <nav class="hidden items-center gap-1 md:flex">
                        <a href="{{ route('departments.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-brand-50 hover:text-ink">Departments</a>
                        <a href="{{ route('doctors.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-brand-50 hover:text-ink">Doctors</a>
                        <a href="#how-it-works" class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-brand-50 hover:text-ink">How it works</a>
                        <a href="#contact" class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-brand-50 hover:text-ink">Contact</a>
                    </nav>

                    <div class="hidden items-center gap-2 md:flex">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary !px-4 !py-2 !text-sm">
                                Go to dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-outline !px-4 !py-2 !text-sm">Log in</a>
                            <a href="{{ route('register') }}" class="btn-primary !px-4 !py-2 !text-sm">Register</a>
                        @endauth
                    </div>

                    <button @click="menuOpen = !menuOpen" class="inline-flex items-center justify-center rounded-lg p-2 text-muted hover:bg-brand-50 hover:text-ink md:hidden">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="menuOpen ? 'hidden' : 'inline-flex'" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path :class="menuOpen ? 'inline-flex' : 'hidden'" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div :class="menuOpen ? 'block' : 'hidden'" class="border-t border-brand-100 bg-surface px-4 py-3 md:hidden">
                    <div class="space-y-1">
                        <a href="{{ route('departments.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-brand-50">Departments</a>
                        <a href="{{ route('doctors.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-brand-50">Doctors</a>
                        <a href="#how-it-works" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-brand-50">How it works</a>
                        <a href="#contact" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-brand-50">Contact</a>
                        <div class="flex gap-2 pt-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-primary flex-1 justify-center">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-outline flex-1 justify-center">Log in</a>
                                <a href="{{ route('register') }}" class="btn-primary flex-1 justify-center">Register</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer id="contact" class="bg-brand-700 text-white">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-3 lg:px-8">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-white">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </span>
                            <span class="text-lg font-bold tracking-tight">MediQueue</span>
                        </div>
                        <p class="mt-4 text-sm leading-relaxed text-brand-100">
                            Outpatient medical management — book an appointment, pay online, and receive a digital queue token with real-time visit tracking.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-brand-100">Explore</h3>
                        <ul class="mt-4 space-y-2 text-sm">
                            <li><a href="{{ route('departments.index') }}" class="text-white/90 transition hover:text-white">Departments</a></li>
                            <li><a href="{{ route('doctors.index') }}" class="text-white/90 transition hover:text-white">Find a doctor</a></li>
                            <li><a href="{{ route('home') }}#how-it-works" class="text-white/90 transition hover:text-white">How it works</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-brand-100">Account</h3>
                        <ul class="mt-4 space-y-2 text-sm">
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="text-white/90 transition hover:text-white">Dashboard</a></li>
                                <li><a href="{{ route('profile.edit') }}" class="text-white/90 transition hover:text-white">Profile</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="text-white/90 transition hover:text-white">Log in</a></li>
                                <li><a href="{{ route('register') }}" class="text-white/90 transition hover:text-white">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/10">
                    <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-xs text-brand-100 sm:flex-row sm:px-6 lg:px-8">
                        <p>&copy; {{ date('Y') }} MediQueue. All rights reserved.</p>
                        <p>Made with care for better outpatient visits.</p>
                    </div>
                </div>
            </footer>
        </div>

        @stack('scripts')
    </body>
</html>
