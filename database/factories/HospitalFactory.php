<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Hospital',
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'country' => $this->faker->country,
            'latitude' => $this->faker->latitude(),
            'longtitude' => $this->faker->longitude(), // use `longitude`
            'openning_hours' => json_encode([
                'mon_fri' => '08:00 - 20:00',
                'sat' => '09:00 - 18:00',
                'sun' => 'Closed'
            ]),
            'facilities' => json_encode($this->faker->randomElements(
                ['ICU', 'Lab', 'Pharmacy', 'Emergency', 'Radiology'],
                3
            )),
            'rating' => $this->faker->randomFloat(1, 2.5, 5),
            'is_active' => true,
        ];
    }
}
