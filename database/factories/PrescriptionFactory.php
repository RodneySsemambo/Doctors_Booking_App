<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;

class PrescriptionFactory extends Factory
{
    public function definition(): array
    {
        $appointment = Appointment::inRandomOrder()->first();

        return [
            'appointment_id' => $appointment->id ?? Appointment::factory(),
            'doctor_id' => $appointment->doctor_id ?? Doctor::factory(),
            'patient_id' => $appointment->patient_id ?? Patient::factory(),
            'prescription_number' => strtoupper(fake()->unique()->bothify('RX-####-??')),
            'medications' => json_encode([
                [
                    'name' => fake()->randomElement(['Paracetamol', 'Ibuprofen', 'Amoxicillin', 'Azithromycin']),
                    'dosage' => fake()->randomElement(['250mg', '500mg', '1 tablet', '2 tablets']),
                    'frequency' => fake()->randomElement(['Once daily', 'Twice daily', 'Every 8 hours']),
                ]
            ]),
            'instructions' => fake()->sentence(10),
            'diagnosis' => fake()->randomElement(['Flu', 'Malaria', 'Headache', 'Infection']),
            'valid_until' => now()->addDays(fake()->numberBetween(7, 30)),
            'is_dispensed' => fake()->boolean(40),
            'dispensed_at' => fake()->boolean(40) ? now() : null,
        ];
    }
}
