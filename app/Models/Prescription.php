<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'patient_id',
        'diagnosis',
        'symptoms',
        'medicines',
        'tests_recommended',
        'advice',
        'follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'medicines'         => 'array',
            'tests_recommended' => 'array',
            'follow_up_date'    => 'date',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function getPrescriptionNumberAttribute(): string
    {
        return 'RX-' . date('Y') . '-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}