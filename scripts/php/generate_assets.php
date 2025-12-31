<?php

// Define paths
$publicPath = __DIR__ . '/../../public';
if (!file_exists($publicPath)) {
    // If running locally relative to script location
    $publicPath = 'public'; 
    if (!is_dir($publicPath)) mkdir($publicPath, 0755, true);
}

// 1. Generate SVG Favicon (Modern, High Quality)
$svgContent = <<<SVG
<svg width="512" height="512" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="512" height="512" rx="128" fill="#4F46E5"/>
    <path d="M160 380V132H280C306.667 132 328.667 139.333 346 154C363.333 168.667 372 188 372 212C372 228 367.333 242 358 254C348.667 266 336 275.333 320 282V284C340 290.667 356 301.333 368 316C380 330.667 386 348.667 386 370C386 398 376 420.667 356 438C336 455.333 309.333 464 276 464H160V380ZM238 274H274C290.667 274 303.333 269.333 312 260C320.667 250.667 325 238.667 325 224C325 208.667 320 196.333 310 187C300 177.667 286.667 173 270 173H238V274ZM238 423H276C296.667 423 312 417.667 322 407C332 396.333 337 382 337 364C337 345.333 331 330.667 319 320C307 309.333 289.333 304 266 304H238V423Z" fill="white"/>
    <path d="M160 132H352" stroke="white" stroke-width="40" stroke-linecap="round"/>
</svg>
SVG;

// Simplify to just initials "ER" for better visibility at small sizes
$svgSimple = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
  <rect width="512" height="512" rx="100" fill="#4F46E5"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-weight="bold" font-size="280" fill="white">ER</text>
</svg>
SVG;

file_put_contents('favicon.svg', $svgSimple);
echo "Generated favicon.svg\n";

// 2. Generate ICO (requires GD or ImageMagick, but we can simulate a simple one or just use SVG for modern browsers)
// For compatibility, we'll try to generate a PNG from the SVG logic if GD is available, otherwise we rely on SVG.
// Since we are in a script, we can't easily rasterize SVG without specific libs. 
// Instead, let's create a simple PNG pixel-by-pixel or download a placeholder if needed.
// Ideally, we upload the SVG and use it. Most modern browsers support SVG favicons.

// Let's create a dummy PNG for fallback using GD if available
if (function_exists('imagecreate')) {
    $im = imagecreate(32, 32);
    $bg = imagecolorallocate($im, 79, 70, 229); // #4F46E5
    $text_color = imagecolorallocate($im, 255, 255, 255);
    imagestring($im, 5, 8, 8, 'ER', $text_color);
    imagepng($im, 'favicon.png');
    imagedestroy($im);
    echo "Generated favicon.png\n";
} else {
    echo "GD library not found, skipping PNG generation.\n";
}

?>
