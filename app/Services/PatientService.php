<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PatientService
{
    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    protected function doctorName($doctor): string
    {
        if (!$doctor) {
            return 'Doctor';
        }

        try {
            $v = $doctor->full_name;
            if (!empty($v)) {
                return $v;
            }
        } catch (\Exception $e) {
        }

        if (!empty($doctor->first_name)) {
            return trim($doctor->first_name . ' ' . ($doctor->last_name ?? ''));
        }

        return $doctor->name ?? 'Doctor';
    }


    protected static ?bool $paymentsHavePatientId = null;

    protected function paymentsHavePatientId(): bool
    {
        if (self::$paymentsHavePatientId === null) {
            try {
                self::$paymentsHavePatientId = Schema::hasColumn('payments', 'patient_id');
            } catch (\Exception $e) {
                self::$paymentsHavePatientId = false;
            }
        }
        return self::$paymentsHavePatientId;
    }

    protected function paymentsForPatient(int $patient_id)
    {
        if ($this->paymentsHavePatientId()) {
            return Payment::where('patient_id', $patient_id);
        }

        // Payments are linked via appointments
        return Payment::whereHas('appointment', fn($q) => $q->where('patient_id', $patient_id));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Patient CRUD
    // ─────────────────────────────────────────────────────────────────────────

    public function createPatient($data)
    {
        DB::beginTransaction();

        $profilePhotoPath = null;
        if (isset($data['profile_photo'])) {
            $profilePhotoPath = $data['profile_photo']->store('patient/profiles', 'public');
        }

        try {
            $patient = Patient::create([
                'user_id'         => $data['user_id'],
                'first_name'      => $data['first_name'],
                'last_name'       => $data['last_name'],
                'profile_photo'   => $profilePhotoPath,
                'city'            => $data['city']             ?? null,
                'address'         => $data['address']          ?? null,
                'country'         => $data['country'],
                'date_of_birth'   => $data['date_of_birth'],
                'gender'          => $data['gender'],
                'blood_group'     => $data['blood_group'],
                'medical_history' => $data['medical_history']  ?? null,
                'allergies'       => $data['allergies']        ?? null,
                'emergency_phone' => $data['emergency_phone']  ?? null,
                'emergency_name'  => $data['emergency_name']   ?? null,
            ]);

            DB::commit();
            return $patient;
        } catch (Exception $e) {
            DB::rollBack();
            if ($profilePhotoPath) {
                Storage::disk('public')->delete($profilePhotoPath);
            }
            throw $e;
        }
    }

    public function updatePatient($patient_id, $data)
    {
        DB::beginTransaction();
        try {
            $patient = Patient::findOrFail($patient_id);

            if (isset($data['profile_photo'])) {
                if ($patient->profile_photo) {
                    Storage::disk('public')->delete($patient->profile_photo);
                }
                $data['profile_photo'] = $data['profile_photo']->store('patient/profiles', 'public');
            }

            $patient->update($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deletePatient($patient_id)
    {
        DB::beginTransaction();
        try {
            $patient = Patient::findOrFail($patient_id);
            if ($patient->profile_photo) {
                Storage::disk('public')->delete($patient->profile_photo);
            }
            $patient->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getPatientDetails($patient_id)
    {
        return Patient::with(['user', 'appointments.doctor', 'medicalRecords'])
            ->findOrFail($patient_id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Appointments
    // ─────────────────────────────────────────────────────────────────────────

    public function getAppointmentDetails($patient_id, $filters = [])
    {
        $query = Appointment::where('patient_id', $patient_id)
            ->with(['doctor.specialization', 'prescription', 'payment']);

        if (isset($filters['status'])) {
            if ($filters['status'] === 'upcoming') {
                $query->where('appointment_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed']);
            } elseif ($filters['status'] === 'past') {
                $query->where(function ($q) {
                    $q->where('appointment_date', '<', now()->toDateString())
                        ->orWhereIn('status', ['completed', 'compeleted']);
                });
            } elseif ($filters['status'] === 'cancelled') {
                $query->where('status', 'cancelled');
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (!empty($filters['from_date'])) {
            $query->where('appointment_date', '>=', $filters['from_date']);
            if (!empty($filters['to_date'])) {
                $query->where('appointment_date', '<=', $filters['to_date']);
            }
        }

        if (isset($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate($filters['per_page'] ?? 15);

        return [
            'appointments' => $appointments,
            'stats'        => $this->getAppointmentStats($patient_id),
        ];
    }

    public function getAppointmentStats($patient_id): array
    {
        $base = Appointment::where('patient_id', $patient_id);

        $total = (clone $base)->count();

        $upcomingAppointments = (clone $base)
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $completedAppointments = (clone $base)
            ->whereIn('status', ['completed', 'compeleted'])
            ->count();

        $cancelledAppointments = (clone $base)
            ->where('status', 'cancelled')
            ->count();

        $pendingAppointments = (clone $base)
            ->where('status', 'pending')
            ->count();

        $nextAppointments = (clone $base)
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        $lastAppointments = (clone $base)
            ->whereIn('status', ['completed', 'compeleted'])
            ->latest('appointment_date')
            ->first();

        return [
            'total'                 => $total,
            'upcomingAppointments'  => $upcomingAppointments,
            'completedAppointments' => $completedAppointments,
            'cancelledAppointments' => $cancelledAppointments,
            'pendingAppointments'   => $pendingAppointments,
            'nextAppointments'      => $nextAppointments,
            'lastAppointments'      => $lastAppointments,
        ];
    }

    public function getUpcomingAppointments($patient_id, $limit = 5)
    {
        return Appointment::where('patient_id', $patient_id)
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['doctor.specialization'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit($limit)
            ->get();
    }

    public function searchAppointments($patient_id, $searchTerm)
    {
        return Appointment::where('patient_id', $patient_id)
            ->where(function ($q) use ($searchTerm) {
                $q->whereHas('doctor', function ($dq) use ($searchTerm) {
                    $dq->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name',  'like', "%{$searchTerm}%");
                })->orWhereHas('doctor.specialization', function ($sq) use ($searchTerm) {
                    $sq->where('name', 'like', "%{$searchTerm}%");
                })->orWhere('reason_for_visit', 'like', "%{$searchTerm}%")
                    ->orWhere('appointment_number', 'like', "%{$searchTerm}%");
            })
            ->with(['doctor.specialization'])
            ->latest('appointment_date')
            ->paginate(15);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Dashboard stats
    // ─────────────────────────────────────────────────────────────────────────

    public function getPatientStats($patient_id): array
    {
        $appointmentStats = $this->getAppointmentStats($patient_id);

        // ── Payments ─────────────────────────────────────────────────────────
        $totalSpent      = 0;
        $pendingPayments = 0;

        try {
            $totalSpent = $this->paymentsForPatient($patient_id)
                ->where('status', 'completed')
                ->sum('amount');

            $pendingPayments = $this->paymentsForPatient($patient_id)
                ->where('status', 'pending')
                ->count();
        } catch (\Exception $e) {
            Log::warning('PatientService: payment stats unavailable — ' . $e->getMessage());
        }

        // ── Prescriptions ─────────────────────────────────────────────────────
        $totalPrescriptions = 0;
        try {
            $totalPrescriptions = Prescription::whereHas(
                'appointment',
                fn($q) =>
                $q->where('patient_id', $patient_id)
            )->count();
        } catch (\Exception $e) {
            Log::warning('PatientService: prescription stats unavailable — ' . $e->getMessage());
        }

        // ── Medical records ───────────────────────────────────────────────────
        $totalMedicalRecords = 0;
        try {
            $totalMedicalRecords = MedicalRecord::where('patient_id', $patient_id)->count();
        } catch (\Exception $e) {
            Log::warning('PatientService: medical record stats unavailable — ' . $e->getMessage());
        }

        // ── Doctors visited ───────────────────────────────────────────────────
        $doctorsVisited = 0;
        try {
            $doctorsVisited = Appointment::where('patient_id', $patient_id)
                ->whereIn('status', ['completed', 'compeleted'])
                ->distinct('doctor_id')
                ->count('doctor_id');
        } catch (\Exception $e) {
            Log::warning('PatientService: doctors visited unavailable — ' . $e->getMessage());
        }

        // ── Recent activity ───────────────────────────────────────────────────
        $recentActivity = collect();
        try {
            $recentActivity = $this->getRecentActivity($patient_id, 5);
        } catch (\Exception $e) {
            Log::warning('PatientService: recent activity unavailable — ' . $e->getMessage());
        }

        return [
            'appointments'    => $appointmentStats,
            'payments'        => [
                'total_spent'     => $totalSpent,
                'pending_payments' => $pendingPayments,
            ],
            'prescriptions'   => ['total' => $totalPrescriptions],
            'medical_records' => ['total' => $totalMedicalRecords],
            'doctors_visited' => $doctorsVisited,
            'recent_activity' => $recentActivity,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Recent activity
    // ─────────────────────────────────────────────────────────────────────────

    public function getRecentActivity($patient_id, $limit = 10): Collection
    {
        $activities = collect();

        // ── Appointments ──────────────────────────────────────────────────────
        try {
            $appointments = Appointment::where('patient_id', $patient_id)
                ->with('doctor')
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($apt) {
                    $name = $this->doctorName($apt->doctor);

                    return [
                        'type'        => 'appointment',
                        'action'      => 'Appointment ' . ucfirst($apt->status),
                        'description' => 'with Dr. ' . $name,
                        'date'        => $apt->updated_at ?? $apt->created_at,
                        'icon'        => 'calendar',
                        'color'       => $this->getStatusColor($apt->status),
                    ];
                });

            $activities = $activities->merge($appointments);
        } catch (\Exception $e) {
            Log::warning('PatientService getRecentActivity appointments: ' . $e->getMessage());
        }

        // ── Payments ──────────────────────────────────────────────────────────
        try {
            $payments = $this->paymentsForPatient($patient_id)
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($payment) {
                    return [
                        'type'        => 'payment',
                        'action'      => 'Payment ' . ucfirst($payment->status),
                        'description' => 'UGX ' . number_format($payment->amount),
                        'date'        => $payment->updated_at ?? $payment->created_at,
                        'icon'        => 'credit-card',
                        'color'       => $payment->status === 'completed' ? 'green' : 'yellow',
                    ];
                });

            $activities = $activities->merge($payments);
        } catch (\Exception $e) {
            Log::warning('PatientService getRecentActivity payments: ' . $e->getMessage());
        }

        // ── Prescriptions ─────────────────────────────────────────────────────
        try {
            $prescriptions = Prescription::whereHas(
                'appointment',
                fn($q) =>
                $q->where('patient_id', $patient_id)
            )
                ->with('appointment.doctor')
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($rx) {
                    // BUG FIX: same safe name resolution
                    $name = $this->doctorName($rx->appointment?->doctor);

                    return [
                        'type'        => 'prescription',
                        'action'      => 'New prescription',
                        'description' => 'from Dr. ' . $name,
                        'date'        => $rx->created_at,
                        'icon'        => 'file-text',
                        'color'       => 'blue',
                    ];
                });

            $activities = $activities->merge($prescriptions);
        } catch (\Exception $e) {
            Log::warning('PatientService getRecentActivity prescriptions: ' . $e->getMessage());
        }

        return $activities->sortByDesc('date')->take($limit)->values();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Medical history / prescriptions / payments
    // ─────────────────────────────────────────────────────────────────────────

    public function getMedicalHistory($patient_id)
    {
        $patient = Patient::findOrFail($patient_id);

        $appointments = Appointment::where('patient_id', $patient_id)
            ->with(['doctor.specialization', 'prescription.medications'])
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get();

        $medicalRecords = MedicalRecord::where('patient_id', $patient_id)
            ->orderBy('record_date', 'desc')
            ->limit(10)
            ->get();

        return [
            'patient_info' => [
                'blood_group'     => $patient->blood_group,
                'allergies'       => $patient->allergies,
                'medical_history' => $patient->medical_history,
            ],
            'recent_appointments' => $appointments,
            'medical_records'     => $medicalRecords,
            'total_appointments'  => $appointments->count(),
            'total_records'       => $medicalRecords->count(),
        ];
    }

    public function getPrescriptionHistory($patient_id, $filters = [])
    {
        $query = Prescription::whereHas(
            'appointment',
            fn($q) =>
            $q->where('patient_id', $patient_id)
        )->with(['appointment.doctor', 'medications']);

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }
        if (isset($filters['doctor_id'])) {
            $query->whereHas(
                'appointment',
                fn($q) =>
                $q->where('doctor_id', $filters['doctor_id'])
            );
        }

        $prescriptions = $query->latest()->paginate($filters['per_page'] ?? 15);

        $totalPrescriptions = Prescription::whereHas(
            'appointment',
            fn($q) =>
            $q->where('patient_id', $patient_id)
        )->count();

        $totalMedications = Prescription::whereHas(
            'appointment',
            fn($q) =>
            $q->where('patient_id', $patient_id)
        )->withCount('medications')->get()->sum('medications_count');

        return [
            'prescriptions'      => $prescriptions,
            'total_prescriptions' => $totalPrescriptions,
            'total_medications'  => $totalMedications,
        ];
    }

    public function getPaymentHistory($patient_id, $filters = [])
    {
        $query = $this->paymentsForPatient($patient_id)
            ->with(['appointment.doctor']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        $payments = $query->latest()->paginate($filters['per_page'] ?? 15);

        $totalSpent = $this->paymentsForPatient($patient_id)
            ->where('status', 'completed')
            ->sum('amount');

        $totalRefunded = $this->paymentsForPatient($patient_id)
            ->where('status', 'refunded')
            ->sum('refund_amount');

        return [
            'payments'       => $payments,
            'total_spent'    => $totalSpent,
            'total_refunded' => $totalRefunded,
        ];
    }

    public function updateMedicalInfo($patient_id, $data)
    {
        $patient    = Patient::findOrFail($patient_id);
        $updateData = [];

        foreach (['blood_group', 'allergies', 'medical_history', 'current_medications'] as $field) {
            if (!isset($data[$field])) {
                continue;
            }
            $v = $data[$field];
            $updateData[$field] = is_array($v) ? $v : json_decode($v, true);
        }

        $patient->update($updateData);
        return $patient->fresh();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'confirmed'              => 'green',
            'pending'                => 'yellow',
            'completed', 'compeleted' => 'blue',
            'cancelled'              => 'red',
            default                  => 'gray',
        };
    }
}
