<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'patient_id', 'doctor_id', 'department_id', 'date', 'time_slot', 'status', 'cancellation_reason', 'notes',
    ];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function medicalReports(): HasMany
    {
        return $this->hasMany(MedicalReport::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function getQueuePositionAttribute(): int
    {
        return $this->newQuery()
            ->where('doctor_id', $this->doctor_id)
            ->where('date', $this->date)
            ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CHECKED_IN])
            ->where('time_slot', '<=', $this->time_slot)
            ->count();
    }

    public function getTokenNumberAttribute(): string
    {
        return strtoupper(substr($this->department->slug ?? 'DEPT', 0, 4)).'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
