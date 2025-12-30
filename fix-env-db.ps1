# Script para corrigir configuração do banco de dados no .env
$envFile = ".env"
$content = Get-Content $envFile

# Substituir configurações do banco de dados
$content = $content -replace "^DB_CONNECTION=.*", "DB_CONNECTION=mysql"
$content = $content -replace "^DB_DATABASE=.*", "DB_DATABASE=makis_ead"

# Adicionar configurações faltantes se não existirem
if ($content -notmatch "^DB_HOST=") {
    $content += "`nDB_HOST=db"
}
if ($content -notmatch "^DB_PORT=") {
    $content += "`nDB_PORT=3306"
}
if ($content -notmatch "^DB_USERNAME=") {
    $content += "`nDB_USERNAME=makis_user"
}
if ($content -notmatch "^DB_PASSWORD=") {
    $content += "`nDB_PASSWORD=makis_password"
}

# Adicionar configurações MySQL se não existirem
if ($content -notmatch "^MYSQL_DATABASE=") {
    $content += "`nMYSQL_DATABASE=makis_ead"
}
if ($content -notmatch "^MYSQL_USER=") {
    $content += "`nMYSQL_USER=makis_user"
}
if ($content -notmatch "^MYSQL_PASSWORD=") {
    $content += "`nMYSQL_PASSWORD=makis_password"
}
if ($content -notmatch "^MYSQL_ROOT_PASSWORD=") {
    $content += "`nMYSQL_ROOT_PASSWORD=root_password_secure_123"
}

# Salvar arquivo
$content | Set-Content $envFile

Write-Host "✅ Arquivo .env atualizado com configurações MySQL!" -ForegroundColor Green
Write-Host "Configurações aplicadas:" -ForegroundColor Cyan
Write-Host "  DB_CONNECTION=mysql"
Write-Host "  DB_HOST=db"
Write-Host "  DB_PORT=3306"
Write-Host "  DB_DATABASE=makis_ead"
Write-Host "  DB_USERNAME=makis_user"
Write-Host "  DB_PASSWORD=makis_password"
