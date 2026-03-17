<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ChatbotConversation;
use App\Models\User;

class ChatbotConversationFactory extends Factory
{
    protected $model = ChatbotConversation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'session_id' => $this->faker->uuid,
            'channel' => $this->faker->randomElement(['web', 'whatsapp']),
            'external_user_id' => $this->faker->optional()->uuid,
            'status' => $this->faker->randomElement(['active', 'closed']),
            'started_at' => $this->faker->dateTimeThisYear(),
            'closed_at' => $this->faker->optional()->dateTimeThisYear(),
        ];
    }
}
