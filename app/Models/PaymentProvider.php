<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentProvider extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentProviderFactory> */
    use HasFactory;

    protected $table = 'payment__providers';
    protected $fillable = [
        'provider_name',
        'api_key',
        'api_secret',
        'merchant_code',
        'configuration',
        'is_active',
        'is_test_mode'
    ];

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
