<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    */
    'currencies' => [
        'HTG' => [
            'name' => 'Gourde Haïtienne',
            'symbol' => 'G',
            'code' => 'HTG',
            'decimals' => 2,
            'countries' => ['Haiti'],
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'code' => 'USD',
            'decimals' => 2,
            'countries' => ['USA', 'Dominican Republic'],
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'decimals' => 2,
            'countries' => ['France', 'French Guiana'],
        ],
        'CAD' => [
            'name' => 'Canadian Dollar',
            'symbol' => 'CA$',
            'code' => 'CAD',
            'decimals' => 2,
            'countries' => ['Canada'],
        ],
        'BRL' => [
            'name' => 'Real Brasileiro',
            'symbol' => 'R$',
            'code' => 'BRL',
            'decimals' => 2,
            'countries' => ['Brazil'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    */
    'default' => env('DEFAULT_CURRENCY', 'HTG'),

    /*
    |--------------------------------------------------------------------------
    | Exchange Rates (Base: USD)
    |--------------------------------------------------------------------------
    | These should be updated regularly via API or manual updates
    */
    'exchange_rates' => [
        'USD' => 1.00,
        'HTG' => 132.50,  // 1 USD = 132.50 HTG (approximate)
        'EUR' => 0.92,    // 1 USD = 0.92 EUR
        'CAD' => 1.35,    // 1 USD = 1.35 CAD
        'BRL' => 4.95,    // 1 USD = 4.95 BRL
    ],

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate API
    |--------------------------------------------------------------------------
    */
    'api' => [
        'provider' => env('CURRENCY_API_PROVIDER', 'exchangerate-api'),
        'key' => env('CURRENCY_API_KEY'),
        'cache_duration' => 3600, // 1 hour
    ],
];
