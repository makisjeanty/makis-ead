<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Filament\Models\Contracts\FilamentUser;

// Verifica se o usuário ID 1 existe e tem permissão
$user = User::find(1);

if ($user) {
    echo "Usuário ID 1 encontrado: {$user->email}\n";
    
    // Verifica se implementa FilamentUser
    $implements = class_implements($user);
    if (in_array(FilamentUser::class, $implements)) {
        echo "User implementa FilamentUser.\n";
    } else {
        echo "AVISO: User NÃO implementa FilamentUser. Isso pode impedir o login.\n";
    }
    
    // Verifica o método canAccessPanel
    if (method_exists($user, 'canAccessPanel')) {
        echo "Método canAccessPanel existe.\n";
    } else {
        echo "AVISO: Método canAccessPanel NÃO existe. O Filament não deixará logar.\n";
    }

} else {
    echo "Usuário ID 1 não encontrado.\n";
}

// Vamos tentar criar um usuário usando o comando make:filament-user via Artisan
// Mas como estamos em um script PHP, vamos simular a criação correta
// Ou melhor, vamos rodar o comando artisan diretamente via shell no próximo passo.
