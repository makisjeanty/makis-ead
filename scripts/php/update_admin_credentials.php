<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'contato@etuderapide.com';
$password = 'admin_password_2025';

// Tenta encontrar pelo ID 1 ou pelo email antigo
$user = \App\Models\User::find(1);

if (!$user) {
    // Se não achar ID 1, tenta pelo email antigo com erro de digitação
    $user = \App\Models\User::where('email', 'jimmy@gmail.om')->first();
}

if (!$user) {
    // Se ainda não achar, cria um novo
    $user = new \App\Models\User();
    $user->name = 'Admin';
    $user->id = 1; // Força ID 1 se possível, ou deixa auto-increment
}

$user->email = $email;
$user->password = \Illuminate\Support\Facades\Hash::make($password);
$user->email_verified_at = now();
$user->save();

echo "Admin user updated successfully.\n";
echo "ID: {$user->id}\n";
echo "Email: {$user->email}\n";
echo "Password: {$password}\n";
