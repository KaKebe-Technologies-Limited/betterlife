<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/shop.php';
$activePage = 'products';

$slug = (string) ($_GET['slug'] ?? '');
$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

// Product addresses before the October 2026 renaming, and the two placeholder products, go to the current product
$renamed = [
    'pure-wild-honey' => 'betterlife-honey', 'golden-comb-honey' => 'betterlife-honey',
    'traditional-ghee' => 'betterlife-ghee', 'cultured-butter-ghee' => 'betterlife-ghee',
    'natural-set-yoghurt' => 'betterlife-yoghurt-vanilla', 'fruit-infused-yoghurt' => 'betterlife-yoghurt-strawberry',
];
if (!$product && isset($renamed[$slug])) {
    header('Location: ' . SITE_URL . '/product.php?slug=' . $renamed[$slug], true, 301);
    exit;
}

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="container-narrow" style="padding:100px 24px;text-align:center;"><h1>Product not found</h1><p class="muted">This product may have moved.</p><a href="' . SITE_URL . '/products.php" class="btn btn-primary">Back to the farm shop</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $product['name'];
$pageDescription = excerpt($product['short_desc'] ?: $product['description'], 160);
$pageImage = $product['image'];
$ogType = 'product';

// More from the farm: the same kind first, then the rest
$more = $pdo->prepare("SELECT * FROM products WHERE status = 1 AND id != ? ORDER BY (category = ?) DESC, sort_order LIMIT 3");
$more->execute([$product['id'], $product['category']]);
$more = $more->fetchAll();

$usd = format_usd($pdo, (float) $product['price']);
$flash = flash_get();

$pageStyles  = ['assets/css/about.css', 'assets/css/shop.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/shop.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab sh" id="top">
  <?= ab_brush_defs() ?>

  <section class="sh-item" aria-labelledby="shItemTitle">
    <div class="container">
      <nav class="sh-crumb is-dark" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/products.php">Farm shop</a><span aria-hidden="true">/</span><span aria-current="page"><?= h($product['name']) ?></span></nav>
      <div class="sh-item-grid">
        <figure class="sh-item-media">
          <span class="sh-sun" aria-hidden="true"></span>
          <?= ab_img($product['image'], $product['name'] . ', ' . $product['unit'], '', false, '', '(max-width: 900px) 90vw, 520px') ?>
        </figure>
        <div class="sh-item-copy">
          <span class="sh-card-cat"><?= h($product['category']) ?></span>
          <h1 id="shItemTitle"><?= h($product['name']) ?></h1>
          <p class="sh-price is-large"><strong><?= h(format_price((float) $product['price'])) ?></strong> <span>/ <?= h($product['unit']) ?></span><?php if ($usd): ?><small><?= h($usd) ?></small><?php endif; ?></p>
          <?php if ($flash): ?><p class="sh-flash is-<?= h($flash['type']) ?>" role="status"><?= h($flash['message']) ?></p><?php endif; ?>
          <form class="sh-add is-large" method="post" action="<?= SITE_URL ?>/cart-add.php">
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <?= shop_qty((int) $product['id'], 1) ?>
            <button type="submit" class="sh-add-btn"><?= icon('shopping-bag', 17) ?> <span>Add to basket</span></button>
          </form>
          <div class="sh-item-text"><?= nl2p($product['description']) ?></div>
          <ul class="sh-promises is-light">
            <li><?= icon('leaf', 16) ?> Made at BetterLife Agro Tourism Farm, Rukungiri</li>
            <li><?= icon('phone', 16) ?> Pay by mobile money or card</li>
            <li><?= icon('map-pin', 16) ?> Delivery arranged by our team after you order</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- How the purchase helps, briefly -->
  <section class="sh-why is-compact" aria-labelledby="shWhyTitle">
    <div class="container">
      <div class="sh-head ab-reveal">
        <span class="ab-eyebrow">Why it matters</span>
        <h2 id="shWhyTitle">What your order does</h2>
      </div>
      <ol class="sh-steps">
        <?php foreach (shop_purpose_steps() as $i => [$ico, $title, $text]): ?>
          <li class="ab-reveal"><span class="sh-step-icon" aria-hidden="true"><?= icon($ico, 22) ?></span><span class="sh-step-n" aria-hidden="true"><?= $i + 1 ?></span><strong><?= h($title) ?></strong><span><?= h($text) ?></span></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <?php if ($more): ?>
    <section class="sh-products is-more" aria-labelledby="shMoreTitle">
      <div class="container">
        <div class="sh-head sh-head-row ab-reveal">
          <div>
            <span class="ab-eyebrow">From the farm</span>
            <h2 id="shMoreTitle">You may also like</h2>
          </div>
          <a class="ab-link" href="<?= SITE_URL ?>/products.php#products">All products <?= icon('arrow-right', 15) ?></a>
        </div>
        <div class="sh-grid">
          <?php foreach ($more as $p): ?><?= shop_product_card($pdo, $p) ?><?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?= shop_basket_bar() ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
