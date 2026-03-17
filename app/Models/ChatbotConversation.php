<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotConversation extends Model
{
    use HasFactory;
    // protected $table = 'chatbot_conversations';

    protected $fillable = [
        'user_id',
        'session_id',
        'external_user_id',
        'started_at',
        'closed_at',
        'channel',
        'status'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'chatbot_conversation_id');
    }
}
