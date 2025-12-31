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
        # 1. Check/Create sessions table
        local_path = "scripts/php/check_create_sessions_table.php"
        remote_path = f"{target_dir}/check_create_sessions_table.php"
        
        print(f"Reading local file: {local_path}")
        with open(local_path, "rb") as f:
            content = f.read()
            
        b64_content = base64.b64encode(content).decode('utf-8')
        print(f"Uploading to {remote_path}...")
        cmd_upload = f"echo {b64_content} | base64 -d > {remote_path}"
        run_command(client, cmd_upload)
        
        run_command(client, f"chmod 644 {remote_path}")
        run_command(client, f"chown ETUDE-RAPIDE:ETUDE-RAPIDE {remote_path}")
        
        print("\nChecking sessions table...")
        cmd_exec = f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {target_dir} && php check_create_sessions_table.php'"
        run_command(client, cmd_exec)
        
        run_command(client, f"rm {remote_path}")

        # 2. Change SESSION_DRIVER to cookie (safest fallback) or database
        # Let's try DATABASE first since we just ensured the table exists. 
        # Database is more robust than cookie for large payloads.
        # But user is very frustrated, COOKIE is almost guaranteed to work if DB has issues.
        # Let's stick with COOKIE as a "fix it now" measure, then move to DB later if needed.
        # Actually, let's try COOKIE. It eliminates server-side storage issues completely.
        
        env_path = f"{target_dir}/.env"
        print("\nSwitching SESSION_DRIVER to cookie in .env...")
        
        run_command(client, f"sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=cookie/' {env_path}")
        
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
