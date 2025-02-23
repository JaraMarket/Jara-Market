<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet; // Assuming you have a Wallet model

class WalletController extends Controller
{
    /**
     * Fund the user's wallet.
     */
    public function fundWallet(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => $request->user_id]);
        $wallet->balance += $request->amount;
        $wallet->save();

        return response()->json(['message' => 'Wallet funded successfully', 'balance' => $wallet->balance], 200);
    }
}
