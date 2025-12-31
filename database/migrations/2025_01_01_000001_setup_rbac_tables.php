<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Roles Table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('label')->nullable();
                $table->timestamps();
            });
        }

        // 2. Create Permissions Table
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('label')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create Role_User Table
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'user_id']);
            });
        }

        // 4. Create Permission_Role Table
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->primary(['permission_id', 'role_id']);
            });
        }

        // 5. Seed Initial Roles
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator'],
            ['name' => 'instructor', 'label' => 'Instructor'],
            ['name' => 'student', 'label' => 'Student'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                ['label' => $role['label'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 6. Migrate Existing Users
        // Check if 'role' column exists in users table
        if (Schema::hasColumn('users', 'role')) {
            $users = DB::table('users')->whereNotNull('role')->get();
            
            foreach ($users as $user) {
                $roleName = $user->role;
                // Map legacy roles to new roles if necessary
                if ($roleName === 'user') $roleName = 'student';

                $role = DB::table('roles')->where('name', $roleName)->first();
                
                if ($role) {
                    DB::table('role_user')->updateOrInsert(
                        ['user_id' => $user->id, 'role_id' => $role->id]
                    );
                } else {
                    // Default to student if role not found
                    $studentRole = DB::table('roles')->where('name', 'student')->first();
                    if ($studentRole) {
                        DB::table('role_user')->updateOrInsert(
                            ['user_id' => $user->id, 'role_id' => $studentRole->id]
                        );
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
