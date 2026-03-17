<?php

namespace App\Livewire;

use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ChatWidget extends Component
{
    public string $message = '';
    public array $chatMessages = [];
    public ?int $conversationId = null;
    public bool $isTyping = false;
    public array $quickReplies = [];

    protected function rules(): array
    {
        return [
            'message' => 'required|string|min:1|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'message.required' => 'Please type a message first.',
            'message.max'      => 'Message is too long (max 1000 characters).',
        ];
    }

    public function mount(): void
    {
        if (Auth::check()) {
            $this->initializeConversation();
        }
    }

    protected function getChatService(): ChatService
    {
        return app(ChatService::class);
    }

    public function initializeConversation(): void
    {
        try {
            if (!Auth::check()) {
                return;
            }

            $conversation = $this->getChatService()->createConversation(Auth::id());

            if (!$conversation) {
                Log::error('ChatWidget: Failed to create conversation');
                return;
            }

            $this->conversationId = $conversation->id;
            $this->loadMessages();

            // Send welcome message only for fresh conversations
            if (empty($this->chatMessages)) {
                $this->sendWelcomeMessage();
            }
        } catch (\Exception $e) {
            Log::error('ChatWidget initializeConversation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function sendWelcomeMessage(): void
    {
        try {
            $this->getChatService()->processMessage($this->conversationId, 'hello');
            $this->loadMessages();
        } catch (\Exception $e) {
            Log::error('ChatWidget sendWelcomeMessage: ' . $e->getMessage());
        }
    }

    public function loadMessages(): void
    {
        try {
            if (!$this->conversationId) {
                return;
            }

            $rows = $this->getChatService()->getConversationHistory($this->conversationId, 50);

            $this->chatMessages = $rows->map(fn($msg) => [
                'id'            => $msg->id,
                'message'       => $msg->message,
                'sender_type'   => $msg->sender_type,
                'is_mine'       => $msg->sender_type === 'user',
                'created_at'    => $msg->created_at->diffForHumans(),
                'quick_replies' => is_array($msg->quick_replies) ? $msg->quick_replies : [],
            ])->toArray();

            $lastBot = collect($this->chatMessages)->where('sender_type', 'bot')->last();

            $this->quickReplies = ($lastBot && !empty($lastBot['quick_replies']))
                ? $lastBot['quick_replies']
                : [];
        } catch (\Exception $e) {
            Log::error('ChatWidget loadMessages: ' . $e->getMessage());
            $this->chatMessages = [];
            $this->quickReplies = [];
        }
    }

    public function sendMessage(): void
    {
        $this->resetErrorBag();
        $this->validate();

        $text          = trim($this->message);
        $this->message = ''; // Clear input immediately

        if ($text === '') {
            return;
        }

        try {
            if (!$this->conversationId) {
                $this->initializeConversation();
            }

            if (!$this->conversationId) {
                $this->addError('message', 'Could not start a conversation. Please refresh the page.');
                return;
            }

            // Saves user message + generates and saves bot reply in one DB transaction
            $this->getChatService()->processMessage($this->conversationId, $text);

            // Reload so both the user message and bot reply render
            $this->loadMessages();
        } catch (\Exception $e) {
            Log::error('ChatWidget sendMessage: ' . $e->getMessage(), [
                'conversation_id' => $this->conversationId,
                'message'         => $text,
            ]);
            $this->addError('message', 'Failed to send. Please try again.');
        }
    }

    public function selectQuickReply(string $reply): void
    {
        $this->message = $reply;
        $this->sendMessage();
    }

    public function resetChat(): void
    {
        try {
            if ($this->conversationId) {
                $this->getChatService()->closeConversation($this->conversationId);
            }
        } catch (\Exception $e) {
            Log::error('ChatWidget resetChat: ' . $e->getMessage());
        }

        $this->reset(['chatMessages', 'quickReplies', 'message', 'conversationId', 'isTyping']);
        $this->resetErrorBag();
        $this->initializeConversation();
    }

    public function render()
    {
        return view('livewire.chat-widget');
    }
}
