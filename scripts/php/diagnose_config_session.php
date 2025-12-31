<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simular uma requisição para ver como o Laravel interpreta
// Mas para ver os headers reais, precisaríamos rodar isso via navegador.
// Como não posso acessar via navegador, vou verificar a configuração de TrustedProxies e o .env atual.

echo "--- Configuração de Sessão ---\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";
echo "SESSION_DOMAIN: " . config('session.domain') . "\n";
echo "SESSION_SECURE_COOKIE: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "SESSION_LIFETIME: " . config('session.lifetime') . "\n";
echo "SESSION_SAME_SITE: " . config('session.same_site') . "\n";
echo "SESSION_HTTP_ONLY: " . (config('session.http_only') ? 'true' : 'false') . "\n";

echo "\n--- Configuração de App ---\n";
echo "APP_URL: " . config('app.url') . "\n";

echo "\n--- Middlewares ---\n";
// Tentar ler o arquivo TrustProxies se existir
$trustProxiesPath = app_path('Http/Middleware/TrustProxies.php');
if (file_exists($trustProxiesPath)) {
    echo "TrustProxies.php encontrado.\n";
    echo file_get_contents($trustProxiesPath);
} else {
    echo "TrustProxies.php NÃO encontrado (pode ser Laravel 11 que usa bootstrap/app.php).\n";
}

// Em Laravel 11, proxies são configurados no bootstrap/app.php
$bootstrapPath = base_path('bootstrap/app.php');
if (file_exists($bootstrapPath)) {
    echo "\n--- bootstrap/app.php ---\n";
    echo file_get_contents($bootstrapPath);
}
