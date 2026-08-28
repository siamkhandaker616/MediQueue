<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_id', 'punctuality_rating',
        'communication_rating', 'knowledge_rating', 'overall_rating', 'comment', 'is_visible',
    ];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->syncDoctorRating();
        });

        static::deleted(function (Review $review) {
            $review->syncDoctorRating();
        });
    }

    public function syncDoctorRating(): void
    {
        if (! isset($this->doctor_id)) {
            return;
        }

        $doctor = $this->doctor()->firstOrFail();
        $visible = $doctor->reviews()->where('is_visible', true);

        $doctor->update([
            'avg_rating'   => round((float) ($visible->avg('overall_rating') ?? 0.0), 2),
            'rating_count' => $visible->count(),
        ]);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
