<?php

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use Illuminate\Support\Facades\Log;

// Fix autoload path
$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/vendor/autoload.php'
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        break;
    }
}

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n--- Validating Mercado Pago Integration ---\n";

$accessToken = config('services.mercadopago.access_token');

if (!$accessToken) {
    echo "Error: Mercado Pago Access Token not found in config.\n";
    exit(1);
}

echo "Access Token found (" . substr($accessToken, 0, 10) . "...)\n";

try {
    MercadoPagoConfig::setAccessToken($accessToken);

    $client = new PreferenceClient();
    $preferenceRequest = [
        "items" => [
            [
                "title" => "Test Item",
                "quantity" => 1,
                "unit_price" => 10.00,
                "currency_id" => "BRL"
            ]
        ],
        "back_urls" => [
            "success" => "https://etuderapide.com/success",
            "failure" => "https://etuderapide.com/failure",
            "pending" => "https://etuderapide.com/pending"
        ],
        "auto_return" => "approved",
    ];

    $preference = $client->create($preferenceRequest);

    echo "Preference created successfully!\n";
    echo "ID: " . $preference->id . "\n";
    echo "Init Point: " . $preference->init_point . "\n";
    echo "Sandbox Init Point: " . $preference->sandbox_init_point . "\n";

} catch (\Exception $e) {
    echo "Error creating preference: " . $e->getMessage() . "\n";
    exit(1);
}
