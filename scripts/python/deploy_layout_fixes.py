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
        "local": "app/Providers/Filament/AdminPanelProvider.php",
        "remote": "app/Providers/Filament/AdminPanelProvider.php"
    },
    {
        "local": "resources/views/layouts/app.blade.php",
        "remote": "resources/views/layouts/app.blade.php"
    },
    {
        "local": "resources/views/student/dashboard.blade.php",
        "remote": "resources/views/student/dashboard.blade.php"
    },
    {
        "local": "resources/views/welcome.blade.php",
        "remote": "resources/views/welcome.blade.php"
    }
]

def deploy_fixes():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        print("\n--- Deploying Layout Fixes ---")
        for file_info in FILES_TO_DEPLOY:
            local_path = file_info['local']
            remote_path = f"{REMOTE_BASE}/{file_info['remote']}"
            
            print(f"Uploading {local_path}...")
            
            with open(local_path, "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            
            # Ensure directory exists (just in case)
            remote_dir = os.path.dirname(remote_path).replace("\\", "/")
            # client.exec_command(f"mkdir -p {remote_dir}") 
            
            cmd_upload = f"echo '{b64_content}' | base64 -d > {remote_path}"
            client.exec_command(cmd_upload)
            
        # Clear view cache to ensure changes take effect
        print("\n--- Clearing View Cache ---")
        cmd_cache = f"php {REMOTE_BASE}/artisan view:clear"
        stdin, stdout, stderr = client.exec_command(cmd_cache)
        print(stdout.read().decode())
        
        client.close()
        print("\nDeployment Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    deploy_fixes()
