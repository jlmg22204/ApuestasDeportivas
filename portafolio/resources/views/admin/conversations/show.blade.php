@extends('layouts.admin')

@section('title', 'Conversación #'.$conversation->id)

@section('content')
    <p><a href="{{ route('admin.conversations.index') }}">← Volver</a></p>
    <h1>Conversación #{{ $conversation->id }}</h1>
    <p style="color:var(--muted)">
        Iniciada {{ $conversation->created_at->format('d/m/Y H:i') }}
        · Página: {{ $conversation->page_url ?? '-' }}
        · Tokens: {{ number_format($conversation->input_tokens + $conversation->output_tokens) }}
    </p>

    @if ($lead = $conversation->lead)
        <section class="lead-box">
            <h2 style="margin:0 0 12px;font-size:1.15rem">Lead</h2>
            <dl>
                <dt>Nombre</dt><dd>{{ $lead->name }}</dd>
                <dt>Correo</dt><dd>@if($lead->email)<a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>@else - @endif</dd>
                <dt>Teléfono</dt><dd>
                    @if($lead->phone)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone) }}" target="_blank" rel="noopener">{{ $lead->phone }}</a>
                    @else - @endif
                </dd>
                <dt>Proyecto</dt><dd>{{ $lead->project_type ?? '-' }}</dd>
                <dt>Presupuesto</dt><dd>{{ $lead->budget ?? '-' }}</dd>
                <dt>Resumen</dt><dd>{{ $lead->summary }}</dd>
            </dl>
            <form method="POST" action="{{ route('admin.leads.update', $lead) }}">
                @csrf
                @method('PATCH')
                <label>Estado
                    <select name="status" onchange="this.form.submit()">
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" @selected($lead->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </label>
                <noscript><button class="btn btn-sm">Guardar</button></noscript>
            </form>
        </section>
    @endif

    <div class="chat">
        @foreach ($conversation->messages as $m)
            <div class="bubble {{ $m->role }}">{{ $m->content }}</div>
        @endforeach
    </div>
@endsection
