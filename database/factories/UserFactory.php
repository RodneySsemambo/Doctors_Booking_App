<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_type' => $this->faker->randomElement(['patient', 'doctor', 'admin']),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->numerify('07########'), // Uganda phone format
            'is_active' => $this->faker->boolean(90),
            'last_login_at' => $this->faker->optional()->dateTime(),
            'phone_verified_at' => $this->faker->optional()->dateTime(),
            'email_verified_at' => $this->faker->optional()->dateTime(),
            'password' => Hash::make('password'), // default password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
