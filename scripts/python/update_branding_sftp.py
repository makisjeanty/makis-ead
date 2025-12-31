import paramiko
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def update_branding_sftp():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        sftp = client.open_sftp()
        
        # 1. Upload AdminPanelProvider
        print("\n--- Updating Admin Panel Config ---")
        local_provider = "app/Providers/Filament/AdminPanelProvider.php"
        remote_provider = f"{REMOTE_BASE}/app/Providers/Filament/AdminPanelProvider.php"
        sftp.put(local_provider, remote_provider)
        print("Uploaded AdminPanelProvider.php")

        # 2. Upload Logo and Favicon (Originals)
        print("\n--- Uploading Branding Assets (SFTP) ---")
        
        assets = [
            ("public/images/brand/logo.png", f"{REMOTE_BASE}/public/images/brand/logo.png"),
            ("public/favicon.png", f"{REMOTE_BASE}/public/favicon.png"),
        ]
        
        # Ensure brand directory exists (exec_command is fine for mkdir)
        client.exec_command(f"mkdir -p {REMOTE_BASE}/public/images/brand")
        
        for local, remote in assets:
            if os.path.exists(local):
                print(f"Uploading {local}...")
                sftp.put(local, remote)
            else:
                print(f"Warning: {local} not found!")

        # 3. Fix Favicon.ico
        local_ico = "public/favicon.ico"
        if os.path.exists(local_ico) and os.path.getsize(local_ico) > 0:
            print("Uploading valid favicon.ico...")
            sftp.put(local_ico, f"{REMOTE_BASE}/public/favicon.ico")
        else:
            print("Local favicon.ico is empty/missing. Copying favicon.png to favicon.ico on remote...")
            client.exec_command(f"cp {REMOTE_BASE}/public/favicon.png {REMOTE_BASE}/public/favicon.ico")

        sftp.close()
        client.close()
        print("\nBranding Update Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    update_branding_sftp()
