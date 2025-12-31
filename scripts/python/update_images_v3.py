import paramiko
import base64
import requests
import os
import sys

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
CHUNK_SIZE = 5000

# Unsplash Image IDs mapping
# Using High Quality Direct Download Links
IMAGE_MAPPING = {
    'tiktok.jpg': 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # TikTok Smartphone
    'marketing.jpg': 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # Analytics/Data
    'programming.jpg': 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # Code
    'ecommerce.jpg': 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # Online Shopping/Payment
    'excel.jpg': 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # Spreadsheet/Data
    'languages.jpg': 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # Books/Learning
    'business.jpg': 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # Meeting/People
    'money.jpg': 'https://images.unsplash.com/photo-1579621970795-87facc2f976d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', # Coins/Finance
}

# Database mapping (Keyword in course title -> Image file)
COURSE_IMAGE_MAP = [
    ('TikTok', 'tiktok.jpg'),
    ('Reels', 'tiktok.jpg'),
    ('Shorts', 'tiktok.jpg'),
    ('Afilia', 'marketing.jpg'),
    ('Marketing', 'marketing.jpg'),
    ('Google', 'marketing.jpg'),
    ('Facebook', 'marketing.jpg'),
    ('Python', 'programming.jpg'),
    ('Programação', 'programming.jpg'),
    ('Web', 'programming.jpg'),
    ('WordPress', 'programming.jpg'),
    ('Laravel', 'programming.jpg'),
    ('E-commerce', 'ecommerce.jpg'),
    ('Dropshipping', 'ecommerce.jpg'),
    ('Excel', 'excel.jpg'),
    ('Inglês', 'languages.jpg'),
    ('Francês', 'languages.jpg'),
    ('Espanhol', 'languages.jpg'),
    ('Português', 'languages.jpg'),
    ('Business', 'business.jpg'),
    ('Negócio', 'business.jpg'),
    ('Freelance', 'business.jpg'),
    ('Branding', 'business.jpg'),
    ('Dinheiro', 'money.jpg'),
    ('Monetiz', 'money.jpg'),
    ('Assinatura', 'money.jpg'),
    ('Vendas', 'business.jpg'),
    ('SEO', 'marketing.jpg'),
    ('Blog', 'marketing.jpg'),
]

def download_image(url, filename):
    print(f"Downloading {filename} from {url}...")
    try:
        response = requests.get(url, timeout=10)
        if response.status_code == 200:
            with open(filename, 'wb') as f:
                f.write(response.content)
            return True
        else:
            print(f"Failed to download {url}: Status {response.status_code}")
            return False
    except Exception as e:
        print(f"Error downloading {url}: {e}")
        return False

def upload_file_ssh(client, local_path, remote_path):
    print(f"Uploading {local_path} to {remote_path}...")
    try:
        with open(local_path, "rb") as f:
            content = f.read()

        b64_content = base64.b64encode(content).decode()

        # Split into chunks to avoid command line length limits
        total_len = len(b64_content)
        chunks = [b64_content[i:i+CHUNK_SIZE] for i in range(0, total_len, CHUNK_SIZE)]

        remote_temp = remote_path + ".b64"
        client.exec_command(f"rm -f {remote_temp}")

        for chunk in chunks:
            cmd = f"echo '{chunk}' >> {remote_temp}"
            client.exec_command(cmd)

        cmd_decode = f"base64 -d {remote_temp} > {remote_path}"
        client.exec_command(cmd_decode)
        client.exec_command(f"rm -f {remote_temp}")
        print("  -> Upload success")
        return True
    except Exception as e:
        print(f"  -> Upload failed: {e}")
        return False

def main():
    # 1. Download images locally
    if not os.path.exists("temp_images"):
        os.makedirs("temp_images")

    downloaded_images = []
    for filename, url in IMAGE_MAPPING.items():
        local_path = os.path.join("temp_images", filename)
        if download_image(url, local_path):
            downloaded_images.append(filename)

    if not downloaded_images:
        print("No images downloaded. Aborting.")
        return

    # 2. Connect to SSH
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # 3. Upload images
        remote_dir = f"{REMOTE_BASE}/storage/app/public/courses"
        client.exec_command(f"mkdir -p {remote_dir}")

        for filename in downloaded_images:
            local_path = os.path.join("temp_images", filename)
            remote_path = f"{remote_dir}/{filename}"
            upload_file_ssh(client, local_path, remote_path)

        # 4. Update Database
        print("\nUpdating Database...")

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

echo "--- Updating Course Images ---\\n";

$mappings = [
"""

        # Add mappings to PHP script
        for keyword, image in COURSE_IMAGE_MAP:
            php_script += f"    '{keyword}' => 'courses/{image}',\n"

        php_script += """
];

$courses = Course::all();
foreach ($courses as $course) {
    $matched = false;
    foreach ($mappings as $keyword => $image) {
        if (stripos($course->title, $keyword) !== false) {
            $course->image = $image;
            $course->save();
            echo "Updated {$course->title} -> {$image}\\n";
            $matched = true;
            break;
        }
    }

    // Fallback for unmatched
    if (!$matched) {
        if (stripos($course->title, 'Dinheiro') !== false || stripos($course->title, 'Renda') !== false) {
             $course->image = 'courses/money.jpg';
        } else {
             $course->image = 'courses/business.jpg'; // Generic fallback
        }
        $course->save();
        echo "Updated {$course->title} -> {$course->image} (Fallback)\\n";
    }
}
echo "Done.\\n";
"""

        # Execute PHP script
        cmd = f"php -r \"{php_script.replace('\"', '\\\"')}\""
        # The above escaping is tricky with multiline. Better to write to file.

        # Write PHP script to remote file
        remote_php_script = f"{REMOTE_BASE}/update_images_v3.php"

        # Simple upload of the PHP content
        with open("temp_script.php", "w", encoding="utf-8") as f:
            f.write(php_script)

        upload_file_ssh(client, "temp_script.php", remote_php_script)

        print("Running database update...")
        stdin, stdout, stderr = client.exec_command(f"php {remote_php_script}")
        print(stdout.read().decode())
        print(stderr.read().decode())

        # Cleanup
        client.exec_command(f"rm {remote_php_script}")
        os.remove("temp_script.php")

        client.close()
        print("\nOperation Completed Successfully.")

    except Exception as e:
        print(f"Error during deployment: {e}")

if __name__ == "__main__":
    main()
