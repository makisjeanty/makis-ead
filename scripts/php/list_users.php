<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Lista de Usuários no Banco de Dados ---\n";
echo str_pad("ID", 5) . str_pad("Name", 25) . str_pad("Email", 35) . "Created At\n";
echo str_repeat("-", 80) . "\n";

$users = \App\Models\User::all();

foreach ($users as $user) {
    echo str_pad($user->id, 5) . 
         str_pad(substr($user->name, 0, 24), 25) . 
         str_pad(substr($user->email, 0, 34), 35) . 
         $user->created_at . "\n";
}

echo "\nTotal de usuários: " . $users->count() . "\n";
