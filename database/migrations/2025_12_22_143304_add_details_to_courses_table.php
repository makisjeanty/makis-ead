<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Verifica se as colunas já existem antes de criar para evitar erros
            if (!Schema::hasColumn('courses', 'level')) {
                $table->string('level')->default('Iniciante');
                $table->decimal('price', 8, 2)->default(0.00);
                $table->decimal('rating', 2, 1)->default(5.0);
                $table->string('category')->default('Geral');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['level', 'price', 'rating', 'category']);
        });
    }
};
