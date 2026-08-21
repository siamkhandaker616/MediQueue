@php
    use App\Models\Payment;
@endphp
<x-layouts.staff>
    <x-slot name="title">Refund management</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Refunds</h1>
        <p class="mt-1 text-sm text-muted">Process manual refunds with reason logging (FR-10).</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Collected (paid)</p>
            <p class="mt-1 text-3xl font-bold text-accent-700">৳{{ number_format($stats['collected']) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Refunded total</p>
            <p class="mt-1 text-3xl font-bold text-brand-600">৳{{ number_format($stats['refundedTotal'], 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-sm font-medium text-muted">Fully refunded payments</p>
            <p class="mt-1 text-3xl font-bold">{{ $stats['refundedCount'] }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mt-6 flex items-center gap-2 rounded-xl border border-accent-200 bg-accent-100/70 px-4 py-3 text-sm font-medium text-accent-700">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="card mt-6 overflow-hidden">
        @if ($payments->isEmpty())
            <p class="px-5 py-12 text-center text-sm text-muted">No payments yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="table-head">Receipt</th>
                            <th class="table-head">Patient</th>
                            <th class="table-head">Doctor</th>
                            <th class="table-head">Department</th>
                            <th class="table-head">Amount</th>
                            <th class="table-head" style="text-align:center">Status</th>
                            <th class="table-head"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @foreach ($payments as $payment)
                            @php
                                $remaining   = round((float) $payment->amount - (float) $payment->refund_amount, 2);
                                $hasRefund   = (float) $payment->refund_amount > 0;
                                $isFull      = $payment->status === Payment::STATUS_REFUNDED;
                                $isPartial   = $hasRefund && !$isFull;
                            @endphp
                            <tr>
                                <td class="table-cell font-mono text-xs">{{ $payment->receipt_number }}</td>
                                <td class="table-cell font-medium">{{ $payment->appointment->patient->name ?? '—' }}</td>
                                <td class="table-cell text-muted">{{ $payment->appointment->doctor->name ?? '—' }}</td>
                                <td class="table-cell text-muted">{{ $payment->appointment->department->name ?? '—' }}</td>
                                <td class="table-cell">৳{{ number_format((float) $payment->amount, 0) }}</td>
                                <td class="table-cell" style="text-align:center" x-data="{ open: false }">
                                    @if ($isFull)
                                        <button @click="open = true" class="badge cursor-pointer bg-brand-100 text-brand-700 hover:bg-brand-200 transition">Fully Refunded</button>
                                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" style="display: none;" @click.outside="open = false">
                                            <div class="card w-full max-w-sm p-6" @click.stop>
                                                <h3 class="font-semibold">Refund details — {{ $payment->receipt_number }}</h3>
                                                <div class="mt-3 space-y-2 text-sm">
                                                    <div class="flex justify-between"><span class="text-muted">Refunded amount</span><span class="font-semibold text-brand-600">৳{{ number_format((float) $payment->refund_amount, 2) }}</span></div>
                                                    <div class="flex justify-between"><span class="text-muted">Original amount</span><span>৳{{ number_format((float) $payment->amount, 2) }}</span></div>
                                                    @if ($payment->refunded_at)<div class="flex justify-between"><span class="text-muted">Refunded on</span><span>{{ $payment->refunded_at->format('d M Y, h:i A') }}</span></div>@endif
                                                    @if ($payment->refund_reason)<div class="border-t border-brand-50 pt-2 mt-2"><span class="text-muted text-xs">Reason</span><p class="mt-1">{{ $payment->refund_reason }}</p></div>@endif
                                                </div>
                                                <div class="mt-4 text-right"><button @click="open = false" class="btn-outline">Close</button></div>
                                            </div>
                                        </div>
                                    @elseif ($isPartial)
                                        <button @click="open = true" class="badge cursor-pointer bg-amber-100 text-amber-700 hover:bg-amber-200 transition">Partially Refunded</button>
                                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" style="display: none;" @click.outside="open = false">
                                            <div class="card w-full max-w-sm p-6" @click.stop>
                                                <h3 class="font-semibold">Refund details — {{ $payment->receipt_number }}</h3>
                                                <div class="mt-3 space-y-2 text-sm">
                                                    <div class="flex justify-between"><span class="text-muted">Refunded amount</span><span class="font-semibold text-brand-600">৳{{ number_format((float) $payment->refund_amount, 2) }}</span></div>
                                                    <div class="flex justify-between"><span class="text-muted">Original amount</span><span>৳{{ number_format((float) $payment->amount, 2) }}</span></div>
                                                    <div class="flex justify-between"><span class="text-muted">Remaining</span><span class="font-semibold text-ink">৳{{ number_format($remaining, 2) }}</span></div>
                                                    @if ($payment->refunded_at)<div class="flex justify-between"><span class="text-muted">Refunded on</span><span>{{ $payment->refunded_at->format('d M Y, h:i A') }}</span></div>@endif
                                                    @if ($payment->refund_reason)<div class="border-t border-brand-50 pt-2 mt-2"><span class="text-muted text-xs">Reason</span><p class="mt-1">{{ $payment->refund_reason }}</p></div>@endif
                                                </div>
                                                <div class="mt-4 text-right"><button @click="open = false" class="btn-outline">Close</button></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-accent-100 text-accent-700">Paid</span>
                                    @endif
                                </td>
                                <td class="table-cell text-right" x-data="{ open: false }">
                                    <a href="{{ route('payments.receipt', $payment) }}" class="btn-outline !px-3 !py-1.5 !text-xs" style="display: inline-flex;">Receipt</a>
                                    @if ($remaining > 0 && $payment->status === Payment::STATUS_PAID)
                                        <button @click="open = true" class="btn-outline !px-3 !py-1.5 !text-xs">Refund</button>
                                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" style="display: none;" @click.outside="open = false">
                                            <div class="card w-full max-w-md p-6" @click.stop>
                                                <h3 class="font-semibold">Process refund — {{ $payment->receipt_number }}</h3>
                                                <p class="mt-1 text-sm text-muted">Remaining refundable: <span class="font-semibold text-ink">৳{{ number_format($remaining, 2) }}</span></p>
                                                <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" class="mt-4 space-y-4">
                                                    @csrf
                                                    <div>
                                                        <label for="amount-{{ $payment->id }}" class="label">Amount (৳)</label>
                                                        <input id="amount-{{ $payment->id }}" type="number" name="amount" value="{{ $remaining }}" step="0.01" min="0.01" max="{{ $remaining }}" required class="input mt-1">
                                                    </div>
                                                    <div>
                                                        <label for="reason-{{ $payment->id }}" class="label">Reason</label>
                                                        <textarea id="reason-{{ $payment->id }}" name="reason" rows="3" required maxlength="500" placeholder="e.g., Duplicate charge, goodwill refund..." class="input mt-1">{{ old('reason') }}</textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="open = false" class="btn-outline">Cancel</button>
                                                        <button type="submit" class="btn-primary">Process refund</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-brand-100 px-5 py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-layouts.staff>
