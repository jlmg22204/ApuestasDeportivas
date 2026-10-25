<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Event;
use App\Models\User;
use App\Models\WalletTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'          => User::where('role', 'user')->count(),
            'events'         => Event::count(),
            'pending_bets'   => Bet::where('status', 'pending')->count(),
            'total_wagered'  => Bet::whereIn('status', ['pending', 'won', 'lost'])->sum('amount'),
        ];

        $recentBets = Bet::with(['user', 'event', 'odd'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentEvents = Event::with('sport')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBets', 'recentEvents'));
    }
}
