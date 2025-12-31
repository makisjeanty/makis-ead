# Script simples para corrigir credenciais
(Get-Content .env) -replace 'DB_USERNAME=makis_ead_user', 'DB_USERNAME=makis_user' -replace 'DB_PASSWORD=admin_password_2025', 'DB_PASSWORD=makis_password' -replace 'MYSQL_DATABASE=makis_ead_db', 'MYSQL_DATABASE=makis_ead' | Set-Content .env

# Adicionar variáveis faltantes
Add-Content .env "`nMYSQL_USER=makis_user"
Add-Content .env "MYSQL_PASSWORD=makis_password"
Add-Content .env "MYSQL_ROOT_PASSWORD=root_password_secure_123"

Write-Host "✅ Credenciais corrigidas!" -ForegroundColor Green
