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

require_once __DIR__ . '/includes/shop.php';
$usd = format_usd($pdo, $subtotal);
$pageStyles  = ['assets/css/about.css', 'assets/css/shop.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/shop.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab sh" id="top">
  <section class="sh-flow" aria-labelledby="shCheckoutTitle">
    <div class="container">
      <nav class="sh-crumb is-dark" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/products.php">Farm shop</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/cart.php">Basket</a><span aria-hidden="true">/</span><span aria-current="page">Your details</span></nav>
      <ol class="sh-progress" aria-label="Your order">
        <li class="is-done"><span><?= icon('check', 13) ?></span> Basket</li>
        <li aria-current="step"><span>2</span> Your details</li>
        <li><span>3</span> Payment</li>
      </ol>
      <h1 id="shCheckoutTitle">Your details</h1>

      <div class="sh-flow-grid">
        <form class="sh-form" method="post">
          <?= csrf_field() ?>
          <?php if ($error): ?><p class="sh-flash is-error" role="alert"><?= h($error) ?></p><?php endif; ?>
          <div class="sh-fields">
            <div class="sh-field"><label for="coName">Full name <span aria-hidden="true">*</span></label><input id="coName" type="text" name="name" required autocomplete="name" value="<?= h($_POST['name'] ?? '') ?>"></div>
            <div class="sh-field"><label for="coEmail">Email address <span aria-hidden="true">*</span></label><input id="coEmail" type="email" name="email" required autocomplete="email" value="<?= h($_POST['email'] ?? '') ?>"><small>Your confirmation and receipt are sent here.</small></div>
            <div class="sh-field"><label for="coPhone">Phone number <span aria-hidden="true">*</span></label><input id="coPhone" type="tel" name="phone" required autocomplete="tel" value="<?= h($_POST['phone'] ?? '') ?>" placeholder="e.g. 0700 000 000"><small>Our team calls this number to arrange delivery.</small></div>
            <div class="sh-field"><label for="coPlace">Delivery location <span aria-hidden="true">*</span></label><input id="coPlace" type="text" name="location" required autocomplete="address-level2" value="<?= h($_POST['location'] ?? '') ?>" placeholder="District, town or address"></div>
            <div class="sh-field is-full"><label for="coNotes">Notes for our team <small>(optional)</small></label><textarea id="coNotes" name="notes" rows="3" placeholder="A good time to deliver, a landmark, anything else we should know"><?= h($_POST['notes'] ?? '') ?></textarea></div>
          </div>
          <button type="submit" class="sh-btn is-wide"><?= icon('phone', 16) ?> Continue to payment</button>
          <p class="sh-form-note"><?= icon('check', 14) ?> You will pay on our secure payment page, by mobile money or card.</p>
        </form>

        <aside class="sh-summary" aria-labelledby="shOrderTitle">
          <h2 id="shOrderTitle">Your order</h2>
          <ul class="sh-mini">
            <?php foreach ($cart as $item): ?>
              <li><span class="sh-mini-media"><?= ab_img($item['image'], '', '', true, '', '56px') ?></span><span><?= h($item['name']) ?> <small>× <?= (int) $item['qty'] ?></small></span><strong><?= h(format_price($item['price'] * $item['qty'])) ?></strong></li>
            <?php endforeach; ?>
          </ul>
          <p class="sh-summary-row is-total"><span>Total</span><strong><?= h(format_price($subtotal)) ?></strong></p>
          <?php if ($usd): ?><p class="sh-summary-note">That is <?= h($usd) ?>. Payment is in Uganda shillings.</p><?php endif; ?>
          <a class="sh-summary-back" href="<?= SITE_URL ?>/cart.php">Change your basket</a>
        </aside>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
