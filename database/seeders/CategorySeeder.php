<?php

namespace Database\Seeders;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programação', 'slug' => 'programacao', 'color' => '#667eea', 'description' => 'Cursos de desenvolvimento de software e programação'],
            ['name' => 'Marketing Digital', 'slug' => 'marketing-digital', 'color' => '#f093fb', 'description' => 'Estratégias de marketing online e redes sociais'],
            ['name' => 'Design', 'slug' => 'design', 'color' => '#fa709a', 'description' => 'Design gráfico, UX/UI e criação visual'],
            ['name' => 'Fotografia', 'slug' => 'fotografia', 'color' => '#30cfd0', 'description' => 'Técnicas de fotografia e edição de imagens'],
            ['name' => 'Idiomas', 'slug' => 'idiomas', 'color' => '#a8edea', 'description' => 'Aprenda novos idiomas'],
            ['name' => 'Desenvolvimento Pessoal', 'slug' => 'desenvolvimento-pessoal', 'color' => '#ffecd2', 'description' => 'Produtividade e crescimento pessoal'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'color' => $category['color'],
                'description' => $category['description'],
            ]);
        }
    }
}
