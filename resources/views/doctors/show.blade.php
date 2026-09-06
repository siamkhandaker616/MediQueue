<x-app-layout>
<div class="max-w-4xl mx-auto px-4 py-8">

    <a href="{{ route('doctors.index') }}" class="text-sm text-teal-600 hover:underline">&larr; Doctor directory</a>

    <div class="mt-4 bg-surface rounded-xl border border-brand-100 shadow-sm p-8 flex flex-col md:flex-row gap-8">
        <img src="{{ $doctor->photoUrl() }}" alt="{{ $doctor->user->name }}"
             class="w-32 h-32 rounded-full object-cover mx-auto md:mx-0">

        <div class="flex-1">
            <h1 class="text-2xl font-bold text-ink">{{ $doctor->user->name }}</h1>
            <p class="text-teal-700 font-medium">{{ $doctor->specialty }}</p>
            <p class="text-sm text-muted">{{ optional($doctor->department)->name }}</p>

            <div class="flex flex-wrap gap-6 mt-4 text-sm text-muted">
                <span>★ {{ number_format($doctor->avg_rating, 1) }} ({{ $doctor->rating_count }} reviews)</span>
                <span>{{ $doctor->experience_years }} yrs experience</span>
                <span>৳{{ number_format($doctor->consultation_fee, 0) }} consultation fee</span>
            </div>

            @if ($doctor->languages)
                <p class="text-sm text-muted mt-2">
                    Speaks: {{ implode(', ', $doctor->languages) }}
                </p>
            @endif

            @if (Route::has('appointments.create'))
                <a href="{{ route('appointments.create', ['doctor' => $doctor->slug]) }}"
                   class="inline-block mt-6 bg-teal-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-teal-700 transition">
                    Book Appointment
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-surface rounded-xl border border-brand-100 shadow-sm p-6">
            <h2 class="font-semibold text-ink mb-2">Qualifications</h2>
            <p class="text-sm text-muted whitespace-pre-line">{{ $doctor->qualifications }}</p>
        </div>

        <div class="bg-surface rounded-xl border border-brand-100 shadow-sm p-6">
            <h2 class="font-semibold text-ink mb-2">About</h2>
            <p class="text-sm text-muted whitespace-pre-line">{{ $doctor->bio ?: 'No biography provided yet.' }}</p>
        </div>
    </div>

    <div class="mt-6 bg-surface rounded-xl border border-brand-100 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-ink">Patient reviews</h2>
            <span class="text-sm font-bold text-amber-500">★ {{ number_format($doctor->avg_rating, 1) }}</span>
        </div>

        @php $publishedReviews = $doctor->reviews ?? collect(); @endphp

        @if ($publishedReviews->isEmpty())
            <p class="mt-3 text-sm text-muted">No published reviews yet.</p>
        @else
            <div class="mt-4 divide-y divide-brand-100">
                @foreach ($publishedReviews as $review)
                    <div class="py-4 first:pt-0 last:pb-0">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="text-amber-500">★ {{ $review->overall_rating }}/5</span>
                                <span class="text-sm font-medium text-ink">{{ $review->reviewerName() }}</span>
                            </div>
                            <span class="text-xs text-muted">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                        @if ($review->comment)
                            <p class="mt-2 text-sm text-muted">"{{ $review->comment }}"</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
</x-app-layout>
