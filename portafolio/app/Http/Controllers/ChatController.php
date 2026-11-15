<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\Chatbot\ChatAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ChatController extends Controller
{
    public function store(Request $request, ChatAssistant $assistant): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:'.config('chatbot.max_input_chars')],
            'conversation_id' => ['nullable', 'uuid'],
            'page_url' => ['nullable', 'string', 'max:500'],
        ]);

        $conversation = $this->conversation($request, $data);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => trim($data['message']),
        ]);

        try {
            $reply = $assistant->reply($conversation);
        } catch (Throwable $e) {
            Log::error('Error del asistente IA', [
                'conversation' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'conversation_id' => $conversation->uuid,
                'reply' => $assistant->fallbackText(),
                'lead_captured' => false,
            ], 503);
        }

        $conversation->messages()->create(['role' => 'assistant', 'content' => $reply->text]);
        $conversation->forceFill([
            'input_tokens' => $conversation->input_tokens + $reply->inputTokens,
            'output_tokens' => $conversation->output_tokens + $reply->outputTokens,
            'last_message_at' => now(),
        ])->save();

        return response()->json([
            'conversation_id' => $conversation->uuid,
            'reply' => $reply->text,
            'lead_captured' => $reply->leadCaptured,
        ]);
    }

    /**
     * Recupera la conversación del visitante o crea una nueva.
     * Solo se reutiliza si pertenece a la misma IP (evita leer chats ajenos).
     */
    private function conversation(Request $request, array $data): ChatConversation
    {
        $ipHash = hash_hmac('sha256', (string) $request->ip(), (string) config('app.key'));

        if (! empty($data['conversation_id'])) {
            $existing = ChatConversation::where('uuid', $data['conversation_id'])
                ->where('ip_hash', $ipHash)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return ChatConversation::create([
            'uuid' => (string) Str::uuid(),
            'ip_hash' => $ipHash,
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            'page_url' => $data['page_url'] ?? null,
            'last_message_at' => now(),
        ]);
    }
}
