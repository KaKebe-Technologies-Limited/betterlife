<?php
require_once __DIR__ . '/includes/functions.php';
$activePage = 'products';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="container-narrow" style="padding:100px 24px;text-align:center;"><h1>Product Not Found</h1><p class="muted">This product may have been removed.</p><a href="' . SITE_URL . '/products.php" class="btn btn-primary">Back to Farm Shop</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $product['name'];

$related = $pdo->prepare("SELECT * FROM products WHERE status = 1 AND category = ? AND id != ? ORDER BY sort_order LIMIT 3");
$related->execute([$product['category'], $product['id']]);
$related = $related->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span><a href="<?= SITE_URL ?>/products.php">BetterLife Farm</a><span>/</span><?= h($product['name']) ?></div>
    <h1><?= h($product['name']) ?></h1>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame">
        <img src="<?= asset_url($product['image']) ?>" alt="<?= h($product['name']) ?>">
      </div>
      <div class="fade-up">
        <span class="cat-badge"><?= h($product['category']) ?></span>
        <h2 style="margin-top:14px;"><?= h($product['name']) ?></h2>
        <div class="price" style="font-size:26px;margin:14px 0;"><?= format_price($product['price']) ?> <small>/ <?= h($product['unit']) ?></small></div>
        <p class="muted"><?= h($product['short_desc']) ?></p>
        <div style="margin:26px 0;"><?= nl2p($product['description']) ?></div>
        <form method="post" action="<?= SITE_URL ?>/cart-add.php" style="display:flex;gap:14px;flex-wrap:wrap;align-items:center;">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <div class="qty-stepper">
            <button type="button" class="qty-btn" data-step="-1">−</button>
            <input type="number" name="qty" value="1" min="1" class="form-control">
            <button type="button" class="qty-btn" data-step="1">+</button>
          </div>
          <button type="submit" class="btn btn-primary"><?= icon('shopping-bag', 16) ?> Add to Cart</button>
          <a href="<?= SITE_URL ?>/products.php" class="btn btn-outline-dark">← Back to Farm Shop</a>
        </form>
      </div>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">You Might Also Like</span>
      <h2>More from <?= h($product['category']) ?></h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($related as $r): ?>
        <div class="card product-card fade-up">
          <div class="thumb"><a href="<?= SITE_URL ?>/product.php?slug=<?= h($r['slug']) ?>"><img src="<?= asset_url($r['image']) ?>" alt="<?= h($r['name']) ?>"></a><span class="badge-cat"><?= h($r['category']) ?></span></div>
          <div class="body">
            <h3><a href="<?= SITE_URL ?>/product.php?slug=<?= h($r['slug']) ?>"><?= h($r['name']) ?></a></h3>
            <div class="price"><?= format_price($r['price']) ?> <small>/ <?= h($r['unit']) ?></small></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
