<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\Appointment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    /**
     * Create a new prescription
     */
    public function createPrescription($data)
    {
        DB::beginTransaction();

        try {
            // Verify appointment exists and belongs to doctor
            $appointment = Appointment::findOrFail($data['appointment_id']);

            if ($appointment->doctor_id !== $data['doctor_id']) {
                throw new Exception('Appointment does not belong to this doctor');
            }

            if ($appointment->status !== 'completed') {
                throw new Exception('Can only create prescription for completed appointments');
            }

            // Check if prescription already exists for this appointment
            if (Prescription::where('appointment_id', $data['appointment_id'])->exists()) {
                throw new Exception('Prescription already exists for this appointment');
            }

            // Generate prescription number
            $prescription_number = 'RX-' . date('Ymd') . '-' . str_pad(
                Prescription::whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            // Calculate valid_until date (default 30 days from now)
            $valid_until = Carbon::now()->addDays($data['validity_days'] ?? 30);

            // Create prescription
            $prescription = Prescription::create([
                'prescription_number' => $prescription_number,
                'appointment_id' => $data['appointment_id'],
                'doctor_id' => $data['doctor_id'],
                'patient_id' => $appointment->patient_id,
                'medications' => json_encode($data['medications']),
                'instructions' => $data['instructions'],
                'diagnosis' => $data['diagnosis'],
                'valid_until' => $valid_until,
                'is_dispensed' => false,
                'dispensed_at' => null,
            ]);

            DB::commit();

            // Send notification to patient
            if (class_exists('App\Services\NotificationService')) {
                app(NotificationService::class)->sendPrescriptionCreated($prescription);
            }

            return $prescription->load(['appointment', 'doctor', 'patient']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get patient's prescriptions
     */
    public function getPatientPrescriptions($patient_id, $status = 'all')
    {
        $query = Prescription::with(['appointment', 'doctor', 'patient'])
            ->where('patient_id', $patient_id)
            ->orderBy('created_at', 'desc');

        if ($status === 'active') {
            $query->where('is_dispensed', false)
                ->where('valid_until', '>=', today());
        } elseif ($status === 'dispensed') {
            $query->where('is_dispensed', true);
        } elseif ($status === 'expired') {
            $query->where('is_dispensed', false)
                ->where('valid_until', '<', today());
        }

        return $query->get();
    }

    /**
     * Get doctor's prescriptions
     */
    public function getDoctorPrescriptions($doctor_id)
    {
        return Prescription::with(['appointment', 'doctor', 'patient'])
            ->where('doctor_id', $doctor_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get prescription by ID
     */
    public function getPrescriptionById($prescription_id)
    {
        return Prescription::with(['appointment', 'doctor', 'patient'])
            ->findOrFail($prescription_id);
    }

    /**
     * Get prescription by prescription number
     */
    public function getPrescriptionByNumber($prescription_number)
    {
        return Prescription::with(['appointment', 'doctor', 'patient'])
            ->where('prescription_number', $prescription_number)
            ->firstOrFail();
    }

    /**
     * Mark prescription as dispensed
     */
    public function markAsDispensed($prescription_id)
    {
        DB::beginTransaction();

        try {
            $prescription = Prescription::findOrFail($prescription_id);

            if ($prescription->is_dispensed) {
                throw new Exception('Prescription has already been dispensed');
            }

            if (Carbon::parse($prescription->valid_until)->isPast()) {
                throw new Exception('Prescription has expired and cannot be dispensed');
            }

            $prescription->update([
                'is_dispensed' => true,
                'dispensed_at' => now(),
            ]);

            DB::commit();

            if (class_exists('App\Services\NotificationService')) {
                app(NotificationService::class)->sendPrescriptionDispensed($prescription);
            }

            return $prescription->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update prescription
     */
    public function updatePrescription($prescription_id, $data)
    {
        DB::beginTransaction();

        try {
            $prescription = Prescription::findOrFail($prescription_id);

            if ($prescription->is_dispensed) {
                throw new Exception('Cannot update a dispensed prescription');
            }

            $updateData = [];

            if (isset($data['medications'])) {
                $updateData['medications'] = json_encode($data['medications']);
            }

            if (isset($data['instructions'])) {
                $updateData['instructions'] = $data['instructions'];
            }

            if (isset($data['diagnosis'])) {
                $updateData['diagnosis'] = $data['diagnosis'];
            }

            if (isset($data['validity_days'])) {
                $updateData['valid_until'] = Carbon::now()->addDays($data['validity_days']);
            }

            $prescription->update($updateData);

            DB::commit();

            return $prescription->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete prescription (soft delete or hard delete)
     */
    public function deletePrescription($prescription_id)
    {
        DB::beginTransaction();

        try {
            $prescription = Prescription::findOrFail($prescription_id);

            if ($prescription->is_dispensed) {
                throw new Exception('Cannot delete a dispensed prescription');
            }

            $prescription->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if prescription is valid
     */
    public function isPrescriptionValid($prescription_id)
    {
        $prescription = Prescription::findOrFail($prescription_id);

        return !$prescription->is_dispensed &&
            Carbon::parse($prescription->valid_until)->isFuture();
    }

    /**
     * Get prescription statistics for patient
     */
    public function getPatientPrescriptionStats($patient_id)
    {
        return [
            'total' => Prescription::where('patient_id', $patient_id)->count(),
            'active' => Prescription::where('patient_id', $patient_id)
                ->where('is_dispensed', false)
                ->where('valid_until', '>=', today())
                ->count(),
            'dispensed' => Prescription::where('patient_id', $patient_id)
                ->where('is_dispensed', true)
                ->count(),
            'expired' => Prescription::where('patient_id', $patient_id)
                ->where('is_dispensed', false)
                ->where('valid_until', '<', today())
                ->count(),
        ];
    }

    /**
     * Decode medications JSON
     */
    public function decodeMedications($prescription)
    {
        if (is_string($prescription->medications)) {
            return json_decode($prescription->medications, true);
        }
        return $prescription->medications;
    }
}
