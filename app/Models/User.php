<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public const ROLES = ['patient', 'doctor', 'admin'];

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function doctor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function medicalProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PatientMedicalProfile::class, 'patient_id');
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function medicalReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MedicalReport::class, 'patient_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
