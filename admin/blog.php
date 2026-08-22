<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'blog';

$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/blog.php');
    }
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $delId = (int) $_POST['id'];
        $img = $pdo->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
        $img->execute([$delId]);
        if ($path = $img->fetchColumn()) {
            $full = __DIR__ . '/../' . $path;
            if (is_file($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$delId]);
        flash_set('success', 'Blog post deleted.');
        redirect(ADMIN_URL . '/blog.php');
    }

    $title      = trim($_POST['title'] ?? '');
    $categoryId = $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
    $excerpt    = trim($_POST['excerpt'] ?? '');
    $content    = $_POST['content'] ?? '';
    $author     = trim($_POST['author'] ?? 'Admin');
    $status     = in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft';
    $publishedAt = trim($_POST['published_at'] ?? '');
    $editId     = (int) ($_POST['id'] ?? 0);

    if ($title === '') {
        flash_set('error', 'Title is required.');
        redirect(ADMIN_URL . '/blog.php?action=' . ($editId ? 'edit&id=' . $editId : 'add'));
    }

    if ($status === 'published' && $publishedAt === '') {
        $publishedAt = date('Y-m-d H:i:s');
    }
    $publishedAtSql = $publishedAt !== '' ? date('Y-m-d H:i:s', strtotime($publishedAt)) : null;

    $errors = [];
    $image = handle_image_upload($_FILES['featured_image'] ?? [], 'blog', $errors);

    if ($editId) {
        $slug = unique_slug($pdo, 'blog_posts', $title, $editId);
        if ($image) {
            $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, category_id=?, excerpt=?, content=?, featured_image=?, author=?, status=?, published_at=? WHERE id=?")
                ->execute([$title, $slug, $categoryId, $excerpt, $content, $image, $author, $status, $publishedAtSql, $editId]);
        } else {
            $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, category_id=?, excerpt=?, content=?, author=?, status=?, published_at=? WHERE id=?")
                ->execute([$title, $slug, $categoryId, $excerpt, $content, $author, $status, $publishedAtSql, $editId]);
        }
        flash_set('success', 'Blog post updated successfully.');
    } else {
        $slug = unique_slug($pdo, 'blog_posts', $title);
        $pdo->prepare("INSERT INTO blog_posts (title, slug, category_id, excerpt, content, featured_image, author, status, published_at) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$title, $slug, $categoryId, $excerpt, $content, $image, $author, $status, $publishedAtSql]);
        flash_set('success', 'Blog post created successfully.');
    }
    redirect(ADMIN_URL . '/blog.php');
}

$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();

