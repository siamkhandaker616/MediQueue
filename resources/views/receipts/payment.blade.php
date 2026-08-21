<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $payment->receipt_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #111827; }
        .header { border-bottom: 2px solid #c34b79; padding-bottom: 12px; margin-bottom: 20px; }
        .clinic-name { font-size: 18px; font-weight: bold; color: #c34b79; margin: 0; }
        .clinic-sub { font-size: 11px; color: #6b7280; margin: 2px 0 0; }
        .meta { float: right; text-align: right; font-size: 11px; color: #6b7280; }
        .meta strong { font-size: 14px; color: #c34b79; }
        .grid { width: 100%; margin-bottom: 18px; }
        .grid td { padding: 2px 8px; vertical-align: top; }
        .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; }
        .value { font-weight: 600; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 4px; }
        table.totals { width: 60%; border-collapse: collapse; margin-left: auto; }
        table.totals td { padding: 5px 8px; }
        table.totals td.num { text-align: right; font-weight: 600; }
        table.totals tr.grand td { border-top: 1px solid #d1d5db; font-size: 14px; font-weight: bold; color: #c34b79; }
        .refund-box { border: 1px solid #fca5a5; background: #fef2f2; padding: 10px 12px; border-radius: 6px; font-size: 12px; }
        .status-paid { display: inline-block; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; }
        .status-refunded { display: inline-block; background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="meta">
            <strong>{{ $payment->receipt_number }}</strong><br>
            Issued {{ $payment->paid_at?->format('d M Y, h:i A') ?? now()->format('d M Y') }}
        </div>
        <p class="clinic-name">MediQueue Medical Center</p>
        <p class="clinic-sub">Outpatient Department &middot; Official Payment Receipt</p>
    </div>

    <table class="grid">
        <tr>
            <td width="50%">
                <div class="label">Billed to</div>
                <div class="value">{{ $payment->appointment->patient->name }}</div>
                <div style="color:#6b7280">{{ $payment->appointment->patient->email }}</div>
            </td>
            <td width="50%">
                <div class="label">Payment status</div>
                <span class="{{ $payment->status === \App\Models\Payment::STATUS_REFUNDED ? 'status-refunded' : 'status-paid' }}">
                    {{ strtoupper($payment->status) }}
                </span>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Appointment details</div>
        <table class="grid">
            <tr>
                <td width="50%">
                    <div class="label">Doctor</div>
                    <div class="value">{{ $payment->appointment->doctor->name }}</div>
                    <div style="color:#6b7280">{{ $payment->appointment->doctor->specialty }}</div>
                </td>
                <td width="50%">
                    <div class="label">Department</div>
                    <div class="value">{{ $payment->appointment->department->name ?? '—' }}</div>
                    <div style="color:#6b7280">{{ $payment->appointment->department?->locationLabel() }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Date &amp; time</div>
                    <div class="value">{{ $payment->appointment->date->format('l, j F Y') }} &middot; {{ $payment->appointment->time_slot }}</div>
                </td>
                <td>
                    <div class="label">Token number</div>
                    <div class="value">{{ $payment->appointment->token_number }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Transaction summary</div>
        <table class="totals">
            <tr>
                <td>Transaction ID</td>
                <td class="num">{{ $payment->transaction_id }}</td>
            </tr>
            <tr>
                <td>Payment method</td>
                <td class="num">{{ ucwords(str_replace('_', ' ', $payment->method)) }}</td>
            </tr>
            <tr>
                <td>Paid at</td>
                <td class="num">{{ $payment->paid_at?->format('d M Y, h:i A') ?? '—' }}</td>
            </tr>
            <tr class="grand">
                <td>Total paid</td>
                <td class="num">৳{{ number_format((float) $payment->amount, 2) }}</td>
            </tr>
        </table>
    </div>

    @if ((float) $payment->refund_amount > 0)
        <div class="section refund-box">
            <strong>Refund issued:</strong> ৳{{ number_format((float) $payment->refund_amount, 2) }}
            on {{ $payment->refunded_at?->format('d M Y, h:i A') }}
            @if ($payment->refund_reason)
                — Reason: {{ $payment->refund_reason }}
            @endif
        </div>
    @endif

    <div class="footer">
        This is a computer-generated receipt from MediQueue and does not require a signature.
        Keep it for your records — you can re-download it anytime from your visit history.
    </div>
</body>
</html>
