<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RealCoursesSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Created categories, courses, modules, and lessons.');
        $this->command->info('Run: php artisan make:filament-user to create an admin user.');
    }
}
