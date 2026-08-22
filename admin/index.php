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

try {
    $orderCounts = $pdo->query("SELECT COUNT(*) AS c, COALESCE(SUM(CASE WHEN status='paid' THEN total_amount ELSE 0 END),0) AS revenue FROM orders")->fetch();
    $recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $orderCounts = ['c' => 0, 'revenue' => 0];
    $recentOrders = [];
}

require __DIR__ . '/includes/header.php';
?>

<div class="stat-cards">
  <div class="stat-card">
    <div class="ico" style="background:var(--a-green-100);color:var(--a-green-700);"><?= icon('shopping-bag', 22) ?></div>
    <div><strong><?= $counts['products'] ?></strong><span>Farm Products</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:#fff3da;color:#93670a;"><?= icon('box', 22) ?></div>
    <div><strong><?= (int) $orderCounts['c'] ?></strong><span><?= format_price((float) $orderCounts['revenue']) ?> paid revenue</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:var(--a-blue-100);color:var(--a-blue-700);"><?= icon('newspaper', 22) ?></div>
    <div><strong><?= $counts['published'] ?> / <?= $counts['posts'] ?></strong><span>Published Blog Posts</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:#eef0ff;color:#4b4fd1;"><?= icon('users', 22) ?></div>
    <div><strong><?= $counts['team'] ?></strong><span>Team &amp; Board Members</span></div>
  </div>
  <div class="stat-card">
    <div class="ico" style="background:#fdeaea;color:var(--a-danger);"><?= icon('mail', 22) ?></div>
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
    <h3>Recent Orders</h3>
    <a href="<?= ADMIN_URL ?>/orders.php" class="btn btn-outline btn-sm">View All →</a>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Order Ref</th><th>Customer</th><th>Total</th><th>Status</th><th>Placed</th></tr></thead>
      <tbody>
        <?php if (!$recentOrders): ?>
          <tr class="empty-row"><td colspan="5">No orders yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><a href="<?= ADMIN_URL ?>/orders.php?view=<?= $o['id'] ?>"><strong><?= h($o['order_ref']) ?></strong></a></td>
            <td><?= h($o['customer_name']) ?></td>
            <td><?= format_price($o['total_amount']) ?></td>
            <td><span class="badge <?= $o['status'] === 'paid' ? 'badge-green' : ($o['status'] === 'failed' ? 'badge-red' : 'badge-warn') ?>"><?= h(ucfirst($o['status'])) ?></span></td>
            <td><?= time_ago($o['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
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
    <a href="<?= ADMIN_URL ?>/settings.php" class="btn btn-outline ico-text"><?= icon('settings', 15) ?> Edit Site Settings</a>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
