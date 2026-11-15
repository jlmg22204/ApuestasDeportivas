<?php

namespace App\Services\Chatbot;

/**
 * Respuesta normalizada del modelo.
 */
final class ModelResponse
{
    /**
     * @param  string|null  $stopReason  end_turn | tool_use | max_tokens | refusal | ...
     * @param  string  $text  Texto concatenado de los bloques de texto.
     * @param  array<int, array{id: string, name: string, input: array<string, mixed>}>  $toolCalls
     * @param  mixed  $rawContent  Contenido original para reenviarlo tal cual en el turno siguiente.
     */
    public function __construct(
        public readonly ?string $stopReason,
        public readonly string $text,
        public readonly array $toolCalls,
        public readonly mixed $rawContent,
        public readonly int $inputTokens = 0,
        public readonly int $outputTokens = 0,
    ) {}
}
