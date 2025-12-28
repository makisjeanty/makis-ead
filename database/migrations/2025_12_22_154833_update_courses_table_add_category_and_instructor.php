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
            // Only add columns if they don't exist
            if (!Schema::hasColumn('courses', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('courses', 'long_description')) {
                $table->text('long_description')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('courses', 'image')) {
                $table->string('image')->nullable()->after('long_description');
            }
            
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 10, 2)->default(0.00)->after('image');
            }
            
            if (!Schema::hasColumn('courses', 'level')) {
                $table->string('level')->default('Iniciante')->after('price');
            }
            
            if (!Schema::hasColumn('courses', 'duration_hours')) {
                $table->integer('duration_hours')->default(0)->after('level');
            }
            
            if (!Schema::hasColumn('courses', 'instructor_name')) {
                $table->string('instructor_name')->nullable()->after('duration_hours');
            }
            
            if (!Schema::hasColumn('courses', 'instructor_bio')) {
                $table->text('instructor_bio')->nullable()->after('instructor_name');
            }
            
            if (!Schema::hasColumn('courses', 'instructor_avatar')) {
                $table->string('instructor_avatar')->nullable()->after('instructor_bio');
            }
            
            if (!Schema::hasColumn('courses', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0.00)->after('instructor_avatar');
            }
            
            if (!Schema::hasColumn('courses', 'students_count')) {
                $table->integer('students_count')->default(0)->after('rating');
            }
            
            // Add indexes
            $table->index('category_id');
            $table->index('level');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'long_description',
                'image',
                'price',
                'level',
                'duration_hours',
                'instructor_name',
                'instructor_bio',
                'instructor_avatar',
                'rating',
                'students_count'
            ]);
        });
    }
};
