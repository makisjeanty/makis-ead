import paramiko
import base64
import os
import time

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
CHUNK_SIZE = 5000  # Conservative chunk size (characters)

def upload_large_file(client, local_path, remote_path):
    print(f"Uploading {local_path}...")
    
    with open(local_path, "rb") as f:
        content = f.read()
    b64_content = base64.b64encode(content).decode()
    
    # Create temp file for base64
    remote_temp = remote_path + ".b64"
    client.exec_command(f"rm {remote_temp}")
    
    total_len = len(b64_content)
    chunks = [b64_content[i:i+CHUNK_SIZE] for i in range(0, total_len, CHUNK_SIZE)]
    
    print(f"Total size: {total_len} chars. Chunks: {len(chunks)}")
    
    for i, chunk in enumerate(chunks):
        # We use printf to avoid newline issues with echo sometimes, but echo is usually fine
        # Using echo -n to append without newline? No, base64 ignores newlines usually.
        # But let's be safe.
        cmd = f"echo '{chunk}' >> {remote_temp}"
        client.exec_command(cmd)
        if i % 10 == 0:
            print(f"  Chunk {i+1}/{len(chunks)} sent")
            
    # Decode
    print("Decoding...")
    cmd_decode = f"base64 -d {remote_temp} > {remote_path}"
    stdin, stdout, stderr = client.exec_command(cmd_decode)
    err = stderr.read().decode()
    if err:
        print(f"  Decode Error: {err}")
    
    # Cleanup
    client.exec_command(f"rm {remote_temp}")
    print("Done.")

def update_branding_chunked():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        # 1. Upload AdminPanelProvider (Small enough for direct upload, but let's use the function for consistency)
        print("\n--- Updating Admin Panel Config ---")
        local_provider = "app/Providers/Filament/AdminPanelProvider.php"
        remote_provider = f"{REMOTE_BASE}/app/Providers/Filament/AdminPanelProvider.php"
        upload_large_file(client, local_provider, remote_provider)

        # 2. Upload Logo and Favicon (Originals)
        print("\n--- Uploading Branding Assets ---")
        
        # Ensure brand directory exists
        client.exec_command(f"mkdir -p {REMOTE_BASE}/public/images/brand")
        
        assets = [
            ("public/images/brand/logo.png", f"{REMOTE_BASE}/public/images/brand/logo.png"),
            ("public/favicon.png", f"{REMOTE_BASE}/public/favicon.png"),
        ]
        
        for local, remote in assets:
            if os.path.exists(local):
                upload_large_file(client, local, remote)
            else:
                print(f"Warning: {local} not found!")

        # 3. Fix Favicon.ico
        local_ico = "public/favicon.ico"
        if os.path.exists(local_ico) and os.path.getsize(local_ico) > 0:
            print("Uploading valid favicon.ico...")
            upload_large_file(client, local_ico, f"{REMOTE_BASE}/public/favicon.ico")
        else:
            print("Local favicon.ico is empty/missing. Copying favicon.png to favicon.ico on remote...")
            client.exec_command(f"cp {REMOTE_BASE}/public/favicon.png {REMOTE_BASE}/public/favicon.ico")

        client.close()
        print("\nBranding Update Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    update_branding_chunked()
