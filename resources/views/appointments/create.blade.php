@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8" x-data="bookingWizard()">

    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900">Book an Appointment</h1>
        <p class="text-gray-500 mt-1">Select a department, doctor, and convenient time slot.</p>
    </div>

    <!-- Step Progress Bar -->
    <div class="flex items-center justify-between max-w-2xl mx-auto mb-8 text-sm font-medium">
        <div :class="step >= 1 ? 'text-teal-600 font-bold' : 'text-gray-400'">1. Department</div>
        <div class="h-0.5 w-12 bg-gray-200" :class="step >= 2 ? 'bg-teal-600' : ''"></div>
        <div :class="step >= 2 ? 'text-teal-600 font-bold' : 'text-gray-400'">2. Doctor</div>
        <div class="h-0.5 w-12 bg-gray-200" :class="step >= 3 ? 'bg-teal-600' : ''"></div>
        <div :class="step >= 3 ? 'text-teal-600 font-bold' : 'text-gray-400'">3. Date &amp; Slot</div>
        <div class="h-0.5 w-12 bg-gray-200" :class="step >= 4 ? 'bg-teal-600' : ''"></div>
        <div :class="step >= 4 ? 'text-teal-600 font-bold' : 'text-gray-400'">4. Review &amp; Pay</div>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
        @csrf

        <input type="hidden" name="doctor_id" :value="selectedDoctorId">
        <input type="hidden" name="appointment_date" :value="selectedDate">
        <input type="hidden" name="time_slot" :value="selectedSlot">

        <!-- STEP 1: SELECT DEPARTMENT -->
        <div x-show="step === 1">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Select a Medical Department</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($departments as $dept)
                    <div 
                        @click="selectDepartment({{ $dept->id }}, '{{ $dept->name }}', {{ json_encode($dept->activeDoctors) }})"
                        :class="selectedDeptId == {{ $dept->id }} ? 'border-teal-600 ring-2 ring-teal-500 bg-teal-50' : 'border-gray-200 hover:border-teal-300'"
                        class="cursor-pointer border rounded-xl p-4 transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="{{ $dept->icon ?? 'fa-solid fa-stethoscope' }} text-teal-600 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $dept->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $dept->active_doctors_count ?? count($dept->activeDoctors) }} Doctor(s)</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- STEP 2: SELECT DOCTOR -->
        <div x-show="step === 2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Choose a Doctor (<span x-text="selectedDeptName"></span>)</h2>
                <button type="button" @click="step = 1" class="text-sm text-teal-600 hover:underline">&larr; Change Department</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="doc in availableDoctors" :key="doc.id">
                    <div 
                        @click="selectDoctor(doc)"
                        :class="selectedDoctorId == doc.id ? 'border-teal-600 ring-2 ring-teal-500 bg-teal-50' : 'border-gray-200 hover:border-teal-300'"
                        class="cursor-pointer border rounded-xl p-4 transition flex gap-4 items-center"
                    >
                        <img :src="doc.photo ? '/storage/' + doc.photo : '/images/doctor-placeholder.png'" class="w-14 h-14 rounded-full object-cover">
                        <div>
                            <h3 class="font-semibold text-gray-900" x-text="doc.name || (doc.user ? 'Dr. ' + doc.user.name : 'Doctor')"></h3>
                            <p class="text-xs text-gray-500" x-text="doc.specialty || (doc.specialties ? doc.specialties.join(', ') : 'Specialist')"></p>
                            <p class="text-sm font-semibold text-teal-700 mt-1">৳<span x-text="doc.consultation_fee"></span></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- STEP 3: SELECT DATE & TIME SLOT -->
        <div x-show="step === 3">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Choose Date &amp; Time Slot</h2>
                <button type="button" @click="step = 2" class="text-sm text-teal-600 hover:underline">&larr; Change Doctor</button>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Appointment Date</label>
                <input 
                    type="date" 
                    x-model="selectedDate" 
                    @change="fetchSlots()" 
                    min="{{ now()->toDateString() }}"
                    class="w-full md:w-64 rounded-lg border-gray-300 shadow-sm focus:ring-teal-500 focus:border-teal-500"
                >
            </div>

            <!-- Slots Loading State -->
            <div x-show="loadingSlots" class="text-teal-600 text-sm py-4">Checking available time slots...</div>

            <!-- Slots Message -->
            <div x-show="slotMessage" class="text-amber-600 bg-amber-50 p-3 rounded-lg text-sm mb-4" x-text="slotMessage"></div>

            <!-- Slots Grid -->
            <div x-show="!loadingSlots && slots.length > 0">
                <label class="block text-sm font-medium text-gray-700 mb-2">Available Slots for <span x-text="selectedDate"></span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="slot in slots" :key="slot.time">
                        <button 
                            type="button" 
                            :disabled="!slot.available"
                            @click="selectedSlot = slot.time"
                            :class="!slot.available ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : (selectedSlot === slot.time ? 'bg-teal-600 text-white font-bold ring-2 ring-teal-500' : 'bg-white border border-gray-200 text-gray-800 hover:border-teal-500')"
                            class="py-2.5 px-3 rounded-lg text-sm transition text-center"
                        >
                            <span x-text="slot.time"></span>
                            <span x-show="!slot.available" class="block text-xs text-red-500 mt-0.5">Booked</span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Visit / Symptoms (Optional)</label>
                <textarea name="symptoms" rows="2" placeholder="Briefly describe your health issue..." class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
            </div>

            <div class="mt-6 flex justify-end">
                <button 
                    type="button" 
                    :disabled="!selectedDate || !selectedSlot"
                    @click="step = 4" 
                    class="bg-teal-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-teal-700 disabled:opacity-50 transition"
                >
                    Proceed to Review &rarr;
                </button>
            </div>
        </div>

        <!-- STEP 4: REVIEW & CONFIRM -->
        <div x-show="step === 4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Review &amp; Confirm Appointment</h2>
                <button type="button" @click="step = 3" class="text-sm text-teal-600 hover:underline">&larr; Back to Slots</button>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mb-6 space-y-3 text-sm text-gray-700">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Department:</span>
                    <span class="font-semibold text-gray-900" x-text="selectedDeptName"></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Doctor:</span>
                    <span class="font-semibold text-gray-900" x-text="selectedDoctorName"></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Date &amp; Slot:</span>
                    <span class="font-semibold text-gray-900"><span x-text="selectedDate"></span> (<span x-text="selectedSlot"></span>)</span>
                </div>
                <div class="flex justify-between text-base pt-2">
                    <span class="font-bold text-gray-900">Consultation Fee:</span>
                    <span class="font-bold text-teal-700">৳<span x-text="selectedDoctorFee"></span></span>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-teal-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-teal-700 shadow-md transition">
                    Confirm &amp; Generate Token
                </button>
            </div>
        </div>

    </form>

