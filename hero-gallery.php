<?php
/**
 * Public JSON endpoint for the homepage hero artwork: returns the list of
 * photos that rotate inside the Africa-shaped collage. Managed from
 * Admin -> Page Content -> Hero Africa Photo Gallery (page "home",
 * section "hero_gallery"), fetched client-side by assets/js/main.js.
 */
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

$items = content_items($pdo, 'home', 'hero_gallery');
$photos = array_map(fn($row) => [
    'src' => asset_url($row['image']),
    'alt' => $row['title'],
], array_filter($items, fn($row) => !empty($row['image'])));

echo json_encode(array_values($photos));
