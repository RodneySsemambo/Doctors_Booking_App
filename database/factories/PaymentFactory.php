<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Appointment;
use App\Models\Patient;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $appointment = Appointment::inRandomOrder()->first();

        return [
            'payment_number' => strtoupper(fake()->bothify('PAY####??')),
            'appointment_id' => $appointment->id ?? Appointment::factory(),
            'patient_id' => $appointment->patient_id ?? Patient::factory(),
            'amount' => fake()->randomFloat(2, 10000, 500000), // UGX sample
            'currency' => fake()->randomElement(['UGX', 'USD']),
            'phone_number' => fake()->phoneNumber(),
            'payment_method' => fake()->randomElement(['mtn_mobile_money', 'airtel_mobile_money', 'card', 'cash', 'flutterwave']),
            'payment_provider' => fake()->randomElement(['mtn', 'airtel', 'flutterwave', 'cash', 'stripe']),
            'status' => fake()->randomElement(['pending', 'refunded', 'processing', 'completed', 'failed']),
            'transaction_reference' => strtoupper(fake()->bothify('TXN########')),
            'provider_reference' => fake()->optional()->bothify('PRV######'),
            'provider_response' => json_encode(['message' => 'Payment processed']),
            'initiated_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'completed_at' => fake()->optional()->dateTimeBetween('-2 months', 'now'),
            'failed_reason' => fake()->optional()->sentence(),
            'refund_amount' => fake()->optional()->randomFloat(2, 1000, 200000),
            'refunded_at' => fake()->optional()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
