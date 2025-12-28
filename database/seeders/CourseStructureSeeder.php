<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa as tabelas antes de começar para não duplicar
        // Desativa verificação de chave estrangeira temporariamente
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Lesson::truncate();
        Module::truncate();
        Course::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $courses = [
            [
                'title' => 'Création de contenu efficace',
                'desc' => 'Blogging, rédaction web, storytelling et planification.',
                'modules' => [
                    'Module 1: Introduction au contenu' => ['Qu’est-ce que le contenu efficace', 'Identifier son audience'],
                    'Module 2: Blogging et storytelling' => ['Techniques de rédaction', 'Storytelling pour captiver'],
                    'Module 3: Planification et organisation' => ['Calendrier éditorial', 'Outils et automatisation'],
                ],
            ],
            [
                'title' => 'Monétiser un blog ou une chaîne',
                'desc' => 'Publicité, marketing d’affiliation et stratégies.',
                'modules' => [
                    'Module 1: Bases de la monétisation' => ['Publicité en ligne', 'Partenariats et sponsoring'],
                    'Module 2: Marketing d’affiliation' => ['Choisir les programmes', 'Optimiser les conversions'],
                    'Module 3: Stratégies avancées' => ['Création de produits numériques', 'Vente de services'],
                ],
            ],
            [
                'title' => 'Lancer un e-commerce rentable',
                'desc' => 'Shopify / WooCommerce, produits et logistique.',
                'modules' => [
                    'Module 1: Choix de la plateforme' => ['Shopify vs WooCommerce', 'Configuration initiale'],
                    'Module 2: Gestion des produits' => ['Sélection de produits', 'Fiches produits efficaces'],
                    'Module 3: Vente et logistique' => ['Paiements et livraisons', 'Support client'],
                ],
            ],
            [
                'title' => 'Marketing digital pour débutants',
                'desc' => 'SEO, réseaux sociaux et publicité.',
                'modules' => [
                    'Module 1: SEO et visibilité' => ['Optimisation sur Google', 'Mots-clés et backlinks'],
                    'Module 2: Réseaux sociaux' => ['Stratégie Facebook/Instagram', 'Créer du contenu engageant'],
                    'Module 3: Publicité et newsletters' => ['Campagnes payantes', 'Email marketing'],
                ],
            ],
            [
                'title' => 'Stratégies d’affiliation et revenus passifs',
                'desc' => 'Programmes, promotion et optimisation.',
                'modules' => [
                    'Module 1: Bases de l’affiliation' => ['Choisir son niche', 'Rechercher des programmes'],
                    'Module 2: Promotion efficace' => ['Techniques de contenu', 'Optimiser les liens'],
                    'Module 3: Suivi et optimisation' => ['Mesurer les conversions', 'Ajuster sa stratégie'],
                ],
            ],
            // 10 cours supplémentaires (Simplificados em 1 Módulo)
            ['title' => 'Design et branding pour créateurs', 'desc' => 'Identité visuelle et design.', 'modules' => ['Module Unique' => ['Identité visuelle', 'Création de logos et bannières', 'Cohérence sur les réseaux']]],
            ['title' => 'Email marketing et automation', 'desc' => 'Newsletters et séquences.', 'modules' => ['Module Unique' => ['Créer une liste email', 'Séquences automatisées', 'Segmentation et personnalisation']]],
            ['title' => 'Création et vente de formations en ligne', 'desc' => 'Structurez votre savoir.', 'modules' => ['Module Unique' => ['Structurer un cours', 'Outils pour créer des vidéos et PDF', 'Plateformes de vente']]],
            ['title' => 'Monétiser YouTube ou TikTok', 'desc' => 'Vidéo et viralité.', 'modules' => ['Module Unique' => ['Création de contenu viral', 'Algorithmes et engagement', 'Partenariats et publicités']]],
            ['title' => 'Publicité payante Facebook/Instagram', 'desc' => 'Meta Ads para iniciantes.', 'modules' => ['Module Unique' => ['Création de campagnes', 'Budget et ciblage', 'Analyse des résultats']]],
            ['title' => 'Growth hacking pour petites entreprises', 'desc' => 'Crescimento rápido.', 'modules' => ['Module Unique' => ['Stratégies à faible coût', 'Expérimentation rapide', 'Outils de croissance']]],
            ['title' => 'Copywriting pour vendre', 'desc' => 'Escrita persuasiva.', 'modules' => ['Module Unique' => ['Techniques d’écriture persuasive', 'Titres accrocheurs', 'CTA et storytelling']]],
            ['title' => 'Gestion et optimisation de sites WordPress', 'desc' => 'Domine o CMS.', 'modules' => ['Module Unique' => ['Installer et configurer WordPress', 'Plugins essentiels', 'Sécurité et performance']]],
            ['title' => 'Photographie et édition pour le web', 'desc' => 'Imagens profissionais.', 'modules' => ['Module Unique' => ['Prise de photos pro avec smartphone', 'Retouche rapide', 'Optimisation pour le web']]],
            ['title' => 'Analyse des données et suivi de performance', 'desc' => 'Data Analytics.', 'modules' => ['Module Unique' => ['Google Analytics et métriques clés', 'Interprétation des données', 'Ajustement de stratégie']]],
        ];

        foreach ($courses as $courseData) {
            $course = Course::create([
                'title' => $courseData['title'],
                'slug' => Str::slug($courseData['title']) . '-' . rand(100, 999), // Slug Obrigatório
                'description' => $courseData['desc'] ?? $courseData['title'],
                'is_published' => true,
                // 'user_id' => removido pois a tabela não tem essa coluna
            ]);

            foreach ($courseData['modules'] as $moduleTitle => $lessons) {
                $module = Module::create([
                    'title' => $moduleTitle,
                    'course_id' => $course->id,
                ]);

                foreach ($lessons as $lessonTitle) {
                    Lesson::create([
                        'title' => $lessonTitle,
                        'module_id' => $module->id,
                        'content' => '<h1>' . $lessonTitle . '</h1><p>Contenu de la leçon...</p>',
                    ]);
                }
            }
            $this->command->info('Curso criado: ' . $courseData['title']);
        }
    }
}
