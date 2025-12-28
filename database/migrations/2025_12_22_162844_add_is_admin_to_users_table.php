<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Usamos boolean para admin, mas adicionamos um status para controle de conta
            $table->boolean('is_admin')->default(false)->after('email')->index();
            
            // Sugestão: Adicionar status para poder banir ou suspender usuários
            $table->string('status')->default('active')->after('is_admin'); 
            
            // Para o sistema de afiliados que você planeja
            $table->string('affiliate_code')->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'status', 'affiliate_code']);
        });
    }
};