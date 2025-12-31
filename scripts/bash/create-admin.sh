#!/bin/bash

# Script para criar usuário admin do Filament

echo "Criando usuário administrador..."

docker compose exec -T app php artisan tinker << 'EOF'
$user = new \App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@makisead.com';
$user->password = bcrypt('admin123');
$user->email_verified_at = now();
$user->save();
echo "✅ Usuário admin criado com sucesso!\n";
echo "Email: admin@makisead.com\n";
echo "Senha: admin123\n";
EOF

echo ""
echo "✅ Usuário administrador criado!"
echo "Acesse: http://localhost:8000/admin"
echo "Email: admin@makisead.com"
echo "Senha: admin123"
