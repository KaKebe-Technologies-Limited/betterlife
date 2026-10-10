<?php
/**
 * Pesapal brings the giver back here. The status is checked with Pesapal itself (never trusted from the
 * redirect), and the page says thank you, or explains what to do when the payment did not go through.
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/donations.php';
$pageTitle = 'Thank you';
$activePage = 'donate';

$ref = (string) ($_GET['ref'] ?? '');
$gift = $ref !== '' && str_starts_with($ref, 'DN') ? donation_find($pdo, $ref) : null;
if (!$gift) redirect(SITE_URL . '/donate.php');

$tracking = (string) ($_GET['OrderTrackingId'] ?? '');
if ($gift['status'] === 'pending' && empty($gift['pesapal_tracking_id']) && $tracking !== '') {
    $pdo->prepare('UPDATE donations SET pesapal_tracking_id = ? WHERE id = ?')->execute([$tracking, $gift['id']]);
    $gift['pesapal_tracking_id'] = $tracking;
}
$status = donation_sync_status($pdo, $gift);
$first = explode(' ', trim($gift['donor_name']))[0];

$pageStyles  = ['assets/css/about.css', 'assets/css/shop.css', 'assets/css/donate.css'];
$pageHead = '<meta name="robots" content="noindex"><script>document.documentElement.classList.add("ab-js")</script>';
require __DIR__ . '/includes/header.php';
?>

<main class="ab sh dn" id="top">
  <section class="dn-hero" aria-labelledby="dnThanksTitle">
    <div class="container">
      <div class="dn-card dn-thanks" role="status">
        <?php if ($status === 'paid'): ?>
          <span class="dn-thanks-icon is-paid" aria-hidden="true"><?= icon('heart', 26) ?></span>
          <h1 id="dnThanksTitle">Thank you, <?= h($first) ?></h1>
          <p>Your gift of <strong><?= h(format_price((float) $gift['amount'])) ?></strong> has reached BetterLife International. A receipt is on its way to your email.</p>
        <?php elseif ($status === 'pending'): ?>
          <span class="dn-thanks-icon" aria-hidden="true"><?= icon('clock', 26) ?></span>
          <h1 id="dnThanksTitle">Your payment is being confirmed</h1>
          <p>Thank you, <?= h($first) ?>. Mobile money payments can take a few minutes to confirm. We will email your receipt as soon as it does. If you did not finish paying, you can <a href="<?= SITE_URL ?>/donate.php">start again</a>.</p>
        <?php else: ?>
          <span class="dn-thanks-icon is-failed" aria-hidden="true"><?= icon('x', 26) ?></span>
          <h1 id="dnThanksTitle">The payment was not completed</h1>
          <p>Your gift of <?= h(format_price((float) $gift['amount'])) ?> did not go through. You can <a href="<?= SITE_URL ?>/donate.php">try again</a>, or <a href="<?= SITE_URL ?>/contact.php?subject=<?= urlencode('General enquiry') ?>">write to us</a> if it keeps happening.</p>
        <?php endif; ?>
        <p class="dn-thanks-ref">Reference <?= h($gift['donation_ref']) ?></p>
        <p class="dn-links">
          <a href="<?= SITE_URL ?>/projects.php" class="ab-link">See the projects <?= icon('arrow-right', 15) ?></a>
          <a href="<?= SITE_URL ?>/blog.php" class="ab-link">Read our stories <?= icon('arrow-right', 15) ?></a>
        </p>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
