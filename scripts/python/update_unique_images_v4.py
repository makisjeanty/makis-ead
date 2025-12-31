import paramiko
import base64
import requests
import os
import sys
import time

# Configuration
HOSTNAME = "195.26.252.210"
USERNAME = "root"
PASSWORD = "kg4TN4inJbCp"
REMOTE_BASE = "/home/ETUDE-RAPIDE/web/etuderapide.com/public_html"
CHUNK_SIZE = 5000

# MAPPING: Course Title Partial Keyword -> Unique Image URL
# ENSURING ZERO REPETITION
UNIQUE_MAPPINGS = [
    {
        'keyword': 'TikTok',
        'filename': 'course_tiktok.jpg',
        'url': 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Blog',
        'filename': 'course_blog.jpg',
        'url': 'https://images.unsplash.com/photo-1488190213531-9e3696629062?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Afilia',
        'filename': 'course_affiliate.jpg',
        'url': 'https://images.unsplash.com/photo-1533750516457-a7f992034fec?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Dropshipping',
        'filename': 'course_dropshipping.jpg',
        'url': 'https://images.unsplash.com/photo-1580674684081-7617fbf3d745?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Freelance',
        'filename': 'course_freelance.jpg',
        'url': 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Brasil',
        'filename': 'course_brazil.jpg',
        'url': 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Espanhol',
        'filename': 'course_spanish.jpg',
        'url': 'https://images.unsplash.com/photo-1565626424209-66d11f71274d?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Francês',
        'filename': 'course_french.jpg',
        'url': 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'WordPress',
        'filename': 'course_wordpress.jpg',
        'url': 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Laravel',
        'filename': 'course_laravel.jpg',
        'url': 'https://images.unsplash.com/photo-1599507593499-a3f7d7d97667?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Automações',
        'filename': 'course_python_auto.jpg',
        'url': 'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Monetizar',
        'filename': 'course_teaching.jpg',
        'url': 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Branding',
        'filename': 'course_branding.jpg',
        'url': 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'WhatsApp',
        'filename': 'course_whatsapp.jpg',
        'url': 'https://images.unsplash.com/photo-1611162618071-b39a2ec055fb?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Business',
        'filename': 'course_business.jpg',
        'url': 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Formação',
        'filename': 'course_filming.jpg',
        'url': 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Assinatura',
        'filename': 'course_subscription.jpg',
        'url': 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'Sem Diploma',
        'filename': 'course_cash.jpg',
        'url': 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?ixlib=rb-4.0.3&w=800&q=80'
    },
    {
        'keyword': 'IA',
        'filename': 'course_ai.jpg',
        'url': 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?ixlib=rb-4.0.3&w=800&q=80'
    }
]

def download_image(url, filename):
    print(f"Downloading {filename}...")
    try:
        response = requests.get(url, timeout=15)
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
    if not os.path.exists("unique_images"):
        os.makedirs("unique_images")

    # 1. Download all images
    for item in UNIQUE_MAPPINGS:
        local_path = os.path.join("unique_images", item['filename'])
        download_image(item['url'], local_path)

    # 2. Connect to SSH
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        print(f"Connecting to {HOSTNAME}...")
        client.connect(HOSTNAME, username=USERNAME, password=PASSWORD)

        # 3. Upload images
        remote_dir = f"{REMOTE_BASE}/storage/app/public/courses"
        client.exec_command(f"mkdir -p {remote_dir}")

        for item in UNIQUE_MAPPINGS:
            local_path = os.path.join("unique_images", item['filename'])
            remote_path = f"{remote_dir}/{item['filename']}"
            if os.path.exists(local_path):
                upload_file_ssh(client, local_path, remote_path)

        # 4. Update Database
        print("\nUpdating Database with UNIQUE images...")

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

echo "--- Updating Course Images (UNIQUE) ---\\n";

$mappings = [
"""

        for item in UNIQUE_MAPPINGS:
            php_script += f"    '{item['keyword']}' => 'courses/{item['filename']}',\n"

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

    if (!$matched) {
        echo "WARNING: No match found for {$course->title}\\n";
    }
}
echo "Done.\\n";
"""

        remote_php_script = f"{REMOTE_BASE}/update_unique_images.php"

        with open("temp_unique_script.php", "w", encoding="utf-8") as f:
            f.write(php_script)

        upload_file_ssh(client, "temp_unique_script.php", remote_php_script)

        print("Running database update...")
        stdin, stdout, stderr = client.exec_command(f"php {remote_php_script}")
        print(stdout.read().decode())
        print(stderr.read().decode())

        client.exec_command(f"rm {remote_php_script}")
        os.remove("temp_unique_script.php")

        client.close()
        print("\nOperation Completed Successfully.")

    except Exception as e:
        print(f"Error during deployment: {e}")

if __name__ == "__main__":
    main()
