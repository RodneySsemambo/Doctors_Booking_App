<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class Appointments extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dateFilter = '';
    public $perPage = 10;
    public $sortBy = 'appointment_date';
    public $sortDirection = 'desc';

    public $showModal = false;
    public $selectedAppointment = null;
    public $doctor;

    public function mount()
    {
        $this->doctor = auth()->user()->doctor;
    }

    // Make it a regular method instead of computed property
    public function getAppointments()
    {
        $query = $this->doctor->appointments()
            ->with(['patient.user'])
            ->when($this->search, function ($q) {
                $q->whereHas('patient', function ($q2) {
                    $q2->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($q3) {
                            $q3->where('email', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->dateFilter, function ($q) {
                $q->whereDate('appointment_date', $this->dateFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmAppointment($id)
    {
        $appointment = $this->doctor->appointments()->findOrFail($id);
        $appointment->update(['status' => 'confirmed']);

        $this->dispatch('appointment-confirmed');
        session()->flash('success', 'Appointment confirmed successfully!');
    }

    public function cancelAppointment($id)
    {
        $appointment = $this->doctor->appointments()->findOrFail($id);
        $appointment->update(['status' => 'cancelled']);

        $this->dispatch('appointment-cancelled');
        session()->flash('success', 'Appointment cancelled successfully!');
    }

    public function completeAppointment($id)
    {
        $appointment = $this->doctor->appointments()->findOrFail($id);
        $appointment->update(['status' => 'completed']);

        $this->dispatch('appointment-completed');
        session()->flash('success', 'Appointment marked as completed!');
    }

    public function viewDetails($id)
    {
        $this->selectedAppointment = $this->doctor->appointments()
            ->with(['patient.user', 'payments'])
            ->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedAppointment = null;
    }

    public function render()
    {
        return view('livewire.doctor.appointments', [
            'appointments' => $this->getAppointments(),
        ])->layout('layouts.doctor', [
            'title' => 'Appointments',
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
