<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'appointment_id',
        'patient_id',
        'prescription_number',
        'medications',
        'diagnosis',
        'instructions',
        'valid_until',
        'is_dispensed',
        'dispensed_at'
    ];

    protected $casts = [
        'medications' => 'array',
        'valid_until' => 'date',
        'is_dispensed' => 'boolean',
        'dispensed_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Accessors & Mutators
     */
    public function getIsValidAttribute()
    {
        return !$this->is_dispensed && Carbon::parse($this->valid_until)->isFuture();
    }

    public function getIsExpiredAttribute()
    {
        return !$this->is_dispensed && Carbon::parse($this->valid_until)->isPast();
    }

    public function getDaysUntilExpiryAttribute()
    {
        return Carbon::now()->diffInDays($this->valid_until, false);
    }

    public function getMedicationsListAttribute()
    {
        if (is_string($this->medications)) {
            return json_decode($this->medications, true);
        }
        return $this->medications;
    }

    public function getMedicationCountAttribute()
    {
        $medications = $this->medications_list;
        return is_array($medications) ? count($medications) : 0;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_dispensed', false)
            ->where('valid_until', '>=', today());
    }

    public function scopeDispensed($query)
    {
        return $query->where('is_dispensed', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('is_dispensed', false)
            ->where('valid_until', '<', today());
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeExpiringWithinDays($query, $days = 7)
    {
        return $query->where('is_dispensed', false)
            ->whereBetween('valid_until', [
                today(),
                today()->addDays($days)
            ]);
    }
}
