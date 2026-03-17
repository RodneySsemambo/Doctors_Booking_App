<?php

namespace Database\Factories;

use App\Models\PlatformWallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformWalletFactory extends Factory
{
    protected $model = PlatformWallet::class;

    public function definition(): array
    {
        // Build logically consistent numbers so the wallet makes sense
        $totalRevenue        = $this->faker->randomFloat(2, 5000000, 500000000);
        // Doctors get ~70%, platform keeps ~30%
        $doctorEarnings      = round($totalRevenue * $this->faker->randomFloat(2, 0.65, 0.75), 2);
        $platformEarnings    = round($totalRevenue - $doctorEarnings, 2);
        // Some portion has been withdrawn already
        $totalWithdrawn      = round($totalRevenue * $this->faker->randomFloat(2, 0.1, 0.6), 2);
        $pendingWithdrawals  = round($totalRevenue * $this->faker->randomFloat(2, 0.01, 0.15), 2);
        $availableBalance    = round($totalRevenue - $totalWithdrawn - $pendingWithdrawals, 2);

        return [
            'total_revenue'       => $totalRevenue,
            'doctor_earnings'     => $doctorEarnings,
            'platform_earnings'   => $platformEarnings,
            'total_withdrawn'     => $totalWithdrawn,
            'pending_withdrawals' => $pendingWithdrawals,
            'available_balance'   => max(0, $availableBalance),  // never negative
        ];
    }

    // ── State helpers ──────────────────────────────────────────────────────

    /** Fresh wallet with no activity */
    public function empty(): static
    {
        return $this->state(fn() => [
            'total_revenue'       => 0,
            'doctor_earnings'     => 0,
            'platform_earnings'   => 0,
            'total_withdrawn'     => 0,
            'pending_withdrawals' => 0,
            'available_balance'   => 0,
        ]);
    }

    /** Wallet with a healthy balance and realistic activity */
    public function healthy(): static
    {
        return $this->state(function () {
            $totalRevenue     = $this->faker->randomFloat(2, 50000000, 200000000);
            $doctorEarnings   = round($totalRevenue * 0.70, 2);
            $platformEarnings = round($totalRevenue * 0.30, 2);
            $totalWithdrawn   = round($totalRevenue * 0.35, 2);
            $pending          = round($totalRevenue * 0.05, 2);
            return [
                'total_revenue'       => $totalRevenue,
                'doctor_earnings'     => $doctorEarnings,
                'platform_earnings'   => $platformEarnings,
                'total_withdrawn'     => $totalWithdrawn,
                'pending_withdrawals' => $pending,
                'available_balance'   => round($totalRevenue - $totalWithdrawn - $pending, 2),
            ];
        });
    }

    /** Wallet where almost everything has been withdrawn */
    public function depleted(): static
    {
        return $this->state(function () {
            $totalRevenue     = $this->faker->randomFloat(2, 10000000, 100000000);
            $doctorEarnings   = round($totalRevenue * 0.70, 2);
            $platformEarnings = round($totalRevenue * 0.30, 2);
            $totalWithdrawn   = round($totalRevenue * 0.92, 2);
            $pending          = round($totalRevenue * 0.05, 2);
            return [
                'total_revenue'       => $totalRevenue,
                'doctor_earnings'     => $doctorEarnings,
                'platform_earnings'   => $platformEarnings,
                'total_withdrawn'     => $totalWithdrawn,
                'pending_withdrawals' => $pending,
                'available_balance'   => max(0, round($totalRevenue - $totalWithdrawn - $pending, 2)),
            ];
        });
    }
}
