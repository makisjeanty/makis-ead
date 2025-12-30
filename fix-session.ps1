# Script para corrigir SESSION_DRIVER no .env
$envFile = ".env"
$content = Get-Content $envFile

# Substituir SESSION_DRIVER=array por SESSION_DRIVER=file
$content = $content -replace "^SESSION_DRIVER=array", "SESSION_DRIVER=file"

# Se não existir SESSION_DRIVER, adicionar
if ($content -notmatch "^SESSION_DRIVER=") {
    $content += "`nSESSION_DRIVER=file"
}

# Adicionar SESSION_LIFETIME se não existir
if ($content -notmatch "^SESSION_LIFETIME=") {
    $content += "`nSESSION_LIFETIME=120"
}

# Salvar arquivo
$content | Set-Content $envFile

Write-Host "✅ SESSION_DRIVER corrigido para 'file'!" -ForegroundColor Green
Write-Host "SESSION_DRIVER=file" -ForegroundColor Yellow
Write-Host "SESSION_LIFETIME=120" -ForegroundColor Yellow
