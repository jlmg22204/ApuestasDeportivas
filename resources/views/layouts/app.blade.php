<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inicio') — Apuestas Deportivas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f1117; color: #e8e8e8; }
        .navbar { background: linear-gradient(135deg, #1a1f2e 0%, #0d1b2a 100%); border-bottom: 1px solid #2d3748; }
        .navbar-brand { font-weight: 700; color: #f6c90e !important; font-size: 1.4rem; }
        .nav-link { color: #a0aec0 !important; transition: color .2s; }
        .nav-link:hover, .nav-link.active { color: #f6c90e !important; }
        .card { background: #1a1f2e; border: 1px solid #2d3748; border-radius: 12px; }
        .card-header { background: #151a27; border-bottom: 1px solid #2d3748; border-radius: 12px 12px 0 0 !important; }
        .event-card { transition: transform .2s, box-shadow .2s; }
        .event-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(246,201,14,.15); }
        .odd-btn { cursor: pointer; border: 2px solid #2d3748; border-radius: 8px; padding: 8px 12px; text-align: center; transition: all .2s; background: #0f1117; color: #e8e8e8; }
        .odd-btn:hover { border-color: #f6c90e; color: #f6c90e; }
        .odd-btn.selected { border-color: #f6c90e; background: rgba(246,201,14,.1); color: #f6c90e; }
        .balance-badge { background: linear-gradient(135deg, #f6c90e, #e0a800); color: #0f1117; font-weight: 700; border-radius: 20px; padding: 4px 14px; }
        .sidebar { background: #151a27; border-right: 1px solid #2d3748; min-height: calc(100vh - 60px); }
        .sidebar .nav-link { color: #a0aec0; border-radius: 8px; margin: 2px 8px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(246,201,14,.1); color: #f6c90e !important; }
        .table { color: #e8e8e8; }
        .table > :not(caption) > * > * { background: transparent; }
        .table-hover > tbody > tr:hover > * { background: rgba(246,201,14,.05); }
        .form-control, .form-select { background: #0f1117; border: 1px solid #2d3748; color: #e8e8e8; }
        .form-control:focus, .form-select:focus { background: #0f1117; border-color: #f6c90e; color: #e8e8e8; box-shadow: 0 0 0 .2rem rgba(246,201,14,.15); }
        .btn-gold { background: linear-gradient(135deg, #f6c90e, #e0a800); color: #0f1117; font-weight: 700; border: none; }
        .btn-gold:hover { background: linear-gradient(135deg, #ffd93d, #f6c90e); color: #0f1117; }
        .sport-pill { background: #1a1f2e; border: 1px solid #2d3748; color: #a0aec0; padding: 6px 16px; border-radius: 20px; cursor: pointer; transition: all .2s; text-decoration: none; display: inline-block; }
        .sport-pill:hover, .sport-pill.active { background: rgba(246,201,14,.15); border-color: #f6c90e; color: #f6c90e; }
        .stat-card { border-left: 4px solid #f6c90e; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #2d3748; border-radius: 3px; }
    </style>
    @yield('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('home') }}">
            🏆 ApuestasDeportivas
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <i class="bi bi-list text-light fs-4"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door me-1"></i>Inicio
                    </a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bets.*') ? 'active' : '' }}" href="{{ route('bets.index') }}">
                        <i class="bi bi-ticket-perforated me-1"></i>Mis Apuestas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('wallet.*') ? 'active' : '' }}" href="{{ route('wallet.index') }}">
                        <i class="bi bi-wallet2 me-1"></i>Wallet
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link text-warning {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-shield-lock me-1"></i>Admin
                    </a>
                </li>
                @endif
                @endauth
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                <li class="nav-item me-3">
                    <span class="balance-badge">
                        <i class="bi bi-coin me-1"></i>$ {{ number_format(auth()->user()->balance, 2) }}
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="background:#1a1f2e; border:1px solid #2d3748;">
                        <li><a class="dropdown-item text-light" href="{{ route('wallet.index') }}"><i class="bi bi-wallet2 me-2"></i>Mi Wallet</a></li>
                        <li><a class="dropdown-item text-light" href="{{ route('bets.index') }}"><i class="bi bi-ticket me-2"></i>Mis Apuestas</a></li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a></li>
                <li class="nav-item ms-2"><a class="btn btn-gold btn-sm px-3" href="{{ route('register') }}">Registrarse</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
</div>
@endif

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
