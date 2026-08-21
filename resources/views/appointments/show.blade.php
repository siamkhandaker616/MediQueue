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
                <span class="font-semibold text-gray-900">{{ $appointment->date->format('M d, Y') }} ({{ $appointment->time_slot }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Consultation Fee:</span>
                <span class="font-bold text-teal-700">৳{{ number_format($appointment->fee, 0) }} (Paid)</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.print()" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-gray-800 transition inline-flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z"/>
                </svg>
                Print Token
            </button>
            <a href="{{ route('appointments.create') }}" class="bg-teal-50 text-teal-700 px-6 py-2.5 rounded-xl font-medium hover:bg-teal-100 transition">
                Book Another
            </a>
        </div>

    </div>

</div>
@endsection