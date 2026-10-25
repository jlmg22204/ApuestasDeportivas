@extends('layouts.app')

@section('title', 'Mis Apuestas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-ticket-perforated me-2 text-warning"></i>Mis Apuestas</h4>
        <a href="{{ route('home') }}" class="btn btn-gold btn-sm px-4">
            <i class="bi bi-plus-circle me-1"></i>Nueva Apuesta
        </a>
    </div>

    @if($bets->count() === 0)
    <div class="text-center py-5">
        <div style="font-size:4rem;">🎯</div>
        <h5 class="text-muted mt-3">No tienes apuestas aún</h5>
        <a href="{{ route('home') }}" class="btn btn-gold mt-3">Ver Eventos</a>
    </div>
    @else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="color:#a0aec0; font-size:.85rem; border-bottom:1px solid #2d3748;">
                        <th class="ps-3">#</th>
                        <th>Evento</th>
                        <th>Selección</th>
                        <th>Cuota</th>
                        <th>Monto</th>
                        <th>Ganancia Potencial</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bets as $bet)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $bet->id }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $bet->event->home_team }} vs {{ $bet->event->away_team }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $bet->event->sport->icon }} {{ $bet->event->sport->name }}</div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $bet->odd->label ?? '—' }}</span></td>
                        <td class="text-warning fw-bold">{{ $bet->odd_value }}</td>
                        <td>${{ number_format($bet->amount, 2) }}</td>
                        <td class="text-success fw-semibold">${{ number_format($bet->potential_winnings, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $bet->status_color }}">{{ $bet->status_label }}</span>
                        </td>
                        <td class="text-muted small">{{ $bet->created_at->format('d/m/y H:i') }}</td>
                        <td>
                            @if($bet->status === 'pending' && $bet->event->status === 'upcoming')
                            <form action="{{ route('bets.cancel', $bet) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('¿Cancelar esta apuesta?')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $bets->links() }}</div>
    @endif
</div>
@endsection
