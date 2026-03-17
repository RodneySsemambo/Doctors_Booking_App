<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorScheduling extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorSchedulingFactory> */
    use HasFactory;

    protected $fillable = [
        'slot_duration',
        'is_available',
        'doctor_id',
        'day_of_the_week',
        'start_time',
        'end_time'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, foreignKey: 'doctor_id');
    }
}
