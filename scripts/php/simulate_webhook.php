<?php

// Definir o caminho para o autoload.php
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/vendor/autoload.php'
];

$autoloadFound = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $autoloadFound = true;
        break;
    }
}

if (!$autoloadFound) {
    exit('Autoload not found');
}

// Tenta carregar o app.php
$bootstrapPaths = [
    __DIR__ . '/../bootstrap/app.php',
    __DIR__ . '/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/bootstrap/app.php'
];

$app = null;
foreach ($bootstrapPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        break;
    }
}

if (!$app) {
     exit('Bootstrap app.php not found');
}
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WebhookService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

echo "--- Simulating Mercado Pago Webhook ---\n";

try {
    // Manually instantiate the service since we are in a script context
    $paymentService = app(PaymentService::class);
    $webhookService = new WebhookService($paymentService);

    $fakePaymentId = '1234567890'; // Use a fake ID, we expect it to fail payment lookup but PASS the webhook logic flow
    
    // Simulate a "payment.created" or "payment.updated" event
    $payload = [
        "action" => "payment.created",
        "api_version" => "v1",
        "data" => [
            "id" => $fakePaymentId 
        ],
        "date_created" => now()->toIso8601String(),
        "id" => 123456,
        "live_mode" => true,
        "type" => "payment",
        "user_id" => "123456789"
    ];

    echo "Payload prepared. Processing...\n";

    // We expect this to throw an exception or return error because the payment ID doesn't exist in MP
    // But we want to verify it DOES NOT crash on code errors (syntax, missing classes, etc.)
    try {
        $result = $webhookService->processMercadoPago($payload);
        print_r($result);
    } catch (\Exception $e) {
        // If it fails because payment not found, that's GOOD (integration works)
        // If it fails because "Class not found", that's BAD
        echo "Result: Exception caught (Expected if payment ID is fake)\n";
        echo "Message: " . $e->getMessage() . "\n";
        
        if (str_contains($e->getMessage(), 'payment not found') || str_contains($e->getMessage(), '404')) {
             echo "SUCCESS: Logic flow validated (Payment lookup attempted).\n";
        } else {
             echo "WARNING: Check error message above.\n";
        }
    }

} catch (\Throwable $t) {
    echo "CRITICAL ERROR: " . $t->getMessage() . "\n";
    echo $t->getTraceAsString();
}
