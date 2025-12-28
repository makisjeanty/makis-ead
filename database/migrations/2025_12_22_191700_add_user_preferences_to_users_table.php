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
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_language', 5)->default('fr')->after('email');
            $table->string('preferred_currency', 3)->default('HTG')->after('preferred_language');
            
            $table->index('preferred_language');
            $table->index('preferred_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['preferred_language']);
            $table->dropIndex(['preferred_currency']);
            $table->dropColumn(['preferred_language', 'preferred_currency']);
        });
    }
};
