import paramiko
import os
import base64

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

FILES_TO_UPLOAD = [
    "resources/views/student/dashboard.blade.php",
    "resources/views/courses/index.blade.php",
    "resources/views/student/courses/index.blade.php",
    "resources/views/welcome.blade.php",
    "resources/views/courses/show.blade.php",
    "app/Filament/Resources/CourseResource.php"
]

def upload_files():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        local_base = os.getcwd()
        if "scripts" in local_base and "python" in local_base:
            local_base = os.path.dirname(os.path.dirname(os.path.dirname(local_base)))
        elif "makis-ead" not in local_base:
             local_base = "F:\\workspace\\backend\\php\\makis-ead"

        print(f"Local Base: {local_base}")

        for relative_path in FILES_TO_UPLOAD:
            local_path = os.path.join(local_base, relative_path)
            # remote path needs forward slashes
            remote_path = f"{REMOTE_BASE}/{relative_path.replace(os.sep, '/')}"

            print(f"Uploading {relative_path}...")

            try:
                with open(local_path, 'rb') as f:
                    content = f.read()
                    encoded_content = base64.b64encode(content).decode('utf-8')

                # Command to decode and write
                cmd = f"echo '{encoded_content}' | base64 -d > {remote_path}"

                stdin, stdout, stderr = client.exec_command(cmd)
                exit_status = stdout.channel.recv_exit_status()

                if exit_status == 0:
                    print(f"  -> Success")
                else:
                    print(f"  -> Failed: {stderr.read().decode()}")

            except Exception as e:
                print(f"  -> Failed locally: {str(e)}")

        # Clear view cache
        print("\nClearing view cache...")
        stdin, stdout, stderr = client.exec_command(f"cd {REMOTE_BASE} && php artisan view:clear")
        print(stdout.read().decode())
        print(stderr.read().decode())

        client.close()
        print("\nDone!")

    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    upload_files()
