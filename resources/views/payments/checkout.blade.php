@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" x-data="{ selectedMethod: 'bkash' }">

    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-ink">Secure Payment</h1>
        <p class="text-muted mt-1">Select your preferred payment gateway to confirm consultation &amp; receive your digital token.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Payment Method Selector (2 cols) -->
        <div class="md:col-span-2 bg-surface border border-brand-100 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-ink mb-4">Select Payment Method</h2>

            <form method="POST" action="{{ route('payments.process', $appointment) }}">
                @csrf
                <input type="hidden" name="method" :value="selectedMethod">

                <div class="space-y-3 mb-6">
                    <!-- bKash -->
                    <label 
                        @click="selectedMethod = 'bkash'"
                        :class="selectedMethod === 'bkash' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-mobile-screen text-pink-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">bKash Mobile Banking</span>
                                <span class="text-xs text-muted">Instant transfer via bKash gateway</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="bkash" :checked="selectedMethod === 'bkash'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- Nagad -->
                    <label 
                        @click="selectedMethod = 'nagad'"
                        :class="selectedMethod === 'nagad' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-money-bill-transfer text-orange-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">Nagad</span>
                                <span class="text-xs text-muted">Direct digital payment</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="nagad" :checked="selectedMethod === 'nagad'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- Credit / Debit Card -->
                    <label 
                        @click="selectedMethod = 'card'"
                        :class="selectedMethod === 'card' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-credit-card text-blue-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">Credit / Debit Card</span>
                                <span class="text-xs text-muted">Visa, Mastercard, Amex</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="card" :checked="selectedMethod === 'card'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- Health Wallet -->
                    <label 
                        @click="selectedMethod = 'wallet'"
                        :class="selectedMethod === 'wallet' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wallet text-emerald-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">Digital Health Wallet</span>
                                <span class="text-xs text-muted">Pay from MediQueue balance</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="wallet" :checked="selectedMethod === 'wallet'" class="text-brand-600 focus:ring-brand-500">
                    </label>
                </div>

                <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-brand-700 transition shadow-sm">
                    Pay ৳{{ number_format($total, 2) }} &amp; Confirm
                </button>
            </form>
        </div>

        <!-- Fee Transparency Breakdown (FR-09) -->
        <div class="bg-surface border border-brand-100 rounded-2xl p-6 shadow-sm h-fit">
            <h2 class="text-lg font-bold text-ink mb-4">Fee Breakdown</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-muted">
                    <span>Consultation Fee:</span>
                    <span class="font-medium text-ink">৳{{ number_format($doctorFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-muted">
                    <span>Hospital Service Charge:</span>
                    <span class="font-medium text-ink">৳{{ number_format($serviceFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-muted">
                    <span>Govt VAT (5%):</span>
                    <span class="font-medium text-ink">৳{{ number_format($vat, 2) }}</span>
                </div>

                <div class="border-t border-brand-100 pt-3 flex justify-between text-base font-bold text-ink">
                    <span>Total Amount:</span>
                    <span class="text-brand-600">৳{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="mt-6 p-3 rounded-xl bg-surface-alt text-xs text-muted">
                <i class="fa-solid fa-shield-halved text-brand-600 mr-1"></i>
                100% Secure SSL encrypted payment. Digital receipt auto-generated.
            </div>
        </div>

    </div>

</div>
@endsection@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" x-data="{ selectedMethod: 'bkash' }">

    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-ink">Secure Payment</h1>
        <p class="text-muted mt-1">Select your preferred payment gateway to confirm consultation &amp; receive your digital token.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Payment Method Selector (2 cols) -->
        <div class="md:col-span-2 bg-surface border border-brand-100 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-ink mb-4">Select Payment Method</h2>

            <form method="POST" action="{{ route('payments.process', $appointment) }}">
                @csrf
                <input type="hidden" name="method" :value="selectedMethod">

                <div class="space-y-3 mb-6">
                    <!-- bKash -->
                    <label 
                        @click="selectedMethod = 'bkash'"
                        :class="selectedMethod === 'bkash' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-mobile-screen text-pink-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">bKash Mobile Banking</span>
                                <span class="text-xs text-muted">Instant transfer via bKash gateway</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="bkash" :checked="selectedMethod === 'bkash'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- Nagad -->
                    <label 
                        @click="selectedMethod = 'nagad'"
                        :class="selectedMethod === 'nagad' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-money-bill-transfer text-orange-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">Nagad</span>
                                <span class="text-xs text-muted">Direct digital payment</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="nagad" :checked="selectedMethod === 'nagad'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- Credit / Debit Card -->
                    <label 
                        @click="selectedMethod = 'card'"
                        :class="selectedMethod === 'card' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-credit-card text-blue-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">Credit / Debit Card</span>
                                <span class="text-xs text-muted">Visa, Mastercard, Amex</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="card" :checked="selectedMethod === 'card'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- Health Wallet -->
                    <label 
                        @click="selectedMethod = 'wallet'"
                        :class="selectedMethod === 'wallet' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-200'"
                        class="cursor-pointer border rounded-xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wallet text-emerald-600 text-xl"></i>
                            <div>
                                <span class="font-semibold text-ink block">Digital Health Wallet</span>
                                <span class="text-xs text-muted">Pay from MediQueue balance</span>
                            </div>
                        </div>
                        <input type="radio" name="_pm" value="wallet" :checked="selectedMethod === 'wallet'" class="text-brand-600 focus:ring-brand-500">
                    </label>
                </div>

                <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-brand-700 transition shadow-sm">
                    Pay ৳{{ number_format($total, 2) }} &amp; Confirm
                </button>
            </form>
        </div>

        <!-- Fee Transparency Breakdown (FR-09) -->
        <div class="bg-surface border border-brand-100 rounded-2xl p-6 shadow-sm h-fit">
            <h2 class="text-lg font-bold text-ink mb-4">Fee Breakdown</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-muted">
                    <span>Consultation Fee:</span>
                    <span class="font-medium text-ink">৳{{ number_format($doctorFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-muted">
                    <span>Hospital Service Charge:</span>
                    <span class="font-medium text-ink">৳{{ number_format($serviceFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-muted">
                    <span>Govt VAT (5%):</span>
                    <span class="font-medium text-ink">৳{{ number_format($vat, 2) }}</span>
                </div>

                <div class="border-t border-brand-100 pt-3 flex justify-between text-base font-bold text-ink">
                    <span>Total Amount:</span>
                    <span class="text-brand-600">৳{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="mt-6 p-3 rounded-xl bg-surface-alt text-xs text-muted">
                <i class="fa-solid fa-shield-halved text-brand-600 mr-1"></i>
                100% Secure SSL encrypted payment. Digital receipt auto-generated.
            </div>
        </div>

    </div>

</div>
@endsection