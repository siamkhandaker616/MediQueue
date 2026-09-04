<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('app.name', 'MediQueue') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @php
            // Per-error accent + icon pairing
            $tones = [
                404 => ['from' => 'from-brand-400', 'to' => 'to-brand-600', 'ring' => 'ring-brand-200', 'chip' => 'bg-brand-100 text-brand-700'],
                403 => ['from' => 'from-amber-400', 'to' => 'to-amber-600', 'ring' => 'ring-amber-200', 'chip' => 'bg-amber-100 text-amber-700'],
                419 => ['from' => 'from-indigo-400', 'to' => 'to-indigo-600', 'ring' => 'ring-indigo-200', 'chip' => 'bg-indigo-100 text-indigo-700'],
                429 => ['from' => 'from-orange-400', 'to' => 'to-orange-600', 'ring' => 'ring-orange-200', 'chip' => 'bg-orange-100 text-orange-700'],
                500 => ['from' => 'from-rose-400',  'to' => 'to-rose-600',  'ring' => 'ring-rose-200',  'chip' => 'bg-rose-100 text-rose-700'],
                503 => ['from' => 'from-sky-400',   'to' => 'to-sky-600',   'ring' => 'ring-sky-200',   'chip' => 'bg-sky-100 text-sky-700'],
            ];
            $tone = $tones[$code] ?? $tones[500];
        @endphp

        <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-canvas px-6 py-16">
            {{-- Decorative brand blobs --}}
            <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-100/60 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-accent-100/60 blur-3xl" aria-hidden="true"></div>

            <div class="relative w-full max-w-lg">
                <div class="card overflow-hidden">
                    {{-- Top accent bar --}}
                    <div class="h-2 w-full bg-gradient-to-r {{ $tone['from'] }} {{ $tone['to'] }}"></div>

                    <div class="px-6 py-10 text-center sm:px-10">
                        {{-- Brand mark --}}
                        <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-sm">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </span>
                            <span class="text-lg font-bold tracking-tight text-brand-700">MediQueue</span>
                        </a>

                        {{-- Themed icon in a tinted circle --}}
                        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-2xl {{ $tone['chip'] }} shadow-inner">
                            @if ($code === 404)
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.35-4.35M8 11h6M11 8v6"/></svg>
                            @elseif ($code === 403)
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="11" rx="2"/><path stroke-linecap="round" d="M8 10V7a4 4 0 018 0v3M12 14v3"/></svg>
                            @elseif ($code === 419)
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path stroke-linecap="round" d="M12 7.5V12l3 2"/></svg>
                            @elseif ($code === 429)
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 20h14M6 20V9l6-3 6 3v11M9 14h.01M15 14h.01M10 9h4"/></svg>
                            @elseif ($code === 500)
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4 4 0 00-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 005.4-5.4L15 11l-2-2 1.7-2.7z"/></svg>
                            @else
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16M8 10l-2 2 2 2M16 10l2 2-2 2"/></svg>
                            @endif
                        </div>

                        {{-- Status chip + code --}}
                        <span class="badge {{ $tone['chip'] }} mb-3 uppercase tracking-wide">{{ $code }} Error</span>

                        <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">{{ $heading }}</h1>
                        <p class="mx-auto mt-3 max-w-sm text-sm text-muted sm:text-base">{{ $message }}</p>

                        {{-- Divider --}}
                        <div class="my-8 flex items-center gap-3">
                            <span class="h-px flex-1 bg-brand-100"></span>
                            <span class="text-xs font-medium text-muted">What now?</span>
                            <span class="h-px flex-1 bg-brand-100"></span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('home') }}" class="btn-primary">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/></svg>
                                    Back to home
                                </span>
                            </a>
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-outline">Go to dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-outline">Log in</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-muted">
                    MediQueue Medical Center &middot;
                    Need help? Email <a href="mailto:{{ config('mail.from.address') ?: 'support@mediqueue.test' }}" class="font-medium text-brand-600 underline decoration-brand-200 underline-offset-2 hover:text-brand-700">{{ config('mail.from.address') ?: 'support@mediqueue.test' }}</a>
                </p>
            </div>
        </div>
    </body>
</html>
