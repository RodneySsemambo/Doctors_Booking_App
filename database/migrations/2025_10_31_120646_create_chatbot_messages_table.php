<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_conversation_id')
                ->constrained('chatbot_conversations')
                ->onDelete('cascade');
            $table->enum('sender_type', ['user', 'bot']);
            $table->text('message');
            $table->string('message_type')->default('text');
            $table->string('intent')->nullable();
            $table->json('entities')->nullable();
            $table->json('quick_replies')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
    }
};
