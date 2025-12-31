<?php

// Fix autoload path based on project root being public_html
$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/vendor/autoload.php'
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        break;
    }
}

// .env is in the same directory as this script (project root)
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    // Try absolute path
    $envPath = '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/.env';
}

if (!file_exists($envPath)) {
    die("ERROR: .env file not found at $envPath\n");
}

$content = file_get_contents($envPath);

$replacements = [
    '/^APP_DEBUG=.*$/m' => 'APP_DEBUG=false',
    '/^SESSION_DRIVER=.*$/m' => 'SESSION_DRIVER=database',
    '/^SESSION_LIFETIME=.*$/m' => 'SESSION_LIFETIME=120',
];

$newContent = $content;
foreach ($replacements as $pattern => $replacement) {
    if (preg_match($pattern, $newContent)) {
        $newContent = preg_replace($pattern, $replacement, $newContent);
    } else {
        // If key doesn't exist, append it (cautiously, usually better to ensure it exists)
        // For these specific keys, they should exist.
        echo "Warning: Key for pattern $pattern not found, skipping.\n";
    }
}

// Backup
copy($envPath, $envPath . '.bak.' . time());

// Write
if (file_put_contents($envPath, $newContent) !== false) {
    echo "Successfully updated .env\n";
} else {
    echo "Failed to write .env\n";
    exit(1);
}

// Clear Config
$bootstrapPaths = [
    __DIR__ . '/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/bootstrap/app.php'
];
$app = null;
foreach ($bootstrapPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        break;
    }
}

if ($app) {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "Running Artisan commands...\n";
    $kernel->call('config:clear');
    echo $kernel->output();

    $kernel->call('view:clear');
    echo $kernel->output();

    // Also run cache:clear to be safe
    $kernel->call('cache:clear');
    echo $kernel->output();
}
