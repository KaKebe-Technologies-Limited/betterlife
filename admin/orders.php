<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/invoice.php';
require_once __DIR__ . '/../includes/mailer.php';
$activeNav = 'orders';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/orders.php');
    }
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'mark_paid') {
        $pdo->prepare("UPDATE orders SET status='paid', paid_at = COALESCE(paid_at, NOW()) WHERE id = ?")->execute([$orderId]);
        flash_set('success', 'Order marked as paid.');
    } elseif ($postAction === 'mark_cancelled') {
        $pdo->prepare("UPDATE orders SET status='cancelled' WHERE id = ?")->execute([$orderId]);
        flash_set('success', 'Order cancelled.');
    } elseif ($postAction === 'resend_receipt') {
        $o = $pdo->prepare("SELECT * FROM orders WHERE id = ?"); $o->execute([$orderId]); $order = $o->fetch();
        $it = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?"); $it->execute([$orderId]); $items = $it->fetchAll();
        if ($order && send_receipt_to_customer($pdo, $order, $items)) {
            $pdo->prepare("UPDATE orders SET receipt_sent = 1 WHERE id = ?")->execute([$orderId]);
            flash_set('success', 'Receipt email resent to ' . $order['customer_email'] . '.');
        } else {
            flash_set('error', 'Could not send email. Check SMTP settings in Site Settings → Payments & Email.');
        }
    } elseif ($postAction === 'delete') {
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
        flash_set('success', 'Order deleted.');
    }
    redirect(ADMIN_URL . '/orders.php' . ($postAction === 'delete' ? '' : '?view=' . $orderId));
}

$viewId = (int) ($_GET['view'] ?? 0);
if ($viewId) {
    $pageTitle = 'Order Detail';
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$viewId]);
    $order = $stmt->fetch();
    if (!$order) { flash_set('error', 'Order not found.'); redirect(ADMIN_URL . '/orders.php'); }
    $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $items->execute([$viewId]);
    $items = $items->fetchAll();

    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
      <div class="panel-head">
        <h3>Order <?= h($order['order_ref']) ?></h3>
        <a href="<?= ADMIN_URL ?>/orders.php" class="btn btn-outline btn-sm">← Back to Orders</a>
      </div>
      <div class="panel-body" style="display:flex;gap:14px;flex-wrap:wrap;">
        <?php if ($order['status'] !== 'paid'): ?>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="mark_paid"><input type="hidden" name="order_id" value="<?= $order['id'] ?>"><button type="submit" class="btn btn-primary">Mark as Paid</button></form>
        <?php endif; ?>
        <?php if (!in_array($order['status'], ['cancelled', 'paid'], true)): ?>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="mark_cancelled"><input type="hidden" name="order_id" value="<?= $order['id'] ?>"><button type="submit" class="btn btn-outline">Cancel Order</button></form>
        <?php endif; ?>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="resend_receipt"><input type="hidden" name="order_id" value="<?= $order['id'] ?>"><button type="submit" class="btn btn-accent">Resend Receipt Email</button></form>
        <a href="<?= SITE_URL ?>/order-confirmation.php?ref=<?= urlencode($order['order_ref']) ?>" target="_blank" class="btn btn-outline">View Public Invoice</a>
        <form method="post" style="margin-left:auto;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="order_id" value="<?= $order['id'] ?>"><button type="submit" class="btn btn-danger" data-confirm="Delete this order permanently?">Delete Order</button></form>
      </div>
    </div>

    <div style="background:#f4f6f5;padding:30px;border-radius:14px;">
      <?= render_invoice_html($pdo, $order, $items) ?>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Orders';
$filterStatus = $_GET['status'] ?? '';
$sql = "SELECT * FROM orders";
$params = [];
if (in_array($filterStatus, ['pending', 'paid', 'failed', 'cancelled'], true)) {
    $sql .= " WHERE status = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Orders (<?= count($orders) ?>)</h3>
    <div class="filter-bar">
      <select onchange="location.href=this.value">
        <option value="<?= ADMIN_URL ?>/orders.php" <?= $filterStatus === '' ? 'selected' : '' ?>>All Status</option>
        <option value="<?= ADMIN_URL ?>/orders.php?status=pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="<?= ADMIN_URL ?>/orders.php?status=paid" <?= $filterStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
        <option value="<?= ADMIN_URL ?>/orders.php?status=failed" <?= $filterStatus === 'failed' ? 'selected' : '' ?>>Failed</option>
        <option value="<?= ADMIN_URL ?>/orders.php?status=cancelled" <?= $filterStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
    </div>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Order Ref</th><th>Customer</th><th>Location</th><th>Total</th><th>Status</th><th>Placed</th><th></th></tr></thead>
      <tbody>
        <?php if (!$orders): ?><tr class="empty-row"><td colspan="7">No orders yet.</td></tr><?php endif; ?>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><strong><?= h($o['order_ref']) ?></strong></td>
            <td><?= h($o['customer_name']) ?><br><span class="help-text"><?= h($o['customer_email']) ?></span></td>
            <td><?= h($o['delivery_location']) ?></td>
            <td><?= format_price($o['total_amount']) ?></td>
            <td><span class="badge <?= $o['status'] === 'paid' ? 'badge-green' : ($o['status'] === 'failed' || $o['status'] === 'cancelled' ? 'badge-red' : 'badge-warn') ?>"><?= h(ucfirst($o['status'])) ?></span></td>
            <td><span class="help-text"><?= time_ago($o['created_at']) ?></span></td>
            <td><a href="<?= ADMIN_URL ?>/orders.php?view=<?= $o['id'] ?>" class="btn btn-outline btn-sm">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
