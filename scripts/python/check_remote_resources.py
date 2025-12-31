import paramiko
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

def run_command(client, command):
    stdin, stdout, stderr = client.exec_command(command)
    return stdout.read().decode('utf-8').strip()

def main():
    client = create_client()
    try:
        print("--- Listando Resources no Servidor Remoto ---")
        # Listar app/Filament/Resources
        resources = run_command(client, f"ls -R {target_dir}/app/Filament/Resources")
        print(resources)
        
        print("\n--- Verificando AdminPanelProvider Remoto ---")
        provider = run_command(client, f"cat {target_dir}/app/Providers/Filament/AdminPanelProvider.php")
        if "discoverResources(in: app_path('Filament/Resources')" in provider:
             print("SUCCESS: AdminPanelProvider está apontando para Filament/Resources")
        else:
             print("WARNING: AdminPanelProvider NÃO está apontando corretamente!")
             
    finally:
        client.close()

if __name__ == "__main__":
    main()
