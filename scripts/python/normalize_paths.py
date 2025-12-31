import paramiko
import base64

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def normalize_paths():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        # Create PHP script
        php_script = """<?php
$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/vendor/autoload.php',
];
foreach ($autoloadPaths as $path) { if (file_exists($path)) { require $path; break; } }

$bootstrapPaths = [
    __DIR__ . '/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/bootstrap/app.php',
];
$app = null;
foreach ($bootstrapPaths as $path) { if (file_exists($path)) { $app = require_once $path; break; } }

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;

echo "--- Normalizing Image Paths (Removing 'storage/' prefix) ---\\n";

$courses = Course::all();
foreach ($courses as $course) {
    $original = $course->image;
    
    if (empty($original)) continue;
    
    $newImage = $original;
    
    // If it starts with 'storage/', remove it
    if (strpos($original, 'storage/') === 0) {
        $newImage = substr($original, 8); // Remove 'storage/'
    }
    
    if ($original !== $newImage) {
        $course->image = $newImage;
        $course->save();
        echo "Fixed Course '{$course->title}': $original -> $newImage\\n";
    } else {
        // echo "Skipping '{$course->title}': Already correct ($original)\\n";
    }
}
echo "Done.\\n";
"""
        
        # Upload script
        remote_script_path = f"{REMOTE_BASE}/normalize_paths.php"
        b64_content = base64.b64encode(php_script.encode()).decode()
        client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_script_path}")
        
        # Execute
        print("Executing path normalization...")
        stdin, stdout, stderr = client.exec_command(f"php {remote_script_path}")
        print(stdout.read().decode())
        err = stderr.read().decode()
        if err:
            print("Errors:", err)
            
        # Cleanup
        client.exec_command(f"rm {remote_script_path}")
        
    except Exception as e:
        print(f"An error occurred: {e}")
    finally:
        client.close()

if __name__ == "__main__":
    normalize_paths()
