<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'appointment_id',
        'patient_id',
        'amount',
        'currency',
        'phone_number',
        'payment_method',
        'payment_reference',
        'payment_provider',
        'status',
        'provider_reference',
        'provider_response',
        'transaction_reference',
        'initiated_at',
        'completed_at',
        'failed_reason',
        'refund_amount',
        'refunded_at'
    ];

    protected $casts = [
        'provider_response' => 'array',
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];


    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, foreignKey: 'patient_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, foreignKey: 'appointment_id');
    }

    public function payment_provider(): BelongsTo
    {
        return $this->belongsTo(PaymentProvider::class);
    }

    public function withdrawals(): BelongsToMany
    {
        return $this->belongsToMany(Withdrawal::class, 'withdrawal_payments');
    }
    public function isWithdrawable()
    {
        return $this->status === 'completed' &&
            $this->withdrawals()->count() === 0 &&
            $this->completed_at <= now()->subDays(3); // Minimum 3 days hold period
    }
}
