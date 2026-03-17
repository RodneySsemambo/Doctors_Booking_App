<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorTimeslot extends Model
{
    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'max_patients_per_slot',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getTimeRangeAttribute()
    {
        return \Carbon\Carbon::parse($this->start_time)->format('h:i A') . ' - ' .
            \Carbon\Carbon::parse($this->end_time)->format('h:i A');
    }

    public function generateSlotsForDate($date)
    {
        $slots = [];
        $currentTime = \Carbon\Carbon::parse($this->start_time);
        $endTime = \Carbon\Carbon::parse($this->end_time);
        $date = \Carbon\Carbon::parse($date);

        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($this->slot_duration);

            if ($slotEnd->lte($endTime)) {
                $slots[] = [
                    'start' => $currentTime->format('H:i:s'),
                    'end' => $slotEnd->format('H:i:s'),
                    'display' => $currentTime->format('h:i A') . ' - ' . $slotEnd->format('h:i A')
                ];
            }

            $currentTime->addMinutes($this->slot_duration);
        }

        return $slots;
    }
}
