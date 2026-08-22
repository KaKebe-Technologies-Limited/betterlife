<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

$counts = [
  'products'    => (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
  'posts'       => (int) $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
  'published'   => (int) $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status='published'")->fetchColumn(),
  'team'        => (int) $pdo->query("SELECT COUNT(*) FROM team_members")->fetchColumn(),
  'messages'    => (int) $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
  'unread'      => (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn(),
  'subscribers' => (int) $pdo->query("SELECT COUNT(*) FROM newsletter_subscribers")->fetchColumn(),
  'programs'    => (int) $pdo->query("SELECT COUNT(*) FROM programs")->fetchColumn(),
];

$recentMessages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentPosts = $pdo->query("SELECT bp.*, bc.name AS cat_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id=bp.category_id ORDER BY bp.created_at DESC LIMIT 5")->fetchAll();
$topProducts = $pdo->query("SELECT * FROM products ORDER BY featured DESC, sort_order LIMIT 5")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="stat-cards">
  <div class="stat-card">
    <div class="ico" style="background:var(--a-green-100);">🍯</div>
    <div><strong><?= $counts['products'] ?></strong><span>Farm Products</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:var(--a-blue-100);">📰</div>
    <div><strong><?= $counts['published'] ?> / <?= $counts['posts'] ?></strong><span>Published Blog Posts</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:#fff3da;">👥</div>
    <div><strong><?= $counts['team'] ?></strong><span>Team &amp; Board Members</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:#fdeaea;">✉️</div>
    <div><strong><?= $counts['messages'] ?></strong><span><?= $counts['unread'] ?> unread messages</span></div>
  </div>
</div>

<div class="form-grid">
  <div class="panel">
    <div class="panel-head">
      <h3>Recent Contact Messages</h3>
      <a href="<?= ADMIN_URL ?>/messages.php" class="btn btn-outline btn-sm">View All →</a>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Subject</th><th>Received</th><th></th></tr></thead>
        <tbody>
          <?php if (!$recentMessages): ?>
            <tr class="empty-row"><td colspan="4">No messages yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($recentMessages as $m): ?>
            <tr>
              <td><strong><?= h($m['name']) ?></strong><?php if (!$m['is_read']): ?> <span class="badge badge-red">New</span><?php endif; ?></td>
              <td><?= h(excerpt($m['subject'] ?: $m['message'], 30)) ?></td>
              <td><?= time_ago($m['created_at']) ?></td>
              <td><a href="<?= ADMIN_URL ?>/messages.php?view=<?= $m['id'] ?>" class="btn btn-outline btn-sm">Open</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h3>Recent Blog Posts</h3>
      <a href="<?= ADMIN_URL ?>/blog.php" class="btn btn-outline btn-sm">Manage →</a>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Title</th><th>Category</th><th>Status</th></tr></thead>
        <tbody>
          <?php if (!$recentPosts): ?>
            <tr class="empty-row"><td colspan="3">No posts yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($recentPosts as $p): ?>
            <tr>
              <td><?= h(excerpt($p['title'], 40)) ?></td>
              <td><?= h($p['cat_name'] ?? '—') ?></td>
              <td><span class="badge <?= $p['status'] === 'published' ? 'badge-green' : 'badge-gray' ?>"><?= h(ucfirst($p['status'])) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h3>Quick Actions</h3>
  </div>
  <div class="panel-body" style="display:flex;gap:14px;flex-wrap:wrap;">
    <a href="<?= ADMIN_URL ?>/blog.php?action=add" class="btn btn-primary">+ New Blog Post</a>
    <a href="<?= ADMIN_URL ?>/products.php?action=add" class="btn btn-accent">+ New Farm Product</a>
    <a href="<?= ADMIN_URL ?>/team.php?action=add" class="btn btn-outline">+ Add Team Member</a>
    <a href="<?= ADMIN_URL ?>/settings.php" class="btn btn-outline">⚙️ Edit Site Settings</a>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
