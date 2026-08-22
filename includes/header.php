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
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="<?= SITE_URL ?>/index.php" class="brand">
      <img src="<?= asset_url($logo) ?>" alt="<?= h($siteName) ?>">
      <span><?= h($siteName) ?><small>Sustainable Livelihoods · Green Skills</small></span>
    </a>

    <nav class="main-nav" id="mainNav">
      <a href="<?= SITE_URL ?>/index.php" class="<?= $activePage === 'home' ? 'active' : '' ?>">Home</a>
      <a href="<?= SITE_URL ?>/about.php" class="<?= $activePage === 'about' ? 'active' : '' ?>">About Us</a>
      <a href="<?= SITE_URL ?>/programs.php" class="<?= $activePage === 'programs' ? 'active' : '' ?>">Our Programs</a>
      <a href="<?= SITE_URL ?>/products.php" class="<?= $activePage === 'products' ? 'active' : '' ?>">BetterLife Farm</a>
      <a href="<?= SITE_URL ?>/team.php" class="<?= $activePage === 'team' ? 'active' : '' ?>">Our Team</a>
      <a href="<?= SITE_URL ?>/blog.php" class="<?= $activePage === 'blog' ? 'active' : '' ?>">Blog</a>
      <a href="<?= SITE_URL ?>/contact.php" class="<?= $activePage === 'contact' ? 'active' : '' ?>">Contact</a>
    </nav>

    <div class="header-actions">
      <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary btn-sm"><span class="label">Shop the Farm</span> 🌿</a>
      <button class="nav-toggle" id="navToggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
