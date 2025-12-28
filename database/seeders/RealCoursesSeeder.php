<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealCoursesSeeder extends Seeder
{
    public function run(): void
    {
        // Desabilitar foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Limpar dados existentes
        Lesson::truncate();
        Module::truncate();
        Course::truncate();
        Category::truncate();
        
        // Reabilitar foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. MARKETING DIGITAL
        $marketingCategory = Category::create([
            'name' => 'Marketing Digital',
            'slug' => 'marketing-digital',
            'description' => 'Cursos para gerar receita online através de conteúdo e estratégias digitais',
        ]);

        $this->createCourse($marketingCategory, [
            'title' => 'Criação de Conteúdo Rentável (TikTok, Reels, Shorts)',
            'description' => 'Aprenda a criar conteúdo viral que gera visualizações, leads e vendas nas principais plataformas de vídeo curto.',
            'price' => 97.00,
            'level' => 'beginner',
            'modules' => [
                'Fundamentos do Conteúdo Viral' => [
                    'O que torna um vídeo viral',
                    'Algoritmos do TikTok, Instagram e YouTube',
                    'Nichos rentáveis para 2025',
                ],
                'Criação e Edição' => [
                    'Ferramentas gratuitas de edição',
                    'Roteiros que convertem',
                    'Thumbnails e primeiros 3 segundos',
                ],
                'Monetização' => [
                    'Programa de parceiros',
                    'Marketing de afiliação em vídeos',
                    'Vendendo produtos próprios',
                ],
            ],
        ]);

        $this->createCourse($marketingCategory, [
            'title' => 'Blog + SEO: De Zero à Monetização',
            'description' => 'Construa um blog lucrativo do zero, domine SEO e gere tráfego orgânico do Google para criar receita passiva.',
            'price' => 127.00,
            'level' => 'beginner',
            'modules' => [
                'Fundação do Blog' => [
                    'Escolher nicho rentável',
                    'WordPress vs plataformas gratuitas',
                    'Estrutura de site que converte',
                ],
                'SEO na Prática' => [
                    'Pesquisa de palavras-chave',
                    'Otimização on-page',
                    'Link building para iniciantes',
                ],
                'Monetização' => [
                    'Google AdSense',
                    'Marketing de afiliação',
                    'Produtos digitais próprios',
                ],
            ],
        ]);

        $this->createCourse($marketingCategory, [
            'title' => 'Marketing de Afiliação Completo',
            'description' => 'Ganhe comissões promovendo produtos da Amazon, CPA e produtos digitais sem criar nada.',
            'price' => 97.00,
            'level' => 'beginner',
            'modules' => [
                'Introdução ao Afiliado' => [
                    'Como funciona o marketing de afiliação',
                    'Melhores programas para iniciantes',
                    'Amazon Associates vs CPA vs Digitais',
                ],
                'Estratégias de Promoção' => [
                    'Conteúdo que converte',
                    'Email marketing para afiliados',
                    'Tráfego pago vs orgânico',
                ],
                'Escala e Automação' => [
                    'Funis de vendas',
                    'Automação de email',
                    'Análise de métricas',
                ],
            ],
        ]);

        $this->createCourse($marketingCategory, [
            'title' => 'E-commerce Simples: Dropshipping Local',
            'description' => 'Monte uma loja online sem estoque, venda rápido e minimize riscos com dropshipping local.',
            'price' => 147.00,
            'level' => 'intermediate',
            'modules' => [
                'Fundamentos do E-commerce' => [
                    'Dropshipping vs estoque próprio',
                    'Escolher produtos vencedores',
                    'Fornecedores locais confiáveis',
                ],
                'Montando a Loja' => [
                    'Shopify vs WooCommerce',
                    'Design que converte',
                    'Checkout otimizado',
                ],
                'Marketing e Vendas' => [
                    'Facebook Ads para e-commerce',
                    'Instagram Shopping',
                    'Atendimento e pós-venda',
                ],
            ],
        ]);

        $this->createCourse($marketingCategory, [
            'title' => 'Freelance Online: Serviços Digitais',
            'description' => 'Ganhe em USD/EUR oferecendo serviços digitais de qualquer lugar do mundo.',
            'price' => 97.00,
            'level' => 'beginner',
            'modules' => [
                'Começando como Freelancer' => [
                    'Serviços mais demandados',
                    'Definir preços competitivos',
                    'Criar portfólio do zero',
                ],
                'Plataformas e Clientes' => [
                    'Upwork, Fiverr, Freelancer.com',
                    'Como conseguir primeiros clientes',
                    'Propostas que vendem',
                ],
                'Escala e Profissionalização' => [
                    'Aumentar preços gradualmente',
                    'Clientes recorrentes',
                    'Terceirizar e escalar',
                ],
            ],
        ]);

        // 2. IMIGRAÇÃO & INTEGRAÇÃO
        $immigrationCategory = Category::create([
            'name' => 'Imigração & Integração',
            'slug' => 'imigracao-integracao',
            'description' => 'Cursos de idiomas práticos para trabalho e integração em novos países',
            
        ]);

        $this->createCourse($immigrationCategory, [
            'title' => 'Português Prático para Trabalho (Brasil)',
            'description' => 'Domine o português brasileiro essencial para conseguir emprego e se comunicar no ambiente profissional.',
            'price' => 77.00,
            'level' => 'beginner',
            'modules' => [
                'Português Básico' => [
                    'Apresentação pessoal',
                    'Vocabulário do dia a dia',
                    'Pronúncia brasileira',
                ],
                'Português Profissional' => [
                    'Entrevistas de emprego',
                    'Email e comunicação formal',
                    'Reuniões e apresentações',
                ],
                'Cultura e Integração' => [
                    'Expressões brasileiras',
                    'Cultura de trabalho no Brasil',
                    'Networking em português',
                ],
            ],
        ]);

        $this->createCourse($immigrationCategory, [
            'title' => 'Espanhol Prático para Trabalho (Chile/México)',
            'description' => 'Aprenda espanhol focado no mercado de trabalho do Chile e México.',
            'price' => 77.00,
            'level' => 'beginner',
            'modules' => [
                'Espanhol Básico' => [
                    'Apresentação e saudações',
                    'Vocabulário essencial',
                    'Diferenças Chile vs México',
                ],
                'Espanhol Profissional' => [
                    'Currículo e entrevistas',
                    'Comunicação no trabalho',
                    'Documentos e contratos',
                ],
                'Integração Cultural' => [
                    'Costumes locais',
                    'Gírias e expressões',
                    'Vida profissional',
                ],
            ],
        ]);

        $this->createCourse($immigrationCategory, [
            'title' => 'Francês Profissional para Imigrantes',
            'description' => 'Francês prático para despachos administrativos e integração profissional.',
            'price' => 87.00,
            'level' => 'beginner',
            'modules' => [
                'Francês Administrativo' => [
                    'Documentos e formulários',
                    'Comunicação com órgãos públicos',
                    'Vocabulário jurídico básico',
                ],
                'Francês Profissional' => [
                    'Entrevistas de emprego',
                    'Comunicação corporativa',
                    'Apresentações formais',
                ],
                'Integração Social' => [
                    'Cultura francesa',
                    'Networking profissional',
                    'Etiqueta no trabalho',
                ],
            ],
        ]);

        // 3. HABILIDADES TÉCNICAS
        $techCategory = Category::create([
            'name' => 'Habilidades Técnicas',
            'slug' => 'habilidades-tecnicas',
            'description' => 'Cursos práticos de programação e tecnologia para criar projetos reais',
            
        ]);

        $this->createCourse($techCategory, [
            'title' => 'WordPress Rápido para Negócios',
            'description' => 'Crie sites profissionais com WordPress sem programar, ideal para negócios e freelancers.',
            'price' => 97.00,
            'level' => 'beginner',
            'modules' => [
                'Fundamentos WordPress' => [
                    'Instalação e configuração',
                    'Temas e plugins essenciais',
                    'Estrutura de páginas',
                ],
                'Design e Personalização' => [
                    'Elementor para iniciantes',
                    'Design responsivo',
                    'Otimização de velocidade',
                ],
                'SEO e Monetização' => [
                    'SEO no WordPress',
                    'WooCommerce básico',
                    'Manutenção e segurança',
                ],
            ],
        ]);

        $this->createCourse($techCategory, [
            'title' => 'Laravel: Criação de Sites Profissionais',
            'description' => 'Aprenda Laravel para criar aplicações web robustas e escaláveis.',
            'price' => 197.00,
            'level' => 'intermediate',
            'modules' => [
                'Fundamentos Laravel' => [
                    'Instalação e ambiente',
                    'MVC e estrutura',
                    'Rotas e controllers',
                ],
                'Banco de Dados' => [
                    'Eloquent ORM',
                    'Migrations e seeders',
                    'Relacionamentos',
                ],
                'Projeto Prático' => [
                    'CRUD completo',
                    'Autenticação',
                    'Deploy em produção',
                ],
            ],
        ]);

        $this->createCourse($techCategory, [
            'title' => 'Automações Simples com Python',
            'description' => 'Automatize tarefas repetitivas e ganhe produtividade com Python.',
            'price' => 127.00,
            'level' => 'beginner',
            'modules' => [
                'Python Básico' => [
                    'Sintaxe e variáveis',
                    'Estruturas de controle',
                    'Funções e módulos',
                ],
                'Automações Práticas' => [
                    'Manipulação de arquivos',
                    'Web scraping',
                    'Automação de emails',
                ],
                'Projetos Reais' => [
                    'Bot do WhatsApp',
                    'Automação de planilhas',
                    'Agendamento de tarefas',
                ],
            ],
        ]);

        // 4. BUSINESS & MINDSET
        $businessCategory = Category::create([
            'name' => 'Business & Mindset',
            'slug' => 'business-mindset',
            'description' => 'Desenvolva mentalidade empreendedora e estratégias de negócios',
            
        ]);

        $this->createCourse($businessCategory, [
            'title' => 'Monetizar Suas Competências',
            'description' => 'Transforme suas habilidades em ofertas rentáveis, defina preços e conquiste clientes.',
            'price' => 97.00,
            'level' => 'beginner',
            'modules' => [
                'Descobrir Seu Valor' => [
                    'Identificar competências rentáveis',
                    'Posicionamento de mercado',
                    'Criar oferta irresistível',
                ],
                'Precificação Estratégica' => [
                    'Como definir preços',
                    'Pacotes e upsells',
                    'Negociação com clientes',
                ],
                'Aquisição de Clientes' => [
                    'Onde encontrar clientes',
                    'Proposta de valor',
                    'Fechamento de vendas',
                ],
            ],
        ]);

        $this->createCourse($businessCategory, [
            'title' => 'Personal Branding para Imigrantes',
            'description' => 'Construa uma marca pessoal forte que abre portas profissionais em qualquer país.',
            'price' => 87.00,
            'level' => 'beginner',
            'modules' => [
                'Fundamentos do Branding' => [
                    'O que é personal branding',
                    'Definir seu nicho',
                    'Proposta de valor única',
                ],
                'Presença Digital' => [
                    'LinkedIn otimizado',
                    'Conteúdo que atrai',
                    'Networking online',
                ],
                'Monetização' => [
                    'Oportunidades de trabalho',
                    'Parcerias e colaborações',
                    'Consultoria e mentorias',
                ],
            ],
        ]);

        $this->createCourse($businessCategory, [
            'title' => 'Vender no WhatsApp & Facebook Marketplace',
            'description' => 'Domine as vendas em plataformas sociais e marketplace sem investimento inicial.',
            'price' => 67.00,
            'level' => 'beginner',
            'modules' => [
                'Vendas no WhatsApp' => [
                    'WhatsApp Business',
                    'Catálogo de produtos',
                    'Atendimento que converte',
                ],
                'Facebook Marketplace' => [
                    'Criar anúncios eficazes',
                    'Precificação competitiva',
                    'Negociação e fechamento',
                ],
                'Escala e Automação' => [
                    'Chatbots básicos',
                    'Gestão de pedidos',
                    'Fidelização de clientes',
                ],
            ],
        ]);

        // 5. CURSOS PREMIUM
        $premiumCategory = Category::create([
            'name' => 'Cursos Premium',
            'slug' => 'cursos-premium',
            'description' => 'Programas completos para transformação profissional e financeira',
            
        ]);

        $this->createCourse($premiumCategory, [
            'title' => 'Business Digital Completo: 0 → 1.000€/mês',
            'description' => 'Programa completo para construir um negócio digital lucrativo do zero.',
            'price' => 497.00,
            'level' => 'advanced',
            'modules' => [
                'Fundação do Negócio' => [
                    'Validação de ideia',
                    'Modelo de negócio',
                    'Plano de ação 90 dias',
                ],
                'Produto e Oferta' => [
                    'Criar produto digital',
                    'Precificação premium',
                    'Funil de vendas',
                ],
                'Tráfego e Vendas' => [
                    'Estratégias de tráfego',
                    'Copywriting que converte',
                    'Automação de vendas',
                ],
                'Escala e Sistemas' => [
                    'Equipe e terceirização',
                    'Métricas e KPIs',
                    'Crescimento sustentável',
                ],
            ],
        ]);

        $this->createCourse($premiumCategory, [
            'title' => 'Criação de Formação Online Rentável',
            'description' => 'Aprenda a criar, lançar e vender cursos online de alto valor.',
            'price' => 397.00,
            'level' => 'intermediate',
            'modules' => [
                'Planejamento do Curso' => [
                    'Validar demanda',
                    'Estrutura pedagógica',
                    'Conteúdo que transforma',
                ],
                'Produção' => [
                    'Gravação profissional',
                    'Edição e plataforma',
                    'Materiais complementares',
                ],
                'Lançamento' => [
                    'Estratégia de lançamento',
                    'Vendas e marketing',
                    'Suporte e comunidade',
                ],
            ],
        ]);

        $this->createCourse($premiumCategory, [
            'title' => 'Sistema de Assinatura e Receitas Recorrentes',
            'description' => 'Construa um negócio de assinaturas com receita previsível e escalável.',
            'price' => 447.00,
            'level' => 'advanced',
            'modules' => [
                'Modelo de Assinatura' => [
                    'Tipos de assinatura',
                    'Precificação recorrente',
                    'Proposta de valor contínua',
                ],
                'Plataforma e Tecnologia' => [
                    'Ferramentas de assinatura',
                    'Automação de cobrança',
                    'Gestão de membros',
                ],
                'Retenção e Crescimento' => [
                    'Reduzir churn',
                    'Upsell e cross-sell',
                    'Comunidade engajada',
                ],
            ],
        ]);

        // 6. CURSO GRATUITO (LEAD MAGNET)
        $freeCategory = Category::create([
            'name' => 'Cursos Gratuitos',
            'slug' => 'cursos-gratuitos',
            'description' => 'Cursos introdutórios gratuitos para começar sua jornada',
            
        ]);

        $this->createCourse($freeCategory, [
            'title' => 'Ganhar Dinheiro Online Sem Diploma',
            'description' => 'Mini-formação gratuita: descubra 5 formas comprovadas de ganhar dinheiro online sem diploma ou experiência.',
            'price' => 0.00,
            'level' => 'beginner',
            'is_free' => true,
            'modules' => [
                'Introdução' => [
                    'Por que você não precisa de diploma',
                    'Mentalidade para ganhar online',
                    'Primeiros passos',
                ],
                '5 Formas Comprovadas' => [
                    'Freelancing de serviços simples',
                    'Revenda e dropshipping',
                    'Criação de conteúdo',
                    'Marketing de afiliação',
                    'Ensinar o que você sabe',
                ],
                'Próximos Passos' => [
                    'Escolher seu caminho',
                    'Recursos gratuitos',
                    'Comunidade e suporte',
                ],
            ],
        ]);

        $this->command->info('✅ Catálogo completo criado com sucesso!');
        $this->command->info('📚 Total de cursos: ' . Course::count());
        $this->command->info('📂 Total de categorias: ' . Category::count());
        $this->command->info('📖 Total de módulos: ' . Module::count());
        $this->command->info('📝 Total de lições: ' . Lesson::count());
    }

    private function createCourse($category, $data)
    {
        $course = Course::create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'],
            'price' => $data['price'],
            'level' => $data['level'],
            'category_id' => $category->id,
            'is_published' => true,
            'image' => null,
        ]);

        $sortOrder = 1;
        foreach ($data['modules'] as $moduleName => $lessons) {
            $module = Module::create([
                'course_id' => $course->id,
                'title' => $moduleName,
                'sort_order' => $sortOrder++,
            ]);

            $lessonSortOrder = 1;
            foreach ($lessons as $lessonTitle) {
                Lesson::create([
                    'module_id' => $module->id,
                    'title' => $lessonTitle,
                    'content' => 'Conteúdo da lição: ' . $lessonTitle,
                    'video_url' => null,
                    'sort_order' => $lessonSortOrder++,
                ]);
            }
        }
    }
}
