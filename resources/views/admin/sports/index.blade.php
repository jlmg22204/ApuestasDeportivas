@extends('layouts.admin')

@section('title', 'Deportes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Gestión de Deportes</h5>
    <a href="{{ route('admin.sports.create') }}" class="btn btn-gold btn-sm px-4">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Deporte
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr style="color:#718096; font-size:.85rem; border-bottom:1px solid #1e2d40;">
                    <th class="ps-3">Ícono</th>
                    <th>Nombre</th>
                    <th>Eventos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sports as $sport)
                <tr>
                    <td class="ps-3 fs-4">{{ $sport->icon }}</td>
                    <td class="fw-semibold">{{ $sport->name }}</td>
                    <td><span class="badge bg-secondary">{{ $sport->events_count }}</span></td>
                    <td>
                        <span class="badge {{ $sport->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $sport->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.sports.edit', $sport) }}" class="btn btn-sm btn-outline-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.sports.destroy', $sport) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('¿Eliminar este deporte?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay deportes registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
