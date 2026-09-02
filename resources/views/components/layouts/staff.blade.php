<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MediQueue') }}{{ isset($title) ? ' - '.$title : '' }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="themeSwitcher()">
        <div class="min-h-screen bg-canvas">
            <header class="sticky top-0 z-20 border-b border-brand-100 bg-surface/90 backdrop-blur">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-sm">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                </svg>
                            </span>
                            <span class="text-lg font-bold tracking-tight text-brand-700">MediQueue</span>
                        </a>
                        <span class="hidden rounded-full bg-accent-100 px-2.5 py-0.5 text-xs font-semibold text-accent-700 sm:inline-flex">
                            {{ auth()->user()->isDoctor() ? 'Doctor' : 'Admin' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden items-center gap-2 text-sm sm:flex">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="font-medium text-ink">{{ auth()->user()->name }}</span>
                        </div>
                        <button type="button" @click="toggle()" aria-label="Toggle theme" class="inline-flex items-center justify-center rounded-lg border border-brand-200 bg-surface p-2 text-brand-700 transition hover:bg-brand-50">
                            <svg x-show="theme === 'dark'" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.39 5.39 0 0 1-4.4 2.26 5.4 5.4 0 0 1-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/>
                            </svg>
                            <svg x-show="theme === 'light'" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0-15a1 1 0 0 1 1 1v1a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1zm0 18a1 1 0 0 1 1 1v1a1 1 0 1 1-2 0v-1a1 1 0 0 1 1-1zM3 11a1 1 0 0 1 1-1h1a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1zm16 0a1 1 0 0 1 1-1h1a1 1 0 1 1 0 2h-1a1 1 0 0 1-1-1zM5.64 5.64a1 1 0 0 1 1.41 0l.71.7a1 1 0 1 1-1.41 1.42l-.71-.7a1 1 0 0 1 0-1.42zM18.36 18.36a1 1 0 0 1 1.41 0l.71.71a1 1 0 1 1-1.42 1.41l-.7-.71a1 1 0 0 1 0-1.41zM5.64 18.36a1 1 0 0 1 0 1.42l-.71.7a1 1 0 1 1-1.42-1.41l.71-.71a1 1 0 0 1 1.42 0zM18.36 5.64a1 1 0 0 1 0 1.42l-.71.7a1 1 0 1 1-1.41-1.42l.71-.7a1 1 0 0 1 1.41 0z"/>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn-outline !px-3 !py-1.5 !text-xs">Sign out</button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="mx-auto flex max-w-7xl flex-col lg:flex-row lg:gap-8 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                <aside class="shrink-0 lg:w-56">
                    <nav class="space-y-6">
                        <div class="space-y-1">
                            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-muted">
                                {{ auth()->user()->isDoctor() ? 'Doctor' : 'Admin' }}
                            </p>
                            @foreach ($navigation as $item)
                                <a href="{{ route($item['route']) }}"
                                   class="{{ request()->routeIs($item['route']) ? 'bg-brand-100 text-brand-700' : 'text-muted hover:bg-brand-50 hover:text-ink' }} flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="{{ $item['icon'] }}"/>
                                    </svg>
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </nav>
                </aside>

                <main class="mt-6 flex-1 lg:mt-0">
                    @if (session('status'))
                        <div class="mb-6 flex items-center gap-2 rounded-xl border border-accent-200 bg-accent-100/70 px-4 py-3 text-sm font-medium text-accent-700">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
