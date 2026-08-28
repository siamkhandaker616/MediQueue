<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification
{
    public function __construct(
        private readonly Appointment $appointment,
        private readonly int $hoursBefore,
    ) {}

    /**
     * FR-17: audit log entry for a dispatched appointment reminder.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'hours_before' => $this->hoursBefore,
            'doctor_name' => $this->appointment->doctor->name,
            'department_name' => $this->appointment->department->name,
            'date' => $this->appointment->date->toDateString(),
            'time_slot' => $this->appointment->time_slot,
        ];
    }
}
