import paramiko
import os
import base64

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

FILES_TO_UPLOAD = [
    {
        "local": "database/migrations/2025_01_01_000001_setup_rbac_tables.php",
        "remote": "database/migrations/2025_01_01_000001_setup_rbac_tables.php"
    },
    {
        "local": "app/Models/Role.php",
        "remote": "app/Models/Role.php"
    },
    {
        "local": "app/Models/Permission.php",
        "remote": "app/Models/Permission.php"
    },
    {
        "local": "app/Models/User.php",
        "remote": "app/Models/User.php"
    }
]

def deploy_rbac():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # 1. Upload Migration
        print("Uploading Migration...")
        migration = FILES_TO_UPLOAD[0]
        upload_file(client, migration['local'], migration['remote'])

        # 2. Run Migration
        print("Running Migration...")
        # Use --path to run only the specific migration to avoid conflicts with existing tables
        cmd_migrate = f"su -s /bin/bash ETUDE-RAPIDE -c 'cd {REMOTE_BASE} && php artisan migrate --force --path=database/migrations/2025_01_01_000001_setup_rbac_tables.php'"
        stdin, stdout, stderr = client.exec_command(cmd_migrate)

        out_str = stdout.read().decode()
        err_str = stderr.read().decode()
        exit_status = stdout.channel.recv_exit_status()

        print(f"Migration STDOUT:\n{out_str}")
        print(f"Migration STDERR:\n{err_str}")

        if exit_status != 0:
            print(f"Migration Failed with code {exit_status}")
            return

        # 3. Upload Models (Role, Permission)
        print("Uploading Models...")
        upload_file(client, FILES_TO_UPLOAD[1]['local'], FILES_TO_UPLOAD[1]['remote'])
        upload_file(client, FILES_TO_UPLOAD[2]['local'], FILES_TO_UPLOAD[2]['remote'])

        # 4. Upload User.php (Last step to avoid breaking auth)
        print("Uploading User.php...")
        upload_file(client, FILES_TO_UPLOAD[3]['local'], FILES_TO_UPLOAD[3]['remote'])

        print("\nRBAC Deployment Completed Successfully!")
        client.close()

    except Exception as e:
        print(f"Error: {str(e)}")

def upload_file(client, local_path, remote_path):
    full_remote_path = f"{REMOTE_BASE}/{remote_path}"
    print(f"  -> {local_path}")

    with open(local_path, "rb") as f:
        content = f.read()

    b64_content = base64.b64encode(content).decode()
    cmd = f"echo '{b64_content}' | base64 -d > {full_remote_path}"

    stdin, stdout, stderr = client.exec_command(cmd)
    if stdout.channel.recv_exit_status() != 0:
        raise Exception(f"Failed to upload {remote_path}: {stderr.read().decode()}")

if __name__ == "__main__":
    deploy_rbac()
