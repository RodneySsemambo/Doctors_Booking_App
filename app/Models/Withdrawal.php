<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use  HasFactory;
    protected $fillable = [
        'doctor_id',
        'withdrawal_number',
        'amount',
        'fee',
        'net_amount',
        'status',
        'method',
        'method_details',
        'notes',
        'requested_at',
        'processed_at',
        'completed_at',
        'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'method_details' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'withdrawal_payments')
            ->withTimestamps();
    }

    public static function generateWithdrawalNumber()
    {
        $prefix = 'WTH';
        $date = now()->format('Ymd');
        $lastWithdrawal = self::where('withdrawal_number', 'like', $prefix . $date . '%')
            ->orderBy('withdrawal_number', 'desc')
            ->first();

        if ($lastWithdrawal) {
            $lastNumber = (int) substr($lastWithdrawal->withdrawal_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getMethodDetailsFormattedAttribute()
    {
        if (!$this->method_details) {
            return 'N/A';
        }

        switch ($this->method) {
            case 'bank_transfer':
                return "Bank: {$this->method_details['bank_name']}\nAccount: {$this->method_details['account_number']}\nName: {$this->method_details['account_name']}";
            case 'mobile_money':
                return "Provider: {$this->method_details['provider']}\nNumber: {$this->method_details['phone_number']}\nName: {$this->method_details['account_name']}";
            case 'paypal':
                return "Email: {$this->method_details['email']}\nName: {$this->method_details['account_name']}";
            default:
                return json_encode($this->method_details, JSON_PRETTY_PRINT);
        }
    }
}
