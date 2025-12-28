<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Dados Identificadores do Stripe (conforme seu server.rb)
            $table->string('type'); // ex: 'default', 'premium'
            $table->string('stripe_id')->unique(); // Subscription ID (sub_...)
            $table->string('stripe_status'); // active, trialing, past_due, canceled
            $table->string('stripe_price')->nullable(); // ID do preço (price_...)
            
            // Controle de Quantidade e Períodos
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // Data de término se cancelado
            
            $table->timestamps();

            // Índices para performance em consultas de acesso
            $table->index(['user_id', 'stripe_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};