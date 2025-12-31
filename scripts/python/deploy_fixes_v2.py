import paramiko
import base64
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def deploy_fixes():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        files_to_deploy = [
            ("app/Filament/Resources/CourseResource.php", f"{REMOTE_BASE}/app/Filament/Resources/CourseResource.php"),
            ("resources/views/student/courses/index.blade.php", f"{REMOTE_BASE}/resources/views/student/courses/index.blade.php"),
        ]

        print("\n--- Deploying Fixes ---")
        for local, remote in files_to_deploy:
            if os.path.exists(local):
                print(f"Uploading {local}...")
                with open(local, "rb") as f:
                    content = f.read()
                b64_content = base64.b64encode(content).decode()
                cmd_upload = f"echo '{b64_content}' | base64 -d > {remote}"
                client.exec_command(cmd_upload)
            else:
                print(f"Error: Local file {local} not found!")

        # Clear view cache to ensure blade changes take effect
        print("\n--- Clearing View Cache ---")
        stdin, stdout, stderr = client.exec_command(f"cd {REMOTE_BASE} && php artisan view:clear")
        print(stdout.read().decode())

        client.close()
        print("\nDeployment Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    deploy_fixes()
