<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/donations.php';
$activeNav = 'donations';
$pageTitle = 'Donations';

donations_ensure_table($pdo);
$filter = in_array($_GET['status'] ?? '', ['paid', 'pending', 'failed', 'cancelled'], true) ? $_GET['status'] : 'paid';

// A pending gift can be checked again with Pesapal (for when the IPN did not arrive)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
    } elseif (($_POST['action'] ?? '') === 'recheck' && ($gift = donation_find($pdo, (string) ($_POST['ref'] ?? '')))) {
        $now = donation_sync_status($pdo, $gift);
        flash_set('success', $gift['donation_ref'] . ' checked with Pesapal: ' . $now . '.');
    }
    redirect(ADMIN_URL . '/donations.php?status=' . urlencode($filter));
}

$stmt = $pdo->prepare('SELECT * FROM donations WHERE status = ? ORDER BY created_at DESC');
$stmt->execute([$filter]);
$gifts = $stmt->fetchAll();

if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="betterlife-donations-' . $filter . '-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Reference', 'Date', 'Name', 'Email', 'Phone', 'Amount', 'Currency', 'Status', 'Paid at', 'Message']);
    foreach ($gifts as $g) fputcsv($out, [$g['donation_ref'], $g['created_at'], $g['donor_name'], $g['donor_email'], $g['donor_phone'], $g['amount'], $g['currency'], $g['status'], $g['paid_at'], $g['message']]);
    exit;
}

$totals = $pdo->query("SELECT COUNT(*) AS n, COALESCE(SUM(amount), 0) AS total, COALESCE(SUM(CASE WHEN paid_at >= DATE_FORMAT(NOW(), '%Y-01-01') THEN amount END), 0) AS year FROM donations WHERE status = 'paid'")->fetch();
$counts = $pdo->query('SELECT status, COUNT(*) FROM donations GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);

require __DIR__ . '/includes/header.php';
?>

<div class="stat-cards">
  <div class="stat-card">
    <div class="ico" style="background:var(--a-green-100);color:var(--a-green-700);"><?= icon('heart', 22) ?></div>
    <div><strong><?= format_price((float) $totals['total']) ?></strong><span><?= (int) $totals['n'] ?> gift<?= (int) $totals['n'] === 1 ? '' : 's' ?> received in all</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:var(--a-blue-100);color:var(--a-blue-700);"><?= icon('calendar', 22) ?></div>
    <div><strong><?= format_price((float) $totals['year']) ?></strong><span>received in <?= date('Y') ?></span></div>
  </div>
</div>

<?php if (!donations_can_pay($pdo)): ?>
  <div class="alert alert-error">The Donate page cannot take payments until the Pesapal keys are saved in Site Settings &gt; Payments &amp; Email. Until then it asks people to write to you instead.</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <?php foreach (['paid' => 'Received', 'pending' => 'Waiting for payment', 'failed' => 'Failed', 'cancelled' => 'Not started'] as $k => $label): ?>
        <a href="?status=<?= $k ?>" class="btn btn-sm <?= $filter === $k ? 'btn-primary' : 'btn-outline' ?>"><?= $label ?> (<?= (int) ($counts[$k] ?? 0) ?>)</a>
      <?php endforeach; ?>
    </div>
    <?php if ($gifts): ?><a href="?status=<?= $filter ?>&amp;export=csv" class="btn btn-outline btn-sm">Download (CSV)</a><?php endif; ?>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Date</th><th>From</th><th>Amount</th><th>Message</th><th>Reference</th></tr></thead>
      <tbody>
        <?php if (!$gifts): ?><tr class="empty-row"><td colspan="5">None here yet. Gifts made on the Donate page appear here.</td></tr><?php endif; ?>
        <?php foreach ($gifts as $g): ?>
          <tr>
            <td><span class="help-text"><?= format_date($g['paid_at'] ?: $g['created_at'], 'j M Y, H:i') ?></span></td>
            <td><strong><?= h($g['donor_name']) ?></strong><br><a href="mailto:<?= h($g['donor_email']) ?>" class="help-text"><?= h($g['donor_email']) ?></a><?= $g['donor_phone'] ? '<br><span class="help-text">' . h($g['donor_phone']) . '</span>' : '' ?></td>
            <td><strong><?= format_price((float) $g['amount']) ?></strong></td>
            <td style="max-width:320px;"><?= $g['message'] ? nl2br(h($g['message'])) : '<span class="help-text">None</span>' ?></td>
            <td>
              <span class="help-text"><?= h($g['donation_ref']) ?></span>
              <?php if ($g['status'] === 'pending' && $g['pesapal_tracking_id']): ?>
                <form method="post" style="margin-top:6px;"><?= csrf_field() ?><input type="hidden" name="action" value="recheck"><input type="hidden" name="ref" value="<?= h($g['donation_ref']) ?>"><button type="submit" class="btn btn-outline btn-sm">Check with Pesapal</button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
