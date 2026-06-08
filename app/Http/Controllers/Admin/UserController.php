<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->withCount('bets')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $bets = $user->bets()->with(['event.sport', 'odd'])->orderByDesc('created_at')->paginate(10);
        $transactions = $user->walletTransactions()->orderByDesc('created_at')->paginate(10);

        return view('admin.users.show', compact('user', 'bets', 'transactions'));
    }

    public function addCredit(Request $request, User $user)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $amount = (float) $request->amount;
        $description = $request->description ?: "Depósito administrativo";

        DB::transaction(function () use ($user, $amount, $description) {
            $balanceBefore = $user->balance;
            $user->balance += $amount;
            $user->save();

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->balance,
                'description'    => $description,
            ]);
        });

        return back()->with('success', "Se agregaron $" . number_format($amount, 2) . " al saldo de {$user->name}.");
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$status} correctamente.");
    }
}
