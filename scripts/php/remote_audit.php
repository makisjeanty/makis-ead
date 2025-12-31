<?php

// Definir o caminho para o autoload.php
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/vendor/autoload.php'
];

$autoloadFound = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $autoloadFound = true;
        break;
    }
}

if (!$autoloadFound) {
    $currentDir = __DIR__;
    echo json_encode([
        'error' => 'Autoload not found',
        'searched_paths' => $autoloadPaths,
        'current_dir_listing' => scandir($currentDir)
    ]);
    exit(1);
}

// Tenta carregar o app.php
$bootstrapPaths = [
    __DIR__ . '/../bootstrap/app.php',
    __DIR__ . '/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/bootstrap/app.php'
];

$app = null;
foreach ($bootstrapPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        break;
    }
}

if (!$app) {
     echo json_encode(['error' => 'Bootstrap app.php not found', 'searched_paths' => $bootstrapPaths]);
     exit(1);
}
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$report = [];

// 1. Environment Checks
$report['environment'] = [
    'app_env' => config('app.env'),
    'app_debug' => config('app.debug'),
    'app_url' => config('app.url'),
    'session_driver' => config('session.driver'),
    'queue_connection' => config('queue.default'),
];

// 2. Directory Permissions
$dirsToCheck = [
    storage_path(),
    storage_path('logs'),
    storage_path('framework/views'),
    storage_path('framework/sessions'),
    storage_path('framework/cache'),
    base_path('bootstrap/cache'),
];

$permissions = [];
foreach ($dirsToCheck as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
    $permissions[$dir] = [
        'exists' => is_dir($dir),
        'writable' => is_writable($dir),
        'perms' => file_exists($dir) ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A',
    ];
}
$report['permissions'] = $permissions;

// 3. Database Check
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    $report['database'] = [
        'status' => 'connected',
        'database_name' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
        'tables_count' => count(\Illuminate\Support\Facades\DB::select('SHOW TABLES')),
    ];

    // Check specific tables
    $tables = ['users', 'roles', 'permissions', 'payments', 'courses', 'enrollments'];
    $tableStatus = [];
    foreach ($tables as $table) {
        try {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            $tableStatus[$table] = "OK ($count rows)";
        } catch (\Exception $e) {
            $tableStatus[$table] = "Error: " . $e->getMessage();
        }
    }
    $report['database']['tables'] = $tableStatus;

} catch (\Exception $e) {
    $report['database'] = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($report, JSON_PRETTY_PRINT);
