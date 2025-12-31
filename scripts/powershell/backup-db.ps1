# Script para fazer backup do banco de dados MySQL

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupDir = "backups"
$backupFile = "$backupDir/makis_ead_backup_$timestamp.sql"

# Criar diretório de backups se não existir
if (!(Test-Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir | Out-Null
}

Write-Host "🔄 Criando backup do banco de dados..." -ForegroundColor Cyan
Write-Host "Arquivo: $backupFile" -ForegroundColor Yellow

# Executar mysqldump
docker compose exec -T db mysqldump -u makis_user -pmakis_password makis_ead > $backupFile

if ($LASTEXITCODE -eq 0) {
    $fileSize = (Get-Item $backupFile).Length / 1KB
    Write-Host ""
    Write-Host "✅ Backup criado com sucesso!" -ForegroundColor Green
    Write-Host "Arquivo: $backupFile" -ForegroundColor White
    Write-Host "Tamanho: $([math]::Round($fileSize, 2)) KB" -ForegroundColor White
    Write-Host ""
    Write-Host "📋 Informações do Banco:" -ForegroundColor Cyan
    Write-Host "  Database: makis_ead" -ForegroundColor White
    Write-Host "  User: makis_user" -ForegroundColor White
    Write-Host "  Host: db (container)" -ForegroundColor White
}
else {
    Write-Host ""
    Write-Host "❌ Erro ao criar backup!" -ForegroundColor Red
}
