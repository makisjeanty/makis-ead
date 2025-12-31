<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Forçar início da sessão para teste CLI
$session = $app->make('session');
$session->start();

// Script para diagnosticar problemas de sessão e permissões
$sessionPath = storage_path('framework/sessions');

echo "--- Diagnóstico de Sessão ---\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . "\n";
echo "Session Domain: " . config('session.domain') . "\n";
echo "Session Secure Cookie: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "Session Path: " . $sessionPath . "\n";

if (is_dir($sessionPath)) {
    echo "Diretório de sessão existe.\n";
    if (is_writable($sessionPath)) {
        echo "Diretório de sessão é gravável.\n";
        // Tentar criar um arquivo de teste
        $testFile = $sessionPath . '/test_write_permission';
        if (file_put_contents($testFile, 'test')) {
            echo "Teste de escrita bem sucedido.\n";
            unlink($testFile);
        } else {
            echo "FALHA: Não foi possível escrever no diretório de sessão.\n";
        }
    } else {
        echo "FALHA: Diretório de sessão NÃO é gravável.\n";
        echo "Permissões atuais: " . substr(sprintf('%o', fileperms($sessionPath)), -4) . "\n";
        echo "Dono atual: " . posix_getpwuid(fileowner($sessionPath))['name'] . "\n";
        echo "Grupo atual: " . posix_getgrgid(filegroup($sessionPath))['name'] . "\n";
    }
} else {
    echo "FALHA: Diretório de sessão NÃO existe.\n";
}

echo "\n--- Variáveis de Ambiente Relevantes ---\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";

echo "\n--- Teste de Criação de Token CSRF ---\n";
try {
    $token = csrf_token();
    echo "Token CSRF gerado: " . ($token ? 'SIM' : 'NÃO') . "\n";
    if ($token) {
        echo "Token: " . substr($token, 0, 10) . "...\n";
    }
} catch (\Exception $e) {
    echo "Erro ao gerar token CSRF: " . $e->getMessage() . "\n";
}
