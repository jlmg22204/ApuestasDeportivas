<?php

namespace App\Providers;

use Anthropic\Client;
use App\Services\Chatbot\AnthropicLanguageModel;
use App\Services\Chatbot\ChatAssistant;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ChatAssistant::class, function () {
            $key = config('chatbot.api_key');

            // Sin clave: modo demostración (el asistente responde con un mensaje fijo).
            $model = $key
                ? new AnthropicLanguageModel(
                    new Client(apiKey: $key),
                    config('chatbot.model'),
                    config('chatbot.effort'),
                    config('chatbot.max_tokens'),
                )
                : null;

            return new ChatAssistant($model);
        });
    }

    public function boot(): void
    {
        // Limita los mensajes por IP para controlar costos y abuso.
        RateLimiter::for('chatbot', function (Request $request) {
            return [
                Limit::perMinute(config('chatbot.rate_limit_per_minute'))->by($request->ip()),
                Limit::perDay(200)->by($request->ip()),
            ];
        });
    }
}
