import paramiko
import base64
import os
import glob

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
CHUNK_SIZE = 5000

def upload_large_file(client, local_path, remote_path):
    print(f"Uploading {local_path}...")
    
    with open(local_path, "rb") as f:
        content = f.read()
    b64_content = base64.b64encode(content).decode()
    
    remote_temp = remote_path + ".b64"
    # Ensure no previous temp file
    client.exec_command(f"rm {remote_temp}")
    
    total_len = len(b64_content)
    chunks = [b64_content[i:i+CHUNK_SIZE] for i in range(0, total_len, CHUNK_SIZE)]
    
    for i, chunk in enumerate(chunks):
        cmd = f"echo '{chunk}' >> {remote_temp}"
        client.exec_command(cmd)
            
    cmd_decode = f"base64 -d {remote_temp} > {remote_path}"
    client.exec_command(cmd_decode)
    client.exec_command(f"rm {remote_temp}")

def deploy_original_images():
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)
        
        # Local source
        local_dir = "public/images/courses/image"
        # Remote destination (storage linked)
        remote_dir = f"{REMOTE_BASE}/storage/app/public/courses"
        
        # Ensure remote dir exists
        client.exec_command(f"mkdir -p {remote_dir}")
        
        images = glob.glob(f"{local_dir}/*.*")
        print(f"Found {len(images)} images in {local_dir}")
        
        uploaded_files = []
        
        for img in images:
            filename = os.path.basename(img)
            remote_path = f"{remote_dir}/{filename}"
            upload_large_file(client, img, remote_path)
            uploaded_files.append(filename)
            
        # Now Create and Run Seeder
        print("\n--- Seeding with Original Images ---")
        
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

echo "--- Mapping Original Images ---\\n";

$mappings = [
    'TikTok' => 'tiktok.jpg',
    'Reels' => 'tiktok.jpg',
    'Shorts' => 'tiktok.jpg',
    'Afilia' => 'afiliado.jpg',
    'Brasil' => 'brasil.jpg',
    'Português' => 'brasil.jpg',
    'Profission' => 'profission.jpg',
    'Francês' => 'profission.jpg',
    'Dinheiro' => 'money symbol.png',
    'Monetiz' => 'money symbol.png',
    'Vender' => 'money symbol.png',
    'Business' => 'capa01.png',
    'E-commerce' => 'capa01.png',
    'Blog' => 'etude.png',
    'SEO' => 'etude.png',
    'Laravel' => 'etude.png',
    'WordPress' => 'etude.png',
];

$defaults = ['capa01.png', 'etude.png', 'foto.img'];

$courses = Course::all();

foreach ($courses as $course) {
    $matched = false;
    $imageName = '';
    
    // Check keywords
    foreach ($mappings as $keyword => $file) {
        if (stripos($course->title, $keyword) !== false) {
            $imageName = $file;
            $matched = true;
            break;
        }
    }
    
    // Fallback
    if (!$matched) {
        $imageName = $defaults[array_rand($defaults)];
    }
    
    // Construct Path (storage/courses/filename)
    $finalPath = 'storage/courses/' . $imageName;
    
    $course->image = $finalPath;
    $course->save();
    
    echo "Course: {$course->title}\\n   -> Image: $finalPath\\n";
}
echo "Done.\\n";
"""
        remote_script = f"{REMOTE_BASE}/seed_original_images.php"
        b64_script = base64.b64encode(php_script.encode()).decode()
        client.exec_command(f"echo '{b64_script}' | base64 -d > {remote_script}")
        
        print("Executing seeder...")
        stdin, stdout, stderr = client.exec_command(f"php {remote_script}")
        print(stdout.read().decode())
        
        # Cleanup
        client.exec_command(f"rm {remote_script}")
        
        client.close()
        print("\nDeployment of Original Images Completed.")

    except Exception as e:
        print(f"Fatal Error: {str(e)}")

if __name__ == "__main__":
    deploy_original_images()
