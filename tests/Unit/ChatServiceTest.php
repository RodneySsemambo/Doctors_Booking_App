<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ChatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ChatService $chatService;
    protected User $user;
    protected Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chatService = app(ChatService::class);

        // Create a test user
        $this->user = User::factory()->create();
        $this->patient = Patient::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    #[Test]
    public function it_can_start_a_conversation()
    {
        $conversation = $this->chatService->startConversation($this->user->id);

        $this->assertDatabaseHas('chatbot_conversations', [
            'id' => $conversation->id,
            'user_id' => $this->user->id,
            'status' => 'active'
        ]);
    }

    #[Test]
    public function it_can_create_or_get_existing_conversation()
    {
        // First call should create
        $conversation1 = $this->chatService->createConversation($this->user->id);

        // Second call should return same conversation
        $conversation2 = $this->chatService->createConversation($this->user->id);

        $this->assertEquals($conversation1->id, $conversation2->id);
        $this->assertEquals(1, ChatbotConversation::count());
    }

    #[Test]
    public function it_can_process_a_user_message()
    {
        // Create conversation first
        $conversation = $this->chatService->createConversation($this->user->id);

        // Process a message
        $result = $this->chatService->processMessage($conversation->id, 'Hello');

        // Assert response structure
        $this->assertArrayHasKey('user_message', $result);
        $this->assertArrayHasKey('bot_message', $result);

        // Assert messages were saved
        $this->assertEquals(2, ChatbotMessage::where('chatbot_conversation_id', $conversation->id)->count());

        // Assert user message
        $this->assertEquals('user', $result['user_message']->sender_type);
        $this->assertEquals('Hello', $result['user_message']->message);

        // Assert bot response
        $this->assertEquals('bot', $result['bot_message']->sender_type);
        $this->assertNotNull($result['bot_message']->message);
    }

    #[Test]
    public function it_detects_greeting_intent()
    {
        $conversation = $this->chatService->createConversation($this->user->id);

        // Test different greetings
        $greetings = ['hello', 'hi', 'hey', 'good morning'];

        foreach ($greetings as $greeting) {
            $analysis = $this->chatService->analyzeMessage($conversation, $greeting);

            $this->assertEquals('greeting', $analysis['intent'], "Failed for greeting: $greeting");
        }
    }

    #[Test]
    public function it_returns_doctors_by_specialization()
    {
        // Create specialization
        $specialization = Specialization::factory()->create([
            'name' => 'Cardiology'
        ]);

        // Create doctors
        Doctor::factory()->count(3)->create([
            'specialization_id' => $specialization->id,
            'is_verified' => true,
            'is_available' => true
        ]);

        $doctors = $this->chatService->findDoctorsBySpecialization('Cardiology');

        $this->assertEquals(3, $doctors->count());
    }

    #[Test]
    public function it_handles_unknown_intent()
    {
        $conversation = $this->chatService->createConversation($this->user->id);

        $analysis = $this->chatService->analyzeMessage($conversation, 'asdfghjkl');

        $this->assertEquals('unknown', $analysis['intent']);
    }

    #[Test]
    public function it_can_get_conversation_context()
    {
        $conversation = $this->chatService->createConversation($this->user->id);

        // Add some messages
        $this->chatService->processMessage($conversation->id, 'Hello');
        $this->chatService->processMessage($conversation->id, 'I need a doctor');

        $context = $this->chatService->getConversationContext($conversation->id);

        $this->assertArrayHasKey('messages', $context);
        $this->assertArrayHasKey('lastBotMessage', $context);
        $this->assertNotEmpty($context['messages']);
    }

    #[Test]
    public function it_can_close_conversation()
    {
        $conversation = $this->chatService->createConversation($this->user->id);

        $closed = $this->chatService->closeConversation($conversation->id);

        $this->assertEquals('closed', $closed->status);
        $this->assertNotNull($closed->closed_at);
    }
}
