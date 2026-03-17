<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
        'gender',
        'date_of_birth',
        'allergies',
        'medical_history',
        'emergency_name',
        'emergency_phone',
        'blood_group',
        'country',
        'address',
        'city',
        'profile_photo'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,  'user_id', 'id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function prescription(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function medical_records(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
