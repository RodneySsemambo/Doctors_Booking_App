<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Doctor;

class DoctorSchedulingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::inRandomOrder()->first()->id ?? Doctor::factory(),
            'day_of_the_week' => fake()->randomElement([
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',


            ]),
            'start_time' => fake()->randomElement(['08:00:00', '09:00:00', '10:00:00']),
            'end_time' => fake()->randomElement(['15:00:00', '16:00:00', '18:00:00']),
            'slot_duration' => fake()->randomElement([15, 20, 30, 45, 60]),
            'is_available' => fake()->boolean(90), // 90% chance available
        ];
    }
}
