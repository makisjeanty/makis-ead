<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Services\Gateways\MonCashGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WalletWebhookController extends Controller
{
    protected $monCashGateway;

    public function __construct(MonCashGateway $monCashGateway)
    {
        $this->monCashGateway = $monCashGateway;
    }

    /**
     * Handle MonCash webhook for wallet deposits
     */
    public function moncash(Request $request)
    {
        Log::info('MonCash Wallet Webhook Received', $request->all());

        $payload = $request->getContent();
        $signature = $request->header('X-MonCash-Signature', '');

        // Verify webhook signature
        if (!$this->monCashGateway->verifyWebhook($payload, $signature)) {
            Log::warning('MonCash Wallet Webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        try {
            $data = json_decode($payload, true);
            $transactionId = $data['transactionId'] ?? null;
            $orderId = $data['reference'] ?? null;

            if (!$transactionId || !$orderId) {
                Log::error('MonCash Wallet Webhook: Missing transaction data');
                return response()->json(['error' => 'Missing data'], 400);
            }

            // Extract wallet transaction ID from order ID (format: WALLET-{id})
            if (!str_starts_with($orderId, 'WALLET-')) {
                Log::warning('MonCash Wallet Webhook: Invalid order ID format', ['orderId' => $orderId]);
                return response()->json(['error' => 'Invalid order ID'], 400);
            }

            $walletTransactionId = str_replace('WALLET-', '', $orderId);
            $transaction = WalletTransaction::find($walletTransactionId);

            if (!$transaction) {
                Log::error('MonCash Wallet Webhook: Transaction not found', ['id' => $walletTransactionId]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Check if already processed
            if ($transaction->isCompleted()) {
                Log::info('MonCash Wallet Webhook: Transaction already completed', ['id' => $transaction->id]);
                return response()->json(['status' => 'already_processed']);
            }

            // Get transaction status from MonCash
            $statusResult = $this->monCashGateway->getTransactionStatus($transactionId);

            if ($statusResult['status'] === '200' || $statusResult['status'] === 'success') {
                // Payment successful - credit wallet
                DB::transaction(function () use ($transaction) {
                    $wallet = $transaction->wallet;

                    // Update balance_after
                    $transaction->update([
                        'balance_after' => $wallet->balance + $transaction->amount,
                        'status' => 'completed',
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'completed_at' => now()->toISOString(),
                        ]),
                    ]);

                    // Credit wallet
                    $wallet->increment('balance', $transaction->amount);

                    Log::info('MonCash Wallet Webhook: Wallet credited', [
                        'transaction_id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'new_balance' => $wallet->fresh()->balance,
                    ]);
                });

                return response()->json(['status' => 'success']);
            } else {
                // Payment failed
                $transaction->markAsFailed();
                
                Log::warning('MonCash Wallet Webhook: Payment failed', [
                    'transaction_id' => $transaction->id,
                    'status' => $statusResult['status'],
                ]);

                return response()->json(['status' => 'failed']);
            }

        } catch (\Exception $e) {
            Log::error('MonCash Wallet Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
