<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\Event;
use App\Models\Odd;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BetController extends Controller
{
    public function index()
    {
        $bets = Bet::with(['event.sport', 'odd'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bets.index', compact('bets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'odd_id' => 'required|exists:odds,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $odd = Odd::with('event')->findOrFail($request->odd_id);
        $user = Auth::user();

        if ($odd->event->status !== 'upcoming') {
            return back()->with('error', 'Este evento ya no acepta apuestas.');
        }

        if (!$odd->is_active) {
            return back()->with('error', 'Esta cuota no está disponible.');
        }

        $amount = (float) $request->amount;

        if ($user->balance < $amount) {
            return back()->with('error', 'Saldo insuficiente. Recarga tu wallet.');
        }

        DB::transaction(function () use ($user, $odd, $amount) {
            $balanceBefore = $user->balance;
            $potentialWinnings = round($amount * $odd->value, 2);

            $user->balance -= $amount;
            $user->save();

            $bet = Bet::create([
                'user_id'           => $user->id,
                'event_id'          => $odd->event_id,
                'odd_id'            => $odd->id,
                'amount'            => $amount,
                'odd_value'         => $odd->value,
                'potential_winnings' => $potentialWinnings,
                'status'            => 'pending',
            ]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'bet',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->balance,
                'description'    => "Apuesta: {$odd->event->home_team} vs {$odd->event->away_team} - {$odd->label}",
                'reference_id'   => $bet->id,
            ]);
        });

        return redirect()->route('bets.index')->with('success', 'Apuesta realizada con éxito.');
    }

    public function cancel(Bet $bet)
    {
        if ($bet->user_id !== Auth::id()) {
            abort(403);
        }

        if ($bet->status !== 'pending') {
            return back()->with('error', 'Solo puedes cancelar apuestas pendientes.');
        }

        if ($bet->event->status !== 'upcoming') {
            return back()->with('error', 'No se puede cancelar: el evento ya comenzó.');
        }

        DB::transaction(function () use ($bet) {
            $user = $bet->user;
            $balanceBefore = $user->balance;

            $user->balance += $bet->amount;
            $user->save();

            $bet->status = 'cancelled';
            $bet->save();

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'refund',
                'amount'         => $bet->amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->balance,
                'description'    => "Reembolso por cancelación de apuesta #" . $bet->id,
                'reference_id'   => $bet->id,
            ]);
        });

        return back()->with('success', 'Apuesta cancelada. El saldo fue reembolsado.');
    }
}
