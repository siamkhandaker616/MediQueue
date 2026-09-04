@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" x-data="medicalProfile()">

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-center font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-black text-ink tracking-tight">Medical &amp; Allergy Profile</h1>
                <p class="text-muted text-sm mt-1">This health profile is synced with your consulting doctors during appointments.</p>
            </div>
            <a href="{{ route('patient.history') }}" class="text-sm font-medium text-brand-600 hover:underline">&larr; Back to History</a>
        </div>
    </div>

    <!-- Emergency Health Summary Card -->
    <div class="bg-rose-500/10 border border-rose-500/20 rounded-3xl p-6 mb-8">
        <div class="flex items-center gap-3 mb-3">
            <span class="w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            <h2 class="text-lg font-bold text-rose-700">Emergency Medical Alerts</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-xs text-rose-600 block">Blood Group</span>
                <span class="font-black text-ink text-xl" x-text="bloodType || 'Not set'"></span>
            </div>
            <div>
                <span class="text-xs text-rose-600 block">Allergies</span>
                <span class="font-semibold text-ink" x-text="allergies.length > 0 ? allergies.join(', ') : 'No known allergies'"></span>
            </div>
            <div>
                <span class="text-xs text-rose-600 block">Chronic Conditions</span>
                <span class="font-semibold text-ink" x-text="conditions.length > 0 ? conditions.join(', ') : 'None declared'"></span>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="bg-surface border border-brand-100 rounded-3xl p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('patient.medical-profile.update') }}">
            @csrf

            <!-- 1. Blood Group -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-ink mb-3">Blood Group</label>
                <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                    <template x-for="bg in bloodGroups" :key="bg">
                        <button 
                            type="button"
                            @click="bloodType = bg"
                            :class="bloodType === bg ? 'bg-rose-600 text-white font-bold ring-2 ring-rose-500' : 'bg-surface border border-brand-100 text-ink hover:border-brand-400'"
                            class="py-2.5 rounded-xl text-center font-semibold text-sm transition"
                            x-text="bg"
                        ></button>
                    </template>
                </div>
                <input type="hidden" name="blood_type" :value="bloodType">
            </div>

            <!-- 2. Known Drug Allergies -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-ink mb-1">Known Drug &amp; Food Allergies</label>
                <p class="text-xs text-muted mb-3">Add any medications or substances you are allergic to (e.g. Penicillin, Sulfa, NSAIDs, Peanuts).</p>
                
                <div class="flex gap-2 mb-3">
                    <input 
                        type="text" 
                        x-model="newAllergy" 
                        @keydown.enter.prevent="addAllergy()" 
                        placeholder="Type allergy and press Enter or Add..."
                        class="flex-1 rounded-xl border-brand-200 bg-surface text-ink text-sm p-3 focus:border-brand-600 focus:ring-brand-600"
                    >
                    <button type="button" @click="addAllergy()" class="bg-brand-50 text-brand-700 hover:bg-brand-100 px-5 py-3 rounded-xl text-sm font-bold transition">
                        Add
                    </button>
                </div>

                <!-- Allergy Tags -->
                <div class="flex flex-wrap gap-2 min-h-[32px]">
                    <template x-for="(alg, index) in allergies" :key="index">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-600 text-xs font-semibold">
                            <span x-text="alg"></span>
                            <button type="button" @click="removeAllergy(index)" class="hover:text-rose-800 text-sm">&times;</button>
                            <input type="hidden" name="allergies[]" :value="alg">
                        </span>
                    </template>
                </div>
            </div>

            <!-- 3. Chronic Health Conditions -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-ink mb-1">Chronic Health Conditions</label>
                <p class="text-xs text-muted mb-3">Add long-term conditions (e.g. Diabetes, Hypertension, Asthma, Heart Disease).</p>
                
                <div class="flex gap-2 mb-3">
                    <input 
                        type="text" 
                        x-model="newCondition" 
                        @keydown.enter.prevent="addCondition()" 
                        placeholder="Type chronic condition and press Enter or Add..."
                        class="flex-1 rounded-xl border-brand-200 bg-surface text-ink text-sm p-3 focus:border-brand-600 focus:ring-brand-600"
                    >
                    <button type="button" @click="addCondition()" class="bg-brand-50 text-brand-700 hover:bg-brand-100 px-5 py-3 rounded-xl text-sm font-bold transition">
                        Add
                    </button>
                </div>

                <!-- Condition Tags -->
                <div class="flex flex-wrap gap-2 min-h-[32px]">
                    <template x-for="(cond, index) in conditions" :key="index">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 text-xs font-semibold">
                            <span x-text="cond"></span>
                            <button type="button" @click="removeCondition(index)" class="hover:text-amber-900 text-sm">&times;</button>
                            <input type="hidden" name="chronic_conditions[]" :value="cond">
                        </span>
                    </template>
                </div>
            </div>

            <!-- 4. Current Regular Medications -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-ink mb-1">Current Regular Medications</label>
                <p class="text-xs text-muted mb-3">Add daily medicines you take (e.g. Metformin 500mg, Losartan 50mg).</p>
                
                <div class="flex gap-2 mb-3">
                    <input 
                        type="text" 
                        x-model="newMedication" 
                        @keydown.enter.prevent="addMedication()" 
                        placeholder="Type medication and press Enter or Add..."
                        class="flex-1 rounded-xl border-brand-200 bg-surface text-ink text-sm p-3 focus:border-brand-600 focus:ring-brand-600"
                    >
                    <button type="button" @click="addMedication()" class="bg-brand-50 text-brand-700 hover:bg-brand-100 px-5 py-3 rounded-xl text-sm font-bold transition">
                        Add
                    </button>
                </div>

                <!-- Medication Tags -->
                <div class="flex flex-wrap gap-2 min-h-[32px]">
                    <template x-for="(med, index) in medications" :key="index">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-semibold">
                            <span x-text="med"></span>
                            <button type="button" @click="removeMedication(index)" class="hover:text-brand-900 text-sm">&times;</button>
                            <input type="hidden" name="current_medications[]" :value="med">
                        </span>
                    </template>
                </div>
            </div>

            <!-- 5. Emergency Contact Info -->
            <div class="border-t border-brand-100 pt-6 mb-8">
                <h3 class="text-base font-bold text-ink mb-4">Emergency Contact Person</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-muted mb-1">Full Name</label>
                        <input 
                            type="text" 
                            name="emergency_contact_name" 
                            value="{{ old('emergency_contact_name', $profile->emergency_contact['name'] ?? '') }}"
                            placeholder="e.g. Sarah Khan"
                            class="w-full rounded-xl border-brand-200 bg-surface text-ink text-sm p-3"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted mb-1">Relationship</label>
                        <input 
                            type="text" 
                            name="emergency_contact_relationship" 
                            value="{{ old('emergency_contact_relationship', $profile->emergency_contact['relationship'] ?? '') }}"
                            placeholder="e.g. Spouse / Parent / Sibling"
                            class="w-full rounded-xl border-brand-200 bg-surface text-ink text-sm p-3"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted mb-1">Phone Number</label>
                        <input 
                            type="text" 
                            name="emergency_contact_phone" 
                            value="{{ old('emergency_contact_phone', $profile->emergency_contact['phone'] ?? '') }}"
                            placeholder="e.g. +880 1712 345678"
                            class="w-full rounded-xl border-brand-200 bg-surface text-ink text-sm p-3"
                        >
                    </div>
                </div>
            </div>

            <!-- 6. Additional Notes -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-ink mb-2">Additional Medical Notes</label>
                <textarea 
                    name="additional_notes" 
                    rows="3" 
                    placeholder="Any past surgeries, medical implants, dietary restrictions, or notes..."
                    class="w-full rounded-2xl border-brand-200 bg-surface text-ink text-sm p-3 focus:border-brand-600 focus:ring-brand-600"
                >{{ old('additional_notes', $profile->additional_notes) }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="submit" class="bg-brand-600 text-white font-bold px-8 py-3 rounded-xl hover:bg-brand-700 transition shadow-sm">
                    Save Medical Profile
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function medicalProfile() {
    return {
        bloodGroups: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
        bloodType: '{{ $profile->blood_type ?? '' }}',
        allergies: @json($profile->allergies ?? []),
        newAllergy: '',
        conditions: @json($profile->chronic_conditions ?? []),
        newCondition: '',
        medications: @json($profile->current_medications ?? []),
        newMedication: '',

        addAllergy() {
            if (this.newAllergy.trim() && !this.allergies.includes(this.newAllergy.trim())) {
                this.allergies.push(this.newAllergy.trim());
                this.newAllergy = '';
            }
        },
        removeAllergy(index) {
            this.allergies.splice(index, 1);
        },
        addCondition() {
            if (this.newCondition.trim() && !this.conditions.includes(this.newCondition.trim())) {
                this.conditions.push(this.newCondition.trim());
                this.newCondition = '';
            }
        },
        removeCondition(index) {
            this.conditions.splice(index, 1);
        },
        addMedication() {
            if (this.newMedication.trim() && !this.medications.includes(this.newMedication.trim())) {
                this.medications.push(this.newMedication.trim());
                this.newMedication = '';
            }
        },
        removeMedication(index) {
            this.medications.splice(index, 1);
        }
    }
}
</script>
@endsection