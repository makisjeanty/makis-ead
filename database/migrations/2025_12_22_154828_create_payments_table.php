<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Relacionamentos e Identificação
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Importante para auditoria direta
            
            // Gateway e Rastreamento
            $table->string('gateway'); // mercadopago, stripe, moncash, etc.
            $table->string('transaction_id')->nullable(); // ID retornado pelo Stripe/MonCash
            $table->string('payment_method')->nullable(); // pix, credit_card, cash
            
            // Valores e Moeda (Crucial para o EtudeRapide)
            $table->decimal('amount', 12, 2); // Aumentado para 12 para suportar moedas desvalorizadas
            $table->char('currency', 3)->default('USD'); // ISO 4217 (USD, BRL, HTG)
            $table->decimal('gateway_fee', 10, 2)->default(0); // Para saber quanto o Stripe descontou
            
            // Status e Controle
            $table->string('status', 20)->default('pending'); // pending, completed, failed, refunded
            $table->json('metadata')->nullable(); // Dados extras criptografados via Model
            
            // Datas
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            // Índices de Performance (Essenciais para o sistema EAD)
            $table->index(['gateway', 'transaction_id']); // Busca composta rápida
            $table->index('status');
            $table->index('created_at'); // Útil para relatórios financeiros
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};