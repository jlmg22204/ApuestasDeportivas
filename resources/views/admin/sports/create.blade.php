@extends('layouts.admin')

@section('title', 'Nuevo Deporte')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.sports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0">Nuevo Deporte</h5>
</div>

<div class="card" style="max-width:500px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.sports.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label text-muted small">Nombre del deporte</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Ej: Fútbol">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small">Ícono (emoji)</label>
                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
                       value="{{ old('icon', '🏆') }}" placeholder="⚽">
                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text text-muted">Sugerencias: ⚽ 🏀 🎾 🏈 ⚾ 🏒 🎱 🏐 🏉 🥊</div>
            </div>
            <button type="submit" class="btn btn-gold w-100">Crear Deporte</button>
        </form>
    </div>
</div>
@endsection
