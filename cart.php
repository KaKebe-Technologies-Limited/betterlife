<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/shop.php';
$pageTitle = 'Your Basket';
$activePage = 'products';

[$cart, $count, $subtotal] = shop_basket();
$flash = flash_get();
$usd = format_usd($pdo, $subtotal);

$pageStyles  = ['assets/css/about.css', 'assets/css/shop.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/shop.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab sh" id="top">
  <section class="sh-flow" aria-labelledby="shBasketTitle">
    <div class="container">
      <nav class="sh-crumb is-dark" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/products.php">Farm shop</a><span aria-hidden="true">/</span><span aria-current="page">Basket</span></nav>
      <ol class="sh-progress" aria-label="Your order">
        <li aria-current="step"><span>1</span> Basket</li>
        <li><span>2</span> Your details</li>
        <li><span>3</span> Payment</li>
      </ol>
      <h1 id="shBasketTitle">Your basket</h1>

      <?php if ($flash): ?><p class="sh-flash is-<?= h($flash['type']) ?>" role="status"><?= h($flash['message']) ?></p><?php endif; ?>

      <?php if (!$cart): ?>
        <div class="sh-empty-basket">
          <span class="sh-empty-icon" aria-hidden="true"><?= icon('shopping-bag', 34) ?></span>
          <h2>Your basket is empty</h2>
          <p>Honey, ghee, yoghurt and Organic Boost are waiting at the farm shop.</p>
          <a class="sh-btn" href="<?= SITE_URL ?>/products.php#products">Visit the farm shop <?= icon('arrow-right', 16) ?></a>
        </div>
      <?php else: ?>
        <div class="sh-flow-grid">
          <ul class="sh-lines" aria-label="Items in your basket">
            <?php foreach ($cart as $productId => $item): ?>
              <li class="sh-line">
                <span class="sh-line-media"><?= ab_img($item['image'], '', '', true, '', '96px') ?></span>
                <div class="sh-line-info">
                  <strong><?= h($item['name']) ?></strong>
                  <span><?= h(format_price($item['price'])) ?> / <?= h($item['unit']) ?></span>
                </div>
                <form class="sh-line-qty" method="post" action="<?= SITE_URL ?>/cart-update.php" data-autosubmit>
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="product_id" value="<?= (int) $productId ?>">
                  <?= shop_qty((int) $productId, (int) $item['qty']) ?>
                  <button type="submit" class="sh-line-update">Update</button>
                </form>
                <strong class="sh-line-total"><?= h(format_price($item['price'] * $item['qty'])) ?></strong>
                <form method="post" action="<?= SITE_URL ?>/cart-update.php">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="remove">
                  <input type="hidden" name="product_id" value="<?= (int) $productId ?>">
                  <button type="submit" class="sh-line-remove" aria-label="Remove <?= h($item['name']) ?>"><?= icon('x', 18) ?></button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>

          <aside class="sh-summary" aria-labelledby="shSummaryTitle">
            <h2 id="shSummaryTitle">Summary</h2>
            <p class="sh-summary-row"><span><?= $count ?> <?= $count === 1 ? 'item' : 'items' ?></span><strong><?= h(format_price($subtotal)) ?></strong></p>
            <?php if ($usd): ?><p class="sh-summary-note">That is <?= h($usd) ?>. Payment is in Uganda shillings.</p><?php endif; ?>
            <p class="sh-summary-note">Delivery is arranged with our team after you order.</p>
            <a class="sh-btn is-wide" href="<?= SITE_URL ?>/checkout.php">Continue to your details <?= icon('arrow-right', 16) ?></a>
            <a class="sh-summary-back" href="<?= SITE_URL ?>/products.php#products">Keep shopping</a>
            <p class="sh-summary-why"><?= icon('heart', 15) ?> Every order helps keep the farm’s market open for the families who supply it.</p>
          </aside>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
