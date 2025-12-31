<?php

$logFile = '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "Log file not found at: $logFile\n";
    // Try relative path if running from public_html
    $logFile = __DIR__ . '/../storage/logs/laravel.log';
    if (!file_exists($logFile)) {
        echo "Log file not found at relative path either.\n";
        exit(1);
    }
}

echo "Reading last 50 lines of log file: $logFile\n\n";

$lines = file($logFile);
$lastLines = array_slice($lines, -50);

foreach ($lastLines as $line) {
    echo $line;
}
