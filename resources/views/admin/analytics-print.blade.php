<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Analytics report — {{ $rangeLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        .topbar { display: none; }
        h1 { font-size: 18px; margin: 0; }
        .sub { color: #6b7280; font-size: 11px; margin: 2px 0 0; }
        .header { border-bottom: 2px solid #4338ca; padding-bottom: 10px; margin-bottom: 16px; }
        .stat-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .stat-table td { border: 1px solid #e5e7eb; padding: 10px; text-align: center; }
        .stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; }
        .stat-value { font-size: 18px; font-weight: bold; color: #4338ca; }
        .grid-2 { display: table; width: 100%; border-spacing: 12px 0; margin: 0 -12px 16px; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; margin-bottom: 8px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { text-align: left; font-size: 10px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #d1d5db; padding: 5px 6px; }
        table.data td { padding: 5px 6px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .right { text-align: right; }
        .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        @media print {
            .topbar { display: block; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="topbar" style="margin-bottom:12px;">
        <button onclick="window.print()" style="padding:6px 14px;border:1px solid #d1d5db;border-radius:6px;background:#fff;cursor:pointer;">Print</button>
    </div>

    <div class="header">
        <div style="float:right;text-align:right;font-size:11px;color:#6b7280;">Generated {{ now()->format('d M Y, g:i a') }}</div>
        <h1>MediQueue Medical Center — Analytics report</h1>
        <p class="sub">{{ $rangeLabel }}</p>
    </div>

    <table class="stat-table">
        <tr>
            <td><div class="stat-label">Appointments</div><div class="stat-value">{{ $appointments }}</div></td>
            <td><div class="stat-label">Revenue</div><div class="stat-value">৳{{ number_format($revenue) }}</div></td>
            <td><div class="stat-label">Cancellation rate</div><div class="stat-value">{{ $cancellationRate }}%</div></td>
            <td><div class="stat-label">Departments</div><div class="stat-value">{{ $byDepartment->count() }}</div></td>
            <td><div class="stat-label">Refunds</div><div class="stat-value">৳{{ number_format($refundAmount) }} ({{ $refundCount }})</div></td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Revenue by method</div>
        <table class="data">
            <thead>
                <tr><th>Method</th><th class="right">Amount</th></tr>
            </thead>
            <tbody>
                @forelse ($revenueByMethod as $row)
                    <tr>
                        <td>{{ $row['method'] }}</td>
                        <td class="right">৳{{ number_format($row['total']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No payments in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Appointments by department</div>
        <table class="data">
            <thead>
                <tr><th>Department</th><th class="right">Appointments</th></tr>
            </thead>
            <tbody>
                @forelse ($byDepartment as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td class="right">{{ $row['count'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No appointments in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Appointment status</div>
        <table class="data">
            <thead>
                <tr><th>Status</th><th class="right">Count</th></tr>
            </thead>
            <tbody>
                @foreach ($statusBreakdown as $status => $count)
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $status)) }}</td>
                        <td class="right">{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Doctor performance</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th class="right">Appointments</th>
                    <th class="right">Avg rating</th>
                    <th class="right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($doctors as $doctor)
                    <tr>
                        <td>{{ $doctor['name'] }}</td>
                        <td>{{ $doctor['department'] }}</td>
                        <td class="right">{{ $doctor['appointments'] }}</td>
                        <td class="right">{{ $doctor['rating'] ?? '—' }}</td>
                        <td class="right">৳{{ number_format($doctor['revenue']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No doctor data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">MediQueue Medical Center · Outpatient Department · This report is generated by the MediQueue admin analytics module.</div>
</body>
</html>
