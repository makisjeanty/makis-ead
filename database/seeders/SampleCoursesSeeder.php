<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Seeder;

class SampleCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample categories first
        $webDevCategory = Category::create([
            'name' => 'Desenvolvimento Web',
            'slug' => 'desenvolvimento-web'
        ]);
        
        $designCategory = Category::create([
            'name' => 'Design Gráfico',
            'slug' => 'design-grafico'
        ]);
        
        $marketingCategory = Category::create([
            'name' => 'Marketing Digital',
            'slug' => 'marketing-digital'
        ]);
        
        $businessCategory = Category::create([
            'name' => 'Negócios',
            'slug' => 'negocios'
        ]);
        
        $photographyCategory = Category::create([
            'name' => 'Fotografia',
            'slug' => 'fotografia'
        ]);

        // Create some sample courses
        $courses = [
            [
                'title' => 'Curso Completo de Desenvolvimento Web',
                'slug' => 'curso-completo-desenvolvimento-web',
                'description' => 'Aprenda HTML, CSS, JavaScript e frameworks modernos',
                'long_description' => 'Curso completo de desenvolvimento web com projetos práticos. Aprenda desde os fundamentos até tecnologias avançadas.',
                'price' => 197,
                'level' => 'iniciante',
                'duration_hours' => 40,
                'instructor_name' => 'Carlos Silva',
                'instructor_bio' => 'Desenvolvedor Full Stack com 10 anos de experiência',
                'rating' => 4.8,
                'students_count' => 1250,
                'is_published' => true,
                'image' => 'https://placehold.co/600x400/6B21A8/FFFFFF?text=Desenvolvimento+Web',
                'category_id' => $webDevCategory->id,
            ],
            [
                'title' => 'Design Gráfico Profissional',
                'slug' => 'design-grafico-profissional',
                'description' => 'Domine ferramentas como Photoshop, Illustrator e InDesign',
                'long_description' => 'Curso completo de design gráfico com projetos práticos. Aprenda técnicas profissionais de design.',
                'price' => 175,
                'level' => 'intermediário',
                'duration_hours' => 35,
                'instructor_name' => 'Ana Costa',
                'instructor_bio' => 'Designer gráfica com 8 anos de experiência em agências renomadas',
                'rating' => 4.7,
                'students_count' => 980,
                'is_published' => true,
                'image' => 'https://placehold.co/600x400/9333EA/FFFFFF?text=Design+Gráfico',
                'category_id' => $designCategory->id,
            ],
            [
                'title' => 'Marketing Digital Estratégico',
                'slug' => 'marketing-digital-estrategico',
                'description' => 'Estratégias de marketing digital para aumentar vendas',
                'long_description' => 'Aprenda estratégias eficazes de marketing digital, SEO, anúncios e redes sociais.',
                'price' => 150,
                'level' => 'intermediário',
                'duration_hours' => 30,
                'instructor_name' => 'Roberto Almeida',
                'instructor_bio' => 'Especialista em marketing digital com mais de 500 clientes atendidos',
                'rating' => 4.9,
                'students_count' => 1500,
                'is_published' => true,
                'image' => 'https://placehold.co/600x400/0D9488/FFFFFF?text=Marketing+Digital',
                'category_id' => $marketingCategory->id,
            ],
            [
                'title' => 'Gestão de Negócios Modernos',
                'slug' => 'gestao-negocios-modernos',
                'description' => 'Técnicas modernas de gestão e empreendedorismo',
                'long_description' => 'Aprenda práticas modernas de gestão empresarial e empreendedorismo digital.',
                'price' => 0, // Free course
                'level' => 'iniciante',
                'duration_hours' => 25,
                'instructor_name' => 'Mariana Oliveira',
                'instructor_bio' => 'Empresária e consultora com 15 anos de experiência em gestão',
                'rating' => 4.6,
                'students_count' => 2100,
                'is_published' => true,
                'image' => 'https://placehold.co/600x400/F59E0B/FFFFFF?text=Gestão+Negócios',
                'category_id' => $businessCategory->id,
            ],
            [
                'title' => 'Fotografia Profissional',
                'slug' => 'fotografia-profissional',
                'description' => 'Técnicas avançadas de fotografia e edição',
                'long_description' => 'Domine técnicas profissionais de fotografia, composição e edição de imagem.',
                'price' => 145,
                'level' => 'iniciante',
                'duration_hours' => 32,
                'instructor_name' => 'Pedro Santos',
                'instructor_bio' => 'Fotógrafo profissional com premiações internacionais',
                'rating' => 4.8,
                'students_count' => 850,
                'is_published' => true,
                'image' => 'https://placehold.co/600x400/EF4444/FFFFFF?text=Fotografia',
                'category_id' => $photographyCategory->id,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}