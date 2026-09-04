<x-layouts.landing>
    <x-slot name="title">Book appointments, skip the wait, and track your visit live</x-slot>

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-brand-50 via-canvas to-canvas">
        <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-200/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 top-32 h-72 w-72 rounded-full bg-accent-200/40 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div>
                <span class="badge bg-accent-100 text-accent-700">
                    <svg class="mr-1.5 h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    Digital queue tokens · Online payments · Live tracking
                </span>

                <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-ink sm:text-5xl">
                    Skip the waiting room.
                    <span class="text-brand-500">Own your visit.</span>
                </h1>

                <p class="mt-5 max-w-xl text-lg leading-relaxed text-muted">
                    MediQueue brings outpatient care online — browse departments, find the right specialist,
                    book an appointment, and follow your queue in real time from your phone.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('doctors.index') }}" class="btn-primary !px-6 !py-3 !text-base">
                        Find a doctor
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('departments.index') }}" class="btn-outline !px-6 !py-3 !text-base">Browse departments</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-600">
                            Go to dashboard →
                        </a>
                    @endauth
                </div>

                <p class="mt-8 text-sm text-muted">
                    Trusted by <span class="font-semibold text-ink">{{ number_format($stats['doctors'] ?? 0) }}+</span> specialists and
                    <span class="font-semibold text-ink">{{ number_format($stats['patients'] ?? 0) }}+</span> patients.
                </p>
            </div>

            <div class="relative hidden lg:block">
                <div class="absolute -left-6 top-10 h-40 w-40 -rotate-12 rounded-2xl border border-brand-200 bg-brand-100/60 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Live queue</p>
                    <p class="mt-2 text-2xl font-extrabold text-brand-600">Room 301</p>
                    <p class="mt-1 text-xs text-muted">Cardiology · now serving</p>
                </div>

                <div class="card relative z-10 ml-12 p-6 shadow-xl sm:p-8">
                    <div class="flex items-center justify-between">
                        <span class="badge bg-accent-100 text-accent-700">Next in queue</span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-muted">Token</span>
                    </div>

                    <p class="mt-4 text-4xl font-extrabold tracking-tight text-brand-600">A-042</p>
                    <p class="mt-1 text-sm text-muted">Dr. Nusrat Jahan · Interventional Cardiologist</p>

                    <div class="mt-5 h-2 overflow-hidden rounded-full bg-brand-100">
                        <div class="h-2 rounded-full bg-gradient-to-r from-brand-400 to-brand-600" style="width: 65%"></div>
                    </div>
                    <div class="mt-2 flex justify-between text-xs text-muted">
                        <span>Checked in 9:42 AM</span>
                        <span class="font-medium text-brand-700">Est. wait 12 min</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- STATS --}}
    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="stat-card flex items-center gap-4">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-100 text-brand-700">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h4a2 2 0 012 2v12H6a2 2 0 01-2-2V6zm12 0h4a2 2 0 012 2v12h-4a2 2 0 01-2-2V8a2 2 0 011-2zM2 4a2 2 0 012-2h16a2 2 0 012 2v2H2V4z"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-extrabold tracking-tight text-ink">{{ number_format($stats['departments'] ?? 0) }}</p>
                    <p class="text-xs font-medium uppercase tracking-wider text-muted">Departments</p>
                </div>
            </div>

            <div class="stat-card flex items-center gap-4">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-100 text-accent-700">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a7 7 0 00-4.6 12.3c.3.3.5.7.6 1.1l.5 2.6c.1.6.6 1.1 1.3 1.1h4.4c.7 0 1.2-.5 1.3-1.1l.5-2.6c.1-.4.3-.8.6-1.1A7 7 0 0012 2zm-1.5 6.5h3a1 1 0 010 2h-1v1a1 1 0 01-2 0v-1h-1a1 1 0 010-2h1v-1z"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-extrabold tracking-tight text-ink">{{ number_format($stats['doctors'] ?? 0) }}</p>
                    <p class="text-xs font-medium uppercase tracking-wider text-muted">Specialists</p>
                </div>
            </div>

            <div class="stat-card flex items-center gap-4">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-100 text-brand-700">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-extrabold tracking-tight text-ink">{{ number_format($stats['patients'] ?? 0) }}</p>
                    <p class="text-xs font-medium uppercase tracking-wider text-muted">Patients</p>
                </div>
            </div>

            <div class="stat-card flex items-center gap-4">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-100 text-accent-700">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22l-1.4-1.4C5.4 15.9 2 12.8 2 9 2 6 4.4 3.7 7.3 3.7c1.7 0 3.3.8 4.7 2.2 1.4-1.4 3-2.2 4.7-2.2 2.9 0 5.3 2.3 5.3 5.3 0 3.8-3.4 6.9-8.6 11.6L12 22z"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-extrabold tracking-tight text-ink">{{ number_format($stats['appointments'] ?? 0) }}</p>
                    <p class="text-xs font-medium uppercase tracking-wider text-muted">Appointments</p>
                </div>
            </div>
        </div>
    </section>

    {{-- DEPARTMENTS PREVIEW --}}
    @if (isset($departments) && $departments->count())
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Departments</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-ink">Browse by specialty</h2>
                    <p class="mt-2 max-w-lg text-muted">Every care area under one roof — find the team that's right for you.</p>
                </div>
                <a href="{{ route('departments.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-700 transition hover:text-brand-600">
                    View all departments
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($departments as $index => $department)
                    <a href="{{ route('departments.show', $department) }}"
                       class="group card flex items-start gap-4 p-5 transition hover:shadow-md">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $index % 2 ? 'bg-accent-100 text-accent-700' : 'bg-brand-100 text-brand-700' }} text-lg font-extrabold">
                            {{ strtoupper(substr($department->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-semibold text-ink group-hover:text-brand-700">{{ $department->name }}</h3>
                                <svg class="h-4 w-4 shrink-0 text-muted transition group-hover:translate-x-0.5 group-hover:text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                            <p class="mt-1 text-sm text-muted [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] [overflow:hidden]">{{ $department->description }}</p>
                            <p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs font-medium">
                                <span class="text-ink">{{ $department->active_doctors_count }} doctor{{ $department->active_doctors_count == 1 ? '' : 's' }}</span>
                                <span class="text-brand-700">{{ $department->feeRangeLabel() }}</span>
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- DOCTORS PREVIEW --}}
    @if (isset($doctors) && $doctors->count())
        <section class="border-y border-brand-100 bg-surface">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Specialists</p>
                        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-ink">Meet our top doctors</h2>
                        <p class="mt-2 max-w-lg text-muted">Rated by real patients. Reviewed, verified, and ready to see you.</p>
                    </div>
                    <a href="{{ route('doctors.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-700 transition hover:text-brand-600">
                        Find a doctor
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($doctors as $index => $doctor)
                        <a href="{{ route('doctors.show', $doctor) }}"
                           class="group card p-6 text-center transition hover:shadow-md">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full {{ $index % 2 ? 'bg-accent-100 text-accent-700' : 'bg-brand-100 text-brand-700' }} text-xl font-extrabold">
                                @php
                                    $initials = collect(explode(' ', str_replace('Dr. ', '', $doctor->name)))
                                        ->take(2)->map(fn ($w) => strtoupper(substr($w, 0, 1)))->join('');
                                @endphp
                                {{ $initials ?: 'D' }}
                            </div>
                            <h3 class="mt-4 font-semibold text-ink group-hover:text-brand-700">{{ $doctor->name }}</h3>
                            <p class="text-sm text-muted">{{ $doctor->specialty }}</p>
                            <p class="text-xs text-muted">{{ optional($doctor->department)->name }}</p>

                            <div class="mt-3 flex items-center justify-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= round((float) $doctor->avg_rating) ? 'text-amber-400' : 'text-brand-100' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @endfor
                            </div>
                            <p class="mt-1 text-xs font-medium text-muted">
                                {{ number_format((float) $doctor->avg_rating, 1) }} · {{ $doctor->rating_count }} reviews
                            </p>

                            <p class="mt-3 text-lg font-bold text-brand-700">৳{{ number_format((float) $doctor->consultation_fee, 0) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- HOW IT WORKS --}}
    <section id="how-it-works" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">How it works</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-ink">From search to seat, in three steps</h2>
            <p class="mt-3 text-muted">No more crowded waiting rooms. Your visit is planned before you arrive.</p>
        </div>

        <div class="mt-12 grid gap-8 md:grid-cols-3">
            <div class="text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500 text-lg font-extrabold text-white shadow-md">1</span>
                <h3 class="mt-5 text-lg font-semibold text-ink">Find your doctor</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">Browse departments, filter by specialty and rating, and compare consultation fees.</p>
            </div>
            <div class="text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-500 text-lg font-extrabold text-white shadow-md">2</span>
                <h3 class="mt-5 text-lg font-semibold text-ink">Book &amp; pay online</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">Pick a slot that suits you, confirm your appointment, and pay securely in advance.</p>
            </div>
            <div class="text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500 text-lg font-extrabold text-white shadow-md">3</span>
                <h3 class="mt-5 text-lg font-semibold text-ink">Track your queue live</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">Receive a digital token and follow your position in real time — arrive right on time.</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 via-brand-500 to-accent-500 px-6 py-16 text-center shadow-lg sm:px-16">
            <div class="pointer-events-none absolute -left-10 -top-10 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-10 -right-10 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>

            <h2 class="relative text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Ready to skip the queue?
            </h2>
            <p class="relative mx-auto mt-3 max-w-xl text-white/90">
                Join MediQueue today and make every outpatient visit predictable, fast, and hassle-free.
            </p>

            <div class="relative mt-8 flex flex-wrap justify-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50">
                        Go to dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50">
                        Create a free account
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Log in
                    </a>
                @endauth
            </div>
        </div>
    </section>
</x-layouts.landing>
