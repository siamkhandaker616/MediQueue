<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' - ' : '' }}MediQueue Mockup</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="themeSwitcher()">
        <div class="min-h-screen">
            <header class="sticky top-0 z-20 border-b border-brand-100 bg-surface/90 backdrop-blur">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('mockup.index') }}" class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-sm">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                </svg>
                            </span>
                            <span class="text-lg font-bold tracking-tight text-brand-700">MediQueue</span>
                        </a>
                        <span class="hidden rounded-full bg-accent-100 px-2.5 py-0.5 text-xs font-semibold text-accent-700 sm:inline-flex">Mockup</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="inline-flex rounded-full border border-brand-200 bg-brand-50 p-0.5">
                            <button @click="theme = 'blush'; apply()" :class="theme === 'blush' ? 'bg-brand-500 text-white shadow-sm' : 'text-brand-700 hover:bg-brand-100'" class="rounded-full px-3 py-1 text-xs font-semibold transition">Blush</button>
                            <button @click="theme = 'soft'; apply()" :class="theme === 'soft' ? 'bg-brand-500 text-white shadow-sm' : 'text-brand-700 hover:bg-brand-100'" class="rounded-full px-3 py-1 text-xs font-semibold transition">Soft</button>
                        </div>
                    </div>
                </div>
            </header>

            <div class="mx-auto flex max-w-7xl flex-col lg:flex-row lg:gap-8 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                <aside class="shrink-0 lg:w-56">
                    <nav class="space-y-6">
                        <div>
                            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-muted">Doctor</p>
                            <div class="space-y-1">
                                @foreach ([
                                    ['mockup.queue', 'Queue dashboard', 'queue'],
                                    ['mockup.schedule', 'Schedule & leave', 'schedule'],
                                    ['mockup.prescription.create', 'New prescription', 'compose'],
                                    ['mockup.prescriptions', 'Prescriptions', 'prescriptions'],
                                    ['mockup.medications', 'Medication tracker', 'medications'],
                                ] as [$route, $label, $key])
                                    <a href="{{ route($route) }}"
                                       class="{{ request()->routeIs($route) ? 'bg-brand-100 text-brand-700' : 'text-muted hover:bg-brand-50 hover:text-ink' }} flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition">
                                        <x-mockup.icon :name="$key" class="h-4 w-4" />
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-muted">Admin</p>
                            <div class="space-y-1">
                                @foreach ([
                                    ['mockup.reviews', 'Reviews', 'reviews'],
                                    ['mockup.analytics', 'Analytics', 'analytics'],
                                ] as [$route, $label, $key])
                                    <a href="{{ route($route) }}"
                                       class="{{ request()->routeIs($route) ? 'bg-brand-100 text-brand-700' : 'text-muted hover:bg-brand-50 hover:text-ink' }} flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition">
                                        <x-mockup.icon :name="$key" class="h-4 w-4" />
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </nav>
                </aside>

                <main class="mt-6 flex-1 lg:mt-0">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
