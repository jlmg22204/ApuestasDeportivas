<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asistente IA del portafolio
    |--------------------------------------------------------------------------
    |
    | La clave de la API se lee de ANTHROPIC_API_KEY (nunca la pongas en el
    | frontend ni en el repositorio). Sin clave, el chat responde en modo
    | demostración y dirige al visitante a WhatsApp.
    |
    */

    'api_key' => env('ANTHROPIC_API_KEY'),

    // Modelo de Claude. Puedes cambiarlo por uno más económico (por ejemplo
    // claude-haiku-5-5) si el volumen de conversaciones crece.
    'model' => env('CHATBOT_MODEL', 'claude-opus-5-5'),

    // Esfuerzo de razonamiento: low es suficiente para un chat de ventas
    // y mantiene las respuestas rápidas y económicas.
    'effort' => env('CHATBOT_EFFORT', 'low'),

    'max_tokens' => (int) env('CHATBOT_MAX_TOKENS', 4000),

    // Mensajes previos que se envían como contexto en cada turno.
    'history_limit' => (int) env('CHATBOT_HISTORY_LIMIT', 20),

    // Longitud máxima del mensaje del visitante.
    'max_input_chars' => (int) env('CHATBOT_MAX_INPUT_CHARS', 1000),

    // Mensajes por minuto permitidos por IP.
    'rate_limit_per_minute' => (int) env('CHATBOT_RATE_LIMIT', 12),

    // Archivo con la información de tu negocio que la IA usa para responder.
    'knowledge_path' => resource_path('chatbot/conocimiento.md'),

    /*
    |--------------------------------------------------------------------------
    | Datos del profesional
    |--------------------------------------------------------------------------
    */

    'owner' => [
        'name' => env('PORTAFOLIO_NOMBRE', 'Tu Nombre'),
        'role' => env('PORTAFOLIO_ROL', 'Desarrollador Web Full Stack'),
        'city' => env('PORTAFOLIO_CIUDAD', 'Tu Ciudad'),
        // Número en formato internacional sin "+" ni espacios, ej. 573001234567
        'whatsapp' => env('PORTAFOLIO_WHATSAPP', ''),
        // Correo que recibe los avisos de nuevos leads.
        'email' => env('PORTAFOLIO_EMAIL_AVISOS'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Panel de conversaciones (/admin/conversaciones)
    |--------------------------------------------------------------------------
    |
    | Protegido con autenticación HTTP Basic. Si no defines la contraseña,
    | el panel queda deshabilitado.
    |
    */

    'admin' => [
        'user' => env('CHATBOT_ADMIN_USER', 'admin'),
        'password' => env('CHATBOT_ADMIN_PASSWORD'),
    ],

];
