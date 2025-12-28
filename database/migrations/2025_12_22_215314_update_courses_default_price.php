<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabela de Cursos - ALTERAÇÃO para adicionar campos
        Schema::table('courses', function (Blueprint $table) {
            // Campos que faltavam na criação original
            if (!Schema::hasColumn('courses', 'thumbnail')) {
                $table->string('thumbnail')->nullable();
            }
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('courses', 'stripe_price_id')) {
                $table->string('stripe_price_id')->nullable();
            }
            if (!Schema::hasColumn('courses', 'level')) {
                $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            }
            if (!Schema::hasColumn('courses', 'is_active')) {
                $table->boolean('is_active')->default(true)->index();
            }
        });

        // Tabela de Aulas (Módulos) - Ignoramos a recriação pois 135318 já criou com estrutura de Módulos (module_id)
        // A versão do arquivo 'update_courses' tentava ligar lessons direto a courses, o que quebraria o CourseSeeder.
        // Se precisarmos de colunas extras em lessons, adicionaríamos aqui via Schema::table, mas por segurança manteremos o original.
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'price', 'stripe_price_id', 'level', 'is_active']);
        });
    }
};