<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Blog';
$activePage = 'blog';

$categorySlug = $_GET['category'] ?? '';
$search = trim($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 6;

$where = ["bp.status = 'published'"];
$params = [];

if ($categorySlug) {
    $where[] = 'bc.slug = ?';
    $params[] = $categorySlug;
}
if ($search !== '') {
    $where[] = '(bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
$whereSql = implode(' AND ', $where);

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE $whereSql");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pg = paginate($total, $page, $perPage);

$sql = "SELECT bp.*, bc.name AS cat_name, bc.slug AS cat_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE $whereSql ORDER BY bp.published_at DESC LIMIT $perPage OFFSET {$pg['offset']}";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

$featured = null;
if ($page === 1 && $categorySlug === '' && $search === '' && $posts) {
    $featured = array_shift($posts);
}

$categories = $pdo->query("SELECT bc.*, COUNT(bp.id) AS cnt FROM blog_categories bc LEFT JOIN blog_posts bp ON bp.category_id = bc.id AND bp.status='published' GROUP BY bc.id ORDER BY bc.name")->fetchAll();
$recent = $pdo->query("SELECT title, slug, featured_image, published_at FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 4")->fetchAll();

require __DIR__ . '/includes/header.php';

function qs(array $overrides = []) {
    $params = array_merge($_GET, $overrides);
    return '?' . http_build_query($params);
}
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Blog</div>
    <h1>Stories, Insights &amp; Impact</h1>
    <p style="max-width:640px;color:#e2f0e9;">News and analysis from our work across climate, agriculture and community empowerment in Sub-Saharan Africa.</p>
  </div>
</section>

<section>
  <div class="container">

    <?php if ($featured): ?>
      <div class="blog-featured fade-up">
        <div class="img"><img src="<?= asset_url($featured['featured_image']) ?>" alt="<?= h($featured['title']) ?>"></div>
        <div class="content">
          <span class="cat-badge"><?= h($featured['cat_name'] ?? 'General') ?></span>
          <h2 style="font-size:28px;"><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($featured['slug']) ?>"><?= h($featured['title']) ?></a></h2>
          <p class="muted"><?= h(excerpt($featured['excerpt'] ?: $featured['content'], 180)) ?></p>
          <div class="post-card meta" style="margin-bottom:18px;">
            <span>✍️ <?= h($featured['author']) ?></span>
            <span>🗓 <?= format_date($featured['published_at']) ?></span>
          </div>
          <a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($featured['slug']) ?>" class="btn btn-primary btn-sm">Read Full Story →</a>
        </div>
      </div>
    <?php endif; ?>

    <div class="blog-layout">
      <div>
        <?php if ($posts): ?>
          <div class="grid grid-2">
            <?php foreach ($posts as $post): ?>
              <div class="card post-card fade-up">
                <div class="thumb"><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>"><img src="<?= asset_url($post['featured_image']) ?>" alt="<?= h($post['title']) ?>"></a></div>
                <div class="body">
                  <span class="cat-badge"><?= h($post['cat_name'] ?? 'General') ?></span>
                  <h3><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>"><?= h($post['title']) ?></a></h3>
                  <div class="meta"><span>🗓 <?= format_date($post['published_at']) ?></span><span>👁 <?= (int)$post['views'] ?> views</span></div>
                  <p class="excerpt"><?= h(excerpt($post['excerpt'] ?: $post['content'], 110)) ?></p>
                  <a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>" class="readmore">Read Story →</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state">No articles found<?= $search ? ' for "' . h($search) . '"' : '' ?>. Try a different search or category.</div>
        <?php endif; ?>

        <?php if ($pg['pages'] > 1): ?>
          <div class="pagination">
            <?php for ($i = 1; $i <= $pg['pages']; $i++): ?>
              <?php if ($i === $pg['page']): ?>
                <span class="current"><?= $i ?></span>
              <?php else: ?>
                <a href="<?= qs(['page' => $i]) ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      </div>

      <aside>
        <div class="sidebar-widget">
          <h4>Search</h4>
          <form class="search-box" method="get" action="<?= SITE_URL ?>/blog.php">
            <input type="text" name="q" placeholder="Search articles…" value="<?= h($search) ?>">
            <button type="submit">🔍</button>
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
          <h4>Recent Posts</h4>
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

        <div class="sidebar-widget" style="background:var(--green-900);color:#fff;">
          <h4 style="color:#fff;border-color:rgba(255,255,255,.2);">Subscribe</h4>
          <p style="font-size:13px;color:#cfe3d8;">Get our latest stories straight to your inbox.</p>
          <form action="<?= SITE_URL ?>/newsletter-submit.php" method="post">
            <input type="email" name="email" class="form-control" placeholder="Your email" required style="margin-bottom:10px;">
            <button type="submit" class="btn btn-accent btn-block">Subscribe</button>
          </form>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
