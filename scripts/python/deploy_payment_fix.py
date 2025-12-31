import paramiko
import base64
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

FILES_TO_DEPLOY = [
    {
        "local": "app/Services/Gateways/MercadoPagoGateway.php",
        "remote": "app/Services/Gateways/MercadoPagoGateway.php"
    }
]

VERIFICATION_SCRIPT = {
    "local": "scripts/php/verify_payment_integration.php",
    "remote": "verify_payment_integration.php"
}

def deploy_and_verify():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # 1. Deploy Core Files
        print("\n--- Deploying Fixes ---")
        for file_info in FILES_TO_DEPLOY:
            local_path = file_info['local']
            remote_path = f"{REMOTE_BASE}/{file_info['remote']}"
            
            print(f"Uploading {local_path} -> {remote_path}")
            
            with open(local_path, "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            
            # Ensure directory exists
            remote_dir = os.path.dirname(remote_path).replace("\\", "/")
            # client.exec_command(f"mkdir -p {remote_dir}") # Assumes dir exists, but safe to skip for now as it should exist
            
            cmd_upload = f"echo '{b64_content}' | base64 -d > {remote_path}"
            stdin, stdout, stderr = client.exec_command(cmd_upload)
            
            err = stderr.read().decode()
            if err:
                print(f"Upload Error for {local_path}: {err}")
            else:
                print("Success.")

        # 2. Run Verification
        print("\n--- Running Verification ---")
        with open(VERIFICATION_SCRIPT['local'], "rb") as f:
            content = f.read()
        b64_content = base64.b64encode(content).decode()
        
        remote_script_path = f"{REMOTE_BASE}/{VERIFICATION_SCRIPT['remote']}"
        cmd_upload_script = f"echo '{b64_content}' | base64 -d > {remote_script_path}"
        client.exec_command(cmd_upload_script)
        
        cmd_exec = f"php {remote_script_path}"
        stdin, stdout, stderr = client.exec_command(cmd_exec)
        
        print(stdout.read().decode())
        err = stderr.read().decode()
        if err:
            print(f"Verification Errors:\n{err}")
            
        # Cleanup script
        client.exec_command(f"rm {remote_script_path}")

        client.close()
        print("\nDeployment and Verification Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    deploy_and_verify()
