import paramiko
import base64
import os
import json

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

SCRIPTS = [
    {
        "local": "scripts/php/remote_audit.php",
        "remote": "remote_audit.php",
        "name": "System Audit"
    },
    {
        "local": "scripts/php/simulate_webhook.php",
        "remote": "simulate_webhook.php",
        "name": "Webhook Simulation"
    },
    {
        "local": "scripts/php/check_logs.php",
        "remote": "check_logs.php",
        "name": "Log Check"
    }
]

def run_final_audit():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        results = {}

        for script in SCRIPTS:
            print(f"\n--- Running {script['name']} ---")
            
            # Upload
            with open(script['local'], "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            
            remote_path = f"{REMOTE_BASE}/{script['remote']}"
            cmd_upload = f"echo '{b64_content}' | base64 -d > {remote_path}"
            client.exec_command(cmd_upload)
            
            # Execute
            cmd_exec = f"php {remote_path}"
            stdin, stdout, stderr = client.exec_command(cmd_exec)
            
            out = stdout.read().decode()
            err = stderr.read().decode()
            
            print(f"Output ({script['name']}):")
            print(out)
            if err:
                print(f"Errors ({script['name']}):")
                print(err)

            results[script['name']] = out
            
            # Cleanup
            client.exec_command(f"rm {remote_path}")

        client.close()
        print("\nAll audit tasks completed.")
        
        return results

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    run_final_audit()
