<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'messages';
$pageTitle = 'Contact Messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/messages.php');
    }
    $postAction = $_POST['action'] ?? '';
    $msgId = (int) ($_POST['id'] ?? 0);

    if ($postAction === 'delete') {
        $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$msgId]);
        flash_set('success', 'Message deleted.');
    } elseif ($postAction === 'mark_read') {
        $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$msgId]);
    }
    redirect(ADMIN_URL . '/messages.php');
}

$viewId = (int) ($_GET['view'] ?? 0);
if ($viewId) {
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$viewId]);
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$viewing = null;
foreach ($messages as $m) { if ((int) $m['id'] === $viewId) { $viewing = $m; break; } }

require __DIR__ . '/includes/header.php';
?>

<?php if ($viewing): ?>
  <div class="panel">
    <div class="panel-head">
      <h3>Message from <?= h($viewing['name']) ?></h3>
      <a href="<?= ADMIN_URL ?>/messages.php" class="btn btn-outline btn-sm">← Back to Inbox</a>
    </div>
    <div class="panel-body">
      <p><strong>Email:</strong> <a href="mailto:<?= h($viewing['email']) ?>"><?= h($viewing['email']) ?></a></p>
      <?php if ($viewing['phone']): ?><p><strong>Phone:</strong> <?= h($viewing['phone']) ?></p><?php endif; ?>
      <?php if ($viewing['subject']): ?><p><strong>Subject:</strong> <?= h($viewing['subject']) ?></p><?php endif; ?>
      <p><strong>Received:</strong> <?= format_date($viewing['created_at'], 'F j, Y \a\t g:i A') ?></p>
      <hr style="border:none;border-top:1px solid var(--a-border);margin:20px 0;">
      <p style="white-space:pre-wrap;"><?= h($viewing['message']) ?></p>
      <div style="margin-top:24px;display:flex;gap:10px;">
        <a href="mailto:<?= h($viewing['email']) ?>?subject=<?= urlencode('Re: ' . ($viewing['subject'] ?: 'Your message to BetterLife International')) ?>" class="btn btn-primary">Reply by Email</a>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $viewing['id'] ?>"><button type="submit" class="btn btn-danger" data-confirm="Delete this message?">Delete</button></form>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head"><h3>Inbox (<?= count($messages) ?>)</h3></div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th></th><th>Name</th><th>Email</th><th>Subject</th><th>Received</th><th></th></tr></thead>
      <tbody>
        <?php if (!$messages): ?><tr class="empty-row"><td colspan="6">No messages received yet.</td></tr><?php endif; ?>
        <?php foreach ($messages as $m): ?>
          <tr style="<?= $m['is_read'] ? '' : 'background:var(--a-blue-100);' ?>">
            <td><?= $m['is_read'] ? '' : '<span class="badge badge-blue">New</span>' ?></td>
            <td><strong><?= h($m['name']) ?></strong></td>
            <td><?= h($m['email']) ?></td>
            <td><?= h(excerpt($m['subject'] ?: $m['message'], 40)) ?></td>
            <td><span class="help-text"><?= time_ago($m['created_at']) ?></span></td>
            <td>
              <div class="row-actions">
                <a href="<?= ADMIN_URL ?>/messages.php?view=<?= $m['id'] ?>" class="btn btn-outline btn-sm">Open</a>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this message?">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
