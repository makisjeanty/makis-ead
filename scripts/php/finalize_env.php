<?php

// Fix autoload path
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

echo "\n--- Updating Environment Configuration ---\n";

$envPath = '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/.env';

if (!file_exists($envPath)) {
    die("Error: .env file not found at $envPath\n");
}

$envContent = file_get_contents($envPath);

$replacements = [
    '/^QUEUE_CONNECTION=.*$/m' => 'QUEUE_CONNECTION=sync',
    '/^APP_DEBUG=.*$/m' => 'APP_DEBUG=false', // Ensure this is forced
];

$newContent = $envContent;
foreach ($replacements as $pattern => $replacement) {
    if (preg_match($pattern, $newContent)) {
        $newContent = preg_replace($pattern, $replacement, $newContent);
        echo "Updated pattern: $pattern\n";
    } else {
        echo "Warning: Key for pattern $pattern not found, appending...\n";
        // If not found, check if we should append or just ignore. 
        // For queue connection, safer to append if missing.
        if (strpos($pattern, 'QUEUE_CONNECTION') !== false) {
             $newContent .= "\nQUEUE_CONNECTION=sync\n";
        }
    }
}

if ($newContent !== $envContent) {
    file_put_contents($envPath, $newContent);
    echo "Successfully updated .env\n";
} else {
    echo "No changes needed in .env\n";
}

echo "Running Artisan commands...\n";
// Use the full path to artisan if needed, or rely on chdir
chdir('/home/ETUDE-RAPIDE/web/etuderapide.com/public_html');
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');