$editing = null;
if (in_array($action, ['add', 'edit'], true)) {
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $editing = $stmt->fetch();
        if (!$editing) { flash_set('error', 'Blog post not found.'); redirect(ADMIN_URL . '/blog.php'); }
    }
    $pageTitle = $action === 'edit' ? 'Edit Blog Post' : 'Write New Blog Post';
    require __DIR__ . '/includes/header.php';
    ?>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <style>
      #quillEditor { background: #fff; min-height: 320px; border-radius: 0 0 9px 9px; }
      .ql-toolbar { border-radius: 9px 9px 0 0; }
    </style>
    <div class="panel">
      <div class="panel-head"><h3><?= h($pageTitle) ?></h3><a href="<?= ADMIN_URL ?>/blog.php" class="btn btn-outline btn-sm">← Back to List</a></div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data" id="postForm">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= h($editing['id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-group full"><label>Post Title *</label><input type="text" name="title" class="form-control" required value="<?= h($editing['title'] ?? '') ?>"></div>

            <div class="form-group">
              <label>Category</label>
              <select name="category_id" class="form-control">
                <option value="">— None —</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= (($editing['category_id'] ?? null) == $c['id']) ? 'selected' : '' ?>><?= h($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label>Author</label><input type="text" name="author" class="form-control" value="<?= h($editing['author'] ?? 'Admin') ?>"></div>

            <div class="form-group full"><label>Excerpt (short summary for cards &amp; SEO)</label><textarea name="excerpt" class="form-control" style="min-height:70px;"><?= h($editing['excerpt'] ?? '') ?></textarea></div>

            <div class="form-group full">
              <label>Content *</label>
              <div id="quillToolbar">
                <span class="ql-formats">
                  <select class="ql-header"><option value="2"></option><option value="3"></option><option selected></option></select>
                </span>
                <span class="ql-formats">
                  <button class="ql-bold"></button><button class="ql-italic"></button><button class="ql-underline"></button>
                </span>
                <span class="ql-formats">
                  <button class="ql-list" value="ordered"></button><button class="ql-list" value="bullet"></button>
                </span>
                <span class="ql-formats">
                  <button class="ql-link"></button><button class="ql-image"></button><button class="ql-blockquote"></button>
                </span>
                <span class="ql-formats">
                  <button class="ql-clean"></button>
                </span>
              </div>
              <div id="quillEditor"><?= $editing['content'] ?? '' ?></div>
              <input type="hidden" name="content" id="contentInput">
            </div>

            <div class="form-group">
              <label>Featured Image</label>
              <?php if (!empty($editing['featured_image'])): ?><img src="<?= asset_url($editing['featured_image']) ?>" class="current-image"><br><?php endif; ?>
              <input type="file" name="featured_image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="draft" <?= (($editing['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= (($editing['status'] ?? '') === 'published') ? 'selected' : '' ?>>Published</option>
              </select>
              <p class="hint">Published posts appear live on the blog immediately.</p>
            </div>
            <div class="form-group">
              <label>Published Date</label>
              <input type="datetime-local" name="published_at" class="form-control" value="<?= !empty($editing['published_at']) ? date('Y-m-d\TH:i', strtotime($editing['published_at'])) : '' ?>">
              <p class="hint">Leave blank to use the current date/time when publishing.</p>
            </div>
          </div>
          <button type="submit" class="btn btn-primary ico-text"><?= icon('save', 16) ?> Save Post</button>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
      var quill = new Quill('#quillEditor', { theme: 'snow', modules: { toolbar: '#quillToolbar' } });
      document.getElementById('postForm').addEventListener('submit', function () {
        document.getElementById('contentInput').value = quill.root.innerHTML;
      });
    </script>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Blog Posts';
$filterStatus = $_GET['status'] ?? '';
$sql = "SELECT bp.*, bc.name AS cat_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id";
$params = [];
if (in_array($filterStatus, ['draft', 'published'], true)) {
    $sql .= " WHERE bp.status = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY bp.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head">
    <h3>All Blog Posts (<?= count($posts) ?>)</h3>
    <div style="display:flex;gap:10px;">
      <div class="filter-bar">
        <select onchange="location.href=this.value">
          <option value="<?= ADMIN_URL ?>/blog.php" <?= $filterStatus === '' ? 'selected' : '' ?>>All Status</option>
          <option value="<?= ADMIN_URL ?>/blog.php?status=published" <?= $filterStatus === 'published' ? 'selected' : '' ?>>Published</option>
          <option value="<?= ADMIN_URL ?>/blog.php?status=draft" <?= $filterStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
        </select>
      </div>
      <a href="<?= ADMIN_URL ?>/blog.php?action=add" class="btn btn-primary btn-sm">+ New Post</a>
    </div>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th></th><th>Title</th><th>Category</th><th>Author</th><th>Views</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php if (!$posts): ?><tr class="empty-row"><td colspan="8">No blog posts yet. Write your first one!</td></tr><?php endif; ?>
        <?php foreach ($posts as $p): ?>
          <tr>
            <td><img src="<?= asset_url($p['featured_image']) ?>" class="row-thumb"></td>
            <td><strong><?= h(excerpt($p['title'], 45)) ?></strong></td>
            <td><?= h($p['cat_name'] ?? '—') ?></td>
            <td><?= h($p['author']) ?></td>
            <td><?= (int) $p['views'] ?></td>
            <td><span class="badge <?= $p['status'] === 'published' ? 'badge-green' : 'badge-warn' ?>"><?= h(ucfirst($p['status'])) ?></span></td>
            <td><span class="help-text"><?= $p['published_at'] ? format_date($p['published_at']) : '—' ?></span></td>
            <td>
              <div class="row-actions">
                <a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($p['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
                <a href="<?= ADMIN_URL ?>/blog.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this blog post?">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
