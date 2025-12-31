import paramiko
import os
import base64
import sys

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"
target_dir = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
local_script = "scripts/check_remote_tables.php"
remote_script = f"{target_dir}/check_remote_tables.php"

def run_command(client, command):
    stdin, stdout, stderr = client.exec_command(command)
    exit_status = stdout.channel.recv_exit_status()
    return exit_status, stdout.read().decode(), stderr.read().decode()

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(hostname, username=username, password=password)

# Upload via base64
print(f"Uploading {local_script}...")
with open(local_script, "rb") as f:
    content = f.read()
    b64_content = base64.b64encode(content).decode()

# Clean remote file
run_command(client, f"rm -f {remote_script}")

# Upload chunks
chunk_size = 1024
for i in range(0, len(b64_content), chunk_size):
    chunk = b64_content[i:i+chunk_size]
    cmd = f"echo -n '{chunk}' >> {remote_script}.b64"
    run_command(client, cmd)

# Decode
run_command(client, f"base64 -d {remote_script}.b64 > {remote_script}")
run_command(client, f"rm {remote_script}.b64")

# Fix permissions
run_command(client, f"chown ETUDE-RAPIDE:ETUDE-RAPIDE {remote_script}")

# Run script
print("Running check...")
cmd = f"su -s /bin/bash ETUDE-RAPIDE -c 'php {remote_script}'"
code, out, err = run_command(client, cmd)

print(out)
if err:
    print(f"ERROR: {err}")

# Cleanup
run_command(client, f"rm {remote_script}")
client.close()
