<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'user_type' => 'patient'
            ]),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'profile_photo' => 'default.jmpeg',
            'city' => $this->faker->city,
            'country' => $this->faker->country,
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            'medical_history' => json_encode($this->faker->randomElement()),
            'allergies' => json_encode($this->faker->randomElement()),
            'emergency_phone' => $this->faker->unique()->numerify('07########'),
            'emergency_name' => $this->faker->name,


        ];
    }
}
