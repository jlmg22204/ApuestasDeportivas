<?php

namespace App\Services\Chatbot;

/**
 * Abstracción del proveedor de IA. Permite cambiar de proveedor
 * o simularlo en las pruebas sin tocar la lógica del asistente.
 */
interface LanguageModel
{
    /**
     * @param  string  $system  Prompt de sistema (estable, se cachea).
     * @param  array<int, array<string, mixed>>  $messages  Historial en formato Messages API.
     * @param  array<int, array<string, mixed>>  $tools  Definiciones de herramientas.
     */
    public function send(string $system, array $messages, array $tools): ModelResponse;
}
