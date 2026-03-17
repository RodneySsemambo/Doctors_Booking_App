<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorScheduling;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    // ─────────────────────────────────────────────────────────────────────────
    // Available slots
    // ─────────────────────────────────────────────────────────────────────────

    public function getAvailableSlots($doctor_id, $date): array
    {
        $doctor    = Doctor::findOrFail($doctor_id);
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        $schedule = DoctorScheduling::where('doctor_id', $doctor_id)
            ->where('day_of_the_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) {
            return [];
        }

        $slots        = [];
        $startTime    = Carbon::parse($schedule->start_time);
        $endTime      = Carbon::parse($schedule->end_time);
        $slotDuration = (int) ($schedule->slot_duration ?? 30);

        while ($startTime->lt($endTime)) {
            $slots[] = $startTime->format('H:i:s');
            $startTime->addMinutes($slotDuration);
        }

        $bookedSlots = Appointment::where('doctor_id', $doctor_id)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->toArray();

        return array_values(array_diff($slots, $bookedSlots));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create appointment
    // ─────────────────────────────────────────────────────────────────────────

    public function createAppointment(array $data): Appointment
    {
        DB::beginTransaction();

        try {
            // ✅ Check slot FIRST — before any DB write, so the exception message
            //    is always 'The time slot is no longer available', never a
            //    constraint violation from a partial insert.
            if (!$this->checkSlotAvailability(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time']
            )) {
                throw new Exception('The time slot is no longer available');
            }

            $doctor = Doctor::findOrFail($data['doctor_id']);

            $appointmentNumber = 'APT-' . date('Ymd') . '-' . str_pad(
                Appointment::whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $appointment = Appointment::create([
                'appointment_number' => $appointmentNumber,
                'patient_id'         => $data['patient_id'],
                'doctor_id'          => $data['doctor_id'],
                'hospital_id'        => $data['hospital_id']      ?? null,
                'appointment_date'   => $data['appointment_date'],
                'appointment_time'   => $data['appointment_time'],
                'appointment_type'   => $data['appointment_type'] ?? 'in-person',
                'status'             => 'pending',
                // ✅ FIX: reason_for_visit and symptoms are NOT NULL in the DB.
                //    Use empty string as fallback so no constraint violation.
                'reason_for_visit'   => $data['reason_for_visit'] ?? '',
                'symptoms'           => $data['symptoms']          ?? '',
                'payment_status'     => 'pending',
                'consultation_fee'   => $doctor->consultation_fee,
            ]);

            DB::commit();
            $this->sendNotificationSafely('sendAppointmentConfirmation', $appointment);

            return $appointment->load(['patient', 'doctor', 'hospital']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Slot availability check
    // ─────────────────────────────────────────────────────────────────────────

    public function checkSlotAvailability($doctor_id, $date, $time): bool
    {
        return !Appointment::where('doctor_id', $doctor_id)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cancel appointment
    // ─────────────────────────────────────────────────────────────────────────

    public function appointmentCancellation($appointment_id, $cancelled_by, $reason): Appointment
    {
        DB::beginTransaction();

        try {
            $appointment = Appointment::findOrFail($appointment_id);

            if ($appointment->status === 'cancelled') {
                throw new Exception('Appointment is already cancelled');
            }

            if (in_array($appointment->status, ['completed', 'compeleted'])) {
                throw new Exception('Cannot cancel a completed appointment');
            }

            $appointment->update([
                'status'              => 'cancelled',
                'cancelled_by'        => $cancelled_by,
                'cancellation_reason' => $reason,
                'cancelled_at'        => now()->toDateString(),
                'reminded_at'         => now()->toDateString(),
            ]);

            if ($appointment->payment_status === 'paid') {
                $payment = Payment::where('appointment_id', $appointment->id)
                    ->where('status', 'completed')
                    ->latest()
                    ->first();

                if ($payment) {
                    app(PaymentService::class)->processRefund($payment->id);
                }
            }

            DB::commit();

            $this->sendNotificationSafely('sendAppointmentCancellation', $appointment);

            return $appointment->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Upcoming appointments for patient
    // ─────────────────────────────────────────────────────────────────────────

    public function getUpcomingAppointment($patient_id)
    {
        return Appointment::with(['doctor', 'doctor.specialization', 'hospital'])
            ->where('patient_id', $patient_id)
            ->where('appointment_date', '>=', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Appointment history for patient
    // ─────────────────────────────────────────────────────────────────────────

    public function getAppointmentHistory($patient_id)
    {
        return Appointment::with(['doctor', 'doctor.specialization', 'hospital', 'review'])
            ->where('patient_id', $patient_id)
            ->where(function ($q) {
                $q->where('appointment_date', '<', today())
                    ->orWhereIn('status', ['completed', 'compeleted', 'cancelled']);
            })
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function sendNotificationSafely(string $method, Appointment $appointment): void
    {
        try {
            $service = app(NotificationService::class);
            if (method_exists($service, $method)) {
                $service->{$method}($appointment);
            }
        } catch (\Exception $e) {
            Log::warning("Notification '{$method}' failed: " . $e->getMessage());
        }
    }
}
