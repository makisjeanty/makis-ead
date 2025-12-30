$repo = 'makisjeanty/makis-ead'
$keys = 'MERCADOPAGO_ACCESS_TOKEN','MERCADOPAGO_PUBLIC_KEY','MERCADOPAGO_MODE','DB_USERNAME','DB_PASSWORD','DB_DATABASE','DB_HOST','MAIL_USERNAME','MAIL_PASSWORD','AWS_ACCESS_KEY_ID','AWS_SECRET_ACCESS_KEY'
foreach($k in $keys){
  $lines = Get-Content .env | Where-Object { $_ -match "^$k=" }
  if(-not $lines){ Write-Host "Skipping ${k}: not found"; continue }
  $val = ($lines | ForEach-Object { $_.Split('=',2)[1] })[-1].Trim('"')
  if([string]::IsNullOrEmpty($val)){ Write-Host "Skipping ${k}: empty"; continue }
  Write-Host "Setting secret: ${k}"
  gh secret set $k --body $val --repo $repo
}
