import paramiko
import time
import sys
import base64
import os

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
        local_path = "scripts/php/diagnose_session.php"
        remote_filename = "diagnose_session.php"
        remote_path = f"{target_dir}/{remote_filename}"
        
        # Read local file
        print(f"Reading local file: {local_path}")
        with open(local_path, "rb") as f:
            content = f.read()
            
        b64_content = base64.b64encode(content).decode('utf-8')
        
        print(f"Uploading to {remote_path}...")
        cmd_upload = f"echo {b64_content} | base64 -d > {remote_path}"
        run_command(client, cmd_upload)
        
        run_command(client, f"chmod 644 {remote_path}")
        run_command(client, f"chown ETUDE-RAPIDE:ETUDE-RAPIDE {remote_path}")
        
        print("\nExecuting diagnostic script...")
        cmd_exec = f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {target_dir} && php {remote_filename}'"
        run_command(client, cmd_exec)
        
        # Clean up
        print("\nCleaning up...")
        run_command(client, f"rm {remote_path}")
        
    finally:
        client.close()

if __name__ == "__main__":
    main()
