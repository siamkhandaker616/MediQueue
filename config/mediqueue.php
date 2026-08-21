<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Appointment reminder stages (FR-17)
    |--------------------------------------------------------------------------
    | Hours before an appointment at which reminder emails are sent.
    | The scheduler runs hourly; each stage is sent once per appointment.
    */

    'reminder_hours' => [24, 2],

];
