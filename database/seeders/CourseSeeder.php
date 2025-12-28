<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Run CategorySeeder first.');
            return;
        }

        // Programming Courses
        $programmingCategory = $categories->where('slug', 'programacao')->first();
        if ($programmingCategory) {
            $this->createProgrammingCourses($programmingCategory);
        }

        // Marketing Courses
        $marketingCategory = $categories->where('slug', 'marketing-digital')->first();
        if ($marketingCategory) {
            $this->createMarketingCourses($marketingCategory);
        }

        // Design Courses
        $designCategory = $categories->where('slug', 'design')->first();
        if ($designCategory) {
            $this->createDesignCourses($designCategory);
        }

        // Photography Courses
        $photographyCategory = $categories->where('slug', 'fotografia')->first();
        if ($photographyCategory) {
            $this->createPhotographyCourses($photographyCategory);
        }

        // Language Courses
        $languageCategory = $categories->where('slug', 'idiomas')->first();
        if ($languageCategory) {
            $this->createLanguageCourses($languageCategory);
        }

        // Personal Development Courses
        $personalDevCategory = $categories->where('slug', 'desenvolvimento-pessoal')->first();
        if ($personalDevCategory) {
            $this->createPersonalDevelopmentCourses($personalDevCategory);
        }
    }

    private function createProgrammingCourses($category)
    {
        // Course 1: Laravel Completo
        $course1 = Course::create([
            'category_id' => $category->id,
            'title' => 'Laravel do Zero ao Avançado',
            'slug' => 'laravel-do-zero-ao-avancado',
            'description' => 'Aprenda a desenvolver aplicações web modernas com Laravel',
            'long_description' => 'Neste curso completo de Laravel, você aprenderá desde os conceitos básicos até técnicas avançadas de desenvolvimento. Inclui projetos práticos, autenticação, APIs RESTful, e muito mais.',
            'price' => 197.00,
            'level' => 'Intermediário',
            'duration_hours' => 40,
            'instructor_name' => 'João Silva',
            'instructor_bio' => 'Desenvolvedor Full Stack com 10 anos de experiência',
            'rating' => 4.8,
            'students_count' => 1250,
            'is_published' => true,
        ]);

        $this->createModulesAndLessons($course1, [
            'Introdução ao Laravel' => [
                'O que é Laravel e por que usar',
                'Instalação e configuração do ambiente',
                'Estrutura de pastas do Laravel',
                'Primeiro projeto: Hello World',
            ],
            'Rotas e Controllers' => [
                'Criando rotas básicas',
                'Controllers e métodos',
                'Route parameters e validação',
                'Resource Controllers',
            ],
            'Banco de Dados e Eloquent' => [
                'Migrations e Schema Builder',
                'Models e Eloquent ORM',
                'Relationships (hasMany, belongsTo)',
                'Query Builder avançado',
            ],
            'Autenticação e Autorização' => [
                'Laravel Breeze',
                'Middleware de autenticação',
                'Gates e Policies',
                'Proteção de rotas',
            ],
        ]);

        // Course 2: JavaScript Moderno
        $course2 = Course::create([
            'category_id' => $category->id,
            'title' => 'JavaScript ES6+ Completo',
            'slug' => 'javascript-es6-completo',
            'description' => 'Domine JavaScript moderno com ES6, ES7 e além',
            'long_description' => 'Aprenda JavaScript moderno com todas as features do ES6+, incluindo arrow functions, promises, async/await, destructuring, e muito mais.',
            'price' => 147.00,
            'level' => 'Iniciante',
            'duration_hours' => 30,
            'instructor_name' => 'Maria Santos',
            'instructor_bio' => 'Frontend Developer e instrutora',
            'rating' => 4.9,
            'students_count' => 2100,
            'is_published' => true,
        ]);

        $this->createModulesAndLessons($course2, [
            'Fundamentos JavaScript' => [
                'Variáveis: let, const e var',
                'Tipos de dados e operadores',
                'Estruturas de controle',
                'Funções e escopo',
            ],
            'ES6+ Features' => [
                'Arrow Functions',
                'Template Literals',
                'Destructuring',
                'Spread e Rest operators',
            ],
            'Programação Assíncrona' => [
                'Callbacks',
                'Promises',
                'Async/Await',
                'Fetch API',
            ],
        ]);
    }

    private function createMarketingCourses($category)
    {
        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'Marketing Digital para Iniciantes',
            'slug' => 'marketing-digital-para-iniciantes',
            'description' => 'Aprenda as estratégias essenciais de marketing digital',
            'long_description' => 'Curso completo de marketing digital cobrindo SEO, Google Ads, Facebook Ads, Email Marketing e Analytics.',
            'price' => 97.00,
            'level' => 'Iniciante',
            'duration_hours' => 20,
            'instructor_name' => 'Carlos Mendes',
            'instructor_bio' => 'Especialista em Marketing Digital',
            'rating' => 4.7,
            'students_count' => 890,
            'is_published' => true,
        ]);

        $this->createModulesAndLessons($course, [
            'Fundamentos do Marketing Digital' => [
                'O que é Marketing Digital',
                'Funil de vendas',
                'Personas e público-alvo',
            ],
            'SEO e Conteúdo' => [
                'Otimização para mecanismos de busca',
                'Marketing de conteúdo',
                'Keywords e pesquisa',
            ],
            'Mídias Sociais' => [
                'Facebook e Instagram Ads',
                'Estratégias de engajamento',
                'Análise de métricas',
            ],
        ]);
    }

    private function createDesignCourses($category)
    {
        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'UI/UX Design Completo',
            'slug' => 'ui-ux-design-completo',
            'description' => 'Aprenda a criar interfaces incríveis e experiências memoráveis',
            'long_description' => 'Curso completo de UI/UX Design com Figma, incluindo design thinking, prototipagem, e design systems.',
            'price' => 167.00,
            'level' => 'Intermediário',
            'duration_hours' => 35,
            'instructor_name' => 'Ana Paula',
            'instructor_bio' => 'UX Designer com 8 anos de experiência',
            'rating' => 4.9,
            'students_count' => 1450,
            'is_published' => true,
        ]);

        $this->createModulesAndLessons($course, [
            'Fundamentos de UX' => [
                'O que é UX Design',
                'Design Thinking',
                'Pesquisa com usuários',
                'Personas e jornadas',
            ],
            'UI Design' => [
                'Princípios de design visual',
                'Tipografia e cores',
                'Grids e layouts',
                'Componentes e padrões',
            ],
            'Ferramentas e Prototipagem' => [
                'Introdução ao Figma',
                'Criando wireframes',
                'Protótipos interativos',
                'Design Systems',
            ],
        ]);
    }

    private function createPhotographyCourses($category)
    {
        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'Fotografia Digital Profissional',
            'slug' => 'fotografia-digital-profissional',
            'description' => 'Do básico ao avançado em fotografia digital',
            'long_description' => 'Aprenda técnicas profissionais de fotografia, composição, iluminação e edição com Lightroom e Photoshop.',
            'price' => 127.00,
            'level' => 'Iniciante',
            'duration_hours' => 25,
            'instructor_name' => 'Pedro Costa',
            'instructor_bio' => 'Fotógrafo profissional há 15 anos',
            'rating' => 4.8,
            'students_count' => 670,
            'is_published' => true,
        ]);

        $this->createModulesAndLessons($course, [
            'Fundamentos da Fotografia' => [
                'Entendendo sua câmera',
                'Triângulo da exposição',
                'Modos de captura',
            ],
            'Composição e Iluminação' => [
                'Regra dos terços',
                'Luz natural vs artificial',
                'Golden hour',
            ],
            'Edição de Fotos' => [
                'Lightroom básico',
                'Ajustes de cor e exposição',
                'Photoshop para fotógrafos',
            ],
        ]);
    }

    private function createLanguageCourses($category)
    {
        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'Inglês do Zero ao Fluente',
            'slug' => 'ingles-do-zero-ao-fluente',
            'description' => 'Aprenda inglês de forma prática e eficiente',
            'long_description' => 'Curso completo de inglês com foco em conversação, gramática e vocabulário para o dia a dia.',
            'price' => 0.00, // Free course
            'level' => 'Iniciante',
            'duration_hours' => 50,
            'instructor_name' => 'Sarah Johnson',
            'instructor_bio' => 'Professora nativa de inglês',
            'rating' => 4.6,
            'students_count' => 3200,
            'is_published' => true,
        ]);

        $this->createModulesAndLessons($course, [
            'Básico de Inglês' => [
                'Alfabeto e pronúncia',
                'Números e cores',
                'Cumprimentos e apresentações',
                'Verbo to be',
            ],
            'Conversação Básica' => [
                'Perguntas e respostas simples',
                'Vocabulário do dia a dia',
                'Presente simples',
            ],
        ]);
    }

    private function createPersonalDevelopmentCourses($category)
    {
        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'Produtividade e Gestão do Tempo',
            'slug' => 'produtividade-e-gestao-do-tempo',
            'description' => 'Maximize sua produtividade e alcance seus objetivos',
            'long_description' => 'Aprenda técnicas comprovadas de produtividade, gestão do tempo e organização pessoal.',
            'price' => 77.00,
            'level' => 'Iniciante',
            'duration_hours' => 15,
            'instructor_name' => 'Roberto Lima',
            'instructor_bio' => 'Coach de produtividade',
            'rating' => 4.7,
            'students_count' => 1100,
            'is_published' => true,
        ]);

        $this->createModulesAndLessons($course, [
            'Fundamentos da Produtividade' => [
                'Mitos sobre produtividade',
                'Definindo prioridades',
                'Matriz de Eisenhower',
            ],
            'Técnicas e Ferramentas' => [
                'Técnica Pomodoro',
                'GTD (Getting Things Done)',
                'Ferramentas digitais',
            ],
        ]);
    }

    private function createModulesAndLessons($course, $structure)
    {
        $moduleOrder = 0;
        foreach ($structure as $moduleName => $lessons) {
            $moduleOrder++;
            $module = Module::create([
                'course_id' => $course->id,
                'title' => $moduleName,
                'sort_order' => $moduleOrder,
            ]);

            $lessonOrder = 0;
            foreach ($lessons as $lessonTitle) {
                $lessonOrder++;
                Lesson::create([
                    'module_id' => $module->id,
                    'title' => $lessonTitle,
                    'content' => $this->generateLessonContent($lessonTitle),
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Placeholder
                    'xp_reward' => 10,
                    'sort_order' => $lessonOrder,
                ]);
            }
        }
    }

    private function generateLessonContent($title)
    {
        return "# {$title}\n\n" .
               "Bem-vindo a esta aula sobre {$title}.\n\n" .
               "## Objetivos de Aprendizagem\n\n" .
               "- Compreender os conceitos fundamentais\n" .
               "- Aplicar o conhecimento em exemplos práticos\n" .
               "- Desenvolver habilidades essenciais\n\n" .
               "## Conteúdo\n\n" .
               "Lorem ipsum dolor sit amet, consectetur adipiscing elit. " .
               "Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\n\n" .
               "## Exercícios Práticos\n\n" .
               "1. Pratique os conceitos apresentados\n" .
               "2. Complete os exercícios propostos\n" .
               "3. Revise o material quando necessário\n\n" .
               "## Recursos Adicionais\n\n" .
               "- Documentação oficial\n" .
               "- Artigos recomendados\n" .
               "- Vídeos complementares";
    }
}
