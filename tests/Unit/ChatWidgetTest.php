<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class ChatWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->patient = Patient::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    #[Test]

    public function chat_widget_can_be_rendered()
    {
        $this->actingAs($this->user);

        Livewire::test('chat-widget')
            ->assertSet('message', '')
            ->assertViewHas('chatMessages');
    }

    #[Test]
    public function message_is_required()
    {
        $this->actingAs($this->user);

        Livewire::test('chat-widget')
            ->set('message', '')
            ->call('sendMessage')
            ->assertHasErrors(['message' => 'required']);
    }

    #[Test]
    public function message_max_length_validation()
    {
        $this->actingAs($this->user);

        $longMessage = str_repeat('a', 1001);

        Livewire::test('chat-widget')
            ->set('message', $longMessage)
            ->call('sendMessage')
            ->assertHasErrors(['message' => 'max']);
    }


    #[Test]
    public function unauthenticated_user_cannot_access_chat()
    {
        Livewire::test('chat-widget')
            ->assertSet('conversationId', null);
    }
}
