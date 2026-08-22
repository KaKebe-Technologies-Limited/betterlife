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
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Subscribers (<?= count($subscribers) ?>)</h3>
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
