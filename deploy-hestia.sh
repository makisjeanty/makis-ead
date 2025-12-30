#!/bin/bash

# Script de Deploy - Makis EAD para HestiaCP
# Execute este script no servidor VPS

set -e

echo "========================================="
echo "   MAKIS EAD - Deploy para HestiaCP"
echo "========================================="
echo ""

# Variáveis
SITE_DIR="/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
USER="ETUDE-RAPIDE"

echo "1. Criando estrutura Laravel..."
cd $SITE_DIR

# Criar estrutura de diretórios Laravel
mkdir -p app/Console app/Exceptions app/Http/Controllers app/Http/Middleware app/Models app/Providers
mkdir -p bootstrap/cache
mkdir -p config
mkdir -p database/factories database/migrations database/seeders
mkdir -p public
mkdir -p resources/css resources/js resources/views
mkdir -p routes
mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p tests/Feature tests/Unit

echo "2. Baixando Laravel via Composer..."
composer create-project laravel/laravel:^12.0 temp-laravel --no-interaction

echo "3. Movendo arquivos base..."
cp -r temp-laravel/* .
cp -r temp-laravel/.* . 2>/dev/null || true
rm -rf temp-laravel

echo "4. Instalando dependências específicas..."
composer require filament/filament:"^3.0" --no-interaction
composer require laravel/sanctum:"^4.2" --no-interaction
composer require laravel/cashier:"^16.1" --no-interaction
composer require mercadopago/dx-php:"^3.8" --no-interaction
composer require stripe/stripe-php:"^17.6" --no-interaction

echo "5. Configurando permissões..."
chown -R $USER:$USER $SITE_DIR
chmod -R 755 $SITE_DIR
chmod -R 775 $SITE_DIR/storage
chmod -R 775 $SITE_DIR/bootstrap/cache

echo "6. Criando arquivo .env..."
cat > $SITE_DIR/.env << 'EOF'
APP_NAME="Étude Rapide"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=America/Port-au-Prince
APP_URL=https://etuderapide.com
APP_LOCALE=fr

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ETUDE-RAPIDE_makis_ead
DB_USERNAME=ETUDE-RAPIDE_makis_user
DB_PASSWORD=CHANGE_THIS_PASSWORD

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

DEFAULT_CURRENCY=HTG

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@etuderapide.com
MAIL_FROM_NAME="${APP_NAME}"
EOF

echo "7. Gerando chave da aplicação..."
php artisan key:generate

echo "8. Criando link de storage..."
php artisan storage:link

echo ""
echo "========================================="
echo "   Deploy Base Concluído!"
echo "========================================="
echo ""
echo "PRÓXIMOS PASSOS:"
echo "1. Edite o arquivo .env e configure:"
echo "   - Senha do banco de dados"
echo "   - Configurações de email"
echo "   - Credenciais de pagamento"
echo ""
echo "2. Crie o banco de dados via HestiaCP"
echo ""
echo "3. Execute as migrations:"
echo "   cd $SITE_DIR"
echo "   php artisan migrate"
echo ""
echo "4. Crie o usuário admin:"
echo "   php artisan make:filament-user"
echo ""
