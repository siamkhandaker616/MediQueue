<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-8">

    <a href="{{ route('departments.index') }}" class="text-sm text-teal-600 hover:underline">&larr; All departments</a>

    <div class="mt-4 mb-8 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h1>
        <p class="text-gray-600 mt-2">{{ $department->description }}</p>

        <div class="flex flex-wrap gap-6 mt-4 text-sm text-gray-500">
            {{-- Uses locationLabel() to support both room_location and floor_number/room_number --}}
            <span><i class="fa-solid fa-location-dot mr-1"></i>{{ $department->locationLabel() }}</span>
            <span><i class="fa-solid fa-money-bill mr-1"></i>Fee range: {{ $department->feeRangeLabel() }}</span>
        </div>
    </div>

    <h2 class="text-xl font-semibold text-gray-900 mb-4">Doctors in {{ $department->name }}</h2>

    @if ($doctors->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($doctors as $doctor)
                <a href="{{ route('doctors.show', $doctor) }}"
                   class="block bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition p-6 text-center">
                    <img src="{{ $doctor->photoUrl() }}" alt="{{ $doctor->user->name }}"
                         class="w-20 h-20 rounded-full mx-auto object-cover mb-3">
                    <h3 class="font-semibold text-gray-900">{{ $doctor->user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $doctor->specialty }}</p>
                    <p class="text-sm text-amber-500 mt-1">★ {{ number_format($doctor->avg_rating, 1) }} ({{ $doctor->rating_count }})</p>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $doctors->links() }}</div>
    @else
        <p class="text-gray-500">No doctors are currently listed under this department.</p>
    @endif

</div>
</x-app-layout>