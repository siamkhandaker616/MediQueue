<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #111827; }
        .header { border-bottom: 2px solid #4338ca; padding-bottom: 12px; margin-bottom: 20px; }
        .clinic-name { font-size: 18px; font-weight: bold; color: #4338ca; margin: 0; }
        .clinic-sub { font-size: 11px; color: #6b7280; margin: 2px 0 0; }
        .rx { float: right; text-align: right; font-size: 11px; color: #6b7280; }
        .rx strong { font-size: 14px; color: #4338ca; }
        .grid { width: 100%; margin-bottom: 18px; }
        .grid td { padding: 2px 8px; }
        .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; }
        .value { font-weight: 600; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 4px; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { text-align: left; font-size: 10px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #d1d5db; padding: 6px; }
        table.items td { padding: 6px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .signature { margin-top: 36px; text-align: right; }
        .sig-name { font-weight: bold; color: #4338ca; }
        .sig-line { border-top: 1px solid #6b7280; margin-top: 4px; padding-top: 2px; font-size: 10px; color: #6b7280; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="rx">
            <strong>RX-{{ str_pad((string) $prescription->id, 6, '0', STR_PAD_LEFT) }}</strong><br>
            {{ $prescription->created_at->format('d M Y') }}
        </div>
        <p class="clinic-name">MediQueue Medical Center</p>
        <p class="clinic-sub">Outpatient Department &middot; Digital Prescription</p>
    </div>

    <table class="grid">
        <tr>
            <td width="50%">
                <div class="label">Patient</div>
                <div class="value">{{ $prescription->patient->name }}</div>
                <div style="color:#6b7280">{{ $prescription->patient->email }}</div>
            </td>
            <td width="50%">
                <div class="label">Doctor</div>
                <div class="value">{{ $prescription->doctor->name }}</div>
                <div style="color:#6b7280">{{ $prescription->doctor->qualifications }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Diagnosis</div>
        <div>{{ $prescription->diagnosis }}</div>
        @if ($prescription->investigation)
            <div class="section-title" style="margin-top:10px">Investigations</div>
            <div>{{ $prescription->investigation }}</div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Medications</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescription->items as $item)
                    <tr>
                        <td><strong>{{ $item->medication_name }}</strong></td>
                        <td>{{ $item->dosage }}</td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ $item->duration ?? '—' }}</td>
                        <td>{{ $item->instructions ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($prescription->dietary_advice || $prescription->follow_up_date)
        <table class="grid">
            <tr>
                @if ($prescription->dietary_advice)
                    <td width="50%">
                        <div class="label">Dietary advice</div>
                        <div>{{ $prescription->dietary_advice }}</div>
                    </td>
                @endif
                @if ($prescription->follow_up_date)
                    <td width="50%">
                        <div class="label">Follow-up</div>
                        <div>{{ $prescription->follow_up_date->format('d M Y') }}</div>
                    </td>
                @endif
            </tr>
        </table>
    @endif

    @if ($prescription->doctor_notes)
        <div class="section">
            <div class="section-title">Notes</div>
            <div>{{ $prescription->doctor_notes }}</div>
        </div>
    @endif

    <div class="signature">
        <div class="sig-name">{{ $prescription->doctor->name }}</div>
        <div class="sig-line">Signature &amp; stamp</div>
    </div>

    <div class="footer">This is a digitally generated prescription. Medicine should be taken only as directed by the physician.</div>
</body>
</html>
