import paramiko
import time
import sys

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"
target_dir = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def create_client():
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    return client

def run_command(client, command, print_output=True):
    print(f"\n[REMOTE] Running: {command}")
    stdin, stdout, stderr = client.exec_command(command, get_pty=True)

    output_buffer = ""
    while not stdout.channel.exit_status_ready():
        if stdout.channel.recv_ready():
            data = stdout.channel.recv(1024).decode('utf-8', errors='ignore')
            output_buffer += data
            if print_output:
                print(data, end="")
                sys.stdout.flush()
        time.sleep(0.1)

    remaining = stdout.read().decode('utf-8', errors='ignore')
    output_buffer += remaining
    if print_output and remaining:
        print(remaining, end="")

    exit_status = stdout.channel.recv_exit_status()
    return exit_status, output_buffer

import base64

def main():
    client = create_client()
    try:
        # Execute filament user creation via artisan
        print(f"\n[REMOTE] Creating Filament User via Artisan...")
        # Primeiro, removemos o usuário se ele existir para recriar limpo
        # Mas o comando make:filament-user é interativo, então precisamos usar o comando com inputs ou criar um script PHP que faça isso
        # Melhor abordagem: criar um comando artisan customizado ou usar tinker

        # Vamos usar um script PHP que chama o Filament User Resource
        local_path = "scripts/php/create_filament_user.php"
        with open("scripts/php/create_filament_user.php", "w") as f:
            f.write(r"""<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'contato@etuderapide.com';
$password = 'admin_password_2025';
$name = 'Admin Filament';

$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User();
    $user->email = $email;
}

$user->name = $name;
$user->password = Hash::make($password);
$user->email_verified_at = now();
$user->save();

echo "User {$user->email} saved successfully.\n";
""");

        with open(local_path, "rb") as f:
            content = f.read()

        b64_content = base64.b64encode(content).decode('utf-8')
        remote_path = f"{target_dir}/create_filament_user.php"

        cmd_upload = f"echo {b64_content} | base64 -d > {remote_path}"
        run_command(client, cmd_upload)
        run_command(client, f"chmod 644 {remote_path}")
        run_command(client, f"chown ETUDE-RAPIDE:ETUDE-RAPIDE {remote_path}")

        cmd_exec = f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {target_dir} && php create_filament_user.php'"
        run_command(client, cmd_exec)

        # Agora vamos rodar o comando artisan make:filament-user de forma não interativa se possível,
        # mas o Filament não suporta flags para senha.
        # Então confiamos no script PHP acima que atualizou o usuário.

        run_command(client, f"rm {remote_path}")

    finally:
        client.close()

if __name__ == "__main__":
    main()
