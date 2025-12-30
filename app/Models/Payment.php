<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    const GATEWAY_STRIPE = 'stripe';
    const GATEWAY_MERCADOPAGO = 'mercadopago';
    const GATEWAY_MONCASH = 'moncash';
    const GATEWAY_PAGSEGURO = 'pagseguro';
    
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    
    // Usamos fillable para maior segurança em atribuição em massa
    protected $fillable = [
        'order_id', 
        'user_id', 
        'course_id', 
        'gateway', 
        'transaction_id', 
        'payment_method',
        'amount', 
        'currency', 
        'gateway_fee',
        'status', 
        'metadata', 
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        // Criptografia automática para dados sensíveis do gateway (PII)
        'metadata' => 'encrypted:json', 
    ];

    /* -------------------------------------------------------------------------- */
    /* RELACIONAMENTOS                                                            */
    /* -------------------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /* -------------------------------------------------------------------------- */
    /* SCOPES (Úteis para o Filament e Relatórios)                                */
    /* -------------------------------------------------------------------------- */

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }
    
    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    /**
     * Mark payment as completed
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }
    
    /**
     * Mark payment as failed
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }
}