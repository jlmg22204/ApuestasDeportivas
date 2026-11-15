@extends('layouts.admin')

@section('title', 'Conversaciones del asistente')

@section('content')
    <h1>Conversaciones del asistente IA</h1>

    <div class="stats">
        <div class="stat"><b>{{ number_format($stats['conversations']) }}</b><span>Conversaciones</span></div>
        <div class="stat"><b>{{ number_format($stats['leads']) }}</b><span>Leads capturados</span></div>
        <div class="stat"><b>{{ number_format($stats['new_leads']) }}</b><span>Leads sin contactar</span></div>
        <div class="stat"><b>{{ number_format($stats['tokens']) }}</b><span>Tokens usados</span></div>
    </div>

    <div class="filters">
        <a href="{{ route('admin.conversations.index') }}" @if(!$onlyLeads) aria-current="page" @endif>Todas</a>
        <a href="{{ route('admin.conversations.index', ['leads' => 1]) }}" @if($onlyLeads) aria-current="page" @endif>Solo con lead</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Última actividad</th>
                <th>Lead</th>
                <th>Proyecto</th>
                <th>Mensajes</th>
                <th>Estado</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($conversations as $c)
                <tr>
                    <td>{{ optional($c->last_message_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($c->lead)
                            <strong>{{ $c->lead->name }}</strong><br>
                            <small>{{ $c->lead->phone ?? $c->lead->email }}</small>
                        @else
                            <span style="color:var(--muted)">Sin datos</span>
                        @endif
                    </td>
                    <td>{{ $c->lead->project_type ?? '-' }}</td>
                    <td>{{ $c->messages_count }}</td>
                    <td>
                        @if ($c->lead)
                            <span class="pill pill-{{ $c->lead->status }}">{{ ucfirst($c->lead->status) }}</span>
                        @endif
                    </td>
                    <td><a href="{{ route('admin.conversations.show', $c) }}">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="6">Aún no hay conversaciones.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-links">{{ $conversations->links() }}</div>
@endsection
