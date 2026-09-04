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
        'symptoms',
        'medicines',
        'tests_recommended',
        'advice',
    ];

    protected function casts(): array
    {
        return [
            'medicines'         => 'array',
            'tests_recommended' => 'array',
            'follow_up_date'    => 'date',
            'is_editable'       => 'boolean',
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

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function getMedicinesAttribute($value): array
    {
        if (!empty($value)) {
            return is_string($value) ? (json_decode($value, true) ?: []) : (array) $value;
        }

        if ($this->relationLoaded('items') || $this->items()->exists()) {
            return $this->items->map(function ($item) {
                return [
                    'name'         => $item->medication_name,
                    'medicine'     => $item->medication_name,
                    'dosage'       => $item->dosage,
                    'frequency'    => $item->frequency,
                    'duration'     => $item->duration,
                    'instructions' => $item->instructions,
                    'timing'       => $item->instructions,
                ];
            })->toArray();
        }

        return [];
    }

    public function getAdviceAttribute($value): ?string
    {
        return $value ?: $this->dietary_advice ?: $this->doctor_notes;
    }

    public function getPrescriptionNumberAttribute(): string
    {
        return 'RX-' . date('Y') . '-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}