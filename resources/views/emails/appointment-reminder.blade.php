<!DOCTYPE html>
<html>
<body style="margin:0; padding:24px; background:#fcf6f9; font-family: Arial, sans-serif; color:#3f3438;">
    <div style="max-width:560px; margin:0 auto; background:#fae8ee; border:1px solid #fbe1ea; border-radius:16px; overflow:hidden;">
        <div style="background:linear-gradient(135deg, #e684ac, #c34b79); padding:20px 28px;">
            <h1 style="margin:0; font-size:20px; color:#ffffff;">MediQueue — Appointment Reminder</h1>
        </div>

        <div style="padding:28px;">
            <p>Hello {{ $appointment->patient->name }},</p>

            <p>Your appointment is in about <strong>{{ $hoursBefore }} hours</strong>. Here are the details:</p>

            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <tr><td style="padding:6px 0; color:#82747b;">Doctor</td><td style="padding:6px 0;"><strong>{{ $appointment->doctor->name }}</strong></td></tr>
                <tr><td style="padding:6px 0; color:#82747b;">Department</td><td style="padding:6px 0;">{{ $appointment->department->name }}</td></tr>
                <tr><td style="padding:6px 0; color:#82747b;">Date</td><td style="padding:6px 0;">{{ $appointment->date->format('l, j F Y') }}</td></tr>
                <tr><td style="padding:6px 0; color:#82747b;">Time</td><td style="padding:6px 0;">{{ $appointment->time_slot }}</td></tr>
                <tr><td style="padding:6px 0; color:#82747b;">Location</td><td style="padding:6px 0;">{{ $appointment->department->locationLabel() }}</td></tr>
                <tr><td style="padding:6px 0; color:#82747b;">Token number</td><td style="padding:6px 0;"><strong>{{ $appointment->token_number }}</strong></td></tr>
            </table>

            <p>Please arrive 15 minutes early and check in at the department desk to confirm your queue token.</p>

            <p>Thanks,<br>{{ config('app.name', 'MediQueue') }}</p>
        </div>
    </div>
</body>
</html>
