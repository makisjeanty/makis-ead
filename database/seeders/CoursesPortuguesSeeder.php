<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use Illuminate\Support\Str;

class CoursesPortuguesSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar cursos existentes
        Course::truncate();
        
        $courses = [
            [
                'title' => 'Desenvolvimento Web Completo',
                'description' => 'Aprenda HTML, CSS, JavaScript e frameworks modernos do zero ao avançado.',
                'category' => 'Programação',
                'price' => 197.00,
                'level' => 'Iniciante',
                'rating' => 4.8,
                'students_count' => 1250,
                'duration_hours' => 40,
                'instructor_name' => 'Carlos Silva',
                'instructor_bio' => 'Desenvolvedor Full Stack com 10 anos de experiência.',
            ],
            [
                'title' => 'Marketing Digital Avançado',
                'description' => 'Domine estratégias de marketing digital, SEO, tráfego pago e redes sociais.',
                'category' => 'Marketing',
                'price' => 247.00,
                'level' => 'Intermediário',
                'rating' => 4.9,
                'students_count' => 980,
                'duration_hours' => 35,
                'instructor_name' => 'Ana Paula',
                'instructor_bio' => 'Especialista em Marketing Digital com certificações Google e Facebook.',
            ],
            [
                'title' => 'Design Gráfico Profissional',
                'description' => 'Crie designs incríveis com Photoshop, Illustrator e Figma.',
                'category' => 'Design',
                'price' => 197.00,
                'level' => 'Iniciante',
                'rating' => 4.7,
                'students_count' => 750,
                'duration_hours' => 30,
                'instructor_name' => 'Roberto Lima',
                'instructor_bio' => 'Designer gráfico premiado com 15 anos de experiência.',
            ],
            [
                'title' => 'Fotografia Profissional',
                'description' => 'Aprenda técnicas profissionais de fotografia e edição de imagens.',
                'category' => 'Design',
                'price' => 297.00,
                'level' => 'Intermediário',
                'rating' => 4.6,
                'students_count' => 620,
                'duration_hours' => 25,
                'instructor_name' => 'Marina Costa',
                'instructor_bio' => 'Fotógrafa profissional especializada em retratos e eventos.',
            ],
            [
                'title' => 'Inglês Fluente em 6 Meses',
                'description' => 'Método comprovado para alcançar fluência em inglês rapidamente.',
                'category' => 'Idiomas',
                'price' => 347.00,
                'level' => 'Iniciante',
                'rating' => 4.9,
                'students_count' => 1500,
                'duration_hours' => 50,
                'instructor_name' => 'John Smith',
                'instructor_bio' => 'Professor nativo com 20 anos de experiência em ensino de inglês.',
            ],
            [
                'title' => 'Produtividade e Gestão do Tempo',
                'description' => 'Técnicas comprovadas para aumentar sua produtividade e alcançar seus objetivos.',
                'category' => 'Desenvolvimento Pessoal',
                'price' => 147.00,
                'level' => 'Iniciante',
                'rating' => 4.5,
                'students_count' => 890,
                'duration_hours' => 15,
                'instructor_name' => 'Paulo Mendes',
                'instructor_bio' => 'Coach de produtividade e palestrante motivacional.',
            ],
            [
                'title' => 'Python para Iniciantes',
                'description' => 'Aprenda programação Python do zero, com projetos práticos.',
                'category' => 'Programação',
                'price' => 197.00,
                'level' => 'Iniciante',
                'rating' => 4.8,
                'students_count' => 1100,
                'duration_hours' => 35,
                'instructor_name' => 'Fernando Santos',
                'instructor_bio' => 'Engenheiro de Software especializado em Python e Machine Learning.',
            ],
            [
                'title' => 'Excel Avançado para Negócios',
                'description' => 'Domine fórmulas, tabelas dinâmicas e automação com VBA.',
                'category' => 'Negócios',
                'price' => 147.00,
                'level' => 'Intermediário',
                'rating' => 4.7,
                'students_count' => 850,
                'duration_hours' => 20,
                'instructor_name' => 'Juliana Oliveira',
                'instructor_bio' => 'Consultora de negócios e especialista em análise de dados.',
            ],
            [
                'title' => 'UX/UI Design Completo',
                'description' => 'Aprenda a criar interfaces incríveis e experiências memoráveis.',
                'category' => 'Design',
                'price' => 297.00,
                'level' => 'Intermediário',
                'rating' => 4.9,
                'students_count' => 720,
                'duration_hours' => 40,
                'instructor_name' => 'Beatriz Almeida',
                'instructor_bio' => 'UX Designer com experiência em startups e grandes empresas.',
            ],
            [
                'title' => 'Gestão de Redes Sociais',
                'description' => 'Estratégias para crescer e engajar sua audiência nas redes sociais.',
                'category' => 'Marketing',
                'price' => 197.00,
                'level' => 'Iniciante',
                'rating' => 4.6,
                'students_count' => 950,
                'duration_hours' => 25,
                'instructor_name' => 'Camila Rodrigues',
                'instructor_bio' => 'Social Media Manager com cases de sucesso em diversas marcas.',
            ],
            [
                'title' => 'Empreendedorismo Digital',
                'description' => 'Como criar e escalar seu negócio online do zero.',
                'category' => 'Negócios',
                'price' => 397.00,
                'level' => 'Intermediário',
                'rating' => 4.8,
                'students_count' => 680,
                'duration_hours' => 45,
                'instructor_name' => 'Ricardo Martins',
                'instructor_bio' => 'Empreendedor serial e mentor de startups.',
            ],
            [
                'title' => 'Edição de Vídeos Profissional',
                'description' => 'Aprenda a editar vídeos como um profissional com Premiere e After Effects.',
                'category' => 'Design',
                'price' => 247.00,
                'level' => 'Iniciante',
                'rating' => 4.7,
                'students_count' => 580,
                'duration_hours' => 30,
                'instructor_name' => 'Lucas Ferreira',
                'instructor_bio' => 'Editor de vídeo profissional com trabalhos em TV e cinema.',
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create([
                'title' => $courseData['title'],
                'slug' => Str::slug($courseData['title']),
                'description' => $courseData['description'],
                'category' => $courseData['category'],
                'price' => $courseData['price'],
                'level' => $courseData['level'],
                'rating' => $courseData['rating'],
                'students_count' => $courseData['students_count'],
                'duration_hours' => $courseData['duration_hours'],
                'instructor_name' => $courseData['instructor_name'] ?? null,
                'instructor_bio' => $courseData['instructor_bio'] ?? null,
                'is_published' => true,
                'image_url' => 'https://picsum.photos/seed/' . rand(1, 1000) . '/400/250',
            ]);
        }
    }
}
