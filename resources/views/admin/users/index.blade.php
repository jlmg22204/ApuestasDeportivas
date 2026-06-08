@extends('layouts.admin')

@section('title', 'Usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Gestión de Usuarios</h5>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr style="color:#718096; font-size:.85rem; border-bottom:1px solid #1e2d40;">
                    <th class="ps-3">Usuario</th>
                    <th>Email</th>
                    <th>Saldo</th>
                    <th>Apuestas</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-3 fw-semibold small">{{ $user->name }}</td>
                    <td class="small text-muted">{{ $user->email }}</td>
                    <td class="fw-bold text-warning">${{ number_format($user->balance, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ $user->bets_count }}</span></td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $user->created_at->format('d/m/y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay usuarios registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $users->links() }}</div>
</div>
@endsection
