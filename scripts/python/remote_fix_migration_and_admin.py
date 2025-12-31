import paramiko
import time
import sys

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"
target_dir = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
owner = "ETUDE-RAPIDE"

def create_client():
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    return client

def run_command(client, command):
    print(f"\n[REMOTE] Running: {command}")
    stdin, stdout, stderr = client.exec_command(command, get_pty=True)

    while not stdout.channel.exit_status_ready():
        if stdout.channel.recv_ready():
            print(stdout.channel.recv(1024).decode('utf-8', errors='ignore'), end="")
            sys.stdout.flush()
        time.sleep(0.1)

    print(stdout.read().decode('utf-8', errors='ignore'), end="")

def main():
    try:
        client = create_client()
        print("Connected.")

        # 1. Run specific migration for posts
        print("\n--- Running Posts Migration ---")
        migration_path = "database/migrations/2025_12_30_225955_create_posts_table.php"
        cmd = f"su -s /bin/bash {owner} -c 'cd {target_dir} && php artisan migrate --path={migration_path} --force'"
        run_command(client, cmd)

        # 2. Get Admin Data
        print("\n--- Getting Admin Data ---")
        # Using a simple artisan tinker command to get admin data
        php_code = r"App\Models\User::where('id', 1)->first(['name', 'email']);"
        # We need to be careful with quotes in the command line
        tinker_cmd = f"php artisan tinker --execute=\"dump({php_code})\""

        cmd = f"su -s /bin/bash {owner} -c 'cd {target_dir} && {tinker_cmd}'"
        run_command(client, cmd)

    except Exception as e:
        print(f"\n❌ Error: {e}")
    finally:
        if 'client' in locals():
            client.close()

if __name__ == "__main__":
    main()
