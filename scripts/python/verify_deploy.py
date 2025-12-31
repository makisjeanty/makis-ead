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
        print("--- Verificando configurações remotas (.env) ---")
        env_content = run_command(client, f"cat {target_dir}/.env | grep -E 'SESSION|APP_URL|APP_ENV'")
        print(env_content)
        
        print("\n--- Verificando bootstrap/app.php remoto ---")
        bootstrap_content = run_command(client, f"cat {target_dir}/bootstrap/app.php")
        if "trustProxies(at: '*')" in bootstrap_content:
            print("SUCCESS: trustProxies(at: '*') encontrado no bootstrap/app.php")
        else:
            print("WARNING: trustProxies(at: '*') NÃO encontrado!")
            
    finally:
        client.close()

if __name__ == "__main__":
    main()
