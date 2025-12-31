if (Test-Path logo.txt) { Remove-Item -Force logo.txt }
if (Test-Path logo.b64) { Remove-Item -Force logo.b64 }
Write-Host "Encoding logo to logo.txt..."
certutil -encode public\images\brand\logo.png logo.txt
if ($LASTEXITCODE -ne 0) { Write-Error "certutil failed with exit code $LASTEXITCODE"; exit 1 }
Get-Content logo.txt | Select-String -NotMatch "-----" | Set-Content logo.b64

$keyPath = (Resolve-Path .\.ssh\id_ed25519).Path
Write-Host "Using key: $keyPath"

Write-Host "Uploading logo.b64 to 195.26.255.210:/tmp/ (will use key for auth)"
scp -i "$keyPath" -o StrictHostKeyChecking=no logo.b64 root@195.26.255.210:/tmp/
if ($LASTEXITCODE -ne 0) { Write-Error "scp failed with exit code $LASTEXITCODE"; exit 1 }

Write-Host "Running remote extraction and chown..."
ssh -i "$keyPath" -o StrictHostKeyChecking=no root@195.26.255.210 "base64 -d /tmp/logo.b64 > /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/public/images/brand/logo.png && chown -R ETUDE-RAPIDE:ETUDE-RAPIDE /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/public/images/brand && echo LOGO_SUCCESS"
if ($LASTEXITCODE -ne 0) { Write-Error "remote ssh command failed with exit code $LASTEXITCODE"; exit 1 }
Write-Host "Done."
