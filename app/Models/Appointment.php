<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use League\Uri\Components\Path;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'doctor_id',
        'patient_id',
        'hospital_id',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'status',
        'reason_for_visit',
        'symptoms',
        'notes',
        'payment_status',
        'consultation_fee',
        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',
        'reminded_at',
        'completed_at'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, foreignKey: 'hospital_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, foreignKey: 'patient_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function medicalRecord(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
