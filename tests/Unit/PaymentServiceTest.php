<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;
    protected User $user;
    protected Patient $patient;
    protected Doctor $doctor;
    protected Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = app(PaymentService::class);

        $this->user    = User::factory()->create();
        $this->patient = Patient::factory()->create(['user_id' => $this->user->id]);
        $this->doctor  = Doctor::factory()->create([
            'consultation_fee' => 50000,
            'is_verified'      => true,
            'is_available'     => true,
        ]);
        $this->appointment = Appointment::factory()->create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'appointment_date' => now()->addDays(1),
            'appointment_time' => '10:00:00',
            'appointment_type' => 'in-person',
            'status'           => 'pending',
            'payment_status'   => 'pending',
            'consultation_fee' => 50000,
        ]);
    }


    #[Test]
    public function it_can_initiate_mtn_mobile_money_payment(): void
    {
        Http::fake([
            'https://wallet.wearemarz.com/api/v1/collect-money' => Http::response([
                'status' => 'success',
                'data'   => [
                    'transaction' => [
                        'uuid'               => 'txn_123456',
                        'provider_reference' => 'ref_789012',
                        'status'             => 'pending',
                    ],
                ],
            ], 200),
        ]);

        $result = $this->paymentService->initiatePayment([
            'appointment_id' => $this->appointment->id,
            'patient_id'     => $this->patient->id,
            'amount'         => 50000,
            'currency'       => 'UGX',
            'phone_number'   => '256700000000',
            'payment_method' => 'mtn_mobile_money',
        ]);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Payment::class, $result['payment']);
        $this->assertEquals('processing', $result['payment']->status);
        $this->assertEquals('mtn', $result['payment']->payment_provider);
        $this->assertEquals(50000, $result['payment']->amount);

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $this->appointment->id,
            'patient_id'     => $this->patient->id,
            'amount'         => 50000,
            'status'         => 'processing',
        ]);
    }

    #[Test]
    public function it_can_initiate_airtel_mobile_money_payment(): void
    {
        Http::fake([
            'https://wallet.wearemarz.com/api/v1/collect-money' => Http::response([
                'status' => 'success',
                'data'   => [
                    'transaction' => [
                        'uuid'               => 'txn_123456',
                        'provider_reference' => 'ref_789012',
                        'status'             => 'pending',
                    ],
                ],
            ], 200),
        ]);

        $result = $this->paymentService->initiatePayment([
            'appointment_id' => $this->appointment->id,
            'patient_id'     => $this->patient->id,
            'amount'         => 50000,
            'currency'       => 'UGX',
            'phone_number'   => '256700000000',
            'payment_method' => 'airtel_mobile_money',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('processing', $result['payment']->status);
        $this->assertEquals('airtel', $result['payment']->payment_provider);
    }

    #[Test]
    public function it_handles_cash_payment(): void
    {
        $result = $this->paymentService->initiatePayment([
            'appointment_id' => $this->appointment->id,
            'patient_id'     => $this->patient->id,
            'amount'         => 50000,
            'currency'       => 'UGX',
            'phone_number'   => null,
            'payment_method' => 'cash',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('pending', $result['payment']->status);
        $this->assertEquals('cash', $result['payment']->payment_provider);
    }


    #[Test]
    public function it_fails_with_invalid_payment_method(): void
    {
        $this->expectException(\Exception::class);

        try {
            $this->paymentService->initiatePayment([
                'appointment_id' => $this->appointment->id,
                'patient_id'     => $this->patient->id,
                'amount'         => 50000,
                'currency'       => 'UGX',
                'phone_number'   => '256700000000',
                'payment_method' => 'invalid_method',
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    #[Test]
    public function it_verifies_sandbox_payment_successfully(): void
    {
        $payment = Payment::factory()->create([
            'appointment_id'      => $this->appointment->id,
            'patient_id'          => $this->patient->id,
            'amount'              => 50000,
            'status'              => 'processing',
            'transaction_reference' => 'PAY-TEST-123',
            'provider_reference'  => 'txn_123456',
            'provider_response'   => json_encode([
                'data' => [
                    'transaction' => ['provider_reference' => 'sandbox_ref_123'],
                    'metadata'    => ['sandbox_mode' => true],
                ],
            ]),
        ]);

        $verified = $this->paymentService->verifyPayment($payment->id);

        $this->assertEquals('completed', $verified->status);
        $this->assertNotNull($verified->completed_at);

        $this->appointment->refresh();
        $this->assertEquals('paid', $this->appointment->payment_status);
    }
}
