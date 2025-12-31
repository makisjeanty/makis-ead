import paramiko
import sys

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"
target_user = "ETUDE-RAPIDE"
target_dir = f"/home/{target_user}/web/etuderapide.com/public_html"

def run_command(ssh, command, description):
    print(f"\n--- {description} ---")
    print(f"CMD: {command}")
    stdin, stdout, stderr = ssh.exec_command(command)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    if out: print(f"OUT:\n{out}")
    if err: print(f"ERR:\n{err}")
    return out

try:
    print(f"Connecting to {hostname}...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    print("Connected.")

    # 1. Check OS and User
    run_command(client, "cat /etc/os-release", "OS Info")
    run_command(client, f"grep {target_user} /etc/passwd", "User Shell Info")

    # 2. Check Tools
    run_command(client, "php -v", "PHP Version")
    run_command(client, "composer --version", "Composer Version")
    run_command(client, "git --version", "Git Version")
    run_command(client, "node -v || echo 'Node not found'", "Node Version")
    run_command(client, "npm -v || echo 'NPM not found'", "NPM Version")
    run_command(client, "docker -v || echo 'Docker not found'", "Docker Version")

    # 3. Check Directory contents
    run_command(client, f"ls -la {target_dir}", "Directory Listing")

    # 4. Check for Git Repo
    run_command(client, f"test -d {target_dir}/.git && echo 'IS GIT REPO' || echo 'NOT GIT REPO'", "Git Status")

    # 5. Check .env (partial)
    run_command(client, f"grep -E 'APP_ENV|APP_DEBUG|DB_CONNECTION' {target_dir}/.env", "Environment Config")

except Exception as e:
    print(f"Error: {e}")
finally:
    client.close()
