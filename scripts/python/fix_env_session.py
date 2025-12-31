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

def main():
    client = create_client()
    try:
        env_path = f"{target_dir}/.env"
        
        # Backup .env
        print("Backing up .env...")
        run_command(client, f"cp {env_path} {env_path}.bak")
        
        # Update SESSION_SECURE_COOKIE
        print("\nUpdating SESSION_SECURE_COOKIE...")
        # Check if exists
        check_cmd = f"grep -q '^SESSION_SECURE_COOKIE=' {env_path}"
        exit_code, _ = run_command(client, check_cmd, print_output=False)
        
        if exit_code == 0:
            # Replace
            run_command(client, f"sed -i 's/^SESSION_SECURE_COOKIE=.*/SESSION_SECURE_COOKIE=true/' {env_path}")
        else:
            # Append
            run_command(client, f"echo 'SESSION_SECURE_COOKIE=true' >> {env_path}")
            
        # Update SESSION_DOMAIN
        print("\nUpdating SESSION_DOMAIN...")
        check_cmd = f"grep -q '^SESSION_DOMAIN=' {env_path}"
        exit_code, _ = run_command(client, check_cmd, print_output=False)
        
        if exit_code == 0:
            # Replace
            run_command(client, f"sed -i 's/^SESSION_DOMAIN=.*/SESSION_DOMAIN=.etuderapide.com/' {env_path}")
        else:
            # Append
            run_command(client, f"echo 'SESSION_DOMAIN=.etuderapide.com' >> {env_path}")

        # Ensure APP_URL is https
        print("\nEnsuring APP_URL is https...")
        run_command(client, f"sed -i 's|^APP_URL=http://|APP_URL=https://|' {env_path}")

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
