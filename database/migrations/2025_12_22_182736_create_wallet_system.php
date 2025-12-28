<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renomeado para 'wallets' (padrão Laravel)
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Saldo e Moeda
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->char('currency', 3)->default('USD'); // Importante para o mercado internacional
            
            // Segurança e Controle
            $table->string('status')->default('active'); // active, frozen, suspended
            $table->timestamp('last_transaction_at')->nullable();
            
            $table->timestamps();

            // Índices para performance financeira
            $table->index(['user_id', 'currency']);
        });

        // Tabela de Histórico (Transações) - Essencial para auditoria
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdraw', 'purchase', 'refund', 'affiliate_commission']);
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            
            // Referência cruzada com a tabela de pagamentos que criamos antes
            $table->unsignedBigInteger('reference_id')->nullable(); // ID do Payment ou Order
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};