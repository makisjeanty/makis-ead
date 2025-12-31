<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Iniciando reset de usuários...\n";

// Desabilitar verificação de chave estrangeira para permitir truncate
Schema::disableForeignKeyConstraints();

// Limpar tabela de usuários
echo "Limpando tabela users...\n";
User::truncate();

// Habilitar novamente
Schema::enableForeignKeyConstraints();

echo "Usuários removidos.\n";

// Criar Admin
echo "Criando Admin...\n";
$user = new User();
$user->name = 'Admin Filament';
$user->email = 'contato@etuderapide.com';
$user->password = Hash::make('admin_password_2025');
$user->email_verified_at = now();
$user->save();

echo "Admin criado com sucesso:\n";
echo "Email: contato@etuderapide.com\n";
echo "Senha: admin_password_2025\n";
