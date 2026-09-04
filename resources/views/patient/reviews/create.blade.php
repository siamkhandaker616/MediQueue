@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10" x-data="ratingComponent()">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-black text-ink tracking-tight">Rate Your Consultation</h1>
        <p class="text-muted text-sm mt-1">Your feedback helps improve patient care and guides future patients.</p>
    </div>

    <div class="bg-surface border border-brand-100 rounded-3xl p-8 shadow-xl">

        <!-- Doctor Info Header -->
        <div class="flex items-center gap-4 bg-surface-alt rounded-2xl p-4 mb-6 border border-brand-100">
            <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl font-bold shrink-0">
                {{ substr($appointment->doctor->display_name ?? $appointment->doctor->user->name, 0, 1) }}
            </div>
            <div>
                <h3 class="font-bold text-ink text-base">Dr. {{ $appointment->doctor->display_name ?? $appointment->doctor->user->name }}</h3>
                <p class="text-xs text-brand-600 font-medium">{{ $appointment->department->name }}</p>
                <p class="text-xs text-muted mt-0.5">Consultation on {{ $appointment->date->format('M d, Y') }} ({{ $appointment->time_slot }})</p>
            </div>
        </div>

        <form method="POST" action="{{ route('patient.reviews.store', $appointment) }}">
            @csrf

            <!-- Star Rating Widget -->
            <div class="text-center my-8">
                <label class="block text-sm font-bold text-ink mb-3">How was your consultation experience?</label>
                
                <div class="flex justify-center items-center gap-2">
                    <template x-for="star in 5" :key="star">
                        <button 
                            type="button"
                            @click="rating = star"
                            @mouseenter="hoverRating = star"
                            @mouseleave="hoverRating = 0"
                            class="text-3xl sm:text-4xl transition transform hover:scale-110 focus:outline-none"
                            :class="(hoverRating || rating) >= star ? 'text-amber-400' : 'text-brand-100'"
                        >
                            ★
                        </button>
                    </template>
                </div>

                <div class="mt-2 text-xs font-bold text-brand-600 h-5" x-text="ratingLabels[hoverRating || rating] || ''"></div>
                <input type="hidden" name="rating" :value="rating">
                @error('rating')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Review Feedback Text -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-ink mb-2">Detailed Feedback (Optional)</label>
                <textarea 
                    name="comment" 
                    rows="4" 
                    placeholder="Share details about the doctor's communication, punctuality, treatment clarity..."
                    class="w-full rounded-2xl border-brand-200 bg-surface text-ink text-sm p-4 focus:border-brand-600 focus:ring-brand-600"
                ></textarea>
            </div>

            <!-- Anonymous Option -->
            <div class="mb-8 flex items-center gap-2 text-xs text-muted">
                <input type="checkbox" name="is_anonymous" value="1" id="anon" class="rounded border-brand-200 text-brand-600 focus:ring-brand-500">
                <label for="anon" class="cursor-pointer">Post review anonymously (hide my full name)</label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('patient.history') }}" class="px-5 py-2.5 rounded-xl border border-brand-200 text-muted hover:text-ink text-sm font-medium">Cancel</a>
                <button 
                    type="submit" 
                    :disabled="!rating"
                    class="bg-brand-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-sm"
                >
                    Submit Review &rarr;
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function ratingComponent() {
    return {
        rating: 5,
        hoverRating: 0,
        ratingLabels: {
            1: '1/5 - Poor Experience',
            2: '2/5 - Fair',
            3: '3/5 - Good',
            4: '4/5 - Very Good',
            5: '5/5 - Excellent & Professional'
        }
    }
}
</script>
@endsection@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10" x-data="ratingComponent()">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-black text-ink tracking-tight">Rate Your Consultation</h1>
        <p class="text-muted text-sm mt-1">Your feedback helps improve patient care and guides future patients.</p>
    </div>

    <div class="bg-surface border border-brand-100 rounded-3xl p-8 shadow-xl">

        <!-- Doctor Info Header -->
        <div class="flex items-center gap-4 bg-surface-alt rounded-2xl p-4 mb-6 border border-brand-100">
            <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl font-bold shrink-0">
                {{ substr($appointment->doctor->display_name ?? $appointment->doctor->user->name, 0, 1) }}
            </div>
            <div>
                <h3 class="font-bold text-ink text-base">Dr. {{ $appointment->doctor->display_name ?? $appointment->doctor->user->name }}</h3>
                <p class="text-xs text-brand-600 font-medium">{{ $appointment->department->name }}</p>
                <p class="text-xs text-muted mt-0.5">Consultation on {{ $appointment->date->format('M d, Y') }} ({{ $appointment->time_slot }})</p>
            </div>
        </div>

        <form method="POST" action="{{ route('patient.reviews.store', $appointment) }}">
            @csrf

            <!-- Star Rating Widget -->
            <div class="text-center my-8">
                <label class="block text-sm font-bold text-ink mb-3">How was your consultation experience?</label>
                
                <div class="flex justify-center items-center gap-2">
                    <template x-for="star in 5" :key="star">
                        <button 
                            type="button"
                            @click="rating = star"
                            @mouseenter="hoverRating = star"
                            @mouseleave="hoverRating = 0"
                            class="text-3xl sm:text-4xl transition transform hover:scale-110 focus:outline-none"
                            :class="(hoverRating || rating) >= star ? 'text-amber-400' : 'text-brand-100'"
                        >
                            ★
                        </button>
                    </template>
                </div>

                <div class="mt-2 text-xs font-bold text-brand-600 h-5" x-text="ratingLabels[hoverRating || rating] || ''"></div>
                <input type="hidden" name="rating" :value="rating">
                @error('rating')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Review Feedback Text -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-ink mb-2">Detailed Feedback (Optional)</label>
                <textarea 
                    name="comment" 
                    rows="4" 
                    placeholder="Share details about the doctor's communication, punctuality, treatment clarity..."
                    class="w-full rounded-2xl border-brand-200 bg-surface text-ink text-sm p-4 focus:border-brand-600 focus:ring-brand-600"
                ></textarea>
            </div>

            <!-- Anonymous Option -->
            <div class="mb-8 flex items-center gap-2 text-xs text-muted">
                <input type="checkbox" name="is_anonymous" value="1" id="anon" class="rounded border-brand-200 text-brand-600 focus:ring-brand-500">
                <label for="anon" class="cursor-pointer">Post review anonymously (hide my full name)</label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('patient.history') }}" class="px-5 py-2.5 rounded-xl border border-brand-200 text-muted hover:text-ink text-sm font-medium">Cancel</a>
                <button 
                    type="submit" 
                    :disabled="!rating"
                    class="bg-brand-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-sm"
                >
                    Submit Review &rarr;
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function ratingComponent() {
    return {
        rating: 5,
        hoverRating: 0,
        ratingLabels: {
            1: '1/5 - Poor Experience',
            2: '2/5 - Fair',
            3: '3/5 - Good',
            4: '4/5 - Very Good',
            5: '5/5 - Excellent & Professional'
        }
    }
}
</script>
@endsection