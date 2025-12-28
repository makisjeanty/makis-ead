<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\Gateways\MonCashGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected $monCashGateway;

    public function __construct(MonCashGateway $monCashGateway)
    {
        $this->middleware('auth');
        $this->monCashGateway = $monCashGateway;
    }

    /**
     * Show wallet dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $wallet = $user->getOrCreateWallet();
        
        // Get recent transactions
        $recentTransactions = $wallet->transactions()
            ->latest()
            ->limit(10)
            ->get();

        // Calculate statistics
        $stats = [
            'total_deposits' => $wallet->transactions()
                ->where('type', 'credit')
                ->where('reference_type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_spent' => $wallet->transactions()
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->sum('amount'),
            'pending_transactions' => $wallet->transactions()
                ->where('status', 'pending')
                ->count(),
        ];

        return view('wallet.index', compact('wallet', 'recentTransactions', 'stats'));
    }

    /**
     * Show deposit form
     */
    public function deposit()
    {
        $user = auth()->user();
        $wallet = $user->getOrCreateWallet();

        return view('wallet.deposit', compact('wallet'));
    }

    /**
     * Process deposit
     */
    public function processDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:100000',
            'gateway' => 'required|in:moncash',
        ]);

        $user = auth()->user();
        $wallet = $user->getOrCreateWallet();
        $amount = $request->amount;

        try {
            // Create pending transaction
            $transaction = $wallet->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance, // Will be updated on confirmation
                'reference_type' => 'deposit',
                'status' => 'pending',
                'description' => "Deposit via {$request->gateway}",
                'metadata' => [
                    'gateway' => $request->gateway,
                    'user_id' => $user->id,
                ],
            ]);

            // Create MonCash payment
            $result = $this->monCashGateway->createPayment($amount, "WALLET-{$transaction->id}");

            if ($result['success']) {
                // Update transaction with payment token
                $transaction->update([
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'payment_token' => $result['payment_token'],
                        'transaction_id' => $result['transaction_id'],
                    ]),
                ]);

                // Redirect to MonCash checkout
                return redirect($result['checkout_url']);
            }

            // Payment creation failed
            $transaction->markAsFailed();
            return redirect()->back()->with('error', 'Failed to create payment: ' . ($result['error'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing deposit: ' . $e->getMessage());
        }
    }

    /**
     * Transaction history
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->getOrCreateWallet();

        $query = $wallet->transactions()->latest();

        // Filter by type
        if ($request->has('type') && in_array($request->type, ['credit', 'debit'])) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'completed', 'failed'])) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(20);

        return view('wallet.history', compact('wallet', 'transactions'));
    }

    /**
     * Deposit success callback
     */
    public function depositSuccess(Request $request)
    {
        return view('wallet.deposit-success');
    }

    /**
     * Deposit failure callback
     */
    public function depositFailure(Request $request)
    {
        return view('wallet.deposit-failure');
    }
}
