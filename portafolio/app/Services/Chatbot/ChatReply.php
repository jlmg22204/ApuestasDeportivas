<?php

namespace App\Services\Chatbot;

final class ChatReply
{
    public function __construct(
        public readonly string $text,
        public readonly bool $leadCaptured = false,
        public readonly int $inputTokens = 0,
        public readonly int $outputTokens = 0,
    ) {}
}
