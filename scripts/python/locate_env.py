import paramiko

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"

def list_files():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        paths = [
            "/home/ETUDE-RAPIDE/web/etuderapide.com/",
            "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/"
        ]
        
        for path in paths:
            print(f"\n--- Listing {path} ---")
            stdin, stdout, stderr = client.exec_command(f"ls -la {path}")
            print(stdout.read().decode())
            err = stderr.read().decode()
            if err:
                print("Error:", err)
                
        client.close()
        
    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    list_files()
