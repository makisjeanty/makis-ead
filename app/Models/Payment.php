<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
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
}