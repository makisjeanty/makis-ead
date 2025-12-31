import paramiko
import base64
import time

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_DIR = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

SCRIPTS = [
    {
        "local": "scripts/php/update_remote_env.php",
        "remote": "update_remote_env.php",
        "name": "Environment Update"
    },
    {
        "local": "scripts/php/verify_payment_integration.php",
        "remote": "verify_payment_integration.php",
        "name": "Payment Verification"
    }
]

def run_tasks():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        for script in SCRIPTS:
            print(f"\n--- Running {script['name']} ---")
            
            # Upload
            with open(script['local'], "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            
            cmd_upload = f"echo '{b64_content}' | base64 -d > {REMOTE_DIR}/{script['remote']}"
            client.exec_command(cmd_upload)
            
            # Execute
            cmd_exec = f"php {REMOTE_DIR}/{script['remote']}"
            stdin, stdout, stderr = client.exec_command(cmd_exec)
            
            out = stdout.read().decode()
            err = stderr.read().decode()
            
            print("Output:")
            print(out)
            if err:
                print("Errors:")
                print(err)
            
            # Cleanup
            client.exec_command(f"rm {REMOTE_DIR}/{script['remote']}")
            
        client.close()
        print("\nAll tasks completed.")
        
    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    run_tasks()
