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

$farmGallery = [
    ['img' => 'assets/img/betterlifeint-source/projects/project-agro-tourism-alt.jpeg', 'cap' => 'Solar-powered irrigation'],
    ['img' => 'assets/img/betterlifeint-source/programs/program-photo-1.jpg', 'cap' => 'Refugees training on the farm'],
    ['img' => 'assets/img/betterlifeint-source/programs/program-photo-2.jpg', 'cap' => 'Greenhouse farming'],
    ['img' => 'assets/img/betterlifeint-source/programs/program-photo-3.jpg', 'cap' => 'Community beekeeping'],
    ['img' => 'assets/img/betterlifeint-source/programs/program-photo-4.jpg', 'cap' => 'Livestock rearing'],
    ['img' => 'assets/img/betterlifeint-source/programs/program-photo-5.jpg', 'cap' => 'Free seedlings for new gardens'],
];

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>BetterLife Farm</div>
    <h1><?= h(setting($pdo, 'farm_title')) ?></h1>
    <p style="max-width:640px;color:#e2f0e9;font-style:italic;"><?= h(setting($pdo, 'farm_tagline', 'From immediate support to lasting independence')) ?></p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split" style="margin-bottom:60px;">
      <div class="fade-up img-frame"><img src="<?= asset_url(setting($pdo,'farm_image')) ?>" alt="BetterLife Agro Tourism Farm"></div>
      <div class="fade-up">
        <span class="eyebrow">Our Strategy: Food Security</span>
        <h2><?= h(setting($pdo, 'farm_tagline', 'From immediate support to lasting independence')) ?></h2>
        <div class="muted"><?= nl2p(setting($pdo, 'farm_text')) ?></div>
      </div>
    </div>

    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">On The Farm</span>
      <h2>Clean Energy, Real Livelihoods</h2>
      <p class="muted">A glimpse of the people and practices behind BetterLife Agro Tourism Farm Ltd.</p>
    </div>
    <div class="impact-photos" style="margin-bottom:80px;">
      <?php foreach ($farmGallery as $g): ?>
        <div class="impact-photo fade-up"><img src="<?= asset_url($g['img']) ?>" alt="<?= h($g['cap']) ?>"><span class="cap"><?= h($g['cap']) ?></span></div>
      <?php endforeach; ?>
    </div>

    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Our Products</span>
      <h2>Pure Honey, Ghee &amp; Vanilla Yoghurt — Made with Care</h2>
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
              <div class="price"><?= format_price($p['price']) ?> <small>/ <?= h($p['unit']) ?></small></div>
              <div class="row">
                <form method="post" action="<?= SITE_URL ?>/cart-add.php" style="flex:1;">
                  <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="qty" value="1">
                  <button type="submit" class="btn btn-primary btn-sm ico-text" style="width:100%;justify-content:center;"><?= icon('shopping-bag', 15) ?> Add to Cart</button>
                </form>
                <a href="<?= SITE_URL ?>/product.php?slug=<?= h($p['slug']) ?>" class="btn btn-outline-dark btn-sm">View</a>
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
        <h3>Want to order in bulk, become a stockist, or visit the farm?</h3>
        <p>Reach out and our farm team will get back to you with pricing, availability and agro-tourism visits.</p>
      </div>
      <a href="<?= SITE_URL ?>/contact.php?subject=Farm+Product+Enquiry" class="btn btn-white">Enquire Now →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
