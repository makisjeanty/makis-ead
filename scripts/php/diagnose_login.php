<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'contato@etuderapide.com';
$password = 'admin_password_2025';

// 1. Tentar logar manualmente para ver se o hash bate
$user = \App\Models\User::where('email', $email)->first();

echo "--- Diagnóstico de Login ---\n";
if ($user) {
    echo "Usuário encontrado: {$user->email} (ID: {$user->id})\n";
    echo "Hash atual no banco: " . substr($user->password, 0, 10) . "...\n";
    
    if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        echo "SUCESSO: A senha fornecida corresponde ao hash no banco.\n";
    } else {
        echo "FALHA: A senha fornecida NÃO corresponde ao hash no banco.\n";
        echo "Resetando senha novamente...\n";
        $user->password = \Illuminate\Support\Facades\Hash::make($password);
        $user->save();
        echo "Senha resetada.\n";
    }
} else {
    echo "ERRO CRÍTICO: Usuário {$email} não encontrado no banco!\n";
    // Criar usuário de emergência
    echo "Criando usuário de emergência...\n";
    $user = new \App\Models\User();
    $user->name = 'Admin Emergency';
    $user->email = $email;
    $user->password = \Illuminate\Support\Facades\Hash::make($password);
    $user->email_verified_at = now();
    $user->save();
    echo "Usuário criado com ID: {$user->id}\n";
}

// 2. Verificar se o usuário tem permissão de acesso ao Filament (se houver política)
// Normalmente Filament usa o método canAccessPanel() no Model User se implementar FilamentUser
echo "\n--- Verificação de Permissões ---\n";
$interfaces = class_implements($user);
if (in_array('Filament\Models\Contracts\FilamentUser', $interfaces)) {
    echo "User implementa FilamentUser interface.\n";
    if (method_exists($user, 'canAccessPanel')) {
         // Simular verificação
         echo "Método canAccessPanel existe.\n";
    }
} else {
    echo "User NÃO implementa FilamentUser interface (pode ser o problema se o painel exigir).\n";
}

echo "\n--- Concluído ---\n";
