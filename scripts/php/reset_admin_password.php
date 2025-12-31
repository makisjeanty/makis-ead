<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(1);
if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('admin_password_2025');
    $user->save();
    echo "Password reset successfully for user: {$user->email}\n";
    echo "New password: admin_password_2025\n";
} else {
    echo "User with ID 1 not found.\n";
}
