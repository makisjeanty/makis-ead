import paramiko
import time
import sys
import os
import zipfile
import base64
import subprocess

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"
target_dir = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
local_zip = "deploy_package.zip"
remote_zip = f"{target_dir}/deploy_package.zip"

def create_client():
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    return client

def run_command(client, command, print_output=True):
    print(f"\n[REMOTE] Running: {command}")
    stdin, stdout, stderr = client.exec_command(command, get_pty=True)

    output_buffer = ""
    while not stdout.channel.exit_status_ready():
        if stdout.channel.recv_ready():
            data = stdout.channel.recv(1024).decode('utf-8', errors='ignore')
            output_buffer += data
            if print_output:
                print(data, end="")
                sys.stdout.flush()
        time.sleep(0.1)

    remaining = stdout.read().decode('utf-8', errors='ignore')
    output_buffer += remaining
    if print_output and remaining:
        print(remaining, end="")

    exit_status = stdout.channel.recv_exit_status()
    return exit_status, output_buffer

def build_frontend():
    print("\n--- Building Frontend (SKIPPED LOCAL) ---")
    # try:
    #     if os.path.exists("public/build"):
    #          # Clean old build
    #          import shutil
    #          shutil.rmtree("public/build")
    #
    #     subprocess.check_call(["npm", "run", "build"], shell=True)
    #     print("Frontend build successful.")
    # except subprocess.CalledProcessError:
    #     print("Frontend build failed!")
    #     sys.exit(1)
    print("Skipping local build due to environment issues. Will build on server.")

def create_zip():
    print(f"\n--- Creating Zip: {local_zip} ---")
    if os.path.exists(local_zip):
        os.remove(local_zip)

    # Ensure public/build exists locally
    if not os.path.exists("public/build"):
        print("ERROR: public/build not found! Run 'npm run build' locally first.")
        sys.exit(1)

    includes = [
        'app', 'bootstrap', 'config', 'database', 'public',
        'resources', 'routes', 'composer.json', 'composer.lock', 'artisan',
        'package.json', 'package-lock.json', 'vite.config.js', 'postcss.config.js', 'tailwind.config.js'
    ]

    with zipfile.ZipFile(local_zip, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for item in includes:
            if os.path.isfile(item):
                zipf.write(item)
            elif os.path.isdir(item):
                for root, dirs, files in os.walk(item):
                    # Skip storage inside public if it's a symlink or dir, but we want the files in public
                    # We want to exclude node_modules if it somehow got into one of these folders (unlikely but safe)
                    if 'node_modules' in root:
                        continue

                    # Skip public/storage specifically
                    if 'public\\storage' in root or 'public/storage' in root:
                         continue

                    for file in files:
                        file_path = os.path.join(root, file)
                        # Skip if file path contains public/storage
                        if 'public\\storage' in file_path or 'public/storage' in file_path:
                             continue

                        arcname = file_path # keep relative path
                        try:
                            zipf.write(file_path, arcname)
                        except OSError as e:
                            print(f"Skipping {file_path}: {e}")

    size_mb = os.path.getsize(local_zip) / (1024 * 1024)
    print(f"Zip created. Size: {size_mb:.2f} MB")

def upload_zip_base64(client):
    print(f"\n--- Uploading {local_zip} via Base64 ---")

    # Check remote zip existence and remove
    run_command(client, f"rm -f {remote_zip}")
    remote_b64 = remote_zip + ".b64"
    run_command(client, f"rm -f {remote_b64}")

    # Create empty b64 file
    run_command(client, f"touch {remote_b64}")

    chunk_size = 50 * 1024 # 50KB chunks

    with open(local_zip, "rb") as f:
        # We read raw bytes, encode to b64 string, then send
        raw_data = f.read()
        encoded_data = base64.b64encode(raw_data).decode()

    total_len = len(encoded_data)
    total_chunks = (total_len // chunk_size) + 1
    print(f"Total size to upload: {total_len} bytes in {total_chunks} chunks")

    for i in range(0, total_len, chunk_size):
        chunk = encoded_data[i:i+chunk_size]
        percent = (i / total_len) * 100
        print(f"\rUploading: {percent:.1f}%", end="")

        # Append chunk to remote file
        # We use a simple echo without newline
        cmd = f"cat >> {remote_b64}"
        stdin, stdout, stderr = client.exec_command(cmd)
        stdin.write(chunk)
        stdin.close()

        exit_status = stdout.channel.recv_exit_status()
        if exit_status != 0:
            print(f"\nChunk upload failed at index {i}")
            sys.exit(1)

    print("\nUpload finished. Decoding...")
    run_command(client, f"base64 -d {remote_b64} > {remote_zip}")
    run_command(client, f"rm {remote_b64}")
    print("Decode finished.")

try:
    # 1. Build
    # build_frontend() # Skipped, assumed done manually before script

    # 2. Zip
    create_zip()

    # 3. Connect
    print(f"\nConnecting to {hostname}...")
    client = create_client()
    print("Connected.")

    # 4. Upload
    upload_zip_base64(client)

    # 5. Get Owner
    code, out = run_command(client, f"stat -c '%U' {target_dir}", print_output=False)
    owner = out.strip()
    print(f"Owner: {owner}")

    # 6. Unzip
    print("\n--- Unzipping Remote ---")
    run_command(client, f"unzip -o {remote_zip} -d {target_dir}")
    run_command(client, f"rm {remote_zip}")

    # 7. Commands
    print("\n--- Running Post-Deploy Commands ---")

    def run_as_user(cmd):
        full = f"su -s /bin/bash {owner} -c 'cd {target_dir} && {cmd}'"
        run_command(client, full)

    # Install dependencies
    run_as_user("composer install --no-dev --optimize-autoloader")

    # Install Node dependencies and Build (SKIPPED - Uploaded from Local)
    print("\n--- Frontend Build Skipped (Using Local Build) ---")
    # run_as_user("npm install")
    # run_as_user("npm run build")

    # Migrations & Cache
    run_as_user("php artisan migrate --force")
    run_as_user("php artisan optimize:clear")
    run_as_user("php artisan optimize")
    run_as_user("php artisan filament:upgrade")
    run_as_user("php artisan view:cache")

    # 8. Permissions
    print("\n--- Fixing Permissions ---")
    run_command(client, f"chown -R {owner}:{owner} {target_dir}")
    run_command(client, f"chmod -R 775 {target_dir}/storage {target_dir}/bootstrap/cache")

    print("\n✅ FULL DEPLOY COMPLETED!")

except Exception as e:
    print(f"\n❌ Error: {e}")
    import traceback
    traceback.print_exc()
finally:
    if 'client' in locals():
        client.close()
