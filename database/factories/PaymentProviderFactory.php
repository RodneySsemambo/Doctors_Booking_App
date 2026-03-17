<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentProvider;
use App\Models\Payment;

class PaymentProviderFactory extends Factory
{
    protected $model = PaymentProvider::class;

    public function definition(): array
    {
        return [
            'payment' => Payment::inRandomOrder()->first()->id ?? Payment::factory(),
            'provider_name' => $this->faker->randomElement(['mtn_momo', 'airtel_money', 'flutterwave', 'stripe']),
            'api_key' => $this->faker->sha256,
            'api_secret' => $this->faker->sha256,
            'merchant_code' => $this->faker->optional()->regexify('[A-Z0-9]{6}'),
            'configuration' => $this->faker->json,
            'is_active' => $this->faker->boolean(90),
            'is_test_mode' => $this->faker->boolean(50),
        ];
    }
}
