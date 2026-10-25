@extends('layouts.admin')

@section('title', 'Editar Evento')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0">Editar Evento</h5>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.events.update', $event) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small">Deporte</label>
                    <select name="sport_id" class="form-select @error('sport_id') is-invalid @enderror">
                        @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" {{ old('sport_id', $event->sport_id) == $sport->id ? 'selected' : '' }}>
                            {{ $sport->icon }} {{ $sport->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('sport_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Estado</label>
                    <select name="status" class="form-select">
                        @foreach(['upcoming' => 'Próximo', 'live' => 'En Vivo', 'finished' => 'Finalizado', 'cancelled' => 'Cancelado'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $event->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Liga / Torneo</label>
                    <input type="text" name="league" class="form-control" value="{{ old('league', $event->league) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Fecha y Hora</label>
                    <input type="datetime-local" name="event_date" class="form-control"
                           value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Equipo Local</label>
                    <input type="text" name="home_team" class="form-control" value="{{ old('home_team', $event->home_team) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Equipo Visitante</label>
                    <input type="text" name="away_team" class="form-control" value="{{ old('away_team', $event->away_team) }}">
                </div>

                <div class="col-12"><hr style="border-color:#2d3748;"><p class="text-muted small mb-1">Cuotas</p></div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Victoria Local</label>
                    <input type="number" name="odd_home" class="form-control"
                           value="{{ old('odd_home', $odds->get('home')?->value ?? '1.80') }}" step="0.01" min="1.01">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Empate</label>
                    <input type="number" name="odd_draw" class="form-control"
                           value="{{ old('odd_draw', $odds->get('draw')?->value ?? '') }}" step="0.01" min="1.01">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Victoria Visitante</label>
                    <input type="number" name="odd_away" class="form-control"
                           value="{{ old('odd_away', $odds->get('away')?->value ?? '4.00') }}" step="0.01" min="1.01">
                </div>
            </div>
            <button type="submit" class="btn btn-gold w-100 mt-4">Guardar Cambios</button>
        </form>
    </div>
</div>
@endsection
