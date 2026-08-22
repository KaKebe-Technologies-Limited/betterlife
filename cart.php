<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Your Cart';
$activePage = 'products';

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $item) { $subtotal += $item['price'] * $item['qty']; }

$flash = flash_get();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span><a href="<?= SITE_URL ?>/products.php">BetterLife Farm</a><span>/</span>Cart</div>
    <h1>Your Cart</h1>
  </div>
</section>

<section>
  <div class="container">
    <?php if ($flash): ?><div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div><?php endif; ?>

    <?php if (!$cart): ?>
      <div class="empty-state">
        <p>Your cart is empty.</p>
        <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary">Browse Farm Products →</a>
      </div>
    <?php else: ?>
      <div class="cart-table">
        <?php foreach ($cart as $productId => $item): ?>
          <div class="cart-row fade-up">
            <img src="<?= asset_url($item['image']) ?>" alt="<?= h($item['name']) ?>">
            <div class="cart-row-info">
              <h4><?= h($item['name']) ?></h4>
              <span class="muted"><?= format_price($item['price']) ?> / <?= h($item['unit']) ?></span>
            </div>
            <form method="post" action="<?= SITE_URL ?>/cart-update.php" class="cart-qty-form">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="product_id" value="<?= $productId ?>">
              <input type="number" name="qty" value="<?= (int) $item['qty'] ?>" min="1" class="form-control">
              <button type="submit" class="btn btn-outline-dark btn-sm">Update</button>
            </form>
            <div class="cart-row-total"><?= format_price($item['price'] * $item['qty']) ?></div>
            <form method="post" action="<?= SITE_URL ?>/cart-update.php">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="product_id" value="<?= $productId ?>">
              <button type="submit" class="cart-remove" aria-label="Remove"><?= icon('x', 18) ?></button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="cart-summary">
        <div class="cart-summary-row"><span>Subtotal</span><strong><?= format_price($subtotal) ?></strong></div>
        <p class="muted" style="font-size:13px;">Delivery is arranged directly with our team after checkout.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;">
          <a href="<?= SITE_URL ?>/products.php" class="btn btn-outline-dark">← Continue Shopping</a>
          <a href="<?= SITE_URL ?>/checkout.php" class="btn btn-primary">Proceed to Checkout →</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
