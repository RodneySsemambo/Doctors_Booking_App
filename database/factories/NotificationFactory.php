<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notification;
use App\Models\User;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'type' => $this->faker->randomElement(['sms', 'email', 'whatsapp', 'push']),
            'title' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['sent', 'pending', 'failed']),
            'sent_at' => $this->faker->optional()->dateTimeThisYear(),
            'read_at' => $this->faker->optional()->dateTimeThisYear(),
            'metadata' => $this->faker->optional()->boolean
                ? json_encode([
                    ['metadata' => $this->faker->word()]
                ])
                : null,
        ];
    }
}
