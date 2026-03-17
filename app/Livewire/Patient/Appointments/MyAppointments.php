<?php

namespace App\Livewire\Patient\Appointments;

use App\Models\Appointment;
use App\Models\Patient;
use App\Services\AppointmentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class MyAppointments extends Component
{
    public string $activeTab           = 'upcoming';
    public bool   $showCancelModal     = false;
    public ?int   $appointmentToCancel = null;
    public string $cancellationReason  = '';
    public string $searchTerm          = '';
    public string $statusFilter        = 'all';

    protected AppointmentService $appointmentService;

    public function boot(AppointmentService $appointmentService): void
    {
        $this->appointmentService = $appointmentService;
    }

    // ------------------------------------------------------------------ helpers


    protected function getPatientId(): ?int
    {
        return Patient::where('user_id', Auth::id())->value('id');
    }


    protected function doctorName($doctor): string
    {
        if (!$doctor) return 'Doctor';

        try {
            if (!empty($doctor->full_name)) return $doctor->full_name;
        } catch (\Throwable $e) {
        }

        if (!empty($doctor->first_name)) {
            return trim($doctor->first_name . ' ' . ($doctor->last_name ?? ''));
        }

        return $doctor->name ?? 'Doctor';
    }

    // ------------------------------------------------------------------ tabs

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->reset(['searchTerm', 'statusFilter']);
    }

    // ------------------------------------------------------------------ cancel modal

    public function openCancelModal(int $appointmentId): void
    {
        $this->appointmentToCancel = $appointmentId;
        $this->showCancelModal     = true;
        $this->cancellationReason  = '';
        $this->resetErrorBag();
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal     = false;
        $this->appointmentToCancel = null;
        $this->cancellationReason  = '';
        $this->resetErrorBag();
    }

    public function cancelAppointment(): void
    {
        $this->validate([
            'cancellationReason' => 'required|string|min:10|max:500',
        ], [
            'cancellationReason.required' => 'Please provide a reason for cancellation.',
            'cancellationReason.min'      => 'Reason must be at least 10 characters.',
        ]);

        try {
            if (method_exists($this->appointmentService, 'appointmentCancellation')) {
                $this->appointmentService->appointmentCancellation(
                    $this->appointmentToCancel,
                    'patient',
                    $this->cancellationReason
                );
            } else {
                // Direct fallback if service method is missing
                Appointment::where('id', $this->appointmentToCancel)->update([
                    'status'              => 'cancelled',
                    'cancelled_by'        => 'patient',
                    'cancellation_reason' => $this->cancellationReason,
                    'cancelled_at'        => now()->toDateString(),
                ]);
            }

            session()->flash('success', 'Appointment cancelled successfully.');
            $this->closeCancelModal();
        } catch (\Exception $e) {
            Log::error('Cancel appointment: ' . $e->getMessage());
            session()->flash('error', 'Unable to cancel: ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------------ computed properties

    public function getUpcomingAppointmentsProperty(): Collection
    {
        try {
            $patientId = $this->getPatientId();
            if (!$patientId) return collect();

            // Try the service, fall back to direct query
            if (method_exists($this->appointmentService, 'getUpcomingAppointment')) {
                $appointments = collect($this->appointmentService->getUpcomingAppointment($patientId));
            } elseif (method_exists($this->appointmentService, 'getUpcomingAppointments')) {
                $appointments = collect($this->appointmentService->getUpcomingAppointment($patientId));
            } else {
                $appointments = Appointment::with(['doctor', 'doctor.specialization', 'hospital'])
                    ->where('patient_id', $patientId)
                    ->where('appointment_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('appointment_date')
                    ->orderBy('appointment_time')
                    ->get();
            }

            return $this->applyFilters($appointments);
        } catch (\Exception $e) {
            Log::error('getUpcomingAppointments: ' . $e->getMessage());
            return collect();
        }
    }

    public function getAppointmentHistoryProperty(): Collection
    {
        try {
            $patientId = $this->getPatientId();
            if (!$patientId) return collect();

            if (method_exists($this->appointmentService, 'getAppointmentHistory')) {
                $appointments = collect($this->appointmentService->getAppointmentHistory($patientId));
            } else {
                $appointments = Appointment::with(['doctor', 'doctor.specialization', 'hospital'])
                    ->where('patient_id', $patientId)
                    ->where(function ($q) {
                        $q->whereIn('status', ['completed', 'compeleted', 'cancelled', 'no_show'])
                            ->orWhere('appointment_date', '<', now()->toDateString());
                    })
                    ->orderBy('appointment_date', 'desc')
                    ->get();
            }

            return $this->applyFilters($appointments);
        } catch (\Exception $e) {
            Log::error('getAppointmentHistory: ' . $e->getMessage());
            return collect();
        }
    }

    protected function applyFilters(Collection $appointments): Collection
    {
        if ($this->searchTerm !== '') {
            $term = strtolower($this->searchTerm);
            $appointments = $appointments->filter(function ($appt) use ($term) {
                $name   = strtolower($this->doctorName($appt->doctor));
                $number = strtolower((string) ($appt->appointment_number ?? ''));
                return str_contains($name, $term) || str_contains($number, $term);
            });
        }

        if ($this->statusFilter !== 'all') {
            $filter = $this->statusFilter;
            $appointments = $appointments->filter(function ($appt) use ($filter) {
                // treat 'compeleted' (typo) as 'completed'
                if ($filter === 'completed') {
                    return in_array($appt->status, ['completed', 'compeleted']);
                }
                return $appt->status === $filter;
            });
        }

        return $appointments->values();
    }

    // ------------------------------------------------------------------ render

    public function render()
    {
        return view('livewire.patient.appointments.my-appointments', [
            'upcomingAppointments' => $this->upcomingAppointments,
            'appointmentHistory'   => $this->appointmentHistory,
        ])->layout('layouts.patient');
    }
}
