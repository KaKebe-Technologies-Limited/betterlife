<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'stats';
$pageTitle = 'Impact Stats';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/stats.php');
    }
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $pdo->prepare("DELETE FROM stats WHERE id = ?")->execute([(int) $_POST['id']]);
        flash_set('success', 'Stat removed.');
        redirect(ADMIN_URL . '/stats.php');
    }

    $label = trim($_POST['label'] ?? '');
    $value = trim($_POST['value'] ?? '');
    $sort  = (int) ($_POST['sort_order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;
    $editId = (int) ($_POST['id'] ?? 0);

    if ($label === '' || $value === '') {
        flash_set('error', 'Label and value are required.');
        redirect(ADMIN_URL . '/stats.php');
    }

    if ($editId) {
        $pdo->prepare("UPDATE stats SET label=?, value=?, sort_order=?, status=? WHERE id=?")->execute([$label, $value, $sort, $status, $editId]);
        flash_set('success', 'Stat updated.');
    } else {
        $pdo->prepare("INSERT INTO stats (label, value, sort_order, status) VALUES (?,?,?,?)")->execute([$label, $value, $sort, $status]);
        flash_set('success', 'Stat added.');
    }
    redirect(ADMIN_URL . '/stats.php');
}

$stats = $pdo->query("SELECT * FROM stats ORDER BY sort_order")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="form-grid">
  <div class="panel">
    <div class="panel-head"><h3>Add Impact Stat</h3></div>
    <div class="panel-body">
      <form method="post">
        <?= csrf_field() ?>
        <div class="form-group"><label>Label *</label><input type="text" name="label" class="form-control" required placeholder="e.g. Trees Planted"></div>
        <div class="form-group"><label>Value *</label><input type="text" name="value" class="form-control" required placeholder="e.g. 1M+"></div>
        <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        <div class="checkbox-row" style="margin-bottom:18px;"><input type="checkbox" name="status" id="status" checked><label for="status" style="margin:0;">Visible on website</label></div>
        <button type="submit" class="btn btn-primary btn-block">+ Add Stat</button>
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>All Stats (<?= count($stats) ?>)</h3></div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Value</th><th>Label</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php if (!$stats): ?><tr class="empty-row"><td colspan="4">No stats yet.</td></tr><?php endif; ?>
          <?php foreach ($stats as $s): ?>
            <tr>
              <td><strong><?= h($s['value']) ?></strong></td>
              <td><?= h($s['label']) ?></td>
              <td><span class="badge <?= $s['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $s['status'] ? 'Visible' : 'Hidden' ?></span></td>
              <td>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $s['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Remove this stat?">Delete</button></form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
