<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MedicalReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'report_type',
        'report_date',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'file_size'   => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getIsPdfAttribute(): bool
    {
        return str_contains($this->file_type ?? '', 'pdf') || str_ends_with(strtolower($this->file_name), '.pdf');
    }

    public function getDisplayTitleAttribute(): string
    {
        return ucfirst($this->report_type) . ' - ' . $this->file_name;
    }
}