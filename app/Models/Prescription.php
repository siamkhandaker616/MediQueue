<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'patient_id',
        'diagnosis',
        'investigation',
        'follow_up_date',
        'dietary_advice',
        'doctor_notes',
        'is_editable',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'is_editable'    => 'boolean',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function getSymptomsAttribute(): ?string
    {
        return $this->attributes['diagnosis'] ?? null;
    }

    public function getMedicinesAttribute(): array
    {
        return $this->items->map(fn (PrescriptionItem $item) => [
            'name'         => $item->medication_name,
            'dosage'       => $item->dosage,
            'frequency'    => $item->frequency,
            'duration'     => $item->duration,
            'instructions' => $item->instructions,
        ])->toArray();
    }

    public function getTestsRecommendedAttribute(): ?string
    {
        return $this->attributes['investigation'] ?? null;
    }

    public function getAdviceAttribute(): ?string
    {
        return $this->attributes['dietary_advice'] ?? null;
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