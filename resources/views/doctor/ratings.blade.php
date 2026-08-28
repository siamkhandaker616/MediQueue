<x-layouts.staff>
    <x-slot name="title">My ratings &amp; reviews</x-slot>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-19 · patient feedback &amp; doctor rating</p>
            <h1 class="text-2xl font-bold tracking-tight">My ratings</h1>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Overall</p>
            <p class="mt-1 text-3xl font-bold text-brand-700">{{ number_format($summary['overall'], 1) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Punctuality</p>
            <p class="mt-1 text-3xl font-bold text-brand-700">{{ number_format($summary['punctuality'], 1) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Communication</p>
            <p class="mt-1 text-3xl font-bold text-brand-700">{{ number_format($summary['communication'], 1) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Knowledge</p>
            <p class="mt-1 text-3xl font-bold text-brand-700">{{ number_format($summary['knowledge'], 1) }}</p>
        </div>
    </div>

    <div class="mt-6 card overflow-hidden">
        <div class="border-b border-brand-100 px-5 py-4">
            <h2 class="font-semibold">Reviews ({{ $summary['count'] }})</h2>
        </div>

        @if ($reviews->isEmpty())
            <p class="px-5 py-12 text-center text-sm text-muted">No published reviews yet.</p>
        @else
            <ul class="divide-y divide-brand-50">
                @foreach ($reviews as $review)
                    <li class="px-5 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold">
                                {{ $review->patient?->name ?? 'Patient' }}
                                <span class="ml-1.5 text-amber-500">★ {{ number_format($review->overall_rating, 1) }}</span>
                            </p>
                            <p class="text-xs text-muted">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                        @if ($review->comment)
                            <p class="mt-1 text-sm text-muted">{{ $review->comment }}</p>
                        @endif
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted">
                            <span>Punctuality {{ $review->punctuality_rating }}/5</span>
                            <span>Communication {{ $review->communication_rating }}/5</span>
                            <span>Knowledge {{ $review->knowledge_rating }}/5</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.staff>
