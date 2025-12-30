$ErrorActionPreference = 'Stop'
$b = Get-Content .\logo.b64 -Raw
[byte[]]$bytes = [Convert]::FromBase64String($b)
$target = 'public\images\brand\logo.png'
$dir = Split-Path $target
if (-not (Test-Path $dir)) { New-Item -Path $dir -ItemType Directory -Force | Out-Null }
[IO.File]::WriteAllBytes($target, $bytes)
if (Test-Path $target) { Write-Host 'PNG_CREATED' } else { Write-Host 'PNG_FAILED' }
