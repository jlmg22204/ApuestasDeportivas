@extends('layouts.admin')

@section('title', 'Eventos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Gestión de Eventos</h5>
    <a href="{{ route('admin.events.create') }}" class="btn btn-gold btn-sm px-4">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Evento
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr style="color:#718096; font-size:.82rem; border-bottom:1px solid #1e2d40;">
                    <th class="ps-3">Deporte</th>
                    <th>Evento</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Resultado</th>
                    <th>Apuestas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td class="ps-3">{{ $event->sport->icon }}</td>
                    <td>
                        <div class="fw-semibold small">{{ $event->home_team }} vs {{ $event->away_team }}</div>
                        @if($event->league)<div class="text-muted" style="font-size:.75rem;">{{ $event->league }}</div>@endif
                    </td>
                    <td class="small text-muted">{{ $event->event_date->format('d/m/y H:i') }}</td>
                    <td><span class="badge bg-{{ $event->status_color }}">{{ $event->status_label }}</span></td>
                    <td class="small">
                        @if($event->status === 'finished')
                            {{ $event->home_score }} - {{ $event->away_score }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td><span class="badge bg-secondary">{{ $event->bets_count }}</span></td>
                    <td>
                        @if($event->status !== 'finished' && $event->status !== 'cancelled')
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                        @if($event->status === 'finished' || $event->status === 'live')
                        <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal"
                                data-bs-target="#resultModal{{ $event->id }}">
                            <i class="bi bi-flag-fill"></i>
                        </button>
                        @endif
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este evento?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- Result Modal --}}
                @if(in_array($event->status, ['live', 'finished']))
                <div class="modal fade" id="resultModal{{ $event->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="background:#1a1f2e; border:1px solid #2d3748;">
                            <div class="modal-header border-0">
                                <h6 class="modal-title">Registrar Resultado</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.events.result', $event) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <p class="text-muted small">{{ $event->home_team }} vs {{ $event->away_team }}</p>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small text-muted">{{ $event->home_team }}</label>
                                            <input type="number" name="home_score" class="form-control" min="0" value="{{ $event->home_score ?? 0 }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small text-muted">{{ $event->away_team }}</label>
                                            <input type="number" name="away_score" class="form-control" min="0" value="{{ $event->away_score ?? 0 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-gold">
                                        <i class="bi bi-check-circle me-1"></i>Registrar y Resolver Apuestas
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay eventos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $events->links() }}</div>
</div>
@endsection
