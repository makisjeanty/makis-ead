import paramiko
import base64
import os
import glob

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
REMOTE_STORAGE_COURSES = f"{REMOTE_BASE}/storage/app/public/courses"
REMOTE_PUBLIC_FAVICON = f"{REMOTE_BASE}"

def deploy_assets():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # 1. Ensure directories exist
        print("Checking directories...")
        client.exec_command(f"mkdir -p {REMOTE_STORAGE_COURSES}")
        
        # 2. Upload Favicons
        print("\n--- Uploading Favicons ---")
        favicons = ["favicon.svg", "favicon.png"]
        for fav in favicons:
            if os.path.exists(fav):
                print(f"Uploading {fav}...")
                with open(fav, "rb") as f:
                    content = f.read()
                b64_content = base64.b64encode(content).decode()
                
                # Upload to public root
                remote_path = f"{REMOTE_PUBLIC_FAVICON}/{fav}"
                cmd_upload = f"echo '{b64_content}' | base64 -d > {remote_path}"
                client.exec_command(cmd_upload)
                
                # Also upload to public/storage if needed, but root is standard for favicon
        
        # 3. Upload Course Images
        print("\n--- Uploading Course Images ---")
        images = glob.glob("temp_images/course_*.jpg")
        for img in images:
            filename = os.path.basename(img)
            remote_path = f"{REMOTE_STORAGE_COURSES}/{filename}"
            
            print(f"Uploading {filename}...")
            with open(img, "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            
            cmd_upload = f"echo '{b64_content}' | base64 -d > {remote_path}"
            client.exec_command(cmd_upload)

        # 4. Run Seeding Script
        print("\n--- Seeding Database ---")
        local_script = "scripts/php/seed_remote_images.php"
        remote_script = "seed_remote_images.php"
        
        with open(local_script, "rb") as f:
            content = f.read()
        b64_content = base64.b64encode(content).decode()
        
        remote_script_path = f"{REMOTE_BASE}/{remote_script}"
        client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_script_path}")
        
        stdin, stdout, stderr = client.exec_command(f"php {remote_script_path}")
        print(stdout.read().decode())
        err = stderr.read().decode()
        if err:
            print("Errors:", err)
            
        # Cleanup
        client.exec_command(f"rm {remote_script_path}")
        
        # 5. Link Storage
        print("\n--- Linking Storage ---")
        # Ensure symlink exists: public/storage -> storage/app/public
        # In Laravel: php artisan storage:link
        stdin, stdout, stderr = client.exec_command(f"cd {REMOTE_BASE} && php artisan storage:link")
        print(stdout.read().decode())

        client.close()
        print("\nDeployment Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    deploy_assets()
