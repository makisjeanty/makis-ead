# Script para sincronizar credenciais do banco de dados
$envFile = ".env"
$content = Get-Content $envFile

Write-Host "🔧 Corrigindo credenciais do banco de dados..." -ForegroundColor Cyan

# Substituir credenciais do banco
$content = $content -replace "^DB_USERNAME=.*", "DB_USERNAME=makis_user"
$content = $content -replace "^DB_PASSWORD=.*", "DB_PASSWORD=makis_password"
$content = $content -replace "^DB_DATABASE=.*", "DB_DATABASE=makis_ead"

# Substituir credenciais MySQL (Docker)
$content = $content -replace "^MYSQL_USER=.*", "MYSQL_USER=makis_user"
$content = $content -replace "^MYSQL_PASSWORD=.*", "MYSQL_PASSWORD=makis_password"
$content = $content -replace "^MYSQL_DATABASE=.*", "MYSQL_DATABASE=makis_ead"
$content = $content -replace "^MYSQL_ROOT_PASSWORD=.*", "MYSQL_ROOT_PASSWORD=root_password_secure_123"

# Adicionar se não existir
if ($content -notmatch "^MYSQL_USER=") {
    $content += "`nMYSQL_USER=makis_user"
}
if ($content -notmatch "^MYSQL_PASSWORD=") {
    $content += "`nMYSQL_PASSWORD=makis_password"
}
if ($content -notmatch "^MYSQL_ROOT_PASSWORD=") {
    $content += "`nMYSQL_ROOT_PASSWORD=root_password_secure_123"
}

# Salvar
$content | Set-Content $envFile

Write-Host ""
Write-Host "✅ Credenciais sincronizadas!" -ForegroundColor Green
Write-Host ""
Write-Host "Credenciais do Banco:" -ForegroundColor Yellow
Write-Host "  DB_USERNAME=makis_user"
Write-Host "  DB_PASSWORD=makis_password"
Write-Host "  DB_DATABASE=makis_ead"
Write-Host ""
Write-Host "Credenciais MySQL (Docker):" -ForegroundColor Yellow
Write-Host "  MYSQL_USER=makis_user"
Write-Host "  MYSQL_PASSWORD=makis_password"
Write-Host "  MYSQL_DATABASE=makis_ead"
Write-Host "  MYSQL_ROOT_PASSWORD=root_password_secure_123"
