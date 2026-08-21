<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    /* -------------------------------------------------------------------------- */
    /*                              Status Constants                              */
    /* -------------------------------------------------------------------------- */

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    /* -------------------------------------------------------------------------- */
    /*                                Mass Assignment                             */
    /* -------------------------------------------------------------------------- */

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'department_id',
        'date',
        'time_slot',
        'status',
        'token_number',
        'queue_position',
        'estimated_wait_minutes',
        'fee',
        'payment_status',
        'cancellation_reason',
        'notes',
        'symptoms',
    ];

    protected function casts(): array
    {
        return [
            'date'                   => 'date',
            'fee'                    => 'decimal:2',
            'queue_position'         => 'integer',
            'estimated_wait_minutes' => 'integer',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

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

    /* -------------------------------------------------------------------------- */
    /*                         Queue & Token Accessors (FR-04)                    */
    /* -------------------------------------------------------------------------- */

    /**
     * Dynamic queue position based on date and time_slot order.
     */
    public function getQueuePositionAttribute(): int
    {
        if (isset($this->attributes['queue_position']) && $this->attributes['queue_position'] > 0) {
            return (int) $this->attributes['queue_position'];
        }

        return $this->newQuery()
            ->where('doctor_id', $this->doctor_id)
            ->where('date', $this->date)
            ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CHECKED_IN])
            ->where('time_slot', '<=', $this->time_slot)
            ->count();
    }

    /**
     * Dynamic unique token code (e.g. CARD-0004 or stored token_number).
     */
    public function getTokenNumberAttribute(): string
    {
        if (!empty($this->attributes['token_number'])) {
            return $this->attributes['token_number'];
        }

        return strtoupper(substr($this->department->slug ?? 'DEPT', 0, 4)) . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Estimated wait time in minutes (~15 min per patient ahead in queue).
     */
    public function getEstimatedWaitMinutesAttribute(): int
    {
        if (isset($this->attributes['estimated_wait_minutes']) && $this->attributes['estimated_wait_minutes'] > 0) {
            return (int) $this->attributes['estimated_wait_minutes'];
        }

        $pos = $this->queue_position;
        return $pos > 1 ? ($pos - 1) * 15 : 0;
    }

    /* -------------------------------------------------------------------------- */
    /*                                Query Scopes                                */
    /* -------------------------------------------------------------------------- */

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CHECKED_IN])
            ->orderBy('date')
            ->orderBy('time_slot');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('date', now()->toDateString());
    }
}