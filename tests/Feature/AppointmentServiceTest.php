<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\Hospital;
use App\Models\Appointment;
use App\Models\DoctorScheduling;
use App\Models\Payment;
use App\Services\AppointmentService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentService $appointmentService;
    protected User $user;
    protected Patient $patient;
    protected Doctor $doctor;
    protected Specialization $specialization;
    protected Hospital $hospital;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appointmentService = app(AppointmentService::class);

        $this->user    = User::factory()->create();
        $this->patient = Patient::factory()->create(['user_id' => $this->user->id]);

        $this->specialization = Specialization::factory()->create(['name' => 'Cardiology']);
        $this->hospital       = Hospital::factory()->create();

        $this->doctor = Doctor::factory()->create([
            'specialization_id' => $this->specialization->id,
            'consultation_fee'  => 50000,
            'is_verified'       => true,
            'is_available'      => true,
        ]);

        DB::table('doctor_hospital')->insert([
            'doctor_id'   => $this->doctor->id,
            'hospital_id' => $this->hospital->id,
        ]);

        DoctorScheduling::factory()->create([
            'doctor_id'       => $this->doctor->id,
            'day_of_the_week' => strtolower(Carbon::now()->addDays(1)->format('l')),
            'start_time'      => '09:00:00',
            'end_time'        => '17:00:00',
            'slot_duration'   => 30,
            'is_available'    => true,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Slot tests
    // ─────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_get_available_slots_for_doctor(): void
    {
        $date  = Carbon::now()->addDays(1)->format('Y-m-d');
        $slots = $this->appointmentService->getAvailableSlots($this->doctor->id, $date);

        $this->assertNotEmpty($slots);
        $this->assertContains('09:00:00', $slots);
        $this->assertContains('16:30:00', $slots);
        $this->assertEquals(16, count($slots)); // 9 AM–5 PM, 30 min slots = 16
    }

    #[Test]
    public function it_excludes_booked_slots_from_available_slots(): void
    {
        $date = Carbon::now()->addDays(1)->format('Y-m-d');

        Appointment::factory()->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00:00',
            'appointment_type' => 'in-person',
            'status'           => 'confirmed',
        ]);

        $slots = $this->appointmentService->getAvailableSlots($this->doctor->id, $date);

        $this->assertNotContains('10:00:00', $slots);
        $this->assertEquals(15, count($slots));
    }

    #[Test]
    public function it_returns_empty_array_when_doctor_has_no_schedule(): void
    {
        DoctorScheduling::where('doctor_id', $this->doctor->id)->delete();

        $date  = Carbon::now()->addDays(1)->format('Y-m-d');
        $slots = $this->appointmentService->getAvailableSlots($this->doctor->id, $date);

        $this->assertEmpty($slots);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Booking tests
    // ─────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_create_an_appointment(): void
    {
        $date = Carbon::now()->addDays(1)->format('Y-m-d');

        $appointment = $this->appointmentService->createAppointment([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => $date,
            'appointment_time' => '11:00:00',
            'appointment_type' => 'in-person',
            'reason_for_visit' => 'Chest pain',
            'symptoms'         => 'Shortness of breath, fatigue',
        ]);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertEquals($this->patient->id, $appointment->patient_id);
        $this->assertEquals($this->doctor->id, $appointment->doctor_id);
        $this->assertEquals($date, $appointment->appointment_date);
        $this->assertEquals('11:00:00', $appointment->appointment_time);
        $this->assertEquals('pending', $appointment->status);
        $this->assertEquals('pending', $appointment->payment_status);
        $this->assertEquals(50000, $appointment->consultation_fee);
        $this->assertStringStartsWith('APT-' . date('Ymd'), $appointment->appointment_number);

        $this->assertDatabaseHas('appointments', [
            'appointment_number' => $appointment->appointment_number,
            'patient_id'         => $this->patient->id,
            'doctor_id'          => $this->doctor->id,
        ]);
    }

    #[Test]
    public function it_prevents_booking_already_taken_slot(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The time slot is no longer available');

        $date = Carbon::now()->addDays(1)->format('Y-m-d');
        $time = '11:00:00';

        $this->appointmentService->createAppointment([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'appointment_type' => 'in-person',
        ]);

        // Second booking on same slot — should throw
        $this->appointmentService->createAppointment([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'appointment_type' => 'in-person',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cancellation tests
    // ─────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_cancel_an_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => now()->addDays(2),
            'appointment_time' => '14:00:00',
            'appointment_type' => 'in-person',
            'status'           => 'confirmed',
            'payment_status'   => 'pending',
        ]);

        $cancelled = $this->appointmentService->appointmentCancellation(
            $appointment->id,
            'patient',
            'Changed my mind'
        );

        $this->assertEquals('cancelled', $cancelled->status);
        $this->assertEquals('patient', $cancelled->cancelled_by);
        $this->assertEquals('Changed my mind', $cancelled->cancellation_reason);
        $this->assertNotNull($cancelled->cancelled_at);
    }

    #[Test]
    public function it_cannot_cancel_already_cancelled_appointment(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Appointment is already cancelled');

        $appointment = Appointment::factory()->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_type' => 'in-person',
            'status'           => 'cancelled',
        ]);

        $this->appointmentService->appointmentCancellation(
            $appointment->id,
            'patient',
            'Try to cancel again'
        );
    }

    #[Test]
    public function it_cannot_cancel_completed_appointment(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot cancel a completed appointment');

        $appointment = Appointment::factory()->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_type' => 'in-person',
            'status'           => 'completed',
        ]);

        $this->appointmentService->appointmentCancellation(
            $appointment->id,
            'patient',
            'Try to cancel completed'
        );
    }

    #[Test]
    public function it_processes_refund_when_cancelling_paid_appointment(): void
    {
        $paymentServiceMock = $this->mock(PaymentService::class);
        $paymentServiceMock->shouldReceive('processRefund')
            ->once()
            ->andReturn(true);

        $appointment = Appointment::factory()->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_type' => 'in-person',
            'status'           => 'confirmed',
            'payment_status'   => 'paid',
        ]);

        Payment::factory()->create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $this->patient->id,
            'status'         => 'completed',
        ]);

        $cancelled = $this->appointmentService->appointmentCancellation(
            $appointment->id,
            'patient',
            'Request refund'
        );

        $this->assertEquals('cancelled', $cancelled->status);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // History / upcoming tests
    // ─────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_gets_upcoming_appointments_for_patient(): void
    {
        Appointment::factory()->count(2)->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => now()->subDays(5),
            'appointment_type' => 'in-person',
            'status'           => 'completed',
        ]);

        Appointment::factory()->count(3)->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => now()->addDays(2),
            'appointment_type' => 'in-person',
            'status'           => 'confirmed',
        ]);

        $upcoming = $this->appointmentService->getUpcomingAppointment($this->patient->id);

        $this->assertEquals(3, $upcoming->count());

        foreach ($upcoming as $apt) {
            $this->assertTrue($apt->appointment_date >= now()->toDateString());
            $this->assertContains($apt->status, ['pending', 'confirmed']);
        }
    }

    #[Test]
    public function it_gets_appointment_history_for_patient(): void
    {
        Appointment::factory()->count(4)->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => now()->subDays(5),
            'appointment_type' => 'in-person',
            'status'           => 'completed',
        ]);

        Appointment::factory()->count(2)->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => now()->subDays(3),
            'appointment_type' => 'in-person',
            'status'           => 'cancelled',
        ]);

        // Upcoming — must NOT appear in history
        Appointment::factory()->count(2)->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => now()->addDays(2),
            'appointment_type' => 'in-person',
            'status'           => 'confirmed',
        ]);

        $history = $this->appointmentService->getAppointmentHistory($this->patient->id);

        $this->assertEquals(6, $history->count());

        foreach ($history as $apt) {
            $this->assertTrue(
                $apt->appointment_date < now()->toDateString() ||
                    in_array($apt->status, ['completed', 'cancelled'])
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Availability check
    // ─────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_checks_slot_availability_correctly(): void
    {
        $date = now()->addDays(1)->format('Y-m-d');
        $time = '15:00:00';

        $this->assertTrue(
            $this->appointmentService->checkSlotAvailability($this->doctor->id, $date, $time)
        );

        Appointment::factory()->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'hospital_id'      => $this->hospital->id,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'appointment_type' => 'in-person',
            'status'           => 'confirmed',
        ]);

        $this->assertFalse(
            $this->appointmentService->checkSlotAvailability($this->doctor->id, $date, $time)
        );
    }

    #[Test]
    public function it_handles_different_appointment_types(): void
    {
        $types = ['in-person', 'video', 'phone'];

        foreach ($types as $index => $type) {
            $date = now()->addDays($index + 1)->format('Y-m-d');

            // Create a schedule for this specific day
            DoctorScheduling::firstOrCreate(
                [
                    'doctor_id'       => $this->doctor->id,
                    'day_of_the_week' => strtolower(Carbon::now()->addDays($index + 1)->format('l')),
                ],
                [
                    'start_time'    => '09:00:00',
                    'end_time'      => '17:00:00',
                    'slot_duration' => 30,
                    'is_available'  => true,
                ]
            );

            $appointment = $this->appointmentService->createAppointment([
                'patient_id'       => $this->patient->id,
                'doctor_id'        => $this->doctor->id,
                'hospital_id'      => $this->hospital->id,
                'appointment_date' => $date,
                'appointment_time' => '09:00:00',
                'appointment_type' => $type,
            ]);

            $this->assertEquals($type, $appointment->appointment_type);
        }
    }
}
