import paramiko
import time

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_DIR = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
LOCAL_SCRIPT = "scripts/php/seed_remote_posts.php"
REMOTE_SCRIPT = "seed_remote_posts.php"

def run_seeder():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # Upload script via base64 to avoid SFTP issues
        print(f"Uploading {LOCAL_SCRIPT} via base64...")
        with open(LOCAL_SCRIPT, "rb") as f:
            content = f.read()
        import base64
        b64_content = base64.b64encode(content).decode()

        cmd_upload = f"echo '{b64_content}' | base64 -d > {REMOTE_DIR}/{REMOTE_SCRIPT}"
        stdin, stdout, stderr = client.exec_command(cmd_upload)
        if stdout.channel.recv_exit_status() != 0:
            print(f"Upload failed: {stderr.read().decode()}")
            return

        # Execute script
        print("Running seeder script...")
        cmd = f"php {REMOTE_DIR}/{REMOTE_SCRIPT}"
        stdin, stdout, stderr = client.exec_command(cmd)

        print("\n--- Output ---")
        print(stdout.read().decode())
        print("--- Errors ---")
        print(stderr.read().decode())

        # Cleanup
        print("Cleaning up...")
        client.exec_command(f"rm {REMOTE_DIR}/{REMOTE_SCRIPT}")

        client.close()
        print("\nSeeding completed.")

    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    run_seeder()
