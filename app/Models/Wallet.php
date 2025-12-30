<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Support\Facades\Log;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // Relationships
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Helper Methods

    /**
     * Check if wallet has sufficient balance
     */
    public function hasBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Get available balance
     */
    public function getAvailableBalance(): float
    {
        return (float) $this->balance;
    }

    /**
     * Credit wallet (add funds)
     */
    public function credit(float $amount, string $referenceType, ?int $referenceId = null, ?string $description = null, array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be greater than zero');
        }

        return DB::transaction(function () use ($amount, $referenceType, $referenceId, $description, $metadata) {
            // Lock wallet for update
            $lockedWallet = $this->lockForUpdate()->find($this->id);

            $balanceBefore = $lockedWallet->balance;
            $balanceAfter = $balanceBefore + $amount;

            // Create transaction
            $transaction = $this->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description ?? "Credit: {$referenceType}",
                'status' => 'completed',
                'metadata' => $metadata,
            ]);

            // Update wallet balance
            $lockedWallet->increment('balance', $amount);

            Log::info("Wallet credited successfully", [
                'wallet_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'new_balance' => $lockedWallet->balance,
                'transaction_id' => $transaction->id
            ]);

            return $transaction;
        });
    }

    /**
     * Debit wallet (subtract funds)
     */
    public function debit(float $amount, string $referenceType, ?int $referenceId = null, ?string $description = null, array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount must be greater than zero');
        }

        return DB::transaction(function () use ($amount, $referenceType, $referenceId, $description, $metadata) {
            // Lock wallet for update
            $lockedWallet = $this->lockForUpdate()->find($this->id);

            // Check balance
            if (!$lockedWallet->hasBalance($amount)) {
                $exception = new InsufficientBalanceException("Insufficient wallet balance. Required: {$amount}, Available: {$lockedWallet->balance}");
                
                Log::warning("Insufficient balance for debit", [
                    'wallet_id' => $this->id,
                    'user_id' => $this->user_id,
                    'required' => $amount,
                    'available' => $lockedWallet->balance
                ]);
                
                throw $exception;
            }

            $balanceBefore = $lockedWallet->balance;
            $balanceAfter = $balanceBefore - $amount;

            // Create transaction
            $transaction = $this->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description ?? "Debit: {$referenceType}",
                'status' => 'completed',
                'metadata' => $metadata,
            ]);

            // Update wallet balance
            $lockedWallet->decrement('balance', $amount);

            Log::info("Wallet debited successfully", [
                'wallet_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'new_balance' => $lockedWallet->balance,
                'transaction_id' => $transaction->id
            ]);

            return $transaction;
        });
    }

    /**
     * Transfer funds to another wallet
     */
    public function transferTo(Wallet $targetWallet, float $amount, ?string $description = null): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Transfer amount must be greater than zero');
        }

        if ($this->currency !== $targetWallet->currency) {
            throw new \InvalidArgumentException('Cannot transfer between wallets with different currencies');
        }

        if (!$this->hasBalance($amount)) {
            throw new InsufficientBalanceException('Insufficient balance for transfer');
        }

        return DB::transaction(function () use ($targetWallet, $amount, $description) {
            // Debit from source wallet
            $debitTransaction = $this->debit(
                $amount,
                'transfer_out',
                $targetWallet->id,
                $description ?? "Transfer to wallet {$targetWallet->id}"
            );

            // Credit to target wallet
            $creditTransaction = $targetWallet->credit(
                $amount,
                'transfer_in',
                $this->id,
                $description ?? "Transfer from wallet {$this->id}"
            );

            Log::info("Fund transfer completed", [
                'from_wallet_id' => $this->id,
                'to_wallet_id' => $targetWallet->id,
                'amount' => $amount,
                'debit_transaction_id' => $debitTransaction->id,
                'credit_transaction_id' => $creditTransaction->id
            ]);

            return [
                'debit_transaction' => $debitTransaction,
                'credit_transaction' => $creditTransaction
            ];
        });
    }

    /**
     * Check if wallet is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Suspend wallet
     */
    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
        Log::info("Wallet suspended", ['wallet_id' => $this->id]);
    }

    /**
     * Activate wallet
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
        Log::info("Wallet activated", ['wallet_id' => $this->id]);
    }
}