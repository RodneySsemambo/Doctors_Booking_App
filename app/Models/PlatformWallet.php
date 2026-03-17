<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class PlatformWallet extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_revenue',
        'doctor_earnings',
        'platform_earnings',
        'total_withdrawn',
        'pending_withdrawals',
        'available_balance',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'doctor_earnings' => 'decimal:2',
        'platform_earnings' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'pending_withdrawals' => 'decimal:2',
        'available_balance' => 'decimal:2',
    ];

    public static function getBalance()
    {
        $wallet = self::first();

        if (!$wallet) {
            $wallet = self::create([
                'total_revenue' => 0,
                'doctor_earnings' => 0,
                'platform_earnings' => 0,
                'total_withdrawn' => 0,
                'pending_withdrawals' => 0,
                'available_balance' => 0,
            ]);
        }

        return $wallet;
    }

    public static function recalculate()
    {
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        $doctorEarnings = Payment::where('status', 'completed')
            ->with('appointment')
            ->get()
            ->sum(function ($payment) {
                $appointment = $payment->appointment;
                if ($appointment && $appointment->doctor) {
                    return $payment->amount;
                }
                return 0;
            });

        $doctorWithdrawn = Withdrawal::where('status', 'completed')->sum('net_amount');

        $platformEarnings = $totalRevenue - $doctorEarnings;
        $platformWithdrawn = AdminWithdrawal::where('status', 'completed')->sum('net_amount');
        $platformPending = AdminWithdrawal::whereIn('status', ['pending', 'processing'])->sum('net_amount');

        $availableBalance = $platformEarnings - $platformWithdrawn - $platformPending;

        $wallet = self::first() ?? new self();
        $wallet->total_revenue = $totalRevenue;
        $wallet->doctor_earnings = $doctorEarnings;
        $wallet->platform_earnings = $platformEarnings;
        $wallet->total_withdrawn = $platformWithdrawn;
        $wallet->pending_withdrawals = $platformPending;
        $wallet->available_balance = max(0, $availableBalance);
        $wallet->save();

        return $wallet;
    }

    public static function recordPayment($amount)
    {
        $wallet = self::getBalance();
        $wallet->increment('total_revenue', $amount);

        $wallet->platform_earnings = Payment::where('status', 'completed')->sum('amount')
            - Withdrawal::where('status', 'completed')->sum('amount');
        $wallet->available_balance = $wallet->platform_earnings
            - AdminWithdrawal::where('status', 'completed')->sum('net_amount')
            - AdminWithdrawal::whereIn('status', ['pending', 'processing'])->sum('net_amount');
        $wallet->save();

        return $wallet;
    }

    public static function recordDoctorWithdrawal($amount)
    {
        $wallet = self::getBalance();
        $wallet->increment('doctor_earnings', $amount);

        $wallet->platform_earnings = Payment::where('status', 'completed')->sum('amount')
            - Withdrawal::where('status', 'completed')->sum('amount');
        $wallet->available_balance = $wallet->platform_earnings
            - AdminWithdrawal::where('status', 'completed')->sum('net_amount')
            - AdminWithdrawal::whereIn('status', ['pending', 'processing'])->sum('net_amount');
        $wallet->save();

        return $wallet;
    }
}
