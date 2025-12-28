<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Removemos o is_admin anterior para centralizar no 'role'
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }

            // Adicionamos o Enum com suporte a futuros papéis
            $table->enum('role', ['admin', 'student', 'teacher', 'affiliate'])
                  ->default('student')
                  ->after('email')
                  ->index(); // O índice que você sugeriu é essencial para performance
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->boolean('is_admin')->default(false); // Reverte para o estado anterior se necessário
        });
    }
};