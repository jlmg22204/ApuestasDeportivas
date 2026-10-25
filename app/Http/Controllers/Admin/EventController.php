<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Event;
use App\Models\Odd;
use App\Models\Sport;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['sport'])
            ->withCount('bets')
            ->orderByDesc('event_date')
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $sports = Sport::where('is_active', true)->orderBy('name')->get();
        return view('admin.events.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sport_id'   => 'required|exists:sports,id',
            'home_team'  => 'required|string|max:100',
            'away_team'  => 'required|string|max:100',
            'league'     => 'nullable|string|max:100',
            'event_date' => 'required|date|after:now',
            'odd_home'   => 'required|numeric|min:1.01|max:999',
            'odd_draw'   => 'nullable|numeric|min:1.01|max:999',
            'odd_away'   => 'required|numeric|min:1.01|max:999',
        ]);

        DB::transaction(function () use ($request) {
            $event = Event::create([
                'sport_id'   => $request->sport_id,
                'home_team'  => $request->home_team,
                'away_team'  => $request->away_team,
                'league'     => $request->league,
                'event_date' => $request->event_date,
                'status'     => 'upcoming',
            ]);

            Odd::create(['event_id' => $event->id, 'type' => 'home', 'label' => 'Victoria Local', 'value' => $request->odd_home]);
            if ($request->odd_draw) {
                Odd::create(['event_id' => $event->id, 'type' => 'draw', 'label' => 'Empate', 'value' => $request->odd_draw]);
            }
            Odd::create(['event_id' => $event->id, 'type' => 'away', 'label' => 'Victoria Visitante', 'value' => $request->odd_away]);
        });

        return redirect()->route('admin.events.index')->with('success', 'Evento creado correctamente.');
    }

    public function edit(Event $event)
    {
        $sports = Sport::where('is_active', true)->orderBy('name')->get();
        $odds = $event->odds()->get()->keyBy('type');
        return view('admin.events.edit', compact('event', 'sports', 'odds'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'sport_id'   => 'required|exists:sports,id',
            'home_team'  => 'required|string|max:100',
            'away_team'  => 'required|string|max:100',
            'league'     => 'nullable|string|max:100',
            'event_date' => 'required|date',
            'status'     => 'required|in:upcoming,live,finished,cancelled',
            'odd_home'   => 'required|numeric|min:1.01|max:999',
            'odd_draw'   => 'nullable|numeric|min:1.01|max:999',
            'odd_away'   => 'required|numeric|min:1.01|max:999',
        ]);

        DB::transaction(function () use ($request, $event) {
            $event->update([
                'sport_id'   => $request->sport_id,
                'home_team'  => $request->home_team,
                'away_team'  => $request->away_team,
                'league'     => $request->league,
                'event_date' => $request->event_date,
                'status'     => $request->status,
            ]);

            $this->upsertOdd($event, 'home', 'Victoria Local', $request->odd_home);
            if ($request->odd_draw) {
                $this->upsertOdd($event, 'draw', 'Empate', $request->odd_draw);
            }
            $this->upsertOdd($event, 'away', 'Victoria Visitante', $request->odd_away);
        });

        return redirect()->route('admin.events.index')->with('success', 'Evento actualizado correctamente.');
    }

    public function result(Request $request, Event $event)
    {
        $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        if ($event->status === 'finished') {
            return back()->with('error', 'El evento ya fue procesado.');
        }

        $homeScore = (int) $request->home_score;
        $awayScore = (int) $request->away_score;

        $result = match (true) {
            $homeScore > $awayScore => 'home',
            $awayScore > $homeScore => 'away',
            default                  => 'draw',
        };

        DB::transaction(function () use ($event, $homeScore, $awayScore, $result) {
            $event->update([
                'status'     => 'finished',
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'result'     => $result,
            ]);

            $pendingBets = Bet::with(['user', 'odd'])->where('event_id', $event->id)->where('status', 'pending')->get();

            foreach ($pendingBets as $bet) {
                $won = $bet->odd->type === $result;
                $bet->status = $won ? 'won' : 'lost';
                $bet->save();

                if ($won) {
                    $user = $bet->user;
                    $balanceBefore = $user->balance;
                    $user->balance += $bet->potential_winnings;
                    $user->save();

                    WalletTransaction::create([
                        'user_id'        => $user->id,
                        'type'           => 'winning',
                        'amount'         => $bet->potential_winnings,
                        'balance_before' => $balanceBefore,
                        'balance_after'  => $user->balance,
                        'description'    => "Ganancia apuesta #{$bet->id}: {$event->home_team} vs {$event->away_team}",
                        'reference_id'   => $bet->id,
                    ]);
                }
            }
        });

        return back()->with('success', 'Resultado registrado. Las apuestas fueron resueltas automáticamente.');
    }

    public function destroy(Event $event)
    {
        if ($event->bets()->whereIn('status', ['pending', 'won', 'lost'])->exists()) {
            return back()->with('error', 'No se puede eliminar un evento con apuestas activas.');
        }

        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Evento eliminado.');
    }

    private function upsertOdd(Event $event, string $type, string $label, float $value): void
    {
        $odd = $event->odds()->where('type', $type)->first();
        if ($odd) {
            $odd->update(['value' => $value, 'label' => $label]);
        } else {
            Odd::create(['event_id' => $event->id, 'type' => $type, 'label' => $label, 'value' => $value]);
        }
    }
}
