<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory;
    protected $casts = [
        'is_available' => 'array',
        'is_verified' => 'boolean',
        'languages_spoken' => 'array',
    ];
    protected $fillable = [
        'user_id',
        'specialization',
        'first_name',
        'last_name',
        'license_number',
        'years_of_experience',
        'qualification',
        'bio',
        'profile_photo',
        'consultation_fee',
        'rating',
        'total_reviews',
        'hospital_affiliation',
        'video_consultation_available',
        'languages_spoken',
        'is_verified',
        "is_available",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function hospital(): BelongsToMany
    {
        return $this->belongsToMany(Hospital::class, 'doctor_hospital');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorScheduling::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function timeslots()
    {
        return $this->hasMany(DoctorTimeslot::class);
    }

    public function availableSlots($date)
    {
        $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->format('l'));

        $timeslots = $this->timeslots()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        $allSlots = [];
        foreach ($timeslots as $timeslot) {
            $slots = $timeslot->generateSlotsForDate($date);

            foreach ($slots as $slot) {
                // Check if slot is already booked
                $existingAppointments = $this->appointments()
                    ->whereDate('appointment_date', $date)
                    ->whereTime('appointment_time', $slot['start'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();

                if ($existingAppointments < $timeslot->max_patients_per_slot) {
                    $allSlots[] = [
                        'timeslot_id' => $timeslot->id,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                        'display_time' => $slot['display'],
                        'date' => $date,
                        'available' => true
                    ];
                }
            }
        }

        return collect($allSlots)->sortBy('start_time')->values();
    }
}
