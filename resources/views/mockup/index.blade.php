<x-layouts.mockup>
    <x-slot name="title">Index</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">Branch B — Doctor &amp; Admin mockups</h1>
        <p class="mt-1 text-sm text-muted">
            Visual exploration screens for the doctor/admin features. All data is sample data.
            Use the <span class="font-semibold text-brand-700">Blush / Soft</span> switch in the header to compare the two
            palettes on every page.
        </p>
    </div>

    <div class="mb-4">
        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-muted">Doctor</p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('mockup.queue') }}" class="card p-5 transition hover:shadow-md">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-mockup.icon name="queue" class="h-5 w-5" />
                    <span class="text-xs font-semibold uppercase tracking-wider">FR-05 / FR-18</span>
                </div>
                <h2 class="mt-3 font-semibold">Queue dashboard</h2>
                <p class="mt-1 text-sm text-muted">Today's patient queue, statuses, and "now serving".</p>
            </a>

            <a href="{{ route('mockup.schedule') }}" class="card p-5 transition hover:shadow-md">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-mockup.icon name="schedule" class="h-5 w-5" />
                    <span class="text-xs font-semibold uppercase tracking-wider">Supporting</span>
                </div>
                <h2 class="mt-3 font-semibold">Schedule &amp; leave</h2>
                <p class="mt-1 text-sm text-muted">Weekly availability slots and leave requests.</p>
            </a>

            <a href="{{ route('mockup.prescription.create') }}" class="card p-5 transition hover:shadow-md">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-mockup.icon name="compose" class="h-5 w-5" />
                    <span class="text-xs font-semibold uppercase tracking-wider">FR-14</span>
                </div>
                <h2 class="mt-3 font-semibold">New prescription</h2>
                <p class="mt-1 text-sm text-muted">Compose prescriptions with dynamic medication rows.</p>
            </a>

            <a href="{{ route('mockup.prescriptions') }}" class="card p-5 transition hover:shadow-md">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-mockup.icon name="prescriptions" class="h-5 w-5" />
                    <span class="text-xs font-semibold uppercase tracking-wider">FR-15</span>
                </div>
                <h2 class="mt-3 font-semibold">Prescription history</h2>
                <p class="mt-1 text-sm text-muted">Issued prescriptions with a print-preview card.</p>
            </a>

            <a href="{{ route('mockup.medications') }}" class="card p-5 transition hover:shadow-md">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-mockup.icon name="medications" class="h-5 w-5" />
                    <span class="text-xs font-semibold uppercase tracking-wider">FR-16</span>
                </div>
                <h2 class="mt-3 font-semibold">Medication tracker</h2>
                <p class="mt-1 text-sm text-muted">Active medications with dosage and remaining duration.</p>
            </a>
        </div>
    </div>

    <div class="mb-4">
        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-muted">Admin</p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('mockup.reviews') }}" class="card p-5 transition hover:shadow-md">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-mockup.icon name="reviews" class="h-5 w-5" />
                    <span class="text-xs font-semibold uppercase tracking-wider">FR-19</span>
                </div>
                <h2 class="mt-3 font-semibold">Reviews moderation</h2>
                <p class="mt-1 text-sm text-muted">Pending patient reviews awaiting approval.</p>
            </a>

            <a href="{{ route('mockup.analytics') }}" class="card p-5 transition hover:shadow-md">
                <div class="flex items-center gap-2 text-brand-700">
                    <x-mockup.icon name="analytics" class="h-5 w-5" />
                    <span class="text-xs font-semibold uppercase tracking-wider">FR-20</span>
                </div>
                <h2 class="mt-3 font-semibold">Admin analytics</h2>
                <p class="mt-1 text-sm text-muted">Appointment stats, revenue, and doctor performance.</p>
            </a>
        </div>
    </div>
</x-layouts.mockup>
