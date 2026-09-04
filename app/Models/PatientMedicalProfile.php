<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientMedicalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'blood_type',
        'allergies',
        'chronic_conditions',
        'current_medications',
        'emergency_contact',
        'additional_notes',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'allergies'           => 'array',
            'chronic_conditions'  => 'array',
            'current_medications' => 'array',
            'emergency_contact'   => 'array',
            'last_updated'        => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Helper for blood group display
    public function getBloodGroupAttribute(): ?string
    {
        return $this->blood_type;
    }
}