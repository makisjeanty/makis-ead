<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@ead.com',
            'password' => bcrypt('12345678'),
            'is_admin' => true, // se tiver controle de admin
        ]);

        // Alunos de teste
        User::factory()->count(5)->create();
    }
}
