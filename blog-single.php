<?php
require_once __DIR__ . '/includes/functions.php';
$activePage = 'blog';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT bp.*, bc.name AS cat_name, bc.slug AS cat_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE bp.slug = ? AND bp.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Article Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="container-narrow" style="padding:100px 24px;text-align:center;"><h1>Article Not Found</h1><p class="muted">This story may have been removed.</p><a href="' . SITE_URL . '/blog.php" class="btn btn-primary">Back to Stories</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

$pageTitle = $post['title'];
$pageDescription = excerpt($post['excerpt'] ?: $post['content'], 160);
$pageImage = $post['featured_image'];
$ogType = 'article';

$related = $pdo->prepare("SELECT * FROM blog_posts WHERE status = 'published' AND category_id <=> ? AND id != ? ORDER BY published_at DESC LIMIT 3");
$related->execute([$post['category_id'], $post['id']]);
$related = $related->fetchAll();

$recent = $pdo->prepare("SELECT title, slug, featured_image, published_at FROM blog_posts WHERE status='published' AND id != ? ORDER BY published_at DESC LIMIT 4");
$recent->execute([$post['id']]);
$recent = $recent->fetchAll();

$categories = $pdo->query("SELECT bc.*, COUNT(bp.id) AS cnt FROM blog_categories bc LEFT JOIN blog_posts bp ON bp.category_id = bc.id AND bp.status='published' GROUP BY bc.id ORDER BY bc.name")->fetchAll();

$postUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span><a href="<?= SITE_URL ?>/blog.php">Stories</a><span>/</span><?= h(excerpt($post['title'], 40)) ?></div>
  </div>
</section>

<section class="post-single">
  <div class="container">
    <div class="blog-layout">
      <article>
        <span class="cat-badge"><?= h($post['cat_name'] ?? 'General') ?></span>
        <h1 class="post-title"><?= h($post['title']) ?></h1>
        <div class="post-meta">
          <span><?= icon('user', 14) ?> <?= h($post['author']) ?></span>
          <span><?= icon('calendar', 14) ?> <?= format_date($post['published_at']) ?></span>
          <span><?= icon('eye', 14) ?> <?= (int) $post['views'] ?> views</span>
        </div>
        <div class="featured-image"><img src="<?= asset_url($post['featured_image']) ?>" alt="<?= h($post['title']) ?>"></div>
        <div class="post-content"><?= $post['content'] ?></div>

        <div class="share-box">
          <strong><?= icon('share', 15) ?> Share this story:</strong>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener" aria-label="Share on Facebook"><?= icon('facebook', 16) ?></a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode($postUrl) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" rel="noopener" aria-label="Share on X"><?= icon('x-twitter', 16) ?></a>
          <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . $postUrl) ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><?= icon('whatsapp', 16) ?></a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><?= icon('linkedin', 16) ?></a>
          <a href="mailto:?subject=<?= urlencode($post['title']) ?>&body=<?= urlencode($postUrl) ?>" aria-label="Share by email"><?= icon('mail', 16) ?></a>
        </div>

        <?php if ($related): ?>
          <div style="margin-top:60px;">
            <h3 style="margin-bottom:24px;">Related Stories</h3>
            <div class="grid grid-3">
              <?php foreach ($related as $r): ?>
                <div class="card post-card">
                  <div class="thumb"><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($r['slug']) ?>"><img src="<?= asset_url($r['featured_image']) ?>" alt="<?= h($r['title']) ?>"></a></div>
                  <div class="body">
                    <h3 style="font-size:16px;"><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($r['slug']) ?>"><?= h($r['title']) ?></a></h3>
                    <div class="meta"><span><?= icon('calendar', 14) ?> <?= format_date($r['published_at']) ?></span></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </article>

      <aside>
        <div class="sidebar-widget">
          <h4>Search</h4>
          <form class="search-box" method="get" action="<?= SITE_URL ?>/blog.php">
            <input type="text" name="q" placeholder="Search articles…">
            <button type="submit"><?= icon('search', 16) ?></button>
          </form>
        </div>
        <div class="sidebar-widget">
          <h4>Categories</h4>
          <ul class="cat-list">
            <?php foreach ($categories as $c): ?>
              <li><a href="<?= SITE_URL ?>/blog.php?category=<?= h($c['slug']) ?>"><?= h($c['name']) ?> <span class="count">(<?= (int)$c['cnt'] ?>)</span></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="sidebar-widget">
          <h4>More Stories</h4>
          <?php foreach ($recent as $r): ?>
            <a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($r['slug']) ?>" class="sidebar-post">
              <img src="<?= asset_url($r['featured_image']) ?>" alt="<?= h($r['title']) ?>">
              <div>
                <div class="t"><?= h(excerpt($r['title'], 50)) ?></div>
                <div class="d"><?= format_date($r['published_at']) ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
