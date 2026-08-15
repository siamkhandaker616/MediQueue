@if ($doctors->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($doctors as $doctor)
            <a href="{{ route('doctors.show', $doctor) }}"
               class="block bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition p-6 text-center">
                <img src="{{ $doctor->photoUrl() }}" alt="{{ $doctor->user->name }}"
                     class="w-20 h-20 rounded-full mx-auto object-cover mb-3">
                <h3 class="font-semibold text-gray-900">{{ $doctor->user->name }}</h3>
                <p class="text-sm text-gray-500">{{ $doctor->specialty }}</p>
                <p class="text-xs text-gray-400">{{ optional($doctor->department)->name }}</p>
                <p class="text-sm text-amber-500 mt-1">★ {{ number_format($doctor->avg_rating, 1) }} ({{ $doctor->rating_count }})</p>
                <p class="text-sm font-medium text-teal-700 mt-1">৳{{ number_format($doctor->consultation_fee, 0) }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $doctors->links() }}
    </div>
@else
    <p class="text-gray-500 text-center py-16">No doctors matched your filters.</p>
@endif
