<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Patient;

class Patients extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $doctor;

    // Modal properties
    public $showPatientModal = false;
    public $selectedPatient = null;
    public $patientStats = [];

    public function mount()
    {
        $this->doctor = auth()->user()->doctor;
    }

    public function getPatients()
    {
        $query = Patient::whereHas('appointments', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })
            ->with(['user', 'appointments' => function ($q) {
                $q->where('doctor_id', $this->doctor->id);
            }])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($q3) {
                            $q3->where('email', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter === 'active', function ($q) {
                $q->whereHas('appointments', function ($q2) {
                    $q2->where('doctor_id', $this->doctor->id)
                        ->where('status', 'confirmed');
                });
            })
            ->when($this->statusFilter === 'inactive', function ($q) {
                $q->whereDoesntHave('appointments', function ($q2) {
                    $q2->where('doctor_id', $this->doctor->id)
                        ->where('created_at', '>', now()->subMonths(6));
                });
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

    public function viewPatient($id)
    {
        $this->selectedPatient = Patient::with(['user', 'appointments' => function ($q) {
            $q->where('doctor_id', $this->doctor->id)
                ->orderBy('appointment_date', 'desc');
        }])->findOrFail($id);

        // Calculate patient statistics
        $this->patientStats = [
            'total_appointments' => $this->selectedPatient->appointments->count(),
            'completed_appointments' => $this->selectedPatient->appointments
                ->where('status', 'completed')->count(),
            'pending_appointments' => $this->selectedPatient->appointments
                ->where('status', 'pending')->count(),
            'cancelled_appointments' => $this->selectedPatient->appointments
                ->where('status', 'cancelled')->count(),
            'last_appointment' => $this->selectedPatient->appointments->first(),
            'total_spent' => Payment::whereHas('appointment', function ($q) {
                $q->where('patient_id', $this->selectedPatient->id)
                    ->where('doctor_id', $this->doctor->id);
            })->where('status', 'completed')->sum('amount')
        ];

        $this->showPatientModal = true;
    }

    public function closeModal()
    {
        $this->showPatientModal = false;
        $this->selectedPatient = null;
        $this->patientStats = [];
    }

    public function getStatistics()
    {
        $totalPatients = Patient::whereHas('appointments', function ($q) {
            $q->where('doctor_id', $this->doctor->id);
        })->count();

        $activePatients = Patient::whereHas('appointments', function ($q) {
            $q->where('doctor_id', $this->doctor->id)
                ->where('status', 'confirmed')
                ->where('appointment_date', '>=', now()->subMonths(1));
        })->count();

        $newPatients = Patient::whereHas('appointments', function ($q) {
            $q->where('doctor_id', $this->doctor->id)
                ->where('created_at', '>=', now()->subDays(30));
        })->count();

        return [
            'total' => $totalPatients,
            'active' => $activePatients,
            'new' => $newPatients,
            'inactive' => $totalPatients - $activePatients
        ];
    }

    public function render()
    {
        $statistics = $this->getStatistics();

        return view('livewire.doctor.patient', [
            'patients' => $this->getPatients(),
            'statistics' => $statistics
        ])->layout('layouts.doctor', [
            'title' => 'Patients',
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
