<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Panel del asistente')</title>
    <link rel="stylesheet" href="{{ asset('css/portafolio.css') }}">
    <style>
        .admin { padding: 32px 0 64px; }
        .admin h1 { margin: 0 0 20px; font-size: 1.6rem; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .stat { border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px; }
        .stat b { display: block; font-size: 1.5rem; }
        .stat span { color: var(--muted); font-size: 14px; }
        .table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: top; }
        th { background: var(--bg-alt); font-weight: 600; }
        .pill { display: inline-block; padding: 1px 9px; border-radius: 999px; font-size: 12px; background: var(--bg-alt); border: 1px solid var(--border); }
        .pill-nuevo { background: #e5f4e4; border-color: #b9dfb6; color: #1d5a1a; }
        .filters { display: flex; gap: 12px; margin-bottom: 14px; font-size: 14px; }
        .chat { display: flex; flex-direction: column; gap: 10px; max-width: 760px; }
        .bubble { padding: 10px 14px; border-radius: 12px; max-width: 85%; white-space: pre-wrap; overflow-wrap: anywhere; }
        .bubble.user { align-self: flex-end; background: var(--accent); color: var(--accent-ink); }
        .bubble.assistant { align-self: flex-start; background: var(--bg-alt); border: 1px solid var(--border); }
        .lead-box { border: 1px solid var(--accent); border-radius: 12px; padding: 16px; margin-bottom: 24px; max-width: 760px; }
        .lead-box dl { display: grid; grid-template-columns: 130px 1fr; gap: 4px 12px; margin: 0 0 12px; }
        .lead-box dt { color: var(--muted); }
        .lead-box dd { margin: 0; }
        .flash { background: #e5f4e4; color: #1d5a1a; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; }
        select { font: inherit; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: var(--text); }
        .pagination-links nav { margin-top: 16px; }
    </style>
</head>
<body>
<main class="wrap admin">
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
