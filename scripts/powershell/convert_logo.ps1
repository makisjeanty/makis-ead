$ErrorActionPreference = "Stop"
try {
    $b64 = [Convert]::ToBase64String([System.IO.File]::ReadAllBytes("public/images/brand/logo.png"))
    Set-Content -Path "logo.b64" -Value $b64
    Write-Host "Arquivo logo.b64 criado com sucesso."
} catch {
    Write-Error $_.Exception.Message
}
