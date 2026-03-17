<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'appointment_number' => strtoupper(Str::random(10)),
            'doctor_id' => Doctor::inRandomOrder()->first()->id ?? Doctor::factory(),
            'patient_id' => Patient::inRandomOrder()->first()->id ?? Patient::factory(),
            'hospital_id' => Hospital::inRandomOrder()->first()->id ?? Hospital::factory(),
            'appointment_date' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'appointment_time' => $this->faker->time('H:i'),

            'appointment_type' => $this->faker->randomElement(['in-person', 'video', 'phone']),
            'status' => $this->faker->randomElement(['confirmed', 'pending', 'cancelled', 'no_show',  'compeleted']),
            'reason_for_visit' => $this->faker->sentence(),
            'symptoms' => $this->faker->sentence(10),
            'notes' => $this->faker->optional()->text(),

            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'refunded']),
            'consultation_fee' => $this->faker->randomFloat(2, 10, 200),

            'cancelled_by' => $this->faker->optional()->randomElement(['doctor', 'patient', 'system']),
            'cancellation_reason' => $this->faker->optional()->sentence(),
            'cancelled_at' => $this->faker->optional()->date(),

            'reminded_at' => $this->faker->optional()->date(),
            'completed_at' => $this->faker->optional()->date(),
        ];
    }
}
