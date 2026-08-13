<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'appointment_id', 'file_path', 'file_name',
        'file_type', 'file_size', 'report_type', 'report_date',
    ];

    protected function casts(): array
    {
        return ['report_date' => 'date'];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
