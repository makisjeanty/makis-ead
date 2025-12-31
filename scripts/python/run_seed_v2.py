import paramiko
import base64
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def run_seed_v2():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # Upload
        local_script = "scripts/php/seed_remote_images_v2.php"
        remote_script = "seed_remote_images_v2.php"

        print(f"Uploading {local_script}...")
        with open(local_script, "rb") as f:
            content = f.read()
        b64_content = base64.b64encode(content).decode()

        remote_script_path = f"{REMOTE_BASE}/{remote_script}"
        client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_script_path}")

        # Execute
        print("Executing seed script...")
        stdin, stdout, stderr = client.exec_command(f"php {remote_script_path}")

        out = stdout.read().decode()
        err = stderr.read().decode()

        print("Output:")
        print(out)
        if err:
            print("Errors:")
            print(err)

        # Cleanup
        client.exec_command(f"rm {remote_script_path}")

        client.close()
        print("Done.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    run_seed_v2()
