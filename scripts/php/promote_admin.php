<?php

$vendor = __DIR__ . '/../../vendor/autoload.php';
if (! file_exists($vendor)) {
    echo "vendor autoload not found. Run composer install first.\n";
    exit(1);
}
require $vendor;

$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = $argv[1] ?? 'admin@example.com';

$user = User::where('email', $email)->first();
if (! $user) {
    echo "User with email '{$email}' not found.\n";
    exit(1);
}

echo "Before: role={$user->role} status={$user->status}\n";

if ($user->role === 'admin' && $user->status === 'active') {
    echo "User is already admin and active.\n";
    exit(0);
}

$user->role = 'admin';
$user->status = 'active';
$user->save();

echo "After: role={$user->role} status={$user->status}\n";

echo "Done. You can now login with {$email}.\n";
