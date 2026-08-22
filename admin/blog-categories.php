<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'blog-categories';
$pageTitle = 'Blog Categories';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/blog-categories.php');
    }
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $pdo->prepare("DELETE FROM blog_categories WHERE id = ?")->execute([(int) $_POST['id']]);
        flash_set('success', 'Category deleted.');
        redirect(ADMIN_URL . '/blog-categories.php');
    }

    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        flash_set('error', 'Category name is required.');
        redirect(ADMIN_URL . '/blog-categories.php');
    }
    $slug = unique_slug($pdo, 'blog_categories', $name);
    $pdo->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)")->execute([$name, $slug]);
    flash_set('success', 'Category added.');
    redirect(ADMIN_URL . '/blog-categories.php');
}

$categories = $pdo->query("SELECT bc.*, COUNT(bp.id) AS post_count FROM blog_categories bc LEFT JOIN blog_posts bp ON bp.category_id = bc.id GROUP BY bc.id ORDER BY bc.name")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="form-grid">
  <div class="panel">
    <div class="panel-head"><h3>Add Category</h3></div>
    <div class="panel-body">
      <form method="post">
        <?= csrf_field() ?>
        <div class="form-group"><label>Category Name *</label><input type="text" name="name" class="form-control" required placeholder="e.g. Agriculture"></div>
        <button type="submit" class="btn btn-primary btn-block">+ Add Category</button>
      </form>
    </div>
  </div>
  <div class="panel">
    <div class="panel-head"><h3>All Categories (<?= count($categories) ?>)</h3></div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Slug</th><th>Posts</th><th></th></tr></thead>
        <tbody>
          <?php if (!$categories): ?><tr class="empty-row"><td colspan="4">No categories yet.</td></tr><?php endif; ?>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td><strong><?= h($c['name']) ?></strong></td>
              <td><span class="help-text"><?= h($c['slug']) ?></span></td>
              <td><?= (int) $c['post_count'] ?></td>
              <td>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this category? Posts in it will become uncategorized.">Delete</button></form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
