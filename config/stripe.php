<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    */
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'mode' => env('STRIPE_MODE', 'test'),

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'price_id' => env('STRIPE_STARTER_PRICE_ID'),
            'price' => 20.00,
            'currency' => 'USD',
            'interval' => 'month',
            'features' => [
                'Accès à 50+ cours premium',
                'Support par email',
                'Certificats de complétion',
                'Accès application mobile',
                'Mises à jour de contenu',
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'price_id' => env('STRIPE_PROFESSIONAL_PRICE_ID'),
            'price' => 50.00,
            'currency' => 'USD',
            'interval' => 'month',
            'popular' => true,
            'features' => [
                'Accès à 200+ cours premium',
                'Support prioritaire 24/7',
                'Certificats avancés',
                'Ressources téléchargeables',
                'Sessions Q&A en direct',
                'Accès aux webinaires exclusifs',
                'Parcours d\'apprentissage personnalisés',
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price_id' => env('STRIPE_ENTERPRISE_PRICE_ID'),
            'price' => 100.00,
            'currency' => 'USD',
            'interval' => 'month',
            'features' => [
                'Accès illimité à tous les cours',
                'Support premium 24/7',
                'Parcours personnalisés',
                'Gestion d\'équipe (jusqu\'à 10 utilisateurs)',
                'Tableau de bord analytique',
                'API d\'intégration',
                'Gestionnaire de compte dédié',
                'Rapports de progression avancés',
                'Formation sur mesure',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial Period
    |--------------------------------------------------------------------------
    */
    'trial_days' => env('STRIPE_TRIAL_DAYS', 0),
];
