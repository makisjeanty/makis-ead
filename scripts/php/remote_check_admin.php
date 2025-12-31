<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::where('email', 'like', '%admin%')
    ->orWhere('id', 1)
    ->get(['id', 'name', 'email', 'created_at']);

echo json_encode($users, JSON_PRETTY_PRINT);
