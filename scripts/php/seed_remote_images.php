<?php

// Script to run on REMOTE SERVER
// It expects images to be already uploaded to storage/app/public/courses/

// Fix autoload
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    '/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/vendor/autoload.php'
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        break;
    }
}

$app = require_once __DIR__ . '/../bootstrap/app.php'; // Adjust path relative to script location in public_html/scripts/
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use Illuminate\Support\Facades\Storage;

echo "--- Seeding Course Images ---\n";

$courses = Course::all();
echo "Found " . $courses->count() . " courses.\n";

$images = glob(storage_path('app/public/courses/course_*.jpg'));
$imageCount = count($images);

if ($imageCount === 0) {
    echo "No images found in storage/app/public/courses/ . Please upload them first.\n";
    // Check if they are in public/courses (direct access)
    $images = glob(public_path('courses/course_*.jpg'));
    if (count($images) > 0) {
        echo "Found images in public/courses, moving to storage...\n";
        // Logic to move if needed, but for now assuming we upload to storage
    } else {
        exit(1);
    }
}

echo "Found $imageCount available images.\n";

foreach ($courses as $index => $course) {
    // Pick an image cyclically
    $imageIndex = $index % $imageCount;
    $imagePath = 'courses/course_' . ($imageIndex + 1) . '.jpg';

    echo "Updating course '{$course->title}' with image: $imagePath\n";

    $course->image = $imagePath;
    // Assuming 'thumbnail' column exists based on migration analysis
    $course->thumbnail = $imagePath;
    $course->save();
}

echo "All courses updated.\n";
