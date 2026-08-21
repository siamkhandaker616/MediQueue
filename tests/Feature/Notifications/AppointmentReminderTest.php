<?php

namespace Tests\Feature\Notifications;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppointmentReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-08-21 09:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_reminder_is_sent_within_24h_window(): void
    {
        $appointment = $this->makeAppointment('2026-08-22', '08:00');

        Mail::fake();
        $this->artisan('appointments:send-reminders')->expectsOutput('Sent 1 appointment reminder(s).');

        Mail::assertSent(AppointmentReminderMail::class, function (AppointmentReminderMail $mail) use ($appointment) {
            return $mail->hasTo($appointment->patient->email)
                && $mail->hoursBefore === 24
                && $mail->appointment->is($appointment);
        });

        $this->assertTrue($this->hasReminderLog($appointment->patient, $appointment->id, 24));
    }

    public function test_reminder_is_not_duplicated_on_second_run(): void
    {
        $appointment = $this->makeAppointment('2026-08-22', '08:00');

        Mail::fake();
        $this->artisan('appointments:send-reminders');
        $this->artisan('appointments:send-reminders');

        Mail::assertSent(AppointmentReminderMail::class, 1);
    }

    public function test_two_hour_stage_is_sent_closer_to_appointment(): void
    {
        $appointment = $this->makeAppointment('2026-08-21', '10:30');

        Mail::fake();
        $this->artisan('appointments:send-reminders');

        Mail::assertSent(AppointmentReminderMail::class, fn (AppointmentReminderMail $mail) => $mail->hoursBefore === 2);
        $this->assertTrue($this->hasReminderLog($appointment->patient, $appointment->id, 2));
    }

    public function test_far_future_appointments_are_skipped(): void
    {
        $this->makeAppointment('2026-08-25', '09:00');

        Mail::fake();
        $this->artisan('appointments:send-reminders');

        Mail::assertNothingSent();
    }

    public function test_cancelled_and_completed_appointments_are_skipped(): void
    {
        $cancelled = $this->makeAppointment('2026-08-22', '08:00', Appointment::STATUS_CANCELLED);
        $completed = $this->makeAppointment('2026-08-22', '08:30', Appointment::STATUS_COMPLETED);

        Mail::fake();
        $this->artisan('appointments:send-reminders');

        Mail::assertNothingSent();
        $this->assertFalse($this->hasReminderLog($cancelled->patient, $cancelled->id, 24));
        $this->assertFalse($this->hasReminderLog($completed->patient, $completed->id, 24));
    }

    private function makeAppointment(string $date, string $slot, string $status = Appointment::STATUS_SCHEDULED): Appointment
    {
        return Appointment::factory()->create([
            'doctor_id' => Doctor::factory(),
            'date' => $date,
            'time_slot' => $slot,
            'status' => $status,
        ]);
    }

    private function hasReminderLog(User $patient, int $appointmentId, int $stage): bool
    {
        return $patient->notifications()
            ->where('type', AppointmentReminder::class)
            ->where('data->appointment_id', $appointmentId)
            ->where('data->hours_before', $stage)
            ->exists();
    }
}
