import paramiko
import base64
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def run_payment_verification():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        # Upload the verification script (in case it's not there or needs update)
        local_script = "scripts/php/verify_payment_integration.php"
        remote_script = "verify_payment_integration.php"
        
        print(f"Uploading {local_script}...")
        with open(local_script, "rb") as f:
            content = f.read()
        b64_content = base64.b64encode(content).decode()
        
        remote_script_path = f"{REMOTE_BASE}/{remote_script}"
        client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_script_path}")
        
        # Execute
        print("Executing payment verification...")
        stdin, stdout, stderr = client.exec_command(f"php {remote_script_path}")
        
        out = stdout.read().decode()
        err = stderr.read().decode()
        
        print("Output:")
        print(out)
        if err:
            print("Errors:")
            print(err)
            
        client.close()
        print("Done.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    run_payment_verification()
