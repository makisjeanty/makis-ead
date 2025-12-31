<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Category;
use Illuminate\Support\Str;

class PythonAICourseSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Category Exists
        $category = Category::firstOrCreate(
            ['slug' => 'programacao'],
            ['name' => 'Programação', 'icon' => 'heroicon-o-code-bracket']
        );

        // 2. Create Course
        $course = Course::where('slug', 'python-com-ia-do-zero-ao-chatbot')->first();

        if (!$course) {
            $course = Course::create([
                'title' => 'Python com IA: Do Zero ao Chatbot',
                'slug' => 'python-com-ia-do-zero-ao-chatbot',
                'description' => 'Aprenda os fundamentos de Python e integre com Inteligência Artificial para criar seus primeiros projetos.',
                'long_description' => 'Neste curso prático e gratuito, você vai aprender a configurar seu ambiente de desenvolvimento, entender a sintaxe básica do Python e dar os primeiros passos com APIs de Inteligência Artificial. Ideal para iniciantes que querem entrar no mundo da tecnologia.',
                'price' => 0.00,
                'is_published' => true,
                'level' => 'beginner',
                'category_id' => $category->id,
                'image_url' => 'courses/image/python-ai.jpg', // Placeholder
                'instructor_name' => 'Makis AI',
            ]);
        } else {
            // If exists, ensure modules are cleared to avoid duplication/errors during re-seed
            $course->modules()->delete();
        }

        // 3. Create Modules & Lessons

        // Module 1: Intro
        $mod1 = Module::create([
            'course_id' => $course->id,
            'title' => 'Introdução e Configuração',
            'sort_order' => 1,
            // 'is_published' => true, // Removed as column doesn't exist
        ]);

        Lesson::create([
            'module_id' => $mod1->id,
            'title' => 'Bem-vindo ao Curso',
            'content' => 'Visão geral do que vamos aprender.',
            'video_url' => 'https://www.youtube.com/embed/placeholder1',
            'sort_order' => 1,
            'xp_reward' => 10
        ]);

        Lesson::create([
            'module_id' => $mod1->id,
            'title' => 'Instalando Python e VS Code',
            'content' => 'Passo a passo para preparar seu computador.',
            'video_url' => 'https://www.youtube.com/embed/placeholder2',
            'sort_order' => 2,
            'xp_reward' => 20
        ]);

        // Module 2: Python Basics
        $mod2 = Module::create([
            'course_id' => $course->id,
            'title' => 'Fundamentos do Python',
            'sort_order' => 2,
            // 'is_published' => true,
        ]);

        Lesson::create([
            'module_id' => $mod2->id,
            'title' => 'Variáveis e Tipos de Dados',
            'content' => 'Entendendo como guardar informações.',
            'video_url' => 'https://www.youtube.com/embed/placeholder3',
            'sort_order' => 1,
            'xp_reward' => 30
        ]);

        Lesson::create([
            'module_id' => $mod2->id,
            'title' => 'Estruturas de Controle (If/Else)',
            'content' => 'Tomando decisões no código.',
            'video_url' => 'https://www.youtube.com/embed/placeholder4',
            'sort_order' => 2,
            'xp_reward' => 30
        ]);

        // Module 3: AI Integration
        $mod3 = Module::create([
            'course_id' => $course->id,
            'title' => 'Integrando com IA',
            'sort_order' => 3,
            // 'is_published' => true,
        ]);

        Lesson::create([
            'module_id' => $mod3->id,
            'title' => 'O que é uma API?',
            'content' => 'Conceitos básicos de comunicação entre sistemas.',
            'video_url' => 'https://www.youtube.com/embed/placeholder5',
            'sort_order' => 1,
            'xp_reward' => 40
        ]);

        Lesson::create([
            'module_id' => $mod3->id,
            'title' => 'Criando um Chatbot Simples',
            'content' => 'Usando a API da OpenAI com Python.',
            'video_url' => 'https://www.youtube.com/embed/placeholder6',
            'sort_order' => 2,
            'xp_reward' => 100
        ]);

        $this->command->info("Curso 'Python com IA' criado com sucesso!");
    }
}
