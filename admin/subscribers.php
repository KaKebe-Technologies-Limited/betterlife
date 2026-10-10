<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'subscribers';
$pageTitle = 'Newsletter Subscribers';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/subscribers.php');
    }
    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?")->execute([(int) $_POST['id']]);
        flash_set('success', 'Subscriber removed.');
    }
    redirect(ADMIN_URL . '/subscribers.php');
}

$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC")->fetchAll();

// The list as a spreadsheet, to import into a newsletter service (Mailchimp, Brevo) or open in Excel
if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="betterlife-subscribers-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");   // so Excel reads it as UTF-8
    fputcsv($out, ['Email Address', 'Subscribed']);
    foreach ($subscribers as $s) fputcsv($out, [$s['email'], date('Y-m-d', strtotime($s['created_at']))]);
    exit;
}
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Subscribers (<?= count($subscribers) ?>)</h3>
    <?php if ($subscribers): ?><a href="<?= ADMIN_URL ?>/subscribers.php?export=csv" class="btn btn-outline btn-sm">Download list (CSV)</a><?php endif; ?>
  </div>
  <div class="panel-body" style="padding-bottom:0;">
    <p class="help-text" style="margin:0;">To send a newsletter, download the list and import it into a free newsletter service such as Brevo or Mailchimp, which handle unsubscribes for you. Only email people who signed up here.</p>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Email</th><th>Subscribed</th><th></th></tr></thead>
      <tbody>
        <?php if (!$subscribers): ?><tr class="empty-row"><td colspan="3">No subscribers yet.</td></tr><?php endif; ?>
        <?php foreach ($subscribers as $s): ?>
          <tr>
            <td><?= h($s['email']) ?></td>
            <td><span class="help-text"><?= format_date($s['created_at']) ?></span></td>
            <td><form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $s['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Remove this subscriber?">Delete</button></form></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
