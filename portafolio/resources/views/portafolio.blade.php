@php
    $owner = config('chatbot.owner');
    $wa = preg_replace('/\D/', '', (string) $owner['whatsapp']);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $owner['name'] }} · {{ $owner['role'] }}</title>
    <meta name="description" content="Desarrollo de páginas web, sistemas a medida en Laravel y asistentes con inteligencia artificial para negocios.">
    <link rel="stylesheet" href="{{ asset('css/portafolio.css') }}">
    <link rel="stylesheet" href="{{ asset('chatbot/widget.css') }}">
</head>
<body>
<header class="nav">
    <div class="wrap nav-inner">
        <a href="#inicio" class="brand">{{ $owner['name'] }}</a>
        <nav aria-label="Principal">
            <a href="#servicios">Servicios</a>
            <a href="#proyectos">Proyectos</a>
            <a href="#proceso">Proceso</a>
            <a href="#contacto" class="btn btn-sm">Cotizar</a>
        </nav>
    </div>
</header>

<main id="inicio">
    <section class="hero wrap">
        <p class="eyebrow">{{ $owner['role'] }} · {{ $owner['city'] }}</p>
        <h1>Sitios web y sistemas inteligentes que trabajan por tu negocio</h1>
        <p class="lead">Diseño y programo páginas web, sistemas a medida en Laravel y asistentes con IA que atienden a tus clientes las 24 horas.</p>
        <div class="actions">
            <button type="button" class="btn" data-open-chat>Habla con mi asistente IA</button>
            @if ($wa)
                <a class="btn btn-ghost" href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener">Escríbeme por WhatsApp</a>
            @endif
        </div>
        <p class="hint">El chat de la esquina es una demo en vivo de lo que puedo construir para tu negocio.</p>
    </section>

    <section id="servicios" class="section">
        <div class="wrap">
            <h2>Servicios</h2>
            <p class="section-lead">Paquetes claros, con precio desde y fecha de entrega.</p>
            <div class="grid">
                <article class="card">
                    <h3>Landing Pro</h3>
                    <p class="price">Desde USD 250</p>
                    <p>Una página optimizada para vender, con WhatsApp, formulario y SEO básico. Entrega en 7 días.</p>
                </article>
                <article class="card">
                    <h3>Web Negocio</h3>
                    <p class="price">Desde USD 600</p>
                    <p>5 a 7 páginas con panel autoadministrable, blog y analítica. Entrega en 3 a 4 semanas.</p>
                </article>
                <article class="card card-featured">
                    <span class="badge">Nuevo</span>
                    <h3>Web Inteligente</h3>
                    <p class="price">Desde USD 900</p>
                    <p>Tu web con un asistente IA entrenado con tu negocio, conectado a WhatsApp, que responde y captura clientes.</p>
                </article>
                <article class="card">
                    <h3>Sistema a medida</h3>
                    <p class="price">Cotización</p>
                    <p>Reservas, inventario, facturación, CRM o tienda online en Laravel, con reportes asistidos por IA.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="proyectos" class="section section-alt">
        <div class="wrap">
            <h2>Proyectos</h2>
            <p class="section-lead">Reemplaza estos ejemplos con tus casos reales: problema, solución y resultado.</p>
            <div class="grid">
                <article class="card">
                    <p class="tag">Laravel · Reservas</p>
                    <h3>Clínica dental</h3>
                    <p>Agenda en línea con recordatorios automáticos que redujo las citas perdidas.</p>
                </article>
                <article class="card">
                    <p class="tag">E-commerce</p>
                    <h3>Tienda de ropa</h3>
                    <p>Catálogo, pasarela de pago y panel de inventario para vender todos los días.</p>
                </article>
                <article class="card">
                    <p class="tag">IA · WhatsApp</p>
                    <h3>Restaurante</h3>
                    <p>Asistente que responde el menú y toma reservas sin intervención del personal.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="proceso" class="section">
        <div class="wrap">
            <h2>Cómo trabajamos</h2>
            <ol class="steps">
                <li><strong>Llamada de 15 minutos.</strong> Entiendo tu negocio y lo que quieres lograr.</li>
                <li><strong>Propuesta por escrito.</strong> Alcance, tiempos e inversión, sin sorpresas.</li>
                <li><strong>Desarrollo con avances semanales.</strong> 2 rondas de ajustes incluidas.</li>
                <li><strong>Entrega y capacitación.</strong> Más 30 días de soporte gratis.</li>
            </ol>
        </div>
    </section>

    <section id="contacto" class="section section-cta">
        <div class="wrap">
            <h2>¿Hablamos de tu proyecto?</h2>
            <p class="section-lead">Cuéntale tu idea al asistente y recibe una propuesta en menos de 24 horas.</p>
            <div class="actions">
                <button type="button" class="btn" data-open-chat>Abrir el asistente</button>
                @if ($wa)
                    <a class="btn btn-ghost" href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener">WhatsApp</a>
                @endif
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="wrap">© {{ date('Y') }} {{ $owner['name'] }} · Desarrollo web e inteligencia artificial</div>
</footer>

<script src="{{ asset('chatbot/widget.js') }}" data-endpoint="{{ route('chat.store') }}" defer></script>
</body>
</html>