</div>

<script>
function bookingWizard() {
    return {
        step: {{ $selectedDoctor ? 3 : ($selectedDepartment ? 2 : 1) }},
        selectedDeptId: '{{ $selectedDepartment->id ?? '' }}',
        selectedDeptName: '{{ $selectedDepartment->name ?? '' }}',
        availableDoctors: @json($selectedDepartment ? $selectedDepartment->activeDoctors : []),
        selectedDoctorId: '{{ $selectedDoctor->id ?? '' }}',
        selectedDoctorName: '{{ $selectedDoctor ? ($selectedDoctor->name ?: "Dr. " . optional($selectedDoctor->user)->name) : "" }}',
        selectedDoctorFee: '{{ $selectedDoctor->consultation_fee ?? 0 }}',
        selectedDate: '{{ now()->toDateString() }}',
        selectedSlot: '',
        slots: [],
        loadingSlots: false,
        slotMessage: '',

        init() {
            if (this.selectedDoctorId && this.selectedDate) {
                this.fetchSlots();
            }
        },
        selectDepartment(id, name, doctors) {
            this.selectedDeptId = id;
            this.selectedDeptName = name;
            this.availableDoctors = doctors;
            this.step = 2;
        },
        selectDoctor(doc) {
            this.selectedDoctorId = doc.id;
            this.selectedDoctorName = doc.name || (doc.user ? 'Dr. ' + doc.user.name : 'Doctor');
            this.selectedDoctorFee = doc.consultation_fee;
            this.step = 3;
            this.fetchSlots();
        },
        fetchSlots() {
            if (!this.selectedDoctorId || !this.selectedDate) return;
            this.loadingSlots = true;
            this.slotMessage = '';
            this.slots = [];
            this.selectedSlot = '';

            fetch(`/api/doctors/${this.selectedDoctorId}/available-slots?date=${this.selectedDate}`)
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