# Portafolio con asistente IA (Laravel)

Sitio de portafolio con un chatbot de inteligencia artificial (Claude, de Anthropic) que responde dudas de los visitantes, recomienda paquetes y **captura leads** (nombre, contacto y resumen del proyecto) que llegan a tu correo y a un panel privado.

## Qué incluye

| Parte | Archivo |
| --- | --- |
| Página del portafolio | `resources/views/portafolio.blade.php`, `public/css/portafolio.css` |
| Widget del chat (JS puro, sin compilar) | `public/chatbot/widget.js`, `public/chatbot/widget.css` |
| Información que usa la IA | `resources/chatbot/conocimiento.md` |
| Lógica del asistente (prompt, herramientas, leads) | `app/Services/Chatbot/ChatAssistant.php` |
| Conexión con Claude (SDK oficial) | `app/Services/Chatbot/AnthropicLanguageModel.php` |
| Endpoint del chat | `app/Http/Controllers/ChatController.php` → `POST /chat/messages` |
| Panel de conversaciones y leads | `/admin/conversaciones` |
| Configuración | `config/chatbot.php` y variables en `.env` |
| Pruebas | `tests/Feature/ChatbotTest.php` |

## Instalación

```bash
cd portafolio
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # o configura MySQL en .env
php artisan migrate
php artisan serve
```

Abre http://localhost:8000. Sin clave de API, el chat funciona en **modo demostración** y dirige al visitante a tu WhatsApp.

## Configuración (.env)

```dotenv
ANTHROPIC_API_KEY=sk-ant-...        # https://platform.claude.com → API Keys
CHATBOT_MODEL=claude-opus-5-5        # o claude-haiku-5-5 para reducir costos
CHATBOT_EFFORT=low

PORTAFOLIO_NOMBRE="Tu Nombre"
PORTAFOLIO_CIUDAD="Tu Ciudad"
PORTAFOLIO_WHATSAPP=573001234567     # formato internacional, sin +
PORTAFOLIO_EMAIL_AVISOS=tu@correo.com

CHATBOT_ADMIN_USER=admin
CHATBOT_ADMIN_PASSWORD=una-clave-larga
```

Para que lleguen los correos de nuevos leads, configura `MAIL_MAILER` (SMTP, Resend, etc.). Por defecto se escriben en `storage/logs/laravel.log`.

## Personalizar

1. **Edita `resources/chatbot/conocimiento.md`** con tus servicios, precios, proceso, preguntas frecuentes y proyectos reales. La IA solo responde con lo que está ahí.
2. Cambia textos y proyectos en `resources/views/portafolio.blade.php`.
3. Cambia los colores del widget en las variables `--pcw-*` de `public/chatbot/widget.css`.
4. Cualquier botón con el atributo `data-open-chat` abre el asistente.

## Cómo funciona

1. El visitante escribe en el widget; el mensaje llega a `POST /chat/messages` (con CSRF y límite de 12 mensajes por minuto por IP).
2. `ChatAssistant` arma el prompt con tu información y llama a Claude con la herramienta `registrar_lead`.
3. Cuando el visitante da su nombre y un contacto, la IA llama a la herramienta: se guarda el lead, te llega un correo y la IA confirma que lo contactarás.
4. Todo queda en `/admin/conversaciones`, donde marcas cada lead como nuevo, contactado, cerrado o descartado.
5. Si el widget detecta Meta Pixel (`fbq`) o Google Tag Manager (`dataLayer`), registra el evento `Lead` para medir tus campañas.

## Seguridad y costos

- La clave de API solo vive en `.env`; el navegador nunca la ve.
- Límite por IP (12/minuto y 200/día), mensajes de máximo 1000 caracteres e historial acotado a 20 mensajes.
- El prompt de sistema se cachea para abaratar cada turno.
- Si el modelo declina una petición, se reintenta con el modelo de respaldo del servidor (`fallbacks: default`); si falla la API, el visitante recibe tu enlace de WhatsApp.
- El panel de administración está protegido con usuario y contraseña, y queda deshabilitado si no defines la contraseña.
- Los tokens consumidos se registran por conversación para que calcules la mensualidad del servicio.

## Pruebas

```bash
php artisan test
```

Las pruebas usan un modelo simulado: no consumen la API.
