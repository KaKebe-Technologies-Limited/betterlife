<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'products';

$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/products.php');
    }
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $delId = (int) $_POST['id'];
        $img = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $img->execute([$delId]);
        if ($path = $img->fetchColumn()) {
            $full = __DIR__ . '/../' . $path;
            if (is_file($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$delId]);
        flash_set('success', 'Product deleted.');
        redirect(ADMIN_URL . '/products.php');
    }

    $name       = trim($_POST['name'] ?? '');
    $category   = trim($_POST['category'] ?? '');
    $price      = $_POST['price'] !== '' ? (float) $_POST['price'] : null;
    $unit       = trim($_POST['unit'] ?? '');
    $shortDesc  = trim($_POST['short_desc'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $featured   = isset($_POST['featured']) ? 1 : 0;
    $status     = isset($_POST['status']) ? 1 : 0;
    $sort       = (int) ($_POST['sort_order'] ?? 0);
    $editId     = (int) ($_POST['id'] ?? 0);

    if ($name === '' || $category === '') {
        flash_set('error', 'Name and category are required.');
        redirect(ADMIN_URL . '/products.php?action=' . ($editId ? 'edit&id=' . $editId : 'add'));
    }

    $errors = [];
    $image = handle_image_upload($_FILES['image'] ?? [], 'products', $errors);

    if ($editId) {
        $slug = unique_slug($pdo, 'products', $name, $editId);
        if ($image) {
            $pdo->prepare("UPDATE products SET name=?, slug=?, category=?, price=?, unit=?, short_desc=?, description=?, image=?, featured=?, status=?, sort_order=? WHERE id=?")
                ->execute([$name, $slug, $category, $price, $unit, $shortDesc, $description, $image, $featured, $status, $sort, $editId]);
        } else {
            $pdo->prepare("UPDATE products SET name=?, slug=?, category=?, price=?, unit=?, short_desc=?, description=?, featured=?, status=?, sort_order=? WHERE id=?")
                ->execute([$name, $slug, $category, $price, $unit, $shortDesc, $description, $featured, $status, $sort, $editId]);
        }
        flash_set('success', 'Product updated successfully.');
    } else {
        $slug = unique_slug($pdo, 'products', $name);
        $pdo->prepare("INSERT INTO products (name, slug, category, price, unit, short_desc, description, image, featured, status, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$name, $slug, $category, $price, $unit, $shortDesc, $description, $image, $featured, $status, $sort]);
        flash_set('success', 'Product created successfully.');
    }
    redirect(ADMIN_URL . '/products.php');
}

$categoryOptions = ['Honey', 'Ghee', 'Yoghurt', 'Dairy', 'Other'];

$editing = null;
if (in_array($action, ['add', 'edit'], true)) {
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $editing = $stmt->fetch();
        if (!$editing) { flash_set('error', 'Product not found.'); redirect(ADMIN_URL . '/products.php'); }
    }
    $pageTitle = $action === 'edit' ? 'Edit Product' : 'Add Farm Product';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
      <div class="panel-head"><h3><?= h($pageTitle) ?></h3><a href="<?= ADMIN_URL ?>/products.php" class="btn btn-outline btn-sm">← Back to List</a></div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= h($editing['id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-group"><label>Product Name *</label><input type="text" name="name" class="form-control" required value="<?= h($editing['name'] ?? '') ?>"></div>
            <div class="form-group">
              <label>Category *</label>
              <select name="category" class="form-control" required>
                <?php foreach ($categoryOptions as $c): ?>
                  <option value="<?= h($c) ?>" <?= (($editing['category'] ?? '') === $c) ? 'selected' : '' ?>><?= h($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label>Price (UGX)</label><input type="number" step="0.01" name="price" class="form-control" value="<?= h($editing['price'] ?? '') ?>"></div>
            <div class="form-group"><label>Unit</label><input type="text" name="unit" class="form-control" placeholder="e.g. 500ml jar" value="<?= h($editing['unit'] ?? '') ?>"></div>
            <div class="form-group full"><label>Short Description (shown on cards)</label><input type="text" name="short_desc" class="form-control" value="<?= h($editing['short_desc'] ?? '') ?>"></div>
            <div class="form-group full"><label>Full Description</label><textarea name="description" class="form-control" style="min-height:160px;"><?= h($editing['description'] ?? '') ?></textarea></div>
            <div class="form-group">
              <label>Product Image</label>
              <?php if (!empty($editing['image'])): ?><img src="<?= asset_url($editing['image']) ?>" class="current-image"><br><?php endif; ?>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
              <label>Sort Order</label>
              <input type="number" name="sort_order" class="form-control" value="<?= h($editing['sort_order'] ?? '0') ?>">
              <div class="checkbox-row" style="margin-top:14px;">
                <input type="checkbox" name="featured" id="featured" <?= !empty($editing['featured']) ? 'checked' : '' ?>>
                <label for="featured" style="margin:0;">Mark as Popular / Featured</label>
              </div>
              <div class="checkbox-row" style="margin-top:10px;">
                <input type="checkbox" name="status" id="status" <?= (!isset($editing) || $editing['status']) ? 'checked' : '' ?>>
                <label for="status" style="margin:0;">Visible on website</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">💾 Save Product</button>
        </form>
      </div>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Farm Products';
$products = $pdo->query("SELECT * FROM products ORDER BY sort_order")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Products (<?= count($products) ?>)</h3>
    <a href="<?= ADMIN_URL ?>/products.php?action=add" class="btn btn-primary btn-sm">+ Add Product</a>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Featured</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php if (!$products): ?><tr class="empty-row"><td colspan="7">No products yet. Add your first one.</td></tr><?php endif; ?>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><img src="<?= asset_url($p['image']) ?>" class="row-thumb"></td>
            <td><strong><?= h($p['name']) ?></strong></td>
            <td><span class="badge badge-blue"><?= h($p['category']) ?></span></td>
            <td><?= format_price($p['price']) ?> <span class="help-text">/ <?= h($p['unit']) ?></span></td>
            <td><?= $p['featured'] ? '⭐' : '—' ?></td>
            <td><span class="badge <?= $p['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $p['status'] ? 'Visible' : 'Hidden' ?></span></td>
            <td>
              <div class="row-actions">
                <a href="<?= ADMIN_URL ?>/products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this product?">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
