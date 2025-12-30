<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'admin@example.com';
if (\App\Models\User::where('email', $email)->exists()) {
    echo "user exists\n";
    exit(0);
}
\App\Models\User::create([
    'name' => 'Admin',
    'email' => $email,
    'password' => 'password123',
    'role' => 'admin',
    'status' => 'active',
]);

echo "user created\n";
