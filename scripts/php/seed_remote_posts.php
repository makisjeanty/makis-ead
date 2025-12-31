<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Carregar autoload se necessário (para execução isolada)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
}

echo "Iniciando criação de posts...\n";

// Buscar o admin para ser o autor
$author = User::where('role', 'admin')->first();
if (!$author) {
    $author = User::first();
    echo "Aviso: Admin não encontrado, usando o primeiro usuário como autor.\n";
}

$posts = [
    [
        'title' => '5 Dicas Essenciais para Estudar Online com Eficiência',
        'content' => 'Estudar online exige disciplina. 1. Crie um cronograma de estudos. 2. Tenha um local tranquilo. 3. Faça pausas regulares. 4. Participe de fóruns. 5. Não deixe para a última hora. A organização é a chave para o sucesso no EAD.',
        'image' => 'https://images.unsplash.com/photo-1513258496098-f107e9f9820f?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'Como Conciliar Trabalho e Estudos sem Enlouquecer',
        'content' => 'Trabalhar e estudar é um desafio comum. A dica de ouro é aproveitar tempos mortos, como no transporte. Priorize tarefas e seja realista com seus prazos. Lembre-se: é uma fase de investimento no seu futuro profissional.',
        'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'O Futuro do Mercado de Trabalho: Habilidades em Alta',
        'content' => 'O mercado muda rápido. Soft skills como inteligência emocional, adaptabilidade e comunicação são tão valiosas quanto conhecimentos técnicos. Invista em cursos que desenvolvam essas competências comportamentais.',
        'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'Por que Aprender Inglês é Obrigatório em 2025',
        'content' => 'O inglês deixou de ser diferencial para ser requisito básico. Acesso a conteúdos globais, melhores salários e oportunidades internacionais dependem disso. Comece hoje mesmo, nem que seja 15 minutos por dia.',
        'image' => 'https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'Tecnologia na Educação: O Que Esperar dos Próximos Anos',
        'content' => 'Inteligência Artificial, Realidade Aumentada e Aprendizado Adaptativo estão revolucionando o ensino. A personalização do aprendizado fará com que cada aluno aprenda no seu próprio ritmo e estilo.',
        'image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'Vantagens de Certificados Digitais no Currículo',
        'content' => 'Certificados online validam suas horas de estudo e dedicação. Eles mostram aos recrutadores que você é proativo e busca atualização constante. Mantenha seu LinkedIn sempre atualizado com suas novas conquistas.',
        'image' => 'https://images.unsplash.com/photo-1589330694653-4d5c95331177?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'Educação Financeira: Comece a Investir Cedo',
        'content' => 'Não espere sobrar dinheiro para investir; invista para sobrar. Entenda o básico sobre juros compostos, reserva de emergência e renda fixa. O tempo é o melhor amigo do investidor jovem.',
        'image' => 'https://images.unsplash.com/photo-1579621970563-ebec7560eb3e?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'Como Melhorar sua Produtividade nos Estudos',
        'content' => 'Técnica Pomodoro, mapas mentais e resumos ativos são ótimas ferramentas. Descubra qual método funciona melhor para você. Evite o multitarefa; foque em uma coisa de cada vez para absorver melhor o conteúdo.',
        'image' => 'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'Carreira em TI: Por Onde Começar?',
        'content' => 'A área de tecnologia está em expansão. Comece pela lógica de programação. Python e JavaScript são ótimas linguagens para iniciantes. Construa um portfólio no GitHub para mostrar seus projetos práticos.',
        'image' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=800&q=80',
    ],
    [
        'title' => 'A Importância do Networking Profissional',
        'content' => 'Quem não é visto, não é lembrado. Participe de eventos, interaja no LinkedIn e mantenha contato com colegas e professores. Uma boa rede de contatos pode abrir portas que o currículo sozinho não consegue.',
        'image' => 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80',
    ],
];

foreach ($posts as $data) {
    // Verifica duplicidade pelo título
    $slug = Str::slug($data['title']);
    if (Post::where('slug', $slug)->exists()) {
        echo "Post já existe: {$data['title']}\n";
        continue;
    }

    Post::create([
        'user_id' => $author->id,
        'title' => $data['title'],
        'slug' => $slug,
        'content' => $data['content'],
        'image' => $data['image'],
        'is_published' => true,
        'published_at' => Carbon::now(),
    ]);
    
    echo "Post criado: {$data['title']}\n";
}

echo "Concluído!\n";
