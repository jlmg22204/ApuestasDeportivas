@extends('layouts.app')

@section('title', 'Mi Wallet')

@section('content')
<div class="container py-4">
    <h4 class="mb-4"><i class="bi bi-wallet2 me-2 text-warning"></i>Mi Wallet</h4>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="text-muted small mb-1">Saldo Disponible</div>
                <div style="font-size:2.2rem; font-weight:800; color:#f6c90e;">
                    ${{ number_format(auth()->user()->balance, 2) }}
                </div>
                <div class="text-muted small mt-1">Créditos en cuenta</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="text-muted small mb-1">Total Apostado</div>
                <div style="font-size:2rem; font-weight:700; color:#e2e8f0;">
                    ${{ number_format(auth()->user()->bets()->whereIn('status', ['pending','won','lost'])->sum('amount'), 2) }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="text-muted small mb-1">Total Ganado</div>
                <div style="font-size:2rem; font-weight:700; color:#48bb78;">
                    ${{ number_format(auth()->user()->bets()->where('status', 'won')->sum('potential_winnings'), 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0 text-muted"><i class="bi bi-clock-history me-2"></i>Historial de Transacciones</h6>
        </div>
        @if($transactions->count() === 0)
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No hay transacciones aún
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="color:#a0aec0; font-size:.85rem; border-bottom:1px solid #2d3748;">
                        <th class="ps-3">Tipo</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Saldo Antes</th>
                        <th>Saldo Después</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-{{ $tx->type_color }}">{{ $tx->type_label }}</span>
                        </td>
                        <td class="small">{{ $tx->description }}</td>
                        <td class="fw-semibold {{ in_array($tx->type, ['deposit','winning','refund']) ? 'text-success' : 'text-danger' }}">
                            {{ in_array($tx->type, ['deposit','winning','refund']) ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="text-muted small">${{ number_format($tx->balance_before, 2) }}</td>
                        <td class="small">${{ number_format($tx->balance_after, 2) }}</td>
                        <td class="text-muted small">{{ $tx->created_at->format('d/m/y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
