import paramiko
import time
import sys
import base64

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

def main():
    client = create_client()
    try:
        # 1. Upload modified bootstrap/app.php
        local_path = "bootstrap/app.php"
        remote_path = f"{target_dir}/bootstrap/app.php"
        
        print(f"Reading local file: {local_path}")
        with open(local_path, "rb") as f:
            content = f.read()
        
        b64_content = base64.b64encode(content).decode('utf-8')
        print(f"Uploading to {remote_path}...")
        cmd_upload = f"echo {b64_content} | base64 -d > {remote_path}"
        run_command(client, cmd_upload)
        
        # 2. Fix permissions for bootstrap/app.php
        run_command(client, f"chmod 644 {remote_path}")
        run_command(client, f"chown ETUDE-RAPIDE:ETUDE-RAPIDE {remote_path}")
        
        # 3. Modify .env to relax session settings
        env_path = f"{target_dir}/.env"
        print("\nRelaxing session settings in .env...")
        
        # Remove SESSION_DOMAIN (comment it out or delete)
        run_command(client, f"sed -i 's/^SESSION_DOMAIN=/#SESSION_DOMAIN=/' {env_path}")
        
        # Remove SESSION_SECURE_COOKIE (comment it out or delete)
        run_command(client, f"sed -i 's/^SESSION_SECURE_COOKIE=/#SESSION_SECURE_COOKIE=/' {env_path}")
        
        # Clear config cache
        print("\nClearing config cache...")
        cmd_exec = f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {target_dir} && php artisan config:clear'"
        run_command(client, cmd_exec)
        
        # Optimize
        cmd_exec = f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {target_dir} && php artisan optimize'"
        run_command(client, cmd_exec)

    finally:
        client.close()

if __name__ == "__main__":
    main()
