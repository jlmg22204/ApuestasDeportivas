@extends('layouts.app')

@section('title', 'Eventos')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Sport filter pills --}}
    <div class="mb-4 d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted small me-2">Filtrar:</span>
        <a href="{{ route('home') }}" class="sport-pill {{ !isset($sport) ? 'active' : '' }}">
            🏆 Todos
        </a>
        @foreach($sports as $s)
        <a href="{{ route('sport.show', $s) }}" class="sport-pill {{ isset($sport) && $sport->id === $s->id ? 'active' : '' }}">
            {{ $s->icon }} {{ $s->name }}
        </a>
        @endforeach
    </div>

    @if($events->count() === 0)
    <div class="text-center py-5">
        <div style="font-size:4rem;">⚽</div>
        <h4 class="text-muted mt-3">No hay eventos disponibles</h4>
        <p class="text-muted small">Vuelve pronto para ver los próximos partidos</p>
    </div>
    @else
    <div class="row g-3">
        @foreach($events as $event)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card event-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <span class="small text-muted">{{ $event->sport->icon }} {{ $event->sport->name }}</span>
                    @if($event->league)
                    <span class="badge bg-secondary small">{{ $event->league }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-center flex-fill">
                                <div class="fw-bold fs-6">{{ $event->home_team }}</div>
                                <div class="text-muted small">Local</div>
                            </div>
                            <div class="text-center px-3">
                                <div class="text-muted small">VS</div>
                                <div class="small text-warning">{{ $event->event_date->format('d/m H:i') }}</div>
                            </div>
                            <div class="text-center flex-fill">
                                <div class="fw-bold fs-6">{{ $event->away_team }}</div>
                                <div class="text-muted small">Visitante</div>
                            </div>
                        </div>
                    </div>

                    {{-- Odds --}}
                    <form action="{{ route('bets.store') }}" method="POST" class="bet-form">
                        @csrf
                        <input type="hidden" name="odd_id" class="selected-odd">
                        <div class="d-flex gap-2 mb-3">
                            @foreach($event->odds->where('is_active', true) as $odd)
                            <div class="odd-btn flex-fill" onclick="selectOdd(this, {{ $odd->id }})">
                                <div class="small text-muted" style="font-size:.7rem;">{{ $odd->label }}</div>
                                <div class="fw-bold text-warning">{{ number_format($odd->value, 2) }}</div>
                            </div>
                            @endforeach
                        </div>

                        <div class="input-group mb-2">
                            <span class="input-group-text" style="background:#0f1117;border-color:#2d3748;color:#a0aec0;">$</span>
                            <input type="number" name="amount" class="form-control"
                                   placeholder="Monto a apostar" min="1" step="0.50">
                        </div>
                        <div class="potential-win text-center small text-muted mb-2" style="min-height:18px;"></div>
                        <button type="submit" class="btn btn-gold w-100 btn-sm" disabled>
                            <i class="bi bi-ticket-perforated me-1"></i>Apostar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $events->links() }}</div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function selectOdd(el, oddId) {
    const form = el.closest('form');
    form.querySelectorAll('.odd-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    form.querySelector('.selected-odd').value = oddId;
    form.querySelector('button[type=submit]').disabled = false;
    updatePotential(form);
}

document.querySelectorAll('input[name=amount]').forEach(input => {
    input.addEventListener('input', () => updatePotential(input.closest('form')));
});

function updatePotential(form) {
    const selected = form.querySelector('.odd-btn.selected');
    const amount = parseFloat(form.querySelector('input[name=amount]').value);
    const winDiv = form.querySelector('.potential-win');
    if (selected && amount > 0) {
        const oddVal = parseFloat(selected.querySelector('.fw-bold').textContent);
        const potential = (amount * oddVal).toFixed(2);
        winDiv.innerHTML = `<span class="text-success">Ganancia potencial: <strong>$${potential}</strong></span>`;
    } else {
        winDiv.innerHTML = '';
    }
}
</script>
@endsection
