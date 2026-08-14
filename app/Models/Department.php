<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'room_location',
        'floor_number',
        'room_number',
        'fee_min',
        'fee_max',
        'fee_range',
        'is_active',
    ];

    /**
     * Attribute casting (supports Laravel 10/11 method syntax).
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'fee_min'   => 'decimal:2',
            'fee_max'   => 'decimal:2',
        ];
    }

    /**
     * Auto-generate slug from name if not provided.
     */
    protected static function booted(): void
    {
        static::saving(function (Department $department) {
            if (empty($department->slug) && !empty($department->name)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function activeDoctors(): HasMany
    {
        return $this->doctors()->where('is_active', true);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /* -------------------------------------------------------------------------- */
    /*                             Route & Query Scopes                           */
    /* -------------------------------------------------------------------------- */

    /**
     * Route model binding by slug (/departments/{slug}) instead of ID.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * FR-01: Search scope for live catalogue filtering.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                               Helper Methods                               */
    /* -------------------------------------------------------------------------- */

    /**
     * Dynamic label for consultation fee range.
     * Falls back to legacy fee_range string if min/max are not set.
     */
    public function feeRangeLabel(): string
    {
        if (!empty($this->fee_min) || !empty($this->fee_max)) {
            if ($this->fee_min == $this->fee_max) {
                return '৳' . number_format((float) $this->fee_min, 0);
            }
            return '৳' . number_format((float) $this->fee_min, 0) . ' - ৳' . number_format((float) $this->fee_max, 0);
        }

        return $this->fee_range ?: 'Fee upon consultation';
    }

    /**
     * Formats room location from room_location or floor/room numbers.
     */
    public function locationLabel(): string
    {
        if (!empty($this->room_location)) {
            return $this->room_location;
        }

        if (!empty($this->floor_number) || !empty($this->room_number)) {
            return "Floor {$this->floor_number}, Room {$this->room_number}";
        }

        return 'Location TBA';
    }
}