import paramiko
import base64
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_DIR = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
LOCAL_SCRIPT = "scripts/php/finalize_env.php"
REMOTE_SCRIPT = "finalize_env.php"

def run_fix():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        # Upload
        print(f"Uploading {LOCAL_SCRIPT}...")
        with open(LOCAL_SCRIPT, "rb") as f:
            content = f.read()
        b64_content = base64.b64encode(content).decode()
        
        cmd_upload = f"echo '{b64_content}' | base64 -d > {REMOTE_DIR}/{REMOTE_SCRIPT}"
        client.exec_command(cmd_upload)
        
        # Execute
        print("Executing fix script...")
        cmd_exec = f"php {REMOTE_DIR}/{REMOTE_SCRIPT}"
        stdin, stdout, stderr = client.exec_command(cmd_exec)
        
        out = stdout.read().decode()
        err = stderr.read().decode()
        
        print(out)
        if err:
            print("STDERR:", err)
            
        # Cleanup
        client.exec_command(f"rm {REMOTE_DIR}/{REMOTE_SCRIPT}")
        
        client.close()
        print("Done.")

    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    run_fix()
