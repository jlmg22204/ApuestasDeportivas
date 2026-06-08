<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $transactions = WalletTransaction::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('wallet.index', compact('transactions'));
    }
}
