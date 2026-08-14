<x-layouts.staff>
    <x-slot name="title">Review moderation</x-slot>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted">FR-19 · patient feedback moderation</p>
            <h1 class="text-2xl font-bold tracking-tight">Reviews</h1>
        </div>
        <span class="badge {{ $pendingCount ? 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200' : 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' }}">
            {{ $pendingCount }} pending
        </span>
    </div>

    @if ($reviews->isEmpty())
        <div class="card px-5 py-16 text-center">
            <p class="text-sm text-muted">No reviews submitted yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($reviews as $review)
                <div class="card p-5 {{ $review->is_visible ? '' : 'border-amber-200 bg-amber-50/40' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">{{ $review->patient->name }}</span>
                                <span class="text-sm text-muted">→ {{ $review->doctor->name }}</span>
                            </div>
                            <p class="text-xs text-muted">{{ $review->created_at->format('d M Y, g:i a') }}</p>
                        </div>
                        <span class="badge {{ $review->is_visible ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200' }}">
                            {{ $review->is_visible ? 'Published' : 'Pending' }}
                        </span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-4 text-sm text-muted">
                        <span>Punctuality <strong class="text-amber-500">{{ $review->punctuality_rating }}/5</strong></span>
                        <span>Communication <strong class="text-amber-500">{{ $review->communication_rating }}/5</strong></span>
                        <span>Knowledge <strong class="text-amber-500">{{ $review->knowledge_rating }}/5</strong></span>
                        <span>Overall <strong class="text-amber-500">{{ $review->overall_rating }}/5</strong></span>
                    </div>

                    @if ($review->comment)
                        <p class="mt-3 rounded-lg bg-surface p-3 text-sm text-ink">"{{ $review->comment }}"</p>
                    @endif

                    <div class="mt-4 flex justify-end gap-2">
                        <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}">
                            @csrf
                            @method('PATCH')
                            <button class="{{ $review->is_visible ? 'btn-outline' : 'btn-primary' }} !px-3 !py-1.5 !text-xs">
                                {{ $review->is_visible ? 'Hide review' : 'Approve & publish' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                              onsubmit="return confirm('Remove this review permanently?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn-outline !px-3 !py-1.5 !text-xs !text-red-600 hover:!bg-red-50">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.staff>
