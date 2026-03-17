<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SpecializationFactory extends Factory
{
    public function definition(): array
    {
        $specialties = [
            'Cardiology',
            'Dermatology',
            'Pediatrics',
            'Neurology',
            'Orthopedics',
            'Gynecology',
            'Oncology',
            'Dentistry',
            'Psychiatry',
            'Radiology'
        ];

        $name = $this->faker->randomElement($specialties);

        return [
            'name' => $name,
            'description' => "Specialist in $name services and treatments.",
            'is_active' => true
        ];
    }
}
