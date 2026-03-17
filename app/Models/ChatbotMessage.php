<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpParser\Node\Expr\FuncCall;

class ChatbotMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatbotMessageFactory> */
    use HasFactory;

    protected $table = 'chatbot_messages';
    protected $casts = [
        'quick_replies' => 'array',
        'entities' => 'array'
    ];
    protected $fillable = [
        'chatbot_conversation_id',
        'sender_type',
        'message',
        'message_type',
        'intent',
        'entities',
        'quick_replies'
    ];


    public function chatbot_Conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class);
    }
}
