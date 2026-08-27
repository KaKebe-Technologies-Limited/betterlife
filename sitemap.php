<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=UTF-8');

$base = public_base_url() . SITE_URL;

$staticPages = [
    ['url' => '/index.php', 'priority' => '1.0', 'freq' => 'weekly'],
    ['url' => '/about.php', 'priority' => '0.8', 'freq' => 'monthly'],
    ['url' => '/programs.php', 'priority' => '0.8', 'freq' => 'monthly'],
    ['url' => '/impact-reports.php', 'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => '/farm.php', 'priority' => '0.8', 'freq' => 'monthly'],
    ['url' => '/products.php', 'priority' => '0.9', 'freq' => 'weekly'],
    ['url' => '/team.php', 'priority' => '0.6', 'freq' => 'monthly'],
    ['url' => '/blog.php', 'priority' => '0.8', 'freq' => 'daily'],
    ['url' => '/contact.php', 'priority' => '0.6', 'freq' => 'yearly'],
];

$products = $pdo->query("SELECT slug, updated_at FROM products WHERE status = 1")->fetchAll();
$posts = $pdo->query("SELECT slug, updated_at FROM blog_posts WHERE status = 'published'")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $p): ?>
  <url>
    <loc><?= h($base . $p['url']) ?></loc>
    <changefreq><?= $p['freq'] ?></changefreq>
    <priority><?= $p['priority'] ?></priority>
  </url>
<?php endforeach; ?>
<?php foreach ($products as $p): ?>
  <url>
    <loc><?= h($base . '/product.php?slug=' . urlencode($p['slug'])) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($p['updated_at'])) ?></lastmod>
    <priority>0.7</priority>
  </url>
<?php endforeach; ?>
<?php foreach ($posts as $p): ?>
  <url>
    <loc><?= h($base . '/blog-single.php?slug=' . urlencode($p['slug'])) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($p['updated_at'])) ?></lastmod>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
</urlset>
