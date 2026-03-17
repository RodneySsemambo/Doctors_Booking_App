<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use App\Models\DoctorTimeslot;
use App\Models\Payment;
use App\Models\Notification;

class Timeslot extends Component
{
    public $timeslots = [];
    public $doctor;

    // Form fields
    public $day_of_week = 'monday';
    public $start_time;
    public $end_time;
    public $slot_duration = 30;
    public $max_patients_per_slot = 1;

    public $editingId = null;

    public $days = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday'
    ];

    public function mount()
    {
        $this->doctor = auth()->user()->doctor;
        $this->loadTimeslots();
        $this->start_time = '09:00';
        $this->end_time = '17:00';
    }

    public function loadTimeslots()
    {
        $this->timeslots = $this->doctor->timeslots()
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->orderBy('start_time')
            ->get();
    }

    public function saveTimeslot()
    {
        $this->validate([
            'day_of_week' => 'required|in:' . implode(',', array_keys($this->days)),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
            'max_patients_per_slot' => 'required|integer|min:1|max:10'
        ]);

        $data = [
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'slot_duration' => $this->slot_duration,
            'max_patients_per_slot' => $this->max_patients_per_slot,
            'is_active' => true
        ];

        if ($this->editingId) {
            $this->doctor->timeslots()->findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Timeslot updated successfully!');
        } else {
            $this->doctor->timeslots()->create($data);
            session()->flash('success', 'Timeslot added successfully!');
        }

        $this->resetForm();
        $this->loadTimeslots();
    }

    public function editTimeslot($id)
    {
        $timeslot = $this->doctor->timeslots()->findOrFail($id);

        $this->editingId = $timeslot->id;
        $this->day_of_week = $timeslot->day_of_week;
        $this->start_time = $timeslot->start_time->format('H:i');
        $this->end_time = $timeslot->end_time->format('H:i');
        $this->slot_duration = $timeslot->slot_duration;
        $this->max_patients_per_slot = $timeslot->max_patients_per_slot;
    }

    public function deleteTimeslot($id)
    {
        $this->doctor->timeslots()->findOrFail($id)->delete();
        session()->flash('success', 'Timeslot deleted successfully!');
        $this->loadTimeslots();
    }

    public function toggleStatus($id)
    {
        $timeslot = $this->doctor->timeslots()->findOrFail($id);
        $timeslot->update(['is_active' => !$timeslot->is_active]);
        $this->loadTimeslots();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->day_of_week = 'monday';
        $this->start_time = '09:00';
        $this->end_time = '17:00';
        $this->slot_duration = 30;
        $this->max_patients_per_slot = 1;
    }

    public function render()
    {
        return view('livewire.doctor.timeslots')
            ->layout('layouts.doctor', [
                'title' => 'Manage Timeslots',
                'todayAppointmentsCount' => $this->doctor->appointments()
                    ->whereDate('appointment_date', today())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count(),
                'monthlyEarnings' => Payment::whereHas('appointment', function ($q) {
                    $q->where('doctor_id', $this->doctor->id);
                })
                    ->where('status', 'completed')
                    ->whereMonth('completed_at', now()->month)
                    ->sum('amount'),
                'recentNotifications' => Notification::where('user_id', auth()->id())
                    ->latest('sent_at')
                    ->limit(5)
                    ->get(),
                'unreadNotificationsCount' => Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count()
            ]);
    }
}
