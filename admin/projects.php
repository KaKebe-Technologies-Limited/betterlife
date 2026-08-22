<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'projects';

$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/projects.php');
    }
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $delId = (int) $_POST['id'];
        $img = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
        $img->execute([$delId]);
        if ($path = $img->fetchColumn()) {
            $full = __DIR__ . '/../' . $path;
            if (is_file($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$delId]);
        flash_set('success', 'Project deleted.');
        redirect(ADMIN_URL . '/projects.php');
    }

    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = isset($_POST['status']) ? 1 : 0;
    $sort        = (int) ($_POST['sort_order'] ?? 0);
    $editId      = (int) ($_POST['id'] ?? 0);

    if ($title === '') {
        flash_set('error', 'Title is required.');
        redirect(ADMIN_URL . '/projects.php?action=' . ($editId ? 'edit&id=' . $editId : 'add'));
    }

    $errors = [];
    $image = handle_image_upload($_FILES['image'] ?? [], 'projects', $errors);

    if ($editId) {
        $slug = unique_slug($pdo, 'projects', $title, $editId);
        if ($image) {
            $pdo->prepare("UPDATE projects SET title=?, slug=?, category=?, description=?, image=?, status=?, sort_order=? WHERE id=?")
                ->execute([$title, $slug, $category, $description, $image, $status, $sort, $editId]);
        } else {
            $pdo->prepare("UPDATE projects SET title=?, slug=?, category=?, description=?, status=?, sort_order=? WHERE id=?")
                ->execute([$title, $slug, $category, $description, $status, $sort, $editId]);
        }
        flash_set('success', 'Project updated successfully.');
    } else {
        $slug = unique_slug($pdo, 'projects', $title);
        $pdo->prepare("INSERT INTO projects (title, slug, category, description, image, status, sort_order) VALUES (?,?,?,?,?,?,?)")
            ->execute([$title, $slug, $category, $description, $image, $status, $sort]);
        flash_set('success', 'Project created successfully.');
    }
    redirect(ADMIN_URL . '/projects.php');
}

$editing = null;
if (in_array($action, ['add', 'edit'], true)) {
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $editing = $stmt->fetch();
        if (!$editing) { flash_set('error', 'Project not found.'); redirect(ADMIN_URL . '/projects.php'); }
    }
    $pageTitle = $action === 'edit' ? 'Edit Project' : 'Add Project';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
      <div class="panel-head"><h3><?= h($pageTitle) ?></h3><a href="<?= ADMIN_URL ?>/projects.php" class="btn btn-outline btn-sm">← Back to List</a></div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= h($editing['id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" required value="<?= h($editing['title'] ?? '') ?>"></div>
            <div class="form-group"><label>Category / Tag</label><input type="text" name="category" class="form-control" placeholder="e.g. Agriculture in Uganda" value="<?= h($editing['category'] ?? '') ?>"></div>
            <div class="form-group full"><label>Description</label><textarea name="description" class="form-control" style="min-height:130px;"><?= h($editing['description'] ?? '') ?></textarea></div>
            <div class="form-group">
              <label>Image</label>
              <?php if (!empty($editing['image'])): ?><img src="<?= asset_url($editing['image']) ?>" class="current-image"><br><?php endif; ?>
              <input type="file" name="image" class="form-control" accept="image/*">
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
          <button type="submit" class="btn btn-primary ico-text"><?= icon('save', 16) ?> Save Project</button>
        </form>
      </div>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Projects';
$projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Projects (<?= count($projects) ?>)</h3>
    <a href="<?= ADMIN_URL ?>/projects.php?action=add" class="btn btn-primary btn-sm">+ Add Project</a>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th></th><th>Title</th><th>Category</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php if (!$projects): ?><tr class="empty-row"><td colspan="6">No projects yet. Add your first one.</td></tr><?php endif; ?>
        <?php foreach ($projects as $p): ?>
          <tr>
            <td><img src="<?= asset_url($p['image']) ?>" class="row-thumb"></td>
            <td><strong><?= h($p['title']) ?></strong></td>
            <td><?= h($p['category']) ?></td>
            <td><?= (int) $p['sort_order'] ?></td>
            <td><span class="badge <?= $p['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $p['status'] ? 'Visible' : 'Hidden' ?></span></td>
            <td>
              <div class="row-actions">
                <a href="<?= ADMIN_URL ?>/projects.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this project?">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
