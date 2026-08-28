<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send appointment reminder emails at configured intervals before appointments (FR-17)';

    public function handle(): int
    {
        $stages = config('mediqueue.reminder_hours', [24, 2]);
        $now = now();
        $sent = 0;

        $appointments = Appointment::query()
            ->with(['patient', 'doctor', 'department'])
            ->whereIn('status', [Appointment::STATUS_SCHEDULED, Appointment::STATUS_CHECKED_IN])
            ->whereBetween('date', [$now->copy()->subDay()->toDateString(), $now->copy()->addDays(ceil(max($stages) / 24) + 1)->toDateString()])
            ->get();

        foreach ($appointments as $appointment) {
            if ($appointment->patient === null) {
                continue;
            }

            $startsAt = Carbon::parse($appointment->date->format('Y-m-d').' '.$appointment->time_slot);

            foreach ($stages as $stage) {
                if ($startsAt->lessThanOrEqualTo($now)) {
                    continue;
                }

                if ($startsAt->greaterThan($now->copy()->addHours((int) $stage))) {
                    continue;
                }

                if ($this->alreadyReminded($appointment, (int) $stage)) {
                    continue;
                }

                Mail::to($appointment->patient->email)
                    ->send(new AppointmentReminderMail($appointment, (int) $stage));

                $appointment->patient->notify(new AppointmentReminder($appointment, (int) $stage));

                $sent++;
            }
        }

        $this->info("Sent {$sent} appointment reminder(s).");

        return self::SUCCESS;
    }

    private function alreadyReminded(Appointment $appointment, int $stage): bool
    {
        return $appointment->patient->notifications()
            ->where('type', AppointmentReminder::class)
            ->where('data->appointment_id', $appointment->id)
            ->where('data->hours_before', $stage)
            ->exists();
    }
}
