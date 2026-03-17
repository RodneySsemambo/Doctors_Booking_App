<?php

namespace Database\Factories;

use App\Models\AdminWithdrawal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AdminWithdrawalFactory extends Factory
{
    protected $model = AdminWithdrawal::class;

    public function definition(): array
    {
        $status    = $this->faker->randomElement(['pending', 'processing', 'completed', 'failed', 'cancelled']);
        $method    = $this->faker->randomElement(['mobile_money', 'bank_transfer', 'paypal']);
        $amount    = $this->faker->randomFloat(2, 500000, 50000000);
        $fee       = round($amount * 0.015, 2);  // 1.5% fee
        $netAmount = round($amount - $fee, 2);

        $requestedAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $processedAt = in_array($status, ['processing', 'completed', 'failed'])
            ? Carbon::instance($requestedAt)->addHours(rand(1, 72))
            : null;
        $completedAt = $status === 'completed'
            ? Carbon::instance($processedAt)->addHours(rand(1, 48))
            : null;

        $methodDetails = match ($method) {
            'mobile_money' => [
                'provider'     => $this->faker->randomElement(['MTN', 'Airtel']),
                'phone_number' => '256' . $this->faker->numerify('7########'),
                'account_name' => $this->faker->name(),
            ],
            'bank_transfer' => [
                'bank_name'      => $this->faker->randomElement(['Stanbic', 'DFCU', 'Centenary', 'Absa', 'Standard Chartered']),
                'account_number' => $this->faker->numerify('##########'),
                'account_name'   => 'HealthCare Platform Ltd',
                'swift_code'     => $this->faker->bothify('????UG??'),
                'branch'         => $this->faker->city(),
            ],
            'paypal' => [
                'email'        => $this->faker->safeEmail(),
                'account_name' => $this->faker->name(),
            ],
        };

        return [
            'withdrawal_number' => 'ADW-' . date('Ymd') . '-' . strtoupper($this->faker->bothify('??####')),
            'amount'            => $amount,
            'fee'               => $fee,
            'net_amount'        => $netAmount,
            'status'            => $status,
            'method'            => $method,
            'method_details'    => $methodDetails,
            'transaction_id'    => in_array($status, ['completed', 'processing'])
                ? strtoupper($this->faker->bothify('TXN-??########'))
                : null,
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
            'status'         => 'pending',
            'transaction_id' => null,
            'processed_at'   => null,
            'completed_at'   => null,
            'processed_by'   => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(function () {
            $requestedAt = $this->faker->dateTimeBetween('-3 months', '-1 week');
            $processedAt = Carbon::instance($requestedAt)->addHours(rand(2, 48));
            $completedAt = Carbon::instance($processedAt)->addHours(rand(1, 24));
            return [
                'status'         => 'completed',
                'transaction_id' => strtoupper($this->faker->bothify('TXN-??########')),
                'requested_at'   => $requestedAt,
                'processed_at'   => $processedAt,
                'completed_at'   => $completedAt,
            ];
        });
    }

    public function failed(): static
    {
        return $this->state(fn() => [
            'status'         => 'failed',
            'transaction_id' => null,
            'completed_at'   => null,
            'notes'          => $this->faker->randomElement([
                'Bank rejected the transfer.',
                'PayPal account not verified.',
                'Amount exceeds daily limit.',
                'Invalid destination details.',
            ]),
        ]);
    }

    public function largePlatformWithdrawal(): static
    {
        return $this->state(function () {
            $amount    = $this->faker->randomFloat(2, 10000000, 100000000);
            $fee       = round($amount * 0.01, 2);
            $netAmount = round($amount - $fee, 2);
            return [
                'amount'     => $amount,
                'fee'        => $fee,
                'net_amount' => $netAmount,
                'method'     => 'bank_transfer',
            ];
        });
    }
}
