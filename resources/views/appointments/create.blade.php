@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10" x-data="appointmentWizard()">

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-ink tracking-tight">Book a Doctor's Appointment</h1>
        <p class="text-muted mt-2">Select a department, doctor, and convenient time slot in 4 simple steps.</p>
    </div>

    <!-- Stepper Progress Bar -->
    <div class="flex items-center justify-between max-w-2xl mx-auto mb-10 text-xs sm:text-sm font-semibold">
        <div class="flex items-center gap-2" :class="step >= 1 ? 'text-brand-600' : 'text-muted'">
            <span class="w-7 h-7 rounded-full flex items-center justify-center border-2 transition" :class="step >= 1 ? 'border-brand-600 bg-brand-50 text-brand-600 font-bold' : 'border-brand-100 text-muted'">1</span>
            <span>Department</span>
        </div>
        <div class="h-0.5 w-8 sm:w-16 bg-brand-100" :class="step >= 2 ? 'bg-brand-600' : ''"></div>
        <div class="flex items-center gap-2" :class="step >= 2 ? 'text-brand-600' : 'text-muted'">
            <span class="w-7 h-7 rounded-full flex items-center justify-center border-2 transition" :class="step >= 2 ? 'border-brand-600 bg-brand-50 text-brand-600 font-bold' : 'border-brand-100 text-muted'">2</span>
            <span>Doctor</span>
        </div>
        <div class="h-0.5 w-8 sm:w-16 bg-brand-100" :class="step >= 3 ? 'bg-brand-600' : ''"></div>
        <div class="flex items-center gap-2" :class="step >= 3 ? 'text-brand-600' : 'text-muted'">
            <span class="w-7 h-7 rounded-full flex items-center justify-center border-2 transition" :class="step >= 3 ? 'border-brand-600 bg-brand-50 text-brand-600 font-bold' : 'border-brand-100 text-muted'">3</span>
            <span>Date &amp; Slot</span>
        </div>
        <div class="h-0.5 w-8 sm:w-16 bg-brand-100" :class="step >= 4 ? 'bg-brand-600' : ''"></div>
        <div class="flex items-center gap-2" :class="step >= 4 ? 'text-brand-600' : 'text-muted'">
            <span class="w-7 h-7 rounded-full flex items-center justify-center border-2 transition" :class="step >= 4 ? 'border-brand-600 bg-brand-50 text-brand-600 font-bold' : 'border-brand-100 text-muted'">4</span>
            <span>Review &amp; Pay</span>
        </div>
    </div>

    <!-- Main Wizard Card -->
    <div class="bg-surface rounded-3xl border border-brand-100 shadow-xl p-6 sm:p-10">

        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf

            <!-- Hidden Inputs for Form Submission -->
            <input type="hidden" name="doctor_id" :value="selectedDoctor?.id">
            <input type="hidden" name="date" :value="selectedDate">
            <input type="hidden" name="time_slot" :value="selectedSlot">
            <input type="hidden" name="symptoms" :value="symptoms">

            <!-- STEP 1: Select Department -->
            <div x-show="step === 1" x-transition>
                <h2 class="text-xl font-bold text-ink mb-6">Choose Clinical Department</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <template x-for="dept in departments" :key="dept.id">
                        <button 
                            type="button"
                            @click="selectDepartment(dept)"
                            :class="selectedDept?.id === dept.id ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/30' : 'border-brand-100 hover:border-brand-300 bg-surface'"
                            class="border rounded-2xl p-5 text-left transition flex flex-col justify-between"
                        >
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-3 text-lg font-bold">
                                    <i :class="dept.icon || 'fa-solid fa-stethoscope'"></i>
                                </div>
                                <h3 class="font-bold text-ink" x-text="dept.name"></h3>
                                <p class="text-xs text-muted mt-1 line-clamp-2" x-text="dept.description"></p>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-xs text-brand-600 font-medium">
                                <span x-text="(dept.active_doctors_count || dept.active_doctors?.length || 0) + ' Doctors'"></span>
                                <span>&rarr;</span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- STEP 2: Select Doctor -->
            <div x-show="step === 2" x-transition>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-ink">Select Doctor in <span x-text="selectedDept?.name" class="text-brand-600"></span></h2>
                    <button type="button" @click="step = 1" class="text-sm text-brand-600 hover:underline">&larr; Change Department</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <template x-for="doc in availableDoctors" :key="doc.id">
                        <button 
                            type="button"
                            @click="selectDoctor(doc)"
                            :class="selectedDoctor?.id === doc.id ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/30' : 'border-brand-100 hover:border-brand-300 bg-surface'"
                            class="border rounded-2xl p-5 text-left transition flex items-start gap-4"
                        >
                            <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xl shrink-0">
                                <span x-text="(doc.display_name || doc.user?.name || 'Dr').charAt(0)"></span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-ink" x-text="'Dr. ' + (doc.display_name || doc.user?.name || doc.name)"></h3>
                                <p class="text-xs text-brand-600 font-medium" x-text="doc.specialty || 'Consultant'"></p>
                                <p class="text-xs text-muted mt-1" x-text="doc.qualifications"></p>
                                <div class="mt-2 flex items-center justify-between text-xs font-semibold">
                                    <span class="text-amber-500">★ <span x-text="doc.avg_rating || '4.8'"></span></span>
                                    <span class="text-brand-600">৳<span x-text="Number(doc.consultation_fee || 0).toFixed(0)"></span></span>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- STEP 3: Choose Date & Time Slot -->
            <div x-show="step === 3" x-transition>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-ink">Choose Date &amp; Time Slot</h2>
                    <button type="button" @click="step = 2" class="text-sm text-brand-600 hover:underline">&larr; Change Doctor</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Date Picker -->
                    <div>
                        <label class="block text-sm font-semibold text-ink mb-2">Appointment Date</label>
                        <input 
                            type="date" 
                            x-model="selectedDate" 
                            @change="fetchSlots()" 
                            min="{{ now()->toDateString() }}"
                            class="w-full rounded-2xl border-brand-200 bg-surface text-ink shadow-sm focus:border-brand-600 focus:ring-brand-600 p-3"
                        >
                        <p class="text-xs text-muted mt-1.5">Consultation days: Sunday to Thursday.</p>
                    </div>

                    <!-- Slot Selector (2 cols) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-ink mb-2">Available 30-min Time Slots</label>

                        <!-- Loading State -->
                        <div x-show="loadingSlots" class="text-brand-600 text-sm py-4 flex items-center gap-2">
                            <span class="animate-spin inline-block w-4 h-4 border-2 border-brand-600 border-t-transparent rounded-full"></span>
                            Fetching available slots...
                        </div>

                        <!-- Off-Day / No Slots Notice -->
                        <div x-show="!loadingSlots && slotMessage" class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 text-sm mb-4" x-text="slotMessage"></div>

                        <!-- Slot Badges -->
                        <div x-show="!loadingSlots && slots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-56 overflow-y-auto pr-1">
                            <template x-for="slot in slots" :key="slot.time">
                                <button 
                                    type="button"
                                    :disabled="!slot.available"
                                    @click="selectedSlot = slot.time"
                                    :class="!slot.available ? 'bg-surface-alt text-muted cursor-not-allowed opacity-50' : (selectedSlot === slot.time ? 'bg-brand-600 text-white font-bold ring-2 ring-brand-500 shadow-md' : 'bg-surface border border-brand-100 text-ink hover:border-brand-500')"
                                    class="py-2.5 px-3 rounded-xl text-xs sm:text-sm font-medium transition text-center"
                                >
                                    <span x-text="slot.time"></span>
                                    <span x-show="!slot.available" class="block text-[10px] text-rose-500">Booked</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Symptoms / Reason -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-ink mb-2">Reason for Visit / Symptoms (Optional)</label>
                    <textarea 
                        x-model="symptoms" 
                        rows="2" 
                        placeholder="Briefly describe your symptoms or reason for visit..."
                        class="w-full rounded-2xl border-brand-200 bg-surface text-ink shadow-sm focus:border-brand-600 focus:ring-brand-600 p-3 text-sm"
                    ></textarea>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-brand-100">
                    <button type="button" @click="step = 2" class="px-5 py-2.5 rounded-xl border border-brand-200 text-muted hover:text-ink text-sm font-medium">Back</button>
                    <button 
                        type="button" 
                        @click="if (selectedSlot) step = 4;"
                        :disabled="!selectedSlot"
                        class="bg-brand-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-sm"
                    >
                        Proceed to Review &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 4: Review & Confirm -->
            <div x-show="step === 4" x-transition>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-ink">Review Appointment Details</h2>
                    <button type="button" @click="step = 3" class="text-sm text-brand-600 hover:underline">&larr; Edit Date/Slot</button>
                </div>

                <div class="bg-surface-alt rounded-2xl p-6 border border-brand-100 space-y-4 mb-8 text-sm">
                    <div class="flex justify-between pb-3 border-b border-brand-100">
                        <span class="text-muted">Department:</span>
                        <span class="font-bold text-ink" x-text="selectedDept?.name"></span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-brand-100">
                        <span class="text-muted">Consulting Doctor:</span>
                        <span class="font-bold text-ink" x-text="'Dr. ' + (selectedDoctor?.display_name || selectedDoctor?.user?.name || selectedDoctor?.name)"></span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-brand-100">
                        <span class="text-muted">Appointment Date:</span>
                        <span class="font-bold text-ink" x-text="selectedDate"></span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-brand-100">
                        <span class="text-muted">Time Slot:</span>
                        <span class="font-bold text-brand-600" x-text="selectedSlot"></span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-brand-100">
                        <span class="text-muted">Consultation Fee:</span>
                        <span class="font-bold text-ink">৳<span x-text="Number(selectedDoctor?.consultation_fee || 0).toFixed(2)"></span></span>
                    </div>
                    <div x-show="symptoms" class="flex justify-between pt-1">
                        <span class="text-muted">Symptoms:</span>
                        <span class="text-ink max-w-xs text-right" x-text="symptoms"></span>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="step = 3" class="px-5 py-2.5 rounded-xl border border-brand-200 text-muted hover:text-ink text-sm font-medium">Back</button>
                    <button 
                        type="submit" 
                        class="bg-brand-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-brand-700 transition shadow-lg flex items-center gap-2"
                    >
                        <span>Confirm &amp; Proceed to Payment</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

        </form>

    </div>

