import paramiko
import time
import sys

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"

def run_command(ssh, command):
    print(f"\n[REMOTE] Running: {command}")
    stdin, stdout, stderr = ssh.exec_command(command, get_pty=True)
    out = stdout.read().decode().strip()
    print(out)
    return out

try:
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    
    run_command(client, "which git")
    run_command(client, "git --version")
    run_command(client, "which docker")
    run_command(client, "docker --version")
    run_command(client, "ls -la /home/ETUDE-RAPIDE/web/etuderapide.com/public_html")
    
except Exception as e:
    print(e)
finally:
    client.close()
