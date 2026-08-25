<?php
/**
 * Admin layout header. Expects $pageTitle and $activeNav to be set by the
 * including page. Requires auth.php to have been included first.
 */
$admin = current_admin();
$logo = setting($pdo, 'logo', 'assets/img/logo.png');

$navItems = [
  ['group' => 'Overview', 'items' => [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid', 'href' => ADMIN_URL . '/index.php'],
  ]],
  ['group' => 'Content', 'items' => [
    ['key' => 'programs', 'label' => 'Programs', 'icon' => 'leaf', 'href' => ADMIN_URL . '/programs.php'],
    ['key' => 'projects', 'label' => 'Projects', 'icon' => 'box', 'href' => ADMIN_URL . '/projects.php'],
    ['key' => 'impact-reports', 'label' => 'Impact & Reports', 'icon' => 'trending-up', 'href' => ADMIN_URL . '/impact-reports.php'],
    ['key' => 'products', 'label' => 'Farm Products', 'icon' => 'shopping-bag', 'href' => ADMIN_URL . '/products.php'],
    ['key' => 'team', 'label' => 'Team & Board', 'icon' => 'users', 'href' => ADMIN_URL . '/team.php'],
    ['key' => 'testimonials', 'label' => 'Testimonials', 'icon' => 'message', 'href' => ADMIN_URL . '/testimonials.php'],
    ['key' => 'stats', 'label' => 'Impact Stats', 'icon' => 'trending-up', 'href' => ADMIN_URL . '/stats.php'],
  ]],
  ['group' => 'Blog', 'items' => [
    ['key' => 'blog', 'label' => 'Blog Posts', 'icon' => 'newspaper', 'href' => ADMIN_URL . '/blog.php'],
    ['key' => 'blog-categories', 'label' => 'Categories', 'icon' => 'tag', 'href' => ADMIN_URL . '/blog-categories.php'],
  ]],
  ['group' => 'Sales', 'items' => [
    ['key' => 'orders', 'label' => 'Orders', 'icon' => 'box', 'href' => ADMIN_URL . '/orders.php'],
  ]],
  ['group' => 'Engagement', 'items' => [
    ['key' => 'messages', 'label' => 'Messages', 'icon' => 'mail', 'href' => ADMIN_URL . '/messages.php'],
    ['key' => 'subscribers', 'label' => 'Subscribers', 'icon' => 'send', 'href' => ADMIN_URL . '/subscribers.php'],
  ]],
  ['group' => 'Site', 'items' => [
    ['key' => 'settings', 'label' => 'Site Settings', 'icon' => 'settings', 'href' => ADMIN_URL . '/settings.php'],
  ]],
];

$unreadCount = (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
try {
    $pendingOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();
} catch (PDOException $e) {
    $pendingOrders = 0; // orders table not migrated yet
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle ?? 'Dashboard') ?> — Admin | BetterLife International</title>
<link rel="icon" href="<?= asset_url($logo) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= ADMIN_URL ?>/assets/css/admin.css?v=<?= @filemtime(__DIR__ . '/../assets/css/admin.css') ?: time() ?>">
</head>
<body>
<div class="admin-layout">
  <aside class="sidebar" id="adminSidebar">
    <div class="brand">
      <img src="<?= asset_url($logo) ?>" alt="Logo">
      <div><strong></strong><span>Content Dashboard</span></div>
    </div>
    <nav>
      <?php foreach ($navItems as $group): ?>
        <div class="nav-label"><?= h($group['group']) ?></div>
        <?php foreach ($group['items'] as $item): ?>
          <a href="<?= $item['href'] ?>" class="nav-link <?= ($activeNav ?? '') === $item['key'] ? 'active' : '' ?>">
            <span class="ico"><?= icon($item['icon'], 18) ?></span> <?= h($item['label']) ?>
            <?php if ($item['key'] === 'messages' && $unreadCount > 0): ?>
              <span class="badge badge-red" style="margin-left:auto;"><?= $unreadCount ?></span>
            <?php endif; ?>
            <?php if ($item['key'] === 'orders' && $pendingOrders > 0): ?>
              <span class="badge badge-green" style="margin-left:auto;"><?= $pendingOrders ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
      <a href="<?= SITE_URL ?>/index.php" target="_blank" class="ico-text"><?= icon('external-link', 15) ?> View Live Site</a>
      <a href="<?= ADMIN_URL ?>/logout.php" class="ico-text"><?= icon('log-out', 15) ?> Logout</a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar-admin">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-toggle" id="adminToggle"><?= icon('menu', 22) ?></button>
        <div>
          <h1><?= h($pageTitle ?? 'Dashboard') ?></h1>
        </div>
      </div>
      <div class="admin-user">
        <div class="av"><?= h(strtoupper(substr($admin['name'] ?? 'A', 0, 1))) ?></div>
        <span><?= h($admin['name'] ?? 'Admin') ?></span>
      </div>
    </div>
    <div class="content">
      <?php $flash = flash_get(); if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
      <?php endif; ?>
