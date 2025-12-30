#!/bin/bash

echo "=== EXECUTANDO MIGRATIONS ==="
ssh root@195.26.252.210 << 'ENDSSH'
cd /home/ETUDE-RAPIDE/web/etuderapide.com/public_html
php artisan migrate:fresh --force
echo "=== MIGRATIONS CONCLUÍDAS ==="
php artisan db:seed --force
echo "=== SEEDERS EXECUTADOS ==="
php artisan make:filament-user
ENDSSH
