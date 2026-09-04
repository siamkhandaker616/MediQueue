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
        'blood_group',
        'allergies',
        'chronic_conditions',
        'current_medications',
        'emergency_contact',
        'additional_notes',
        'notes',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'allergies'           => 'encrypted:array',
            'chronic_conditions'  => 'encrypted:array',
            'current_medications' => 'encrypted:array',
            'emergency_contact'   => 'encrypted:array',
            'last_updated'        => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Helper accessor to support both blood_type and blood_group
     */
    public function getBloodGroupAttribute(): ?string
    {
        return $this->attributes['blood_type'] ?? $this->attributes['blood_group'] ?? null;
    }

    public function setBloodGroupAttribute($value): void
    {
        $this->attributes['blood_type'] = $value;
        $this->attributes['blood_group'] = $value;
    }
}