<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientBalanceException;

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
        return DB::transaction(function () use ($amount, $referenceType, $referenceId, $description, $metadata) {
            // Lock wallet for update
            $this->lockForUpdate()->find($this->id);

            $balanceBefore = $this->balance;
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
            $this->increment('balance', $amount);

            return $transaction;
        });
    }

    /**
     * Debit wallet (subtract funds)
     */
    public function debit(float $amount, string $referenceType, ?int $referenceId = null, ?string $description = null, array $metadata = []): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $referenceType, $referenceId, $description, $metadata) {
            // Lock wallet for update
            $wallet = $this->lockForUpdate()->find($this->id);

            // Check balance
            if (!$wallet->hasBalance($amount)) {
                throw new InsufficientBalanceException("Insufficient wallet balance. Required: {$amount}, Available: {$wallet->balance}");
            }

            $balanceBefore = $wallet->balance;
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
            $wallet->decrement('balance', $amount);

            return $transaction;
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
    }

    /**
     * Activate wallet
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }
}
