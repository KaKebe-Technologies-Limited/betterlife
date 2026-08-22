<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'team';

$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);
$categories = ['leadership' => 'Leadership', 'staff' => 'Staff', 'board' => 'Board of Directors', 'volunteer' => 'Volunteer'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/team.php');
    }
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $delId = (int) $_POST['id'];
        $img = $pdo->prepare("SELECT photo FROM team_members WHERE id = ?");
        $img->execute([$delId]);
        if ($path = $img->fetchColumn()) {
            $full = __DIR__ . '/../' . $path;
            if (is_file($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM team_members WHERE id = ?")->execute([$delId]);
        flash_set('success', 'Team member deleted.');
        redirect(ADMIN_URL . '/team.php');
    }

    $name     = trim($_POST['name'] ?? '');
    $role     = trim($_POST['role'] ?? '');
    $category = $_POST['category'] ?? 'staff';
    $bio      = trim($_POST['bio'] ?? '');
    $facebook = trim($_POST['facebook'] ?? '');
    $twitter  = trim($_POST['twitter'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $status   = isset($_POST['status']) ? 1 : 0;
    $sort     = (int) ($_POST['sort_order'] ?? 0);
    $editId   = (int) ($_POST['id'] ?? 0);

    if (!array_key_exists($category, $categories)) $category = 'staff';

    if ($name === '' || $role === '') {
        flash_set('error', 'Name and role are required.');
        redirect(ADMIN_URL . '/team.php?action=' . ($editId ? 'edit&id=' . $editId : 'add'));
    }

    $errors = [];
    $photo = handle_image_upload($_FILES['photo'] ?? [], 'team', $errors);

    if ($editId) {
        if ($photo) {
            $pdo->prepare("UPDATE team_members SET name=?, role=?, category=?, bio=?, photo=?, facebook=?, twitter=?, linkedin=?, status=?, sort_order=? WHERE id=?")
                ->execute([$name, $role, $category, $bio, $photo, $facebook, $twitter, $linkedin, $status, $sort, $editId]);
        } else {
            $pdo->prepare("UPDATE team_members SET name=?, role=?, category=?, bio=?, facebook=?, twitter=?, linkedin=?, status=?, sort_order=? WHERE id=?")
                ->execute([$name, $role, $category, $bio, $facebook, $twitter, $linkedin, $status, $sort, $editId]);
        }
        flash_set('success', 'Team member updated successfully.');
    } else {
        $pdo->prepare("INSERT INTO team_members (name, role, category, bio, photo, facebook, twitter, linkedin, status, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)")
            ->execute([$name, $role, $category, $bio, $photo, $facebook, $twitter, $linkedin, $status, $sort]);
        flash_set('success', 'Team member added successfully.');
    }
    redirect(ADMIN_URL . '/team.php');
}

$editing = null;
if (in_array($action, ['add', 'edit'], true)) {
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $editing = $stmt->fetch();
        if (!$editing) { flash_set('error', 'Team member not found.'); redirect(ADMIN_URL . '/team.php'); }
    }
    $pageTitle = $action === 'edit' ? 'Edit Team Member' : 'Add Team Member';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
      <div class="panel-head"><h3><?= h($pageTitle) ?></h3><a href="<?= ADMIN_URL ?>/team.php" class="btn btn-outline btn-sm">← Back to List</a></div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= h($editing['id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" required value="<?= h($editing['name'] ?? '') ?>"></div>
            <div class="form-group"><label>Role / Title *</label><input type="text" name="role" class="form-control" required value="<?= h($editing['role'] ?? '') ?>"></div>
            <div class="form-group">
              <label>Category *</label>
              <select name="category" class="form-control" required>
                <?php foreach ($categories as $key => $label): ?>
                  <option value="<?= $key ?>" <?= (($editing['category'] ?? 'staff') === $key) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Photo</label>
              <?php if (!empty($editing['photo'])): ?><img src="<?= asset_url($editing['photo']) ?>" class="current-image"><br><?php endif; ?>
              <input type="file" name="photo" class="form-control" accept="image/*">
              <p class="hint">Leave blank to auto-generate an initials avatar.</p>
            </div>
            <div class="form-group full"><label>Bio</label><textarea name="bio" class="form-control"><?= h($editing['bio'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Facebook URL</label><input type="text" name="facebook" class="form-control" value="<?= h($editing['facebook'] ?? '') ?>"></div>
            <div class="form-group"><label>Twitter / X URL</label><input type="text" name="twitter" class="form-control" value="<?= h($editing['twitter'] ?? '') ?>"></div>
            <div class="form-group"><label>LinkedIn URL</label><input type="text" name="linkedin" class="form-control" value="<?= h($editing['linkedin'] ?? '') ?>"></div>
            <div class="form-group">
              <label>Sort Order</label>
              <input type="number" name="sort_order" class="form-control" value="<?= h($editing['sort_order'] ?? '0') ?>">
              <div class="checkbox-row" style="margin-top:14px;">
                <input type="checkbox" name="status" id="status" <?= (!isset($editing) || $editing['status']) ? 'checked' : '' ?>>
                <label for="status" style="margin:0;">Visible on website</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary ico-text"><?= icon('save', 16) ?> Save Team Member</button>
        </form>
      </div>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Team & Board';
$members = $pdo->query("SELECT * FROM team_members ORDER BY FIELD(category,'leadership','staff','board','volunteer'), sort_order")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Team &amp; Board Members (<?= count($members) ?>)</h3>
    <a href="<?= ADMIN_URL ?>/team.php?action=add" class="btn btn-primary btn-sm">+ Add Member</a>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th></th><th>Name</th><th>Role</th><th>Category</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php if (!$members): ?><tr class="empty-row"><td colspan="6">No team members yet.</td></tr><?php endif; ?>
        <?php foreach ($members as $m): ?>
          <tr>
            <td><img src="<?= $m['photo'] ? asset_url($m['photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($m['name']) . '&background=16593f&color=fff' ?>" class="row-thumb" style="border-radius:50%;"></td>
            <td><strong><?= h($m['name']) ?></strong></td>
            <td><?= h($m['role']) ?></td>
            <td><span class="badge badge-blue"><?= h($categories[$m['category']] ?? $m['category']) ?></span></td>
            <td><span class="badge <?= $m['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $m['status'] ? 'Visible' : 'Hidden' ?></span></td>
            <td>
              <div class="row-actions">
                <a href="<?= ADMIN_URL ?>/team.php?action=edit&id=<?= $m['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this team member?">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
