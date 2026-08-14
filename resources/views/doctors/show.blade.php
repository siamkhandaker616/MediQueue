@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <a href="{{ route('doctors.index') }}" class="text-sm text-teal-600 hover:underline">&larr; Doctor directory</a>

    <div class="mt-4 bg-white rounded-xl border border-gray-100 shadow-sm p-8 flex flex-col md:flex-row gap-8">
        <img src="{{ $doctor->photoUrl() }}" alt="{{ $doctor->user->name }}"
             class="w-32 h-32 rounded-full object-cover mx-auto md:mx-0">

        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Dr. {{ $doctor->user->name }}</h1>
            <p class="text-teal-700 font-medium">{{ $doctor->specialty }}</p>
            <p class="text-sm text-gray-500">{{ optional($doctor->department)->name }}</p>

            <div class="flex flex-wrap gap-6 mt-4 text-sm text-gray-600">
                <span>★ {{ number_format($doctor->avg_rating, 1) }} ({{ $doctor->rating_count }} reviews)</span>
                <span>{{ $doctor->experience_years }} yrs experience</span>
                <span>৳{{ number_format($doctor->consultation_fee, 0) }} consultation fee</span>
            </div>

            @if ($doctor->languages)
                <p class="text-sm text-gray-500 mt-2">
                    Speaks: {{ implode(', ', $doctor->languages) }}
                </p>
            @endif

            <a href="{{ route('appointments.create', ['doctor' => $doctor->slug]) }}"
               class="inline-block mt-6 bg-teal-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-teal-700 transition">
                Book Appointment
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-semibold text-gray-900 mb-2">Qualifications</h2>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $doctor->qualifications }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-semibold text-gray-900 mb-2">About</h2>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $doctor->bio ?: 'No biography provided yet.' }}</p>
        </div>
    </div>

</div>
@endsection
