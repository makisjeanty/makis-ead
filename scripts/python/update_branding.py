import paramiko
import base64
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def update_branding():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        # 1. Upload AdminPanelProvider
        print("\n--- Updating Admin Panel Config ---")
        local_provider = "app/Providers/Filament/AdminPanelProvider.php"
        remote_provider = f"{REMOTE_BASE}/app/Providers/Filament/AdminPanelProvider.php"
        
        with open(local_provider, "rb") as f:
            content = f.read()
        b64_content = base64.b64encode(content).decode()
        client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_provider}")

        # 2. Upload Logo and Favicon (Originals)
        print("\n--- Uploading Branding Assets ---")
        
        assets = [
            ("public/images/brand/logo.png", f"{REMOTE_BASE}/public/images/brand/logo.png"),
            ("public/favicon.png", f"{REMOTE_BASE}/public/favicon.png"),
        ]
        
        # Ensure brand directory exists
        client.exec_command(f"mkdir -p {REMOTE_BASE}/public/images/brand")
        
        for local, remote in assets:
            if os.path.exists(local):
                print(f"Uploading {local}...")
                with open(local, "rb") as f:
                    content = f.read()
                b64_content = base64.b64encode(content).decode()
                client.exec_command(f"echo '{b64_content}' | base64 -d > {remote}")
            else:
                print(f"Warning: {local} not found!")

        # 3. Fix Favicon.ico (Use png if ico is empty/missing)
        # Check local ico size
        local_ico = "public/favicon.ico"
        if os.path.exists(local_ico) and os.path.getsize(local_ico) > 0:
            print("Uploading valid favicon.ico...")
            with open(local_ico, "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            client.exec_command(f"echo '{b64_content}' | base64 -d > {REMOTE_BASE}/public/favicon.ico")
        else:
            print("Local favicon.ico is empty/missing. Copying favicon.png to favicon.ico on remote...")
            client.exec_command(f"cp {REMOTE_BASE}/public/favicon.png {REMOTE_BASE}/public/favicon.ico")

        client.close()
        print("\nBranding Update Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    update_branding()
