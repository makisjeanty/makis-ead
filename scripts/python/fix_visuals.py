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
REMOTE_VIEWS = f"{REMOTE_BASE}/resources/views"

def fix_visuals():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # 1. Upload Modified Views
        print("\n--- Uploading Fixed Views ---")
        views_to_upload = [
            ("resources/views/courses/show.blade.php", f"{REMOTE_VIEWS}/courses/show.blade.php"),
            ("resources/views/checkout/pending.blade.php", f"{REMOTE_VIEWS}/checkout/pending.blade.php"),
            ("resources/views/checkout/success.blade.php", f"{REMOTE_VIEWS}/checkout/success.blade.php"),
        ]
        
        for local, remote in views_to_upload:
            if os.path.exists(local):
                print(f"Uploading {local}...")
                with open(local, "rb") as f:
                    content = f.read()
                b64_content = base64.b64encode(content).decode()
                cmd_upload = f"echo '{b64_content}' | base64 -d > {remote}"
                client.exec_command(cmd_upload)
            else:
                print(f"Warning: Local file {local} not found!")

        # 2. Upload Favicons (Just in case)
        print("\n--- Uploading Favicons ---")
        favicons = ["favicon.svg", "favicon.png", "favicon.ico"]
        for fav in favicons:
            if os.path.exists(fav):
                print(f"Uploading {fav}...")
                with open(fav, "rb") as f:
                    content = f.read()
                b64_content = base64.b64encode(content).decode()
                remote_path = f"{REMOTE_PUBLIC_FAVICON}/{fav}"
                client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_path}")

        # 3. Ensure Course Images Directory
        print("\n--- Checking Course Images ---")
        client.exec_command(f"mkdir -p {REMOTE_STORAGE_COURSES}")
        
        # Upload images if they don't exist (blind upload for now to be safe)
        images = glob.glob("temp_images/course_*.jpg")
        print(f"Found {len(images)} images to upload.")
        
        for img in images:
            filename = os.path.basename(img)
            remote_path = f"{REMOTE_STORAGE_COURSES}/{filename}"
            # We assume we want to overwrite to be sure
            print(f"Uploading {filename}...")
            with open(img, "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_path}")

        # 4. Run Seeder V2
        print("\n--- Seeding Database (V2) ---")
        local_script = "scripts/php/seed_remote_images_v2.php"
        remote_script = "seed_remote_images_v2.php"
        
        if os.path.exists(local_script):
            with open(local_script, "rb") as f:
                content = f.read()
            b64_content = base64.b64encode(content).decode()
            
            remote_script_path = f"{REMOTE_BASE}/{remote_script}"
            client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_script_path}")
            
            print("Executing seeder...")
            stdin, stdout, stderr = client.exec_command(f"php {remote_script_path}")
            print(stdout.read().decode())
            err = stderr.read().decode()
            if err:
                print("Errors:", err)
                
            # Cleanup
            client.exec_command(f"rm {remote_script_path}")
        else:
            print(f"Error: {local_script} not found!")

        # 5. Verify Storage Link
        print("\n--- Verifying Storage Link ---")
        stdin, stdout, stderr = client.exec_command(f"ls -l {REMOTE_BASE}/public/storage")
        out = stdout.read().decode()
        print(out)
        if "No such file" in stderr.read().decode() or not out:
            print("Link missing, creating...")
            client.exec_command(f"cd {REMOTE_BASE} && php artisan storage:link")
        
        # 6. Verify one image URL
        print("\n--- Verifying Image Access ---")
        # Check if we can access an image via the web root path
        # We can't curl from here easily to the public URL, but we can check if the file exists in the linked path
        stdin, stdout, stderr = client.exec_command(f"ls -l {REMOTE_BASE}/public/storage/courses/course_1.jpg")
        print("Public link check:", stdout.read().decode())

        client.close()
        print("\nVisual Fixes Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    fix_visuals()
