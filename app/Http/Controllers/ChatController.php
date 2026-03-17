<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ChatService;
use Exception;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Start a new conversation
     * POST /api/chatbot/conversation/start
     */
    public function startConversation(Request $request)
    {
        try {
            $request->validate([
                'channel' => 'sometimes|in:web,whatsapp',
                'external_user_id' => 'nullable|string',
            ]);

            $conversation = $this->chatService->startConversation(
                auth()->id(),
                $request->channel ?? 'web',
                $request->external_user_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Conversation started successfully',
                'data' => $conversation,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get or create conversation
     * GET /api/chatbot/conversation
     */
    public function getOrCreateConversation(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'nullable|string',
                'channel' => 'sometimes|in:web,whatsapp',
            ]);

            $conversation = $this->chatService->createConversation(
                auth()->id(),
                $request->session_id,
                $request->channel ?? 'web'
            );

            return response()->json([
                'success' => true,
                'data' => $conversation,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message
     * POST /api/chatbot/message
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:chatbot_conversation,id',
                'message' => 'required|string|max:1000',
            ]);

            $result = $this->chatService->processMessage(
                $request->conversation_id,
                $request->message
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversation history
     * GET /api/chatbot/conversation/{id}/history
     */
    public function getHistory($conversationId)
    {
        try {
            $messages = $this->chatService->getConversationHistory($conversationId);

            return response()->json([
                'success' => true,
                'data' => $messages,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conversation history',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Close conversation
     * POST /api/chatbot/conversation/{id}/close
     */
    public function closeConversation($conversationId)
    {
        try {
            $conversation = $this->chatService->closeConversation($conversationId);

            return response()->json([
                'success' => true,
                'message' => 'Conversation closed successfully',
                'data' => $conversation,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's conversations
     * GET /api/chatbot/user/{userId}/conversations
     */
    public function getUserConversations(Request $request, $userId)
    {
        try {
            // Check authorization
            if (auth()->id() != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $status = $request->input('status');
            $conversations = $this->chatService->getUserConversations($userId, $status);

            return response()->json([
                'success' => true,
                'data' => $conversations,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chatbot statistics (Admin only)
     * GET /api/chatbot/stats
     */
    public function getStats()
    {
        try {
            $stats = $this->chatService->getChatbotStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
