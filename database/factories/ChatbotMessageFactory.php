<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ChatbotMessage;
use App\Models\ChatbotConversation;

class ChatbotMessageFactory extends Factory
{
    protected $model = ChatbotMessage::class;

    public function definition(): array
    {
        return [
            'chatbot_conversation_id' => ChatbotConversation::inRandomOrder()->first()->id ?? ChatbotConversation::factory(),
            'sender_type' => $this->faker->randomElement(['user', 'bot']),
            'message' => $this->faker->sentence,
            'message_type' => $this->faker->randomElement(['quick_reply', 'text', 'appointment_card', 'button']),
            'intent' => $this->faker->optional()->word,
            'entities' => $this->faker->optional()->boolean
                ? json_encode([
                    ['entity' => $this->faker->word(), 'value' => $this->faker->word()],
                ])
                : null,

            'quick_replies' => $this->faker->optional()->boolean
                ? json_encode([
                    ['title' => $this->faker->word(), 'payload' => $this->faker->word()],
                    ['title' => $this->faker->word(), 'payload' => $this->faker->word()],
                ])
                : null,

        ];
    }
}
