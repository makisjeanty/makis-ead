<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'contato@etuderapide.com';
$password = 'admin_password_2025';
$name = 'Admin Filament';

$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User();
    $user->email = $email;
}

$user->name = $name;
$user->password = Hash::make($password);
$user->email_verified_at = now();
$user->save();

echo "User {$user->email} saved successfully.\n";
