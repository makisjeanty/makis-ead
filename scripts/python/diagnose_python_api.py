import paramiko
import sys

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"

def run_command(client, command, title):
    print(f"\n--- {title} ---")
    print(f"CMD: {command}")
    stdin, stdout, stderr = client.exec_command(command)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    if out: print(f"OUT:\n{out}")
    if err: print(f"ERR:\n{err}")

try:
    print(f"Connecting to {hostname}...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    print("Connected.")

    # 1. Check Nginx Config Locations
    run_command(client, "ls -F /home/ETUDE-RAPIDE/conf/web/etuderapide.com/", "Hestia Web Configs")

    # 2. Check if custom includes are supported in the main config
    # Usually Hestia uses /home/user/conf/web/domain/nginx.conf_sweb or similar
    # Let's check the content of the main nginx config for this domain if possible, or search for includes
    run_command(client, "grep -r 'include' /home/ETUDE-RAPIDE/conf/web/etuderapide.com/ | head -n 20", "Includes in Web Config")

    # 3. Check Python Env
    run_command(client, "python3 --version", "Python3 Version")
    run_command(client, "pip3 --version || echo 'pip3 not found'", "Pip3 Version")

    # 4. Check if we can create a virtualenv
    run_command(client, "apt-get install -y python3-venv", "Install Venv Support")

    # 5. Check ports
    run_command(client, "netstat -tuln | grep 8000 || echo 'Port 8000 free'", "Check Port 8000")

except Exception as e:
    print(f"Error: {e}")
finally:
    client.close()
