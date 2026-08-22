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

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Order <?= h($order['order_ref']) ?></div>
    <h1><?= $order['status'] === 'paid' ? 'Payment Successful' : ($order['status'] === 'failed' ? 'Payment Not Completed' : 'Order Received') ?></h1>
  </div>
</section>

<section>
  <div class="container">
    <?php if ($order['status'] === 'paid'): ?>
      <div class="alert alert-success" style="max-width:720px;margin:0 auto 24px;">Thank you! Your payment was received and a receipt has been emailed to <?= h($order['customer_email']) ?>.</div>
    <?php elseif ($order['status'] === 'failed'): ?>
      <div class="alert alert-error" style="max-width:720px;margin:0 auto 24px;">Your payment did not go through. You can try again or contact us for help completing your order.</div>
    <?php else: ?>
      <div class="alert alert-success" style="max-width:720px;margin:0 auto 24px;">Your order has been received and is awaiting payment confirmation.</div>
    <?php endif; ?>

    <?= render_invoice_html($pdo, $order, $items) ?>

    <div style="text-align:center;margin-top:30px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <button onclick="window.print()" class="btn btn-outline-dark"><?= icon('file-text', 16) ?> Print / Save PDF</button>
      <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary">Continue Shopping →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
