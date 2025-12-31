<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "--- Verificando Tabela Sessions ---\n";
if (Schema::hasTable('sessions')) {
    echo "Tabela 'sessions' EXISTE.\n";
    // Check columns
    $columns = Schema::getColumnListing('sessions');
    echo "Colunas: " . implode(', ', $columns) . "\n";
} else {
    echo "Tabela 'sessions' NÃO EXISTE.\n";
    echo "Criando tabela sessions...\n";
    
    // Create sessions table manually if needed, but it's better to run migration
    // Let's try running the migration command specifically for sessions if we can find it,
    // or just create it via Schema builder here as a fallback fix.
    
    Schema::create('sessions', function ($table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
    echo "Tabela 'sessions' criada com sucesso.\n";
}
