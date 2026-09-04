<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'name',
        'email',
        'slug',
        'photo',
        'qualifications',
        'specialty',
        'specialties',
        'experience_years',
        'consultation_fee',
        'languages',
        'avg_rating',
        'rating_count',
        'bio',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'consultation_fee' => 'decimal:2',
            'avg_rating'       => 'decimal:2',
            'experience_years' => 'integer',
            'rating_count'     => 'integer',
            'is_active'        => 'boolean',
            'specialties'      => 'array',
            'languages'        => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Doctor $doctor) {
            if (empty($doctor->slug)) {
                $name = $doctor->name ?? optional($doctor->user)->name ?? 'doctor';
                $doctor->slug = Str::slug($name);
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(DoctorLeave::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /* -------------------------------------------------------------------------- */
    /*                              Rating Accessors                              */
    /* -------------------------------------------------------------------------- */

    public function getRatingAvgAttribute(): float
    {
        if ($this->relationLoaded('reviews') || $this->reviews()->exists()) {
            return round((float) ($this->reviews()->where('is_visible', true)->avg('overall_rating') ?? 0.0), 1);
        }

        return (float) ($this->attributes['avg_rating'] ?? 0.0);
    }

    public function getRatingCountAttribute(): int
    {
        if ($this->relationLoaded('reviews') || $this->reviews()->exists()) {
            return $this->reviews()->where('is_visible', true)->count();
        }

        return (int) ($this->attributes['rating_count'] ?? 0);
    }

    /* -------------------------------------------------------------------------- */
    /*                           Scopes & Route Binding                           */
    /* -------------------------------------------------------------------------- */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('specialty', 'like', "%{$term}%")
              ->orWhere('specialties', 'like', "%{$term}%")
              ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$term}%"));
        });
    }

    public function scopeInDepartment(Builder $query, ?int $departmentId): Builder
    {
        return $departmentId ? $query->where('department_id', $departmentId) : $query;
    }

    public function scopeMinRating(Builder $query, ?float $rating): Builder
    {
        return $rating ? $query->where('avg_rating', '>=', $rating) : $query;
    }

    /* -------------------------------------------------------------------------- */
    /*                                Helper Methods                              */
    /* -------------------------------------------------------------------------- */

    public function getDisplayNameAttribute(): ?string
    {
        return $this->name ?? $this->user?->name;
    }

    public function photoUrl(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/doctor-placeholder.png');
    }
}