<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/invoice.php';
$pageTitle = 'Order Confirmation';
$activePage = 'products';

$ref = $_GET['ref'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_ref = ?");
$stmt->execute([$ref]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    require __DIR__ . '/includes/header.php';
    echo '<section class="container-narrow" style="padding:100px 24px;text-align:center;"><h1>Order Not Found</h1><p class="muted">We could not find that order.</p><a href="' . SITE_URL . '/products.php" class="btn btn-primary">Back to Farm Shop</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items->execute([$order['id']]);
$items = $items->fetchAll();

$pageStyles  = ['assets/css/about.css', 'assets/css/shop.css'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';
$paid = $order['status'] === 'paid';
$failed = $order['status'] === 'failed';

require __DIR__ . '/includes/header.php';
?>

<main class="ab sh" id="top">
  <section class="sh-flow" aria-labelledby="shDoneTitle">
    <div class="container">
      <ol class="sh-progress" aria-label="Your order">
        <li class="is-done"><span><?= icon('check', 13) ?></span> Basket</li>
        <li class="is-done"><span><?= icon('check', 13) ?></span> Your details</li>
        <li<?= $paid ? ' class="is-done"' : ' aria-current="step"' ?>><span><?= $paid ? icon('check', 13) : '3' ?></span> Payment</li>
      </ol>
      <div class="sh-done<?= $failed ? ' is-failed' : '' ?>" role="status">
        <span class="sh-done-icon" aria-hidden="true"><?= icon($failed ? 'x' : 'check', 32) ?></span>
        <div>
          <h1 id="shDoneTitle"><?= $paid ? 'Thank you. Your order is paid.' : ($failed ? 'Your payment did not go through' : 'Thank you. Your order is in.') ?></h1>
          <?php if ($paid): ?>
            <p>A receipt is on its way to <?= h($order['customer_email']) ?>. Our team will call you to arrange delivery. Order <?= h($order['order_ref']) ?>.</p>
          <?php elseif ($failed): ?>
            <p>The payment was not completed. You can try again, or contact us and we will help you complete order <?= h($order['order_ref']) ?>.</p>
          <?php else: ?>
            <p>We are waiting for the payment to be confirmed. Order <?= h($order['order_ref']) ?>.</p>
          <?php endif; ?>
        </div>
      </div>

      <?= render_invoice_html($pdo, $order, $items) ?>

      <div class="sh-done-actions">
        <button type="button" onclick="window.print()" class="sh-btn is-quiet"><?= icon('file-text', 16) ?> Print or save as PDF</button>
        <?php if ($failed): ?><a class="sh-btn is-quiet" href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Product order') ?>#write">Contact us</a><?php endif; ?>
        <a class="sh-btn" href="<?= SITE_URL ?>/products.php#products">Back to the farm shop <?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
