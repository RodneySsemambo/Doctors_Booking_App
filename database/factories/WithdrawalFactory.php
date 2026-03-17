<?php

namespace Database\Factories;

use App\Models\Withdrawal;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        $status    = $this->faker->randomElement(['pending', 'processing', 'completed', 'failed', 'cancelled']);
        $method    = $this->faker->randomElement(['bank_transfer', 'mobile_money', 'cash']);
        $amount    = $this->faker->randomFloat(2, 50000, 2000000);
        $fee       = round($amount * 0.02, 2);   // 2% fee
        $netAmount = round($amount - $fee, 2);

        $requestedAt  = $this->faker->dateTimeBetween('-6 months', 'now');
        $processedAt  = in_array($status, ['processing', 'completed', 'failed'])
            ? Carbon::instance($requestedAt)->addHours(rand(1, 48))
            : null;
        $completedAt  = $status === 'completed'
            ? Carbon::instance($processedAt)->addHours(rand(1, 24))
            : null;

        // Method-specific details
        $methodDetails = match ($method) {
            'mobile_money' => [
                'provider'     => $this->faker->randomElement(['MTN', 'Airtel']),
                'phone_number' => '256' . $this->faker->numerify('7########'),
                'account_name' => $this->faker->name(),
            ],
            'bank_transfer' => [
                'bank_name'      => $this->faker->randomElement(['Stanbic', 'DFCU', 'Centenary', 'Absa', 'Equity']),
                'account_number' => $this->faker->numerify('##########'),
                'account_name'   => $this->faker->name(),
                'branch'         => $this->faker->city(),
            ],
            'cash' => [
                'collected_by' => $this->faker->name(),
                'location'     => $this->faker->city(),
            ],
        };

        return [
            'doctor_id'         => Doctor::factory(),
            'withdrawal_number' => 'WD-' . date('Ymd') . '-' . strtoupper($this->faker->bothify('??####')),
            'amount'            => $amount,
            'fee'               => $fee,
            'net_amount'        => $netAmount,
            'status'            => $status,
            'method'            => $method,
            'method_details'    => $methodDetails,
            'notes'             => $this->faker->optional(0.4)->sentence(),
            'requested_at'      => $requestedAt,
            'processed_at'      => $processedAt,
            'completed_at'      => $completedAt,
            'processed_by'      => in_array($status, ['processing', 'completed', 'failed'])
                ? User::factory()
                : null,
        ];
    }

    // ── State helpers ──────────────────────────────────────────────────────

    public function pending(): static
    {
        return $this->state(fn() => [
            'status'       => 'pending',
            'processed_at' => null,
            'completed_at' => null,
            'processed_by' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(function () {
            $requestedAt = $this->faker->dateTimeBetween('-3 months', '-1 week');
            $processedAt = Carbon::instance($requestedAt)->addHours(rand(2, 24));
            $completedAt = Carbon::instance($processedAt)->addHours(rand(1, 12));
            return [
                'status'       => 'completed',
                'requested_at' => $requestedAt,
                'processed_at' => $processedAt,
                'completed_at' => $completedAt,
            ];
        });
    }

    public function failed(): static
    {
        return $this->state(fn() => [
            'status' => 'failed',
            'notes'  => $this->faker->randomElement([
                'Insufficient funds in account.',
                'Invalid account details provided.',
                'Transaction declined by provider.',
                'Network timeout.',
            ]),
            'completed_at' => null,
        ]);
    }

    public function mobileMoney(): static
    {
        return $this->state(fn() => [
            'method'         => 'mobile_money',
            'method_details' => [
                'provider'     => $this->faker->randomElement(['MTN', 'Airtel']),
                'phone_number' => '256' . $this->faker->numerify('7########'),
                'account_name' => $this->faker->name(),
            ],
        ]);
    }

    public function bankTransfer(): static
    {
        return $this->state(fn() => [
            'method'         => 'bank_transfer',
            'method_details' => [
                'bank_name'      => $this->faker->randomElement(['Stanbic', 'DFCU', 'Centenary', 'Absa']),
                'account_number' => $this->faker->numerify('##########'),
                'account_name'   => $this->faker->name(),
                'branch'         => $this->faker->city(),
            ],
        ]);
    }
}
