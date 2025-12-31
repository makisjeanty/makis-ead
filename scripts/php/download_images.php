<?php

// List of high-quality education/tech related images (Unsplash direct links)
$imageUrls = [
    'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=800&q=80', // Education/Coffee
    'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800&q=80', // Books/Library
    'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80', // Students working
    'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=800&q=80', // Coding/Screens
    'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&q=80', // Digital Learning
    'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&q=80', // Strategy/Planning
    'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=800&q=80', // Teamwork
    'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&q=80', // Graduation/Success
    'https://images.unsplash.com/photo-1588196749597-9ff075ee6b5b?w=800&q=80', // Online Course
    'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=800&q=80', // Typing/Blog
];

$outputDir = __DIR__ . '/../../temp_images';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

echo "Downloading images...\n";

foreach ($imageUrls as $index => $url) {
    $filename = "course_" . ($index + 1) . ".jpg";
    $filepath = $outputDir . '/' . $filename;
    
    echo "Downloading $filename...\n";
    
    $content = file_get_contents($url);
    if ($content) {
        file_put_contents($filepath, $content);
        echo "Saved $filename\n";
    } else {
        echo "Failed to download $url\n";
    }
}

echo "Done.\n";
