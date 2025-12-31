import paramiko
import time
import sys
import os
import shutil
import zipfile

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"
target_dir = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
local_build_dir = "public/build"
zip_filename = "public_build.zip"

def run_command(ssh, command, print_output=True):
    print(f"\n[REMOTE] Running: {command}")
    stdin, stdout, stderr = ssh.exec_command(command, get_pty=True)

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

def zip_folder(folder_path, output_path):
    print(f"Zipping {folder_path} to {output_path}...")
    with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(folder_path):
            for file in files:
                file_path = os.path.join(root, file)
                arcname = os.path.relpath(file_path, os.path.dirname(folder_path))
                zipf.write(file_path, arcname)
    print("Zip created.")

try:
    # 1. Zip local build folder
    if not os.path.exists(local_build_dir):
        print("Local build directory not found! Run 'npm run build' first.")
        exit(1)

    zip_folder(local_build_dir, zip_filename)

    # 2. Connect
    print(f"Connecting to {hostname}...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    print("Connected successfully.")

    # 3. Get owner
    code, out = run_command(client, f"stat -c '%U' {target_dir}", print_output=False)
    owner = out.strip()
    print(f"Directory owner is: {owner}")
    
    # 4. Git Operations
    # Check if .git exists
    code, _ = run_command(client, f"test -d {target_dir}/.git")
    if code != 0:
        print("Git not initialized. Initializing...")
        cmds = [
            f"cd {target_dir}",
            "git init",
            "git remote add origin https://github.com/makisjeanty/makis-ead.git",
            "git fetch --all",
            "git reset --hard origin/main",
            "git branch --set-upstream-to=origin/main main"
        ]
    else:
        print("Git repo exists. Pulling...")
        cmds = [
            f"cd {target_dir}",
            "git fetch --all",
            "git reset --hard origin/main"
        ]

    for cmd in cmds:
        c, o = run_command(client, cmd)
        if c != 0:
            print(f"Failed: {cmd}")
            # If 'git remote add' fails because it exists, ignore
            if "remote origin already exists" in o:
                continue
            # exit(1) # Don't exit, try to continue
            
    # 5. Composer Install
    run_command(client, f"cd {target_dir} && composer install --no-dev --optimize-autoloader")
    
    # 6. Upload Build Assets
    print("Uploading build assets...")
    sftp = client.open_sftp()
    remote_zip = f"{target_dir}/{zip_filename}"
    sftp.put(zip_filename, remote_zip)
    sftp.close()
    print("Upload complete.")

    # 7. Unzip and Fix
    unzip_cmds = [
        f"cd {target_dir}",
        "apt-get install -y unzip", # Ensure unzip is installed
        f"unzip -o {zip_filename} -d public/", # unzip public_build.zip containing 'build/...' into public/
        f"rm {zip_filename}"
    ]
    # Note: our zip contains 'build/assets/...'. If we unzip to 'public/', it will be 'public/build/assets/...'
    # Let's verify zip structure. 
    # zip_folder uses relpath from dirname. 
    # dirname('public/build') is 'public'.
    # So arcname starts with 'build/'.
    # So unzipping to 'public/' (or target_dir/public) is correct.

    for cmd in unzip_cmds:
        run_command(client, cmd)

    # 8. Artisan Commands
    artisan_cmds = [
        f"cd {target_dir}",
        "php artisan migrate --force",
        "php artisan optimize:clear",
        "php artisan optimize",
        "php artisan filament:upgrade" # Just in case
    ]

    for cmd in artisan_cmds:
        run_command(client, cmd)

    # 9. Fix Permissions
    print("Fixing permissions...")
    run_command(client, f"chown -R {owner}:{owner} {target_dir}")
    # Ensure storage is writable
    run_command(client, f"chmod -R 775 {target_dir}/storage {target_dir}/bootstrap/cache")

    print("\n✅ Deployment process completed!")

except Exception as e:
    print(f"An error occurred: {e}")
    import traceback
    traceback.print_exc()
finally:
    client.close()
    if os.path.exists(zip_filename):
        os.remove(zip_filename)
