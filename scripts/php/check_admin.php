<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admins = User::where('email', 'like', '%admin%')
    ->orWhere('id', 1)
    ->get(['id', 'name', 'email', 'created_at']);

if ($admins->isEmpty()) {
    echo "Nenhum usuário admin encontrado (buscado por id=1 ou email contendo 'admin').\n";
    // Listar todos os usuários (limite 5)
    $users = User::limit(5)->get(['id', 'name', 'email']);
    echo "Usuários encontrados:\n";
    foreach ($users as $user) {
        echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
    }
} else {
    echo "Possíveis administradores encontrados:\n";
    foreach ($admins as $admin) {
        echo "ID: {$admin->id} | Name: {$admin->name} | Email: {$admin->email} | Criado em: {$admin->created_at}\n";
    }
}
