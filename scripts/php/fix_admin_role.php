<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "--- Diagnosticando e Corrigindo Admin ---\n";

$email = 'contato@etuderapide.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "ERRO: Usuário $email não encontrado!\n";
    exit(1);
}

echo "Usuário encontrado: " . $user->name . "\n";
echo "Role atual: " . ($user->role ?? 'NULL') . "\n";
echo "Status atual: " . ($user->status ?? 'NULL') . "\n";

// Correção forçada
$user->role = 'admin';
$user->status = 'active';
// Garantir is_admin legado também, por segurança
if (Schema::hasColumn('users', 'is_admin')) {
    $user->is_admin = true;
}
$user->save();

echo "--- ATUALIZAÇÃO REALIZADA ---\n";
echo "Role definido para: admin\n";
echo "Status definido para: active\n";

// Verificação final
$user->refresh();
if ($user->role === 'admin' && $user->status === 'active') {
    echo "SUCESSO: Usuário agora tem permissão de acesso ao Filament.\n";
} else {
    echo "ALERTA: Algo impediu a atualização completa.\n";
}
