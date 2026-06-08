<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title', 'Panel') — Apuestas Deportivas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #0a0e1a; color: #e8e8e8; }
        .admin-sidebar { background: #0f1117; border-right: 1px solid #1e2d40; min-height: 100vh; width: 240px; position: fixed; top: 0; left: 0; z-index: 100; }
        .admin-content { margin-left: 240px; padding: 0; }
        .sidebar-brand { background: linear-gradient(135deg, #1a1f2e, #151a27); border-bottom: 1px solid #1e2d40; padding: 16px 20px; }
        .sidebar-brand a { color: #f6c90e !important; font-weight: 700; font-size: 1.1rem; text-decoration: none; }
        .sidebar-nav .nav-link { color: #8899aa; padding: 10px 20px; border-radius: 0; transition: all .2s; display: flex; align-items: center; gap: 10px; }
        .sidebar-nav .nav-link:hover { background: rgba(246,201,14,.08); color: #f6c90e; }
        .sidebar-nav .nav-link.active { background: rgba(246,201,14,.12); color: #f6c90e; border-right: 3px solid #f6c90e; }
        .sidebar-nav .nav-section { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #4a5568; padding: 12px 20px 4px; }
        .admin-topbar { background: #0f1117; border-bottom: 1px solid #1e2d40; padding: 12px 24px; position: sticky; top: 0; z-index: 99; }
        .card { background: #1a1f2e; border: 1px solid #1e2d40; border-radius: 10px; }
        .card-header { background: #0f1117; border-bottom: 1px solid #1e2d40; }
        .table { color: #e8e8e8; }
        .table > :not(caption) > * > * { background: transparent; }
        .table-hover > tbody > tr:hover > * { background: rgba(246,201,14,.04); }
        .form-control, .form-select { background: #0a0e1a; border: 1px solid #1e2d40; color: #e8e8e8; }
        .form-control:focus, .form-select:focus { background: #0a0e1a; border-color: #f6c90e; color: #e8e8e8; box-shadow: 0 0 0 .2rem rgba(246,201,14,.12); }
        .btn-gold { background: linear-gradient(135deg, #f6c90e, #e0a800); color: #0f1117; font-weight: 700; border: none; }
        .btn-gold:hover { background: linear-gradient(135deg, #ffd93d, #f6c90e); color: #0f1117; }
        .stat-card { border: 1px solid #1e2d40; }
        .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        @media(max-width:768px) {
            .admin-sidebar { transform: translateX(-100%); transition: transform .3s; }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-content { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-fill-check me-2"></i>Admin Panel</a>
    </div>
    <div class="sidebar-nav mt-2">
        <div class="nav-section">General</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section">Gestión</div>
        <a href="{{ route('admin.events.index') }}" class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i> Eventos
        </a>
        <a href="{{ route('admin.sports.index') }}" class="nav-link {{ request()->routeIs('admin.sports.*') ? 'active' : '' }}">
            <i class="bi bi-trophy"></i> Deportes
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Usuarios
        </a>

        <div class="nav-section">App</div>
        <a href="{{ route('home') }}" class="nav-link">
            <i class="bi bi-house-door"></i> Ver Sitio
        </a>
        <form action="{{ route('logout') }}" method="POST" class="px-3 mt-2">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
            </button>
        </form>
    </div>
</div>

<div class="admin-content">
    <div class="admin-topbar d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted small">Admin</span>
            <span class="text-muted small mx-2">/</span>
            <span class="small">@yield('title', 'Panel')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3 mb-0">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show m-3 mb-0">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="p-4">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
