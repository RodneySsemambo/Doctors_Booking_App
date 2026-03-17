<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalRecordsFactory> */
    use HasFactory;

    protected $table = 'medical__records';
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'record_type',
        'title',
        'description',
        'file_path',
        'file_type',
        'recorded_by',
        'recorded_date'
    ];

    protected $casts = [
        'recorded_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, foreignKey: 'patient_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, foreignKey: 'appointment_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
