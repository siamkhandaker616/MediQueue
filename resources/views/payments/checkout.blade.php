@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10" x-data="{ gateway: 'all' }">

    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold mb-2">
            <i class="fa-solid fa-lock text-emerald-600"></i> SSLCommerz 256-bit Secure Gateway
        </div>
        <h1 class="text-3xl font-black text-ink tracking-tight">Checkout &amp; Payment</h1>
        <p class="text-muted text-sm mt-1">Complete your consultation payment to confirm your queue token.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Gateway Selector (2 Cols) -->
        <div class="md:col-span-2 bg-surface border border-brand-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h2 class="text-lg font-bold text-ink mb-2">Select Payment Channel</h2>
            <p class="text-xs text-muted mb-6">Choose how you want to pay via Bangladesh's largest payment gateway.</p>

            <form method="POST" action="{{ route('sslcommerz.pay', $appointment) }}">
                @csrf
                <input type="hidden" name="gateway" :value="gateway">

                <div class="space-y-3 mb-8">

                    <!-- 1. All Gateways (SSLCommerz Hosted) -->
                    <label 
                        @click="gateway = 'all'"
                        :class="gateway === 'all' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-300 bg-surface'"
                        class="cursor-pointer border rounded-2xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl font-bold">
                                💳
                            </div>
                            <div>
                                <span class="font-bold text-ink block text-sm">All Payment Methods (Recommended)</span>
                                <span class="text-xs text-muted">bKash, Nagad, Rocket, Visa, Mastercard, Amex, Internet Banking</span>
                            </div>
                        </div>
                        <input type="radio" name="_gw" value="all" :checked="gateway === 'all'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- 2. bKash Direct -->
                    <label 
                        @click="gateway = 'bkash'"
                        :class="gateway === 'bkash' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-300 bg-surface'"
                        class="cursor-pointer border rounded-2xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold text-base">
                                bKash
                            </div>
                            <div>
                                <span class="font-bold text-ink block text-sm">bKash Mobile Banking</span>
                                <span class="text-xs text-muted">Direct bKash payment gateway</span>
                            </div>
                        </div>
                        <input type="radio" name="_gw" value="bkash" :checked="gateway === 'bkash'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- 3. Nagad Direct -->
                    <label 
                        @click="gateway = 'nagad'"
                        :class="gateway === 'nagad' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-300 bg-surface'"
                        class="cursor-pointer border rounded-2xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-base">
                                Nagad
                            </div>
                            <div>
                                <span class="font-bold text-ink block text-sm">Nagad Mobile Banking</span>
                                <span class="text-xs text-muted">Fast checkout via Nagad</span>
                            </div>
                        </div>
                        <input type="radio" name="_gw" value="nagad" :checked="gateway === 'nagad'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                    <!-- 4. Credit / Debit Cards -->
                    <label 
                        @click="gateway = 'card'"
                        :class="gateway === 'card' ? 'border-brand-600 ring-2 ring-brand-500 bg-brand-50/20' : 'border-brand-100 hover:border-brand-300 bg-surface'"
                        class="cursor-pointer border rounded-2xl p-4 flex items-center justify-between transition"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div>
                                <span class="font-bold text-ink block text-sm">Credit / Debit Cards</span>
                                <span class="text-xs text-muted">Visa, Mastercard, DBBL Nexus, UnionPay</span>
                            </div>
                        </div>
                        <input type="radio" name="_gw" value="card" :checked="gateway === 'card'" class="text-brand-600 focus:ring-brand-500">
                    </label>

                </div>

                <button 
                    type="submit" 
                    class="w-full bg-brand-600 text-white font-bold py-3.5 px-6 rounded-2xl hover:bg-brand-700 transition shadow-lg flex items-center justify-center gap-2"
                >
                    <span>Proceed to SSLCommerz (৳{{ number_format($total, 2) }})</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- Fee Transparency Breakdown (FR-09) -->
        <div class="bg-surface border border-brand-100 rounded-3xl p-6 sm:p-8 shadow-sm h-fit">
            <h2 class="text-lg font-bold text-ink mb-4">Fee Summary</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-muted">
                    <span>Consultation Fee:</span>
                    <span class="font-medium text-ink">৳{{ number_format($doctorFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-muted">
                    <span>Hospital Service Fee:</span>
                    <span class="font-medium text-ink">৳{{ number_format($serviceFee, 2) }}</span>
                </div>
                <div class="flex justify-between text-muted">
                    <span>Govt VAT (5%):</span>
                    <span class="font-medium text-ink">৳{{ number_format($vat, 2) }}</span>
                </div>

                <div class="border-t border-brand-100 pt-3 flex justify-between text-base font-bold text-ink">
                    <span>Total Payable:</span>
                    <span class="text-brand-600">৳{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="mt-6 p-4 rounded-2xl bg-surface-alt text-xs text-muted space-y-2">
                <p class="font-semibold text-ink flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald-600"></i> SSLCommerz Verified
                </p>
                <p>Digital receipts and instant token validation are auto-generated upon payment confirmation.</p>
            </div>
        </div>

    </div>

</div>
@endsection