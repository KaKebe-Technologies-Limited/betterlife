<?php
/**
 * One-time batch job: takes the raw camera/phone photos just added to
 * assets/img (multi-megabyte JPEGs straight off a camera/phone, several
 * over 20MB and 6000px+ on the long edge) and produces light, web-ready
 * WebP versions: resized to a sane max dimension, EXIF stripped, given
 * clean descriptive filenames. Originals are moved to assets/img/originals/
 * (kept, not deleted, in case full-resolution copies are ever wanted) so
 * they stop being served by the site or counted in any future image audit.
 *
 * Run once: php migrations/optimize_new_photos.php
 */

$imgDir = __DIR__ . '/../assets/img';
$originalsDir = $imgDir . '/originals';
if (!is_dir($originalsDir)) mkdir($originalsDir, 0755, true);

// [source filename, new webp filename, max long-edge px, quality]
$jobs = [
    ['BETTER-LYF-141.jpg', 'yumbe-greenhouse-group.webp', 2000, 78],
    ['BETTER-LYF-146.jpg', 'yumbe-greenhouse-interior.webp', 2000, 78],
    ['BETTER-LYF-27.jpg', 'smiles-group-under-tree-1.webp', 2000, 78],
    ['BETTER-LYF-28.jpg', 'smiles-group-under-tree-2.webp', 2000, 78],
    ['BETTER-LYF-3 (1).jpg', 'farm-aerial-view-1.webp', 2000, 78],
    ['BETTER-LYF-37.jpg', 'market-stall-vendor.webp', 2000, 78],
    ['BETTER-LYF-38.jpg', 'children-at-borehole.webp', 2000, 78],
    ['BETTER-LYF-4 (1).jpg', 'farm-aerial-view-2.webp', 2000, 78],
    ['BETTER-LYF-54.jpg', 'field-team-conversation.webp', 2000, 78],
    ['BETTER-LYF-60.jpg', 'vendor-and-children-food-stall.webp', 2000, 78],
    ['BETTER-LYF-65.jpg', 'field-team-group-under-tree.webp', 2000, 78],
    ['BETTER-LYF-74.jpg', 'soilla-app-field-demo.webp', 2000, 78],
    ['BETTER-LYF-78.jpg', 'solar-panel-installation-1.webp', 2000, 78],
    ['Betterlife-ug-104.jpg', 'village-children-portrait.webp', 2000, 78],
    ['Betterlife-ug-110.jpg', 'village-girl-portrait.webp', 2000, 78],
    ['FK8A0332.jpg', 'solar-panel-farm-sky.webp', 2000, 78],
    ['IMG_0034.jpg', 'farmers-planting-together.webp', 2000, 78],
    ['IMG_0151.jpg', 'farmer-spraying-crops.webp', 2000, 78],
    ['IMG_0172.jpg', 'solar-panel-installation-2.webp', 2000, 78],
    ['IMG_0249.jpg', 'woman-winnowing-grain.webp', 2000, 78],
    ['IMG_0298.jpg', 'grain-milling-machine.webp', 2000, 78],
    ['IMG_4984.jpg', 'school-child-drinking-water.webp', 2000, 78],
    ['IMG_5027.jpg', 'classroom-climate-club.webp', 2000, 78],
    ['IMG_5068.jpg', 'soil-sample-in-hand.webp', 2000, 78],
    ['IMG_5081.jpg', 'soilla-app-portrait.webp', 2000, 78],
    ['IMG_5131.jpg', 'agribusiness-connekt-app.webp', 2000, 78],
    ['IMG_7990.JPG', 'speaker-at-betterlife-banner.webp', 2000, 78],
    ['IMG_7996.JPG', 'miss-change-partnership-event.webp', 2000, 78],
    ['IMG_8031.JPG', 'team-conference-group-photo.webp', 2000, 78],
    ['sedrick otolo professional pic.png', 'team-sedrick-otolo.webp', 900, 85],
];

function load_image(string $path)
{
    $info = getimagesize($path);
    if (!$info) return null;
    switch ($info['mime']) {
        case 'image/jpeg': return imagecreatefromjpeg($path);
        case 'image/png':  return imagecreatefrompng($path);
        default: return null;
    }
}

$totalBefore = 0;
$totalAfter = 0;

foreach ($jobs as [$src, $dstName, $maxDim, $quality]) {
    $srcPath = $imgDir . '/' . $src;
    if (!is_file($srcPath)) {
        echo "SKIP (not found): $src\n";
        continue;
    }
    $beforeSize = filesize($srcPath);
    $totalBefore += $beforeSize;

    $img = load_image($srcPath);
    if (!$img) {
        echo "SKIP (unreadable): $src\n";
        continue;
    }

    // Respect JPEG EXIF orientation so photos don't end up sideways.
    if (function_exists('exif_read_data') && preg_match('/\.jpe?g$/i', $src)) {
        $exif = @exif_read_data($srcPath);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3: $img = imagerotate($img, 180, 0); break;
                case 6: $img = imagerotate($img, -90, 0); break;
                case 8: $img = imagerotate($img, 90, 0); break;
            }
        }
    }

    $w = imagesx($img);
    $h = imagesy($img);
    $longEdge = max($w, $h);
    if ($longEdge > $maxDim) {
        $scale = $maxDim / $longEdge;
        $newW = (int) round($w * $scale);
        $newH = (int) round($h * $scale);
        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($img);
        $img = $resized;
    }

    $dstPath = $imgDir . '/' . $dstName;
    imagewebp($img, $dstPath, $quality);
    imagedestroy($img);

    $afterSize = filesize($dstPath);
    $totalAfter += $afterSize;
    printf("%-40s %6.1fMB -> %-30s %5.1fKB\n", $src, $beforeSize / 1024 / 1024, $dstName, $afterSize / 1024);

    // Move the heavy original out of the served img tree.
    rename($srcPath, $originalsDir . '/' . basename($src));
}

printf("\nTotal: %.1fMB -> %.1fMB (%.1f%% smaller)\n",
    $totalBefore / 1024 / 1024, $totalAfter / 1024 / 1024,
    100 - ($totalAfter / max($totalBefore, 1) * 100));
