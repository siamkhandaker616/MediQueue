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

    /*
    |--------------------------------------------------------------------------
    | Prescription edit grace window (FR-14)
    |--------------------------------------------------------------------------
    | Minutes after a prescription is created during which the composing doctor
    | may edit it before it is locked.
    */

    'prescription_edit_grace_minutes' => 60,

];
