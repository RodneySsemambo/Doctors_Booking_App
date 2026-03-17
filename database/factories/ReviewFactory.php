<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        $appointment = Appointment::inRandomOrder()->first();

        return [
            'doctor_id' => $appointment->doctor_id ?? Doctor::factory(),
            'patient_id' => $appointment->patient_id ?? Patient::factory(),
            'appointment_id' => $appointment->id ?? Appointment::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'review_text' => fake()->realTextBetween(30, 200),
            'recommend' => fake()->boolean(85),
            'is_verified' => true, // reviews should normally be verified
        ];
    }
}
