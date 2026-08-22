<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'BetterLife Farm';
$activePage = 'products';

$category = $_GET['category'] ?? '';
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE status = 1 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

if ($category && in_array($category, $categories, true)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = 1 AND category = ? ORDER BY featured DESC, sort_order");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY featured DESC, sort_order");
}
$products = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>BetterLife Farm</div>
    <h1><?= h(setting($pdo, 'farm_title')) ?></h1>
    <p style="max-width:640px;color:#e2f0e9;"><?= h(excerpt(setting($pdo, 'farm_text'), 260)) ?></p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split" style="margin-bottom:70px;">
      <div class="fade-up img-frame"><img src="<?= asset_url(setting($pdo,'farm_image')) ?>" alt="BetterLife Farm"></div>
      <div class="fade-up">
        <span class="eyebrow">From Farm to Table</span>
        <h2>Honest food, grown with purpose</h2>
        <div class="muted"><?= nl2p(setting($pdo, 'farm_text')) ?></div>
      </div>
    </div>

    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Our Products</span>
      <h2>Pure Honey, Ghee &amp; Yoghurt — Made with Care</h2>
      <p class="muted">Every purchase supports beekeeping, dairy farming and livelihoods training in the communities we serve.</p>
    </div>

    <div class="pill-tabs" style="justify-content:center;">
      <a href="<?= SITE_URL ?>/products.php" class="<?= $category === '' ? 'active' : '' ?>">All Products</a>
      <?php foreach ($categories as $c): ?>
        <a href="<?= SITE_URL ?>/products.php?category=<?= urlencode($c) ?>" class="<?= $category === $c ? 'active' : '' ?>"><?= h($c) ?></a>
      <?php endforeach; ?>
    </div>

    <?php if ($products): ?>
      <div class="grid grid-3">
        <?php foreach ($products as $p): ?>
          <div class="card product-card fade-up">
            <div class="thumb">
              <a href="<?= SITE_URL ?>/product.php?slug=<?= h($p['slug']) ?>"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['name']) ?>"></a>
              <span class="badge-cat"><?= h($p['category']) ?></span>
              <?php if ($p['featured']): ?><span class="badge-featured">Popular</span><?php endif; ?>
            </div>
            <div class="body">
              <h3><a href="<?= SITE_URL ?>/product.php?slug=<?= h($p['slug']) ?>"><?= h($p['name']) ?></a></h3>
              <p class="muted" style="font-size:14px;"><?= h($p['short_desc']) ?></p>
              <div class="row">
                <div class="price"><?= format_price($p['price']) ?> <small>/ <?= h($p['unit']) ?></small></div>
                <a href="<?= SITE_URL ?>/product.php?slug=<?= h($p['slug']) ?>" class="btn btn-outline-dark btn-sm">View →</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">No products found in this category yet.</div>
    <?php endif; ?>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="cta-banner fade-up" style="background:linear-gradient(120deg,var(--green-700),var(--blue-700));">
      <div>
        <h3>Want to order in bulk or become a stockist?</h3>
        <p>Reach out and our farm team will get back to you with pricing and availability.</p>
      </div>
      <a href="<?= SITE_URL ?>/contact.php?subject=Farm+Product+Enquiry" class="btn btn-white">Enquire Now →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
