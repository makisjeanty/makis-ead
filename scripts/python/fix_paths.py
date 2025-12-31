import paramiko
import base64
import os

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"

def fix_paths():
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

$app = null;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        break;
    }
}

$bootstrapPaths = [
    __DIR__ . '/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/bootstrap/app.php',
];

foreach ($bootstrapPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        break;
    }
}

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;

echo "--- Fixing Image Paths ---\\n";

$courses = Course::all();
foreach ($courses as $course) {
    $original = $course->image;
    
    if (empty($original)) continue;
    
    // Check if it needs 'storage/' prefix
    // We assume images are in storage/app/public/courses
    // So the web path should be storage/courses/filename.jpg
    
    $newImage = $original;
    
    // If it starts with 'courses/', prepend 'storage/'
    if (strpos($original, 'courses/') === 0) {
        $newImage = 'storage/' . $original;
    }
    
    // If it's just a filename (unlikely given previous seeder), fix it too
    if (strpos($original, '/') === false) {
        $newImage = 'storage/courses/' . $original;
    }

    if ($original !== $newImage) {
        $course->image = $newImage;
        $course->save();
        echo "Fixed Course '{$course->title}': $original -> $newImage\\n";
    } else {
        echo "Skipping Course '{$course->title}': Already correct ($original)\\n";
    }
}
echo "Done.\\n";
"""
        
        # Upload script
        remote_script_path = f"{REMOTE_BASE}/fix_paths.php"
        b64_content = base64.b64encode(php_script.encode()).decode()
        client.exec_command(f"echo '{b64_content}' | base64 -d > {remote_script_path}")
        
        # Execute
        print("Executing path fix...")
        stdin, stdout, stderr = client.exec_command(f"php {remote_script_path}")
        print(stdout.read().decode())
        err = stderr.read().decode()
        if err:
            print("Errors:", err)
            
        # Cleanup
        client.exec_command(f"rm {remote_script_path}")
        
        client.close()
        print("Path Fix Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    fix_paths()
