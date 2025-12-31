# Script PowerShell para criar usuário admin

Write-Host "Criando usuário administrador..." -ForegroundColor Cyan

$tinkerScript = @"
`$user = new \App\Models\User();
`$user->name = 'Admin';
`$user->email = 'admin@makisead.com';
`$user->password = bcrypt('admin123');
`$user->email_verified_at = now();
`$user->save();
echo "Usuario criado!\n";
"@

$tinkerScript | docker compose exec -T app php artisan tinker

Write-Host ""
Write-Host "✅ Usuário administrador criado!" -ForegroundColor Green
Write-Host "Acesse: http://localhost:8000/admin" -ForegroundColor Yellow
Write-Host "Email: admin@makisead.com" -ForegroundColor Yellow
Write-Host "Senha: admin123" -ForegroundColor Yellow
