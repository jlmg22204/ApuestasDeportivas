@extends('layouts.admin')

@section('title', 'Nuevo Evento')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0">Nuevo Evento</h5>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.events.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small">Deporte</label>
                    <select name="sport_id" class="form-select @error('sport_id') is-invalid @enderror">
                        <option value="">Seleccionar...</option>
                        @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                            {{ $sport->icon }} {{ $sport->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('sport_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Liga / Torneo (opcional)</label>
                    <input type="text" name="league" class="form-control" value="{{ old('league') }}" placeholder="Ej: Champions League">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Equipo Local</label>
                    <input type="text" name="home_team" class="form-control @error('home_team') is-invalid @enderror"
                           value="{{ old('home_team') }}" placeholder="Nombre del equipo local">
                    @error('home_team')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Equipo Visitante</label>
                    <input type="text" name="away_team" class="form-control @error('away_team') is-invalid @enderror"
                           value="{{ old('away_team') }}" placeholder="Nombre del equipo visitante">
                    @error('away_team')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label text-muted small">Fecha y Hora del Evento</label>
                    <input type="datetime-local" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
                           value="{{ old('event_date') }}">
                    @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12"><hr style="border-color:#2d3748;"><p class="text-muted small mb-1">Cuotas</p></div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Victoria Local</label>
                    <input type="number" name="odd_home" class="form-control @error('odd_home') is-invalid @enderror"
                           value="{{ old('odd_home', '1.80') }}" step="0.01" min="1.01">
                    @error('odd_home')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Empate (dejar en blanco si no aplica)</label>
                    <input type="number" name="odd_draw" class="form-control @error('odd_draw') is-invalid @enderror"
                           value="{{ old('odd_draw', '3.20') }}" step="0.01" min="1.01">
                    @error('odd_draw')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Victoria Visitante</label>
                    <input type="number" name="odd_away" class="form-control @error('odd_away') is-invalid @enderror"
                           value="{{ old('odd_away', '4.00') }}" step="0.01" min="1.01">
                    @error('odd_away')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-gold w-100 mt-4">Crear Evento</button>
        </form>
    </div>
</div>
@endsection
