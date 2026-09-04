<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'patient_id',
        'punctuality_rating',
        'communication_rating',
        'knowledge_rating',
        'overall_rating',
        'comment',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'punctuality_rating' => 'integer',
            'communication_rating' => 'integer',
            'knowledge_rating'   => 'integer',
            'overall_rating'     => 'integer',
            'is_visible'         => 'boolean',
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
}