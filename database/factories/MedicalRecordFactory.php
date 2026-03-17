<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::inRandomOrder()->first()->id ?? Patient::factory(),
            'appointment_id' => Appointment::inRandomOrder()->first()->id ?? Appointment::factory(),
            'record_type' => $this->faker->randomElement(['lab_result', 'consultation_note', 'vaccination', 'imaging']),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'file_path' => null,
            'file_type' => null,
            'recorded_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'recorded_date' => $this->faker->date(),
        ];
    }
}
