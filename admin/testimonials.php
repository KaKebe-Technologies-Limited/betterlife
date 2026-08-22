<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'testimonials';

$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/testimonials.php');
    }
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $delId = (int) $_POST['id'];
        $img = $pdo->prepare("SELECT photo FROM testimonials WHERE id = ?");
        $img->execute([$delId]);
        if ($path = $img->fetchColumn()) {
            $full = __DIR__ . '/../' . $path;
            if (is_file($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$delId]);
        flash_set('success', 'Testimonial deleted.');
        redirect(ADMIN_URL . '/testimonials.php');
    }

    $quote      = trim($_POST['quote'] ?? '');
    $authorName = trim($_POST['author_name'] ?? '');
    $authorRole = trim($_POST['author_role'] ?? '');
    $status     = isset($_POST['status']) ? 1 : 0;
    $sort       = (int) ($_POST['sort_order'] ?? 0);
    $editId     = (int) ($_POST['id'] ?? 0);

    if ($quote === '' || $authorName === '') {
        flash_set('error', 'Quote and author name are required.');
        redirect(ADMIN_URL . '/testimonials.php?action=' . ($editId ? 'edit&id=' . $editId : 'add'));
    }

    $errors = [];
    $photo = handle_image_upload($_FILES['photo'] ?? [], 'testimonials', $errors);

    if ($editId) {
        if ($photo) {
            $pdo->prepare("UPDATE testimonials SET quote=?, author_name=?, author_role=?, photo=?, status=?, sort_order=? WHERE id=?")
                ->execute([$quote, $authorName, $authorRole, $photo, $status, $sort, $editId]);
        } else {
            $pdo->prepare("UPDATE testimonials SET quote=?, author_name=?, author_role=?, status=?, sort_order=? WHERE id=?")
                ->execute([$quote, $authorName, $authorRole, $status, $sort, $editId]);
        }
        flash_set('success', 'Testimonial updated successfully.');
    } else {
        $pdo->prepare("INSERT INTO testimonials (quote, author_name, author_role, photo, status, sort_order) VALUES (?,?,?,?,?,?)")
            ->execute([$quote, $authorName, $authorRole, $photo, $status, $sort]);
        flash_set('success', 'Testimonial added successfully.');
    }
    redirect(ADMIN_URL . '/testimonials.php');
}

$editing = null;
if (in_array($action, ['add', 'edit'], true)) {
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
        $editing = $stmt->fetch();
        if (!$editing) { flash_set('error', 'Testimonial not found.'); redirect(ADMIN_URL . '/testimonials.php'); }
    }
    $pageTitle = $action === 'edit' ? 'Edit Testimonial' : 'Add Testimonial';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
      <div class="panel-head"><h3><?= h($pageTitle) ?></h3><a href="<?= ADMIN_URL ?>/testimonials.php" class="btn btn-outline btn-sm">← Back to List</a></div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= h($editing['id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-group full"><label>Quote *</label><textarea name="quote" class="form-control" required><?= h($editing['quote'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Author Name *</label><input type="text" name="author_name" class="form-control" required value="<?= h($editing['author_name'] ?? '') ?>"></div>
            <div class="form-group"><label>Author Role</label><input type="text" name="author_role" class="form-control" value="<?= h($editing['author_role'] ?? '') ?>"></div>
            <div class="form-group">
              <label>Photo</label>
              <?php if (!empty($editing['photo'])): ?><img src="<?= asset_url($editing['photo']) ?>" class="current-image"><br><?php endif; ?>
              <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
              <label>Sort Order</label>
              <input type="number" name="sort_order" class="form-control" value="<?= h($editing['sort_order'] ?? '0') ?>">
              <div class="checkbox-row" style="margin-top:14px;">
                <input type="checkbox" name="status" id="status" <?= (!isset($editing) || $editing['status']) ? 'checked' : '' ?>>
                <label for="status" style="margin:0;">Visible on website</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary ico-text"><?= icon('save', 16) ?> Save Testimonial</button>
        </form>
      </div>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Testimonials';
$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Testimonials (<?= count($testimonials) ?>)</h3>
    <a href="<?= ADMIN_URL ?>/testimonials.php?action=add" class="btn btn-primary btn-sm">+ Add Testimonial</a>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Quote</th><th>Author</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php if (!$testimonials): ?><tr class="empty-row"><td colspan="4">No testimonials yet.</td></tr><?php endif; ?>
        <?php foreach ($testimonials as $t): ?>
          <tr>
            <td><?= h(excerpt($t['quote'], 70)) ?></td>
            <td><strong><?= h($t['author_name']) ?></strong><br><span class="help-text"><?= h($t['author_role']) ?></span></td>
            <td><span class="badge <?= $t['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $t['status'] ? 'Visible' : 'Hidden' ?></span></td>
            <td>
              <div class="row-actions">
                <a href="<?= ADMIN_URL ?>/testimonials.php?action=edit&id=<?= $t['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $t['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this testimonial?">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
