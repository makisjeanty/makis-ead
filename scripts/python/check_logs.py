import paramiko
import time
import sys

hostname = "195.26.252.210"
username = "root"
password = "kg4TN4inJbCp"
target_dir = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

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

def main():
    client = create_client()
    try:
        log_path = f"{target_dir}/storage/logs/laravel.log"
        print(f"Checking logs at {log_path}...")
        
        # Check if file exists
        cmd_check = f"test -f {log_path} && echo 'EXISTS' || echo 'MISSING'"
        exit_code, output = run_command(client, cmd_check, print_output=False)
        
        if "EXISTS" in output:
            print("\nLast 100 lines of log:")
            run_command(client, f"tail -n 100 {log_path}")
        else:
            print("Log file not found.")

    finally:
        client.close()

if __name__ == "__main__":
    main()
