@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(102,126,234,.15);color:#667eea;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Usuarios</div>
                    <div class="fs-3 fw-bold">{{ $stats['users'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(72,187,120,.15);color:#48bb78;">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Eventos</div>
                    <div class="fs-3 fw-bold">{{ $stats['events'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(246,201,14,.15);color:#f6c90e;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted small">Apuestas Pendientes</div>
                    <div class="fs-3 fw-bold">{{ $stats['pending_bets'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(237,100,166,.15);color:#ed64a6;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Apostado</div>
                    <div class="fs-3 fw-bold">${{ number_format($stats['total_wagered'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <span class="fw-semibold small">Últimas Apuestas</span>
                <a href="{{ route('admin.users.index') }}" class="small text-warning">Ver todo →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="color:#718096; font-size:.8rem; border-bottom:1px solid #1e2d40;">
                            <th class="ps-3">Usuario</th>
                            <th>Evento</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBets as $bet)
                        <tr>
                            <td class="ps-3 small">{{ $bet->user->name }}</td>
                            <td class="small">{{ $bet->event->home_team }} vs {{ $bet->event->away_team }}</td>
                            <td class="small fw-semibold">${{ number_format($bet->amount, 2) }}</td>
                            <td><span class="badge bg-{{ $bet->status_color }} small">{{ $bet->status_label }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <span class="fw-semibold small">Últimos Eventos</span>
                <a href="{{ route('admin.events.index') }}" class="small text-warning">Ver todo →</a>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($recentEvents as $event)
                <li class="list-group-item" style="background:transparent; border-color:#1e2d40;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small fw-semibold">{{ $event->home_team }} vs {{ $event->away_team }}</div>
                            <div style="font-size:.75rem; color:#718096;">{{ $event->sport->icon }} {{ $event->sport->name }} · {{ $event->event_date->format('d/m H:i') }}</div>
                        </div>
                        <span class="badge bg-{{ $event->status_color }} small">{{ $event->status_label }}</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
