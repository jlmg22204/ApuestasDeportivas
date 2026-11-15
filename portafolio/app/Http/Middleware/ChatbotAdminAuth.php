<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autenticación HTTP Basic para el panel de conversaciones.
 * Credenciales en CHATBOT_ADMIN_USER y CHATBOT_ADMIN_PASSWORD.
 */
class ChatbotAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $password = (string) config('chatbot.admin.password');

        if ($password === '') {
            abort(404);
        }

        $validUser = hash_equals((string) config('chatbot.admin.user'), (string) $request->getUser());
        $validPassword = hash_equals($password, (string) $request->getPassword());

        if (! $validUser || ! $validPassword) {
            return response('Acceso restringido', 401, [
                'WWW-Authenticate' => 'Basic realm="Panel del asistente"',
            ]);
        }

        return $next($request);
    }
}
