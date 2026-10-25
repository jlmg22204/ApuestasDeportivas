@extends('layouts.admin')

@section('title', 'Editar Deporte')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.sports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0">Editar Deporte</h5>
</div>

<div class="card" style="max-width:500px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.sports.update', $sport) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label text-muted small">Nombre del deporte</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $sport->name) }}">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small">Ícono (emoji)</label>
                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
                       value="{{ old('icon', $sport->icon) }}">
                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active"
                           style="accent-color:#f6c90e;" {{ $sport->is_active ? 'checked' : '' }}>
                    <label class="form-check-label text-muted" for="active">Activo</label>
                </div>
            </div>
            <button type="submit" class="btn btn-gold w-100">Guardar Cambios</button>
        </form>
    </div>
</div>
@endsection
