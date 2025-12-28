<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    // Tela da Carteira (Saldo + Histórico)
    public function index()
    {
        $user = Auth::user();
        $transactions = $user->wallet->transactions()->latest()->paginate(10);
        return view('student.wallet.index', compact('user', 'transactions'));
    }

    // Processar Recarga (Simulando MonCash)
    public function deposit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10']);

        // AQUI ENTRARIA A CHAMADA API MONCASH REAL
        // $paymentUrl = MonCashService::createPayment(...)
        // return redirect($paymentUrl);

        // --- SIMULAÇÃO ---
        $user = Auth::user();
        
        // Deposita na carteira usando a lógica segura
        $user->wallet->deposit(
            $request->amount, 
            'Recarga via MonCash', 
            'MON-' . Str::upper(Str::random(8))
        );

        return redirect()->back()->with('success', 'Recarga de HTG ' . number_format($request->amount, 2) . ' realizada com sucesso!');
    }
}
