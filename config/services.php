<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mercado Pago Configuration
    |--------------------------------------------------------------------------
    */
    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PagSeguro Configuration
    |--------------------------------------------------------------------------
    */
    'pagseguro' => [
        'email' => env('PAGSEGURO_EMAIL'),
        'token' => env('PAGSEGURO_TOKEN'),
        'sandbox' => env('PAGSEGURO_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | MonCash Configuration
    |--------------------------------------------------------------------------
    */
    'moncash' => [
        'client_id' => env('MONCASH_CLIENT_ID'),
        'client_secret' => env('MONCASH_CLIENT_SECRET'),
        'mode' => env('MONCASH_MODE', 'sandbox'), // sandbox or production
        'endpoint' => env('MONCASH_ENDPOINT', 'https://sandbox.moncashbutton.digicelgroup.com'),
    ],
];
