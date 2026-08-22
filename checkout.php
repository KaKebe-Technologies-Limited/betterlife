<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/pesapal.php';
require_once __DIR__ . '/includes/mailer.php';
$pageTitle = 'Checkout';
$activePage = 'products';

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    redirect(SITE_URL . '/cart.php');
}

$subtotal = 0;
foreach ($cart as $item) { $subtotal += $item['price'] * $item['qty']; }

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $location === '') {
            $error = 'Please fill in your name, a valid email, phone number and delivery location.';
        } else {
            try {
                $pdo->beginTransaction();

                $orderRef = generate_order_ref();
                $pdo->prepare("INSERT INTO orders (order_ref, customer_name, customer_email, customer_phone, delivery_location, notes, subtotal, total_amount, currency, status) VALUES (?,?,?,?,?,?,?,?,?, 'pending')")
                    ->execute([$orderRef, $name, $email, $phone, $location, $notes, $subtotal, $subtotal, 'UGX']);
                $orderId = (int) $pdo->lastInsertId();

                $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total) VALUES (?,?,?,?,?,?)");
                foreach ($cart as $item) {
                    $itemStmt->execute([$orderId, $item['product_id'], $item['name'], $item['price'], $item['qty'], $item['price'] * $item['qty']]);
                }

                $pdo->commit();

                $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
                $orderStmt->execute([$orderId]);
                $order = $orderStmt->fetch();
                $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $items->execute([$orderId]);
                $items = $items->fetchAll();

                // Notify (best-effort — never block checkout on email failure)
                try { send_order_confirmation_to_customer($pdo, $order, $items); } catch (Throwable $e) { error_log($e->getMessage()); }
                try { send_order_alert_to_admin($pdo, $order, $items); } catch (Throwable $e) { error_log($e->getMessage()); }

                $redirectUrl = pesapal_initiate_order_payment($pdo, $order);
                $_SESSION['cart'] = [];
                redirect($redirectUrl);
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                error_log('Checkout failed: ' . $e->getMessage());
                $error = 'We could not start your payment right now. Please try again in a moment, or contact us directly.';
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span><a href="<?= SITE_URL ?>/cart.php">Cart</a><span>/</span>Checkout</div>
    <h1>Checkout</h1>
  </div>
</section>

<section>
  <div class="container">
    <div class="split" style="align-items:flex-start;">
      <div class="contact-card fade-up">
        <h3>Delivery &amp; Contact Details</h3>
        <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="grid grid-2" style="gap:18px;">
            <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" required value="<?= h($_POST['name'] ?? '') ?>"></div>
            <div class="form-group"><label>Email Address *</label><input type="email" name="email" class="form-control" required value="<?= h($_POST['email'] ?? '') ?>"></div>
          </div>
          <div class="grid grid-2" style="gap:18px;">
            <div class="form-group"><label>Phone Number *</label><input type="text" name="phone" class="form-control" required value="<?= h($_POST['phone'] ?? '') ?>" placeholder="e.g. 0700 000 000"></div>
            <div class="form-group"><label>Delivery Location *</label><input type="text" name="location" class="form-control" required value="<?= h($_POST['location'] ?? '') ?>" placeholder="District / town / address"></div>
          </div>
          <div class="form-group"><label>Order Notes</label><textarea name="notes" class="form-control" placeholder="Preferred delivery time, landmark, special instructions…"><?= h($_POST['notes'] ?? '') ?></textarea></div>
          <button type="submit" class="btn btn-primary btn-block">Continue to Payment (Card / Mobile Money) <?= icon('arrow-right', 16) ?></button>
          <p class="hint" style="margin-top:12px;">You'll be redirected to our secure payment page to complete payment by card or mobile money.</p>
        </form>
      </div>

      <div class="cart-summary fade-up" style="max-width:none;">
        <h4 style="margin-bottom:16px;">Order Summary</h4>
        <?php foreach ($cart as $item): ?>
          <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:10px;">
            <span><?= h($item['name']) ?> × <?= (int) $item['qty'] ?></span>
            <strong><?= format_price($item['price'] * $item['qty']) ?></strong>
          </div>
        <?php endforeach; ?>
        <div class="cart-summary-row" style="border-top:1px solid var(--border);padding-top:14px;margin-top:10px;">
          <span>Total</span><strong><?= format_price($subtotal) ?></strong>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
