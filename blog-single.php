<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$activePage = 'blog';

$slug = (string) ($_GET['slug'] ?? '');
$stmt = $pdo->prepare("SELECT bp.*, bc.name AS cat_name, bc.slug AS cat_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE bp.slug = ? AND bp.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Story Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="container-narrow" style="padding:100px 24px;text-align:center;"><h1>Story not found</h1><p class="muted">This story may have moved.</p><a href="' . SITE_URL . '/blog.php" class="btn btn-primary">Back to Stories</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

$pageTitle = $post['title'];
$pageDescription = excerpt($post['excerpt'] ?: $post['content'], 160);
$pageImage = $post['featured_image'];
$ogType = 'article';

// More stories: the same topic first, then the newest of the rest
$more = $pdo->prepare("SELECT bp.*, bc.name AS cat_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE bp.status = 'published' AND bp.id != ? ORDER BY (bp.category_id <=> ?) DESC, bp.published_at DESC LIMIT 3");
$more->execute([$post['id'], $post['category_id']]);
$more = $more->fetchAll();

$minutes = fn(array $p): int => max(1, (int) round(str_word_count(strip_tags((string) $p['content'])) / 200));
$storyUrl = fn(array $p): string => SITE_URL . '/blog-single.php?slug=' . rawurlencode($p['slug']);
$postUrl = SITE_URL . '/blog-single.php?slug=' . rawurlencode($post['slug']);
if (!preg_match('#^https?://#', $postUrl)) $postUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $postUrl;

$pageStyles  = ['assets/css/about.css', 'assets/css/stories.css'];
$pageScripts = ['assets/js/about.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab st st-read" id="top">
  <?= ab_brush_defs() ?>

  <article aria-labelledby="stPostTitle">
    <header class="st-post-head">
      <div class="container st-post-head-inner">
        <nav class="st-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/blog.php">Stories</a><span aria-hidden="true">/</span><span aria-current="page"><?= h(excerpt($post['title'], 48)) ?></span></nav>
        <?php if (!empty($post['cat_slug'])): ?><a class="st-post-cat" href="<?= SITE_URL ?>/blog.php?category=<?= h(rawurlencode($post['cat_slug'])) ?>#stories"><?= h($post['cat_name']) ?></a><?php endif; ?>
        <h1 id="stPostTitle"><?= h($post['title']) ?></h1>
        <p class="st-post-meta"><span><?= h($post['author'] && $post['author'] !== 'Admin' ? $post['author'] : 'BetterLife International') ?></span><span><?= h(format_date($post['published_at'], 'j F Y')) ?></span><span><?= $minutes($post) ?> min read</span></p>
      </div>
    </header>

    <?php if (!empty($post['featured_image'])): ?>
      <div class="container st-post-figure"><?= ab_img($post['featured_image'], '', '', false, '', '(max-width: 1000px) 100vw, 1000px') ?></div>
    <?php endif; ?>

    <div class="container st-post-body">
      <div class="st-prose"><?= $post['content'] ?></div>

      <aside class="st-share" aria-label="Share this story">
        <p>Share this story</p>
        <div>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener" aria-label="Share on Facebook (opens in a new tab)"><?= icon('facebook', 17) ?></a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode($postUrl) ?>&amp;text=<?= urlencode($post['title']) ?>" target="_blank" rel="noopener" aria-label="Share on X (opens in a new tab)"><?= icon('x-twitter', 17) ?></a>
          <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . $postUrl) ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp (opens in a new tab)"><?= icon('whatsapp', 17) ?></a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn (opens in a new tab)"><?= icon('linkedin', 17) ?></a>
          <a href="mailto:?subject=<?= rawurlencode($post['title']) ?>&amp;body=<?= rawurlencode($postUrl) ?>" aria-label="Share by email"><?= icon('mail', 17) ?></a>
        </div>
      </aside>
    </div>
  </article>

  <?php if ($more): ?>
    <section class="st-more-stories" aria-labelledby="stMoreTitle">
      <div class="container">
        <div class="st-head-row ab-reveal">
          <div class="ab-head">
            <span class="ab-eyebrow">Keep reading</span>
            <h2 id="stMoreTitle">More stories</h2>
          </div>
          <a class="st-all" href="<?= SITE_URL ?>/blog.php#stories">All stories <?= icon('arrow-right', 15) ?></a>
        </div>
        <div class="st-grid">
          <?php foreach ($more as $p): ?>
            <article class="st-card ab-reveal">
              <div class="st-card-media"><?= ab_img($p['featured_image'], '', '', true, '', '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 380px') ?></div>
              <div class="st-card-body">
                <span class="st-tag"><?= h($p['cat_name'] ?? 'Stories') ?></span>
                <h3><a href="<?= h($storyUrl($p)) ?>"><?= h($p['title']) ?></a></h3>
                <p><?= h(excerpt($p['excerpt'] ?: $p['content'], 140)) ?></p>
                <p class="st-meta"><?= h(format_date($p['published_at'], 'j F Y')) ?> · <?= $minutes($p) ?> min read</p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
