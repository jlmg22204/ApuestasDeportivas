@extends('layouts.admin')

@section('title', 'Usuario: ' . $user->name)

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0">{{ $user->name }}</h5>
    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
    </span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Saldo Actual</div>
            <div class="fs-2 fw-bold text-warning">${{ number_format($user->balance, 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Apostado</div>
            <div class="fs-2 fw-bold">${{ number_format($user->bets()->whereIn('status',['pending','won','lost'])->sum('amount'), 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Apuestas Ganadas</div>
            <div class="fs-2 fw-bold text-success">{{ $user->bets()->where('status','won')->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Apuestas Perdidas</div>
            <div class="fs-2 fw-bold text-danger">{{ $user->bets()->where('status','lost')->count() }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Add Credit form --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="mb-0"><i class="bi bi-plus-circle-fill text-success me-2"></i>Cargar Saldo</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.credit', $user) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small">Monto ($)</label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                               placeholder="0.00" min="0.01" step="0.01">
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Descripción (opcional)</label>
                        <input type="text" name="description" class="form-control" placeholder="Motivo del depósito">
                    </div>
                    <button type="submit" class="btn btn-gold w-100">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Saldo
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- User info --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header py-3"><h6 class="mb-0">Información del Usuario</h6></div>
            <div class="card-body">
                <table class="table table-sm mb-0" style="color:#e8e8e8;">
                    <tr><td class="text-muted" style="width:150px;">Nombre</td><td>{{ $user->name }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $user->email }}</td></tr>
                    <tr><td class="text-muted">Registro</td><td>{{ $user->created_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><td class="text-muted">Estado</td>
                        <td>
                            <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    {{ $user->is_active ? 'Desactivar cuenta' : 'Activar cuenta' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Bets table --}}
<div class="card mb-3">
    <div class="card-header py-3"><h6 class="mb-0">Historial de Apuestas</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr style="color:#718096; font-size:.82rem; border-bottom:1px solid #1e2d40;">
                    <th class="ps-3">#</th>
                    <th>Evento</th>
                    <th>Selección</th>
                    <th>Cuota</th>
                    <th>Monto</th>
                    <th>Ganancia Potencial</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bets as $bet)
                <tr>
                    <td class="ps-3 text-muted small">{{ $bet->id }}</td>
                    <td class="small">{{ $bet->event->home_team }} vs {{ $bet->event->away_team }}</td>
                    <td><span class="badge bg-secondary small">{{ $bet->odd->label ?? '—' }}</span></td>
                    <td class="text-warning small">{{ $bet->odd_value }}</td>
                    <td class="small">${{ number_format($bet->amount, 2) }}</td>
                    <td class="text-success small">${{ number_format($bet->potential_winnings, 2) }}</td>
                    <td><span class="badge bg-{{ $bet->status_color }} small">{{ $bet->status_label }}</span></td>
                    <td class="text-muted small">{{ $bet->created_at->format('d/m/y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Sin apuestas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $bets->links() }}</div>
</div>

{{-- Transactions --}}
<div class="card">
    <div class="card-header py-3"><h6 class="mb-0">Historial de Wallet</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr style="color:#718096; font-size:.82rem; border-bottom:1px solid #1e2d40;">
                    <th class="ps-3">Tipo</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Saldo Antes</th>
                    <th>Saldo Después</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td class="ps-3"><span class="badge bg-{{ $tx->type_color }} small">{{ $tx->type_label }}</span></td>
                    <td class="small">{{ $tx->description }}</td>
                    <td class="small fw-semibold {{ in_array($tx->type, ['deposit','winning','refund']) ? 'text-success' : 'text-danger' }}">
                        {{ in_array($tx->type, ['deposit','winning','refund']) ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                    </td>
                    <td class="text-muted small">${{ number_format($tx->balance_before, 2) }}</td>
                    <td class="small">${{ number_format($tx->balance_after, 2) }}</td>
                    <td class="text-muted small">{{ $tx->created_at->format('d/m/y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Sin transacciones</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $transactions->links() }}</div>
</div>
@endsection
