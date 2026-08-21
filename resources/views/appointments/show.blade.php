@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    @if (session('success'))
        <div class="mb-6 bg-teal-50 border border-teal-200 text-teal-800 p-4 rounded-xl text-center font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Digital Token Card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden text-center p-8 relative">
        
        <!-- Hospital Header -->
        <div class="border-b border-dashed border-gray-200 pb-6 mb-6">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">MediQueue</h2>
            <p class="text-xs uppercase tracking-widest text-teal-600 font-semibold mt-1">Outpatient Queue Token</p>
        </div>

        <!-- Big Token Number -->
        <div class="my-6">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Your Token Number</span>
            <div class="text-4xl md:text-5xl font-black text-teal-700 tracking-wider mt-1">
                {{ $appointment->token_number }}
            </div>
        </div>

        <!-- Live Queue Metrics -->
        <div class="grid grid-cols-2 gap-4 bg-teal-50/60 rounded-2xl p-4 my-6">
            <div>
                <span class="text-xs text-gray-500 block">Queue Position</span>
                <span class="text-2xl font-bold text-gray-900">#{{ $appointment->queue_position }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 block">Est. Wait Time</span>
                <span class="text-2xl font-bold text-teal-700">~{{ $appointment->estimated_wait_minutes }} mins</span>
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="space-y-3 text-left text-sm text-gray-600 bg-gray-50 rounded-2xl p-6">
            <div class="flex justify-between">
                <span class="text-gray-400">Doctor:</span>
                <span class="font-semibold text-gray-900">Dr. {{ $appointment->doctor->display_name ?? $appointment->doctor->user->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Department:</span>
                <span class="font-semibold text-gray-900">{{ $appointment->department->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Location:</span>
                <span class="font-semibold text-gray-900">{{ $appointment->department->locationLabel() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Date &amp; Slot:</span>
                <span class="font-semibold text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }} ({{ $appointment->time_slot }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Consultation Fee:</span>
                <span class="font-bold text-teal-700">৳{{ number_format($appointment->fee, 0) }} (Paid)</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.print()" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-gray-800 transition">
                <i class="fa-solid fa-print mr-2"></i>Print Token
            </button>
            <a href="{{ route('appointments.create') }}" class="bg-teal-50 text-teal-700 px-6 py-2.5 rounded-xl font-medium hover:bg-teal-100 transition">
                Book Another
            </a>
        </div>

    </div>

</div>
@endsection