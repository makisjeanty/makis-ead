<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking database tables...\n";

$tables = DB::select('SHOW TABLES');
$tableNames = array_map(function($table) {
    return array_values((array)$table)[0];
}, $tables);

echo "Found tables: " . implode(", ", $tableNames) . "\n\n";

if (in_array('posts', $tableNames)) {
    echo "Table 'posts' exists.\n";
    $columns = Schema::getColumnListing('posts');
    echo "Columns: " . implode(", ", $columns) . "\n";
} else {
    echo "Table 'posts' DOES NOT exist.\n";
}

if (in_array('migrations', $tableNames)) {
    echo "\nMigrations table exists. Last 5 migrations:\n";
    $migrations = DB::table('migrations')->orderBy('id', 'desc')->limit(5)->get();
    foreach ($migrations as $m) {
        echo " - {$m->migration} (Batch: {$m->batch})\n";
    }
} else {
    echo "\nMigrations table DOES NOT exist.\n";
}
