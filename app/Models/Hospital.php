<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    /** @use HasFactory<\Database\Factories\HospitalFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'address',
        'phone',
        'email',
        'country',
        'latitude',
        'longtitude',
        'facilities',
        'rating',
        'is_active'
    ];
    public function doctor(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_hospital');
    }

    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
