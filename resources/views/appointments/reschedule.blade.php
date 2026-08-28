@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" x-data="rescheduleHandler()">

    <a href="{{ route('appointments.show', $appointment) }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">&larr; Back to Token</a>

    <div class="mt-4 mb-8">
        <h1 class="text-3xl font-bold text-ink">Reschedule Appointment</h1>
        <p class="text-muted mt-1">Select a new date and time slot for your consultation with <strong>Dr. {{ $appointment->doctor->display_name ?? $appointment->doctor->user->name }}</strong>.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-surface border border-brand-100 rounded-2xl shadow-sm p-6 md:p-8">
        <!-- Current Appointment Info -->
        <div class="bg-surface-alt border border-brand-100 rounded-xl p-4 mb-6 flex flex-wrap justify-between items-center text-sm">
            <div>
                <span class="text-muted block text-xs">Current Booking</span>
                <span class="font-semibold text-ink">{{ $appointment->date->format('M d, Y') }} at {{ $appointment->time_slot }}</span>
            </div>
            <div class="mt-2 sm:mt-0">
                <span class="text-xs text-muted block">Token Number</span>
                <span class="font-bold text-brand-600">{{ $appointment->token_number }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('appointments.updateSchedule', $appointment) }}">
            @csrf
            @method('PATCH')

            <input type="hidden" name="time_slot" :value="selectedSlot">

            <!-- Pick New Date -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-ink mb-2">Select New Date</label>
                <input 
                    type="date" 
                    name="date"
                    x-model="selectedDate" 
                    @change="fetchSlots()" 
                    min="{{ now()->toDateString() }}"
                    class="w-full md:w-64 rounded-xl border-brand-200 bg-surface text-ink shadow-sm focus:border-brand-600 focus:ring-brand-600"
                >
            </div>

            <!-- Loading Indicator -->
            <div x-show="loadingSlots" class="text-brand-600 text-sm py-4">Checking doctor's available slots...</div>

            <!-- Message if off-day / leave -->
            <div x-show="slotMessage" class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 text-sm mb-4" x-text="slotMessage"></div>

            <!-- Slot Grid -->
            <div x-show="!loadingSlots && slots.length > 0">
                <label class="block text-sm font-semibold text-ink mb-2">Available Slots for <span x-text="selectedDate" class="text-brand-600"></span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="slot in slots" :key="slot.time">
                        <button 
                            type="button" 
                            :disabled="!slot.available"
                            @click="selectedSlot = slot.time"
                            :class="!slot.available ? 'bg-surface-alt text-muted cursor-not-allowed opacity-50' : (selectedSlot === slot.time ? 'bg-brand-600 text-white font-bold ring-2 ring-brand-500' : 'bg-surface border border-brand-100 text-ink hover:border-brand-500')"
                            class="py-2.5 px-3 rounded-xl text-sm transition text-center"
                        >
                            <span x-text="slot.time"></span>
                            <span x-show="!slot.available" class="block text-xs text-rose-500 mt-0.5">Booked</span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('appointments.show', $appointment) }}" class="px-5 py-2.5 rounded-xl border border-brand-200 text-muted hover:text-ink transition text-sm font-medium">Cancel</a>
                <button 
                    type="submit" 
                    :disabled="!selectedDate || !selectedSlot"
                    class="bg-brand-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-brand-700 disabled:opacity-50 transition shadow-sm"
                >
                    Confirm Reschedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function rescheduleHandler() {
    return {
        doctorId: '{{ $appointment->doctor_id }}',
        selectedDate: '{{ now()->toDateString() }}',
        selectedSlot: '',
        slots: [],
        loadingSlots: false,
        slotMessage: '',

        init() {
            this.fetchSlots();
        },
        fetchSlots() {
            if (!this.selectedDate) return;
            this.loadingSlots = true;
            this.slotMessage = '';
            this.slots = [];
            this.selectedSlot = '';

            fetch(`/api/doctors/${this.doctorId}/available-slots?date=${this.selectedDate}`)
                .then(res => res.json())
                .then(data => {
                    this.loadingSlots = false;
                    if (data.available) {
                        this.slots = data.slots;
                    } else {
                        this.slotMessage = data.reason || 'No available slots on this date.';
                    }
                });
        }
    }
}
</script>
@endsection