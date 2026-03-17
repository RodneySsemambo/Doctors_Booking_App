<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Specialization;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'user_type' => 'doctor',
                'is_active' => true,
            ]),
            'specialization_id' => Specialization::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'license_number' => strtoupper($this->faker->unique()->bothify('MD-###??')),
            'years_of_experience' => $this->faker->numberBetween(1, 30),
            'qualification' => $this->faker->randomElement(['MBBS', 'MD', 'BMed', 'PhD']),
            'bio' => $this->faker->paragraph,
            'profile_photo' => 'default.png',
            'consultation_fee' => $this->faker->randomFloat(2, 50000, 300000),
            'rating' => $this->faker->randomFloat(1, 3, 5),
            'total_reviews' => $this->faker->numberBetween(0, 300),
            'hospital_affiliation' => $this->faker->company . ' Hospital',
            'video_consultation_available' => $this->faker->boolean,
            'languages_spoken' => json_encode($this->faker->randomElements(['English', 'Luganda', 'Swahili'], rand(1, 3))),
            'is_verified' => $this->faker->boolean(80),
            'is_available' => $this->faker->boolean(90),
        ];
    }
}