</div>

<script>
function appointmentWizard() {
    return {
        step: 1,
        departments: @json($departments),
        selectedDept: @json($selectedDepartment),
        selectedDoctor: @json($selectedDoctor),
        availableDoctors: [],
        selectedDate: '{{ now()->addDay()->toDateString() }}',
        selectedSlot: '',
        slots: [],
        loadingSlots: false,
        slotMessage: '',
        symptoms: '',

        init() {
            if (this.selectedDept) {
                this.availableDoctors = this.selectedDept.active_doctors || [];
                this.step = 2;
            }
            if (this.selectedDoctor) {
                this.step = 3;
                this.fetchSlots();
            }
        },

        selectDepartment(dept) {
            this.selectedDept = dept;
            this.availableDoctors = dept.active_doctors || [];
            this.selectedDoctor = null;
            this.selectedSlot = '';
            this.step = 2;
        },

        selectDoctor(doc) {
            this.selectedDoctor = doc;
            this.selectedSlot = '';
            this.step = 3;
            this.fetchSlots();
        },

        fetchSlots() {
            if (!this.selectedDoctor || !this.selectedDate) return;
            this.loadingSlots = true;
            this.slotMessage = '';
            this.slots = [];
            this.selectedSlot = '';

            const docIdentifier = this.selectedDoctor.slug || this.selectedDoctor.id;

            fetch(`/api/doctors/${docIdentifier}/available-slots?date=${this.selectedDate}`)
                .then(res => res.json())
                .then(data => {
                    this.loadingSlots = false;
                    if (data.available && data.slots && data.slots.length > 0) {
                        this.slots = data.slots;
                    } else {
                        this.slotMessage = data.reason || 'Doctor has no consultation hours on this weekday. Please select Sunday – Thursday.';
                    }
                })
                .catch(err => {
                    this.loadingSlots = false;
                    this.slotMessage = 'Could not load slots. Please pick another date.';
                });
        }
    }
}
</script>
@endsection