<?php
/**
 * Public site header. Expects (optionally) $pageTitle and $activePage to be
 * set by the including page before this file is required.
 */
require_once __DIR__ . '/functions.php';

$pageTitle  = $pageTitle ?? setting($pdo, 'site_name', 'BetterLife International');
$activePage = $activePage ?? '';
$siteName   = setting($pdo, 'site_name', 'BetterLife International');
$logo       = setting($pdo, 'logo', 'assets/img/logo.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle) ?> | <?= h($siteName) ?></title>
<meta name="description" content="<?= h(setting($pdo, 'hero_subtitle')) ?>">
<link rel="icon" href="<?= asset_url($logo) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?: time() ?>">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="<?= SITE_URL ?>/index.php" class="brand">
      <img src="<?= asset_url($logo) ?>" alt="<?= h($siteName) ?>">
    </a>

    <nav class="main-nav" id="mainNav">
      <a href="<?= SITE_URL ?>/index.php" class="<?= $activePage === 'home' ? 'active' : '' ?>">Home</a>
      <a href="<?= SITE_URL ?>/about.php" class="<?= $activePage === 'about' ? 'active' : '' ?>">About Us</a>
      <div class="nav-dropdown <?= in_array($activePage, ['programs', 'projects', 'impact']) ? 'active' : '' ?>">
        <button type="button" class="nav-dropdown-toggle">Our Work <?= icon('chevron-down', 15) ?></button>
        <div class="nav-dropdown-menu">
          <a href="<?= SITE_URL ?>/programs.php"><?= icon('leaf', 17) ?> Our Programs</a>
          <a href="<?= SITE_URL ?>/projects.php"><?= icon('box', 17) ?> Projects</a>
          <a href="<?= SITE_URL ?>/impact-reports.php"><?= icon('trending-up', 17) ?> Impact &amp; Reports</a>
        </div>
      </div>
      <a href="<?= SITE_URL ?>/products.php" class="<?= $activePage === 'products' ? 'active' : '' ?>">BetterLife Farm</a>
      <a href="<?= SITE_URL ?>/team.php" class="<?= $activePage === 'team' ? 'active' : '' ?>">Our Team</a>
      <a href="<?= SITE_URL ?>/blog.php" class="<?= $activePage === 'blog' ? 'active' : '' ?>">Blog</a>
      <a href="<?= SITE_URL ?>/contact.php" class="<?= $activePage === 'contact' ? 'active' : '' ?>">Contact</a>
    </nav>

    <div class="header-actions">
      <a href="<?= SITE_URL ?>/cart.php" class="cart-link" aria-label="Cart">
        <?= icon('shopping-bag', 20) ?>
        <?php if (!empty($_SESSION['cart']) && ($cartCount = array_sum(array_column($_SESSION['cart'], 'qty')))): ?>
          <span class="cart-count"><?= $cartCount ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary btn-sm"><span class="label">Shop the Farm</span></a>
      <button class="nav-toggle" id="navToggle" aria-label="Toggle menu"><?= icon('menu', 22) ?></button>
    </div>
  </div>
</header>
