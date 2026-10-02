<?php
/**
 * Makes lighter, resized WebP copies of the photos a page uses, so phones and
 * laptops download a size close to what they display (see ab_variants() in
 * about.php). Re-run after adding or changing photos.
 *
 * Usage (GD is loaded just for this run; no php.ini change needed):
 *   C:\xampp\php\php.exe -d extension=gd tools/make_sized_images.php about.php
 *
 * Output: assets/img/sized/<path-with-__-instead-of-/>-<width>.webp
 */
if (!function_exists('imagewebp')) {
    fwrite(STDERR, "GD with WebP support is required. Run with: php -d extension=gd ...\n");
    exit(1);
}
$root   = dirname(__DIR__);
$pages  = array_slice($argv, 1) ?: ['about.php'];
$widths = [480, 960, 1600];
$quality = 74;
$outDir = "$root/assets/img/sized";
if (!is_dir($outDir)) mkdir($outDir, 0755, true);

// Collect every assets/img/... photo referenced by the given pages
$paths = [];
foreach ($pages as $page) {
    preg_match_all('~assets/img/[A-Za-z0-9_./-]+\.(?:jpe?g|png|webp)~i', file_get_contents("$root/$page"), $m);
    foreach ($m[0] as $p) if (strpos($p, '/sized/') === false && strpos($p, '/partners/') === false) $paths[$p] = true;
}

$before = $after = 0;
foreach (array_keys($paths) as $p) {
    $file = "$root/$p";
    if (!is_file($file)) { echo "missing: $p\n"; continue; }
    [$w, $h, $type] = getimagesize($file);
    $src = match ($type) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($file),
        IMAGETYPE_PNG  => imagecreatefrompng($file),
        IMAGETYPE_WEBP => imagecreatefromwebp($file),
        default        => null,
    };
    if (!$src) { echo "skip (type): $p\n"; continue; }
    $slug = preg_replace('~\.[a-z0-9]+$~i', '', str_replace('/', '__', preg_replace('~^assets/img/~', '', $p)));
    $before += filesize($file);
    foreach ($widths as $tw) {
        if ($tw > $w && $tw !== $widths[0]) continue;   // never upscale (always keep the smallest size)
        $nw = min($tw, $w);
        $nh = (int) round($h * $nw / $w);
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false); imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        $out = "$outDir/$slug-$tw.webp";
        imagewebp($dst, $out, $quality);
        imagedestroy($dst);
        if ($tw === 960 || ($tw === 480 && $w < 960)) $after += filesize($out);
    }
    imagedestroy($src);
    printf("%-70s %5dx%-5d done\n", $p, $w, $h);
}
printf("\n%d photos. Originals: %.1f MB. Typical 960px set: %.1f MB\n", count($paths), $before / 1048576, $after / 1048576);
