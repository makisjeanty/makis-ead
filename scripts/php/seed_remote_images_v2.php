<?php

// Script to run on REMOTE SERVER
// It expects images to be already uploaded to storage/app/public/courses/

// Fix autoload
$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/vendor/autoload.php'
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        break;
    }
}

// Fix bootstrap
$bootstrapPaths = [
    __DIR__ . '/bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/bootstrap/app.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/bootstrap/app.php'
];

$app = null;
foreach ($bootstrapPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        break;
    }
}

if (!$app) {
    die("Error: Could not find bootstrap/app.php\n");
}

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use Illuminate\Support\Facades\Storage;

echo "--- Seeding Course Images ---\n";

$courses = Course::all();
echo "Found " . $courses->count() . " courses.\n";

// Check storage paths
$storagePath = storage_path('app/public/courses');
$images = glob($storagePath . '/course_*.jpg');
$imageCount = count($images);

echo "Checking path: $storagePath\n";

if ($imageCount === 0) {
    echo "No images found in standard storage path.\n";
    // Try absolute path check just in case storage_path is weird
    $absolutePath = '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/storage/app/public/courses';
    $images = glob($absolutePath . '/course_*.jpg');
    $imageCount = count($images);
    
    if ($imageCount > 0) {
        echo "Found images in absolute path: $absolutePath\n";
    } else {
        echo "Trying public path fallback...\n";
        // Fallback to public folder directly if storage link is broken or used differently
        $publicPath = public_path('courses'); // e.g., public_html/courses
        if (!is_dir($publicPath)) @mkdir($publicPath, 0755, true);
        
        $images = glob($publicPath . '/course_*.jpg');
        $imageCount = count($images);
    }
}

if ($imageCount === 0) {
    echo "CRITICAL: No images found to seed. Upload images to storage/app/public/courses first.\n";
    exit(1);
}

echo "Found $imageCount available images.\n";

foreach ($courses as $index => $course) {
    // Pick an image cyclically
    $imageIndex = $index % $imageCount;
    // We store the relative path that Storage::url() or asset() would expect
    // Usually 'courses/filename.jpg' if in storage/app/public/courses
    $filename = basename($images[$imageIndex]);
    $imagePath = 'courses/' . $filename;
    
    echo "Updating course '{$course->title}' with image: $imagePath\n";
    
    $course->image = $imagePath;
    // Update thumbnail if column exists (we assume it does based on migration)
    $course->thumbnail = $imagePath; 
    $course->save();
}

echo "All courses updated.\n";
