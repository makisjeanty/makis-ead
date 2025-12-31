<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Índices compostos para otimizar queries que filtram por 'is_published'
            // e ordenam por outras colunas (padrão muito comum no CourseController)
            $table->index(['is_published', 'created_at'], 'idx_courses_pub_created');
            $table->index(['is_published', 'students_count'], 'idx_courses_pub_students');
            $table->index(['is_published', 'rating'], 'idx_courses_pub_rating');
            $table->index(['is_published', 'price'], 'idx_courses_pub_price');
        });

        Schema::table('modules', function (Blueprint $table) {
            // Otimizar carregamento ordenado de módulos de um curso
            // Permite pegar módulos de um curso já na ordem correta sem sort no PHP/DB pesado
            $table->index(['course_id', 'sort_order'], 'idx_modules_course_order');
        });

        Schema::table('lessons', function (Blueprint $table) {
            // Otimizar carregamento ordenado de aulas de um módulo
            $table->index(['module_id', 'sort_order'], 'idx_lessons_module_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('idx_courses_pub_created');
            $table->dropIndex('idx_courses_pub_students');
            $table->dropIndex('idx_courses_pub_rating');
            $table->dropIndex('idx_courses_pub_price');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->dropIndex('idx_modules_course_order');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('idx_lessons_module_order');
        });
    }
};
