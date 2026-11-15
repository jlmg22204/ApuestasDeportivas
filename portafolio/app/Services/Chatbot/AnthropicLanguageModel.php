<?php

namespace App\Services\Chatbot;

use Anthropic\Beta\Messages\BetaToolUseBlock;
use Anthropic\Client;

/**
 * Implementación con el SDK oficial de Anthropic (Claude).
 */
class AnthropicLanguageModel implements LanguageModel
{
    public function __construct(
        private readonly Client $client,
        private readonly string $model,
        private readonly string $effort,
        private readonly int $maxTokens,
    ) {}

    public function send(string $system, array $messages, array $tools): ModelResponse
    {
        $message = $this->client->beta->messages->create(
            model: $this->model,
            maxTokens: $this->maxTokens,
            // El prompt de sistema no cambia entre turnos: se cachea para
            // abaratar y acelerar cada respuesta.
            system: [
                ['type' => 'text', 'text' => $system, 'cacheControl' => ['type' => 'ephemeral']],
            ],
            messages: $messages,
            tools: $tools,
            outputConfig: ['effort' => $this->effort],
            // Si el modelo declina una petición por política, la API la reintenta
            // automáticamente con el modelo de respaldo que define el servidor.
            fallbacks: 'default',
            betas: ['server-side-fallback-2026-07-01'],
        );

        $text = '';
        $toolCalls = [];

        foreach ($message->content as $block) {
            if ($block instanceof BetaToolUseBlock) {
                $toolCalls[] = ['id' => $block->id, 'name' => $block->name, 'input' => $block->input];
            } elseif ($block->type === 'text') {
                $text .= $block->text;
            }
        }

        return new ModelResponse(
            stopReason: $message->stopReason,
            text: $text,
            toolCalls: $toolCalls,
            rawContent: $message->content,
            inputTokens: $message->usage->inputTokens
                + ($message->usage->cacheReadInputTokens ?? 0)
                + ($message->usage->cacheCreationInputTokens ?? 0),
            outputTokens: $message->usage->outputTokens,
        );
    }
}
