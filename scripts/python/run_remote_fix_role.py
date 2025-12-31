import paramiko
import os
import base64
import time

# Configurações
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_DIR = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
LOCAL_SCRIPT = "scripts/php/fix_admin_role.php"
REMOTE_SCRIPT = "fix_admin_role.php"

def create_client():
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
    return client

def run_command(client, command):
    print(f"Executando: {command}")
    stdin, stdout, stderr = client.exec_command(command)
    exit_status = stdout.channel.recv_exit_status()
    out = stdout.read().decode('utf-8')
    err = stderr.read().decode('utf-8')
    if out: print(f"[STDOUT]\n{out}")
    if err: print(f"[STDERR]\n{err}")
    return exit_status

def main():
    client = create_client()
    try:
        # 1. Upload do script PHP
        print(f"Lendo {LOCAL_SCRIPT}...")
        with open(LOCAL_SCRIPT, "rb") as f:
            content = f.read()
        
        b64_content = base64.b64encode(content).decode('utf-8')
        remote_path = f"{REMOTE_DIR}/{REMOTE_SCRIPT}"
        
        print(f"Fazendo upload para {remote_path} via base64...")
        cmd_upload = f"echo {b64_content} | base64 -d > {remote_path}"
        run_command(client, cmd_upload)
        
        # 2. Ajustar permissões
        run_command(client, f"chown ETUDE-RAPIDE:ETUDE-RAPIDE {remote_path}")
        
        # 3. Executar o script PHP
        print("\n--- Executando Correção de Role/Status ---")
        cmd_exec = f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {REMOTE_DIR} && php {REMOTE_SCRIPT}'"
        run_command(client, cmd_exec)
        
        # 4. Otimizar Filament e Limpar Cache
        print("\n--- Otimizando Filament e Cache ---")
        cmds = [
            f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {REMOTE_DIR} && php artisan filament:optimize'",
            f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {REMOTE_DIR} && php artisan icons:cache'",
            f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {REMOTE_DIR} && php artisan optimize:clear'",
            f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {REMOTE_DIR} && php artisan view:cache'",
            f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {REMOTE_DIR} && php artisan config:cache'" # Recriar config cache por último
        ]
        
        for cmd in cmds:
            run_command(client, cmd)

        # 5. Remover script temporário
        run_command(client, f"rm {remote_path}")
        
    finally:
        client.close()
        print("\nConcluído.")

if __name__ == "__main__":
    main()
