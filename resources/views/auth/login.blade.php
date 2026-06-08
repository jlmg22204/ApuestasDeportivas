<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Apuestas Deportivas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: radial-gradient(ellipse at top, #1a2a4a 0%, #0f1117 60%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { background: #1a1f2e; border: 1px solid #2d3748; border-radius: 16px; padding: 40px; max-width: 420px; width: 100%; }
        .auth-logo { font-size: 2.5rem; font-weight: 800; color: #f6c90e; text-align: center; margin-bottom: 8px; }
        .auth-subtitle { color: #718096; text-align: center; margin-bottom: 32px; }
        .form-control { background: #0f1117; border: 1px solid #2d3748; color: #e8e8e8; padding: 12px; }
        .form-control:focus { background: #0f1117; border-color: #f6c90e; color: #e8e8e8; box-shadow: 0 0 0 .2rem rgba(246,201,14,.15); }
        .btn-gold { background: linear-gradient(135deg, #f6c90e, #e0a800); color: #0f1117; font-weight: 700; border: none; padding: 12px; font-size: 1rem; }
        .btn-gold:hover { background: linear-gradient(135deg, #ffd93d, #f6c90e); color: #0f1117; }
        label { color: #a0aec0; font-size: .9rem; }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-logo">🏆</div>
    <div class="auth-subtitle">Apuestas Deportivas</div>

    @if(session('error'))
    <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="tu@email.com" autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember" style="accent-color:#f6c90e;">
                <label class="form-check-label text-muted small" for="remember">Recordarme</label>
            </div>
        </div>
        <button type="submit" class="btn btn-gold w-100 rounded-3">Iniciar Sesión</button>
    </form>

    <p class="text-center text-muted mt-4 mb-0 small">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" style="color:#f6c90e;">Regístrate aquí</a>
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
