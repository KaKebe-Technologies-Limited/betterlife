<?php
/**
 * Public site header. Expects (optionally) $pageTitle and $activePage to be
 * set by the including page before this file is required.
 */
require_once __DIR__ . '/functions.php';

// Maintenance mode: public visitors get a holding page; logged-in admins pass through.
if (setting($pdo, 'maintenance_mode') === '1' && !is_logged_in()) {
    http_response_code(503);
    header('Retry-After: 3600');
    require __DIR__ . '/maintenance.php';
    exit;
}

$pageTitle       = $pageTitle ?? setting($pdo, 'site_name', 'BetterLife International');
$activePage      = $activePage ?? '';
$siteName        = setting($pdo, 'site_name', 'BetterLife International');
$logo            = setting($pdo, 'logo', 'assets/img/logo.png');
$pageDescription = $pageDescription ?? excerpt(setting($pdo, 'hero_subtitle'), 160);
$pageImage       = $pageImage ?? setting($pdo, 'hero_image_1', $logo);
$requestPath     = '/' . ltrim($_SERVER['REQUEST_URI'] ?? '', '/');
// REQUEST_URI already includes the site folder when the site lives in a subfolder (e.g. /betterlife)
$canonicalUrl    = public_base_url() . (SITE_URL !== '' && str_starts_with($requestPath, SITE_URL . '/') ? '' : SITE_URL) . $requestPath;
$ogType          = $ogType ?? 'website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle) ?> | <?= h($siteName) ?></title>
<meta name="description" content="<?= h($pageDescription) ?>">
<link rel="canonical" href="<?= h($canonicalUrl) ?>">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#0b3d2e">

<!-- Open Graph / Facebook / WhatsApp -->
<meta property="og:type" content="<?= h($ogType) ?>">
<meta property="og:site_name" content="<?= h($siteName) ?>">
<meta property="og:title" content="<?= h($pageTitle) ?>">
<meta property="og:description" content="<?= h($pageDescription) ?>">
<meta property="og:url" content="<?= h($canonicalUrl) ?>">
<meta property="og:image" content="<?= h(full_asset_url($pageImage)) ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= h($pageTitle) ?>">
<meta name="twitter:description" content="<?= h($pageDescription) ?>">
<meta name="twitter:image" content="<?= h(full_asset_url($pageImage)) ?>">

<link rel="icon" href="<?= asset_url(setting($pdo, 'favicon', 'assets/img/favicon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?: time() ?>">
<?php // Optional per-page extras: $pageStyles (paths under the site root) and raw $pageHead markup
foreach ($pageStyles ?? [] as $css): ?>
<link rel="stylesheet" href="<?= SITE_URL . '/' . $css ?>?v=<?= @filemtime(__DIR__ . '/../' . $css) ?: time() ?>">
<?php endforeach; ?>
<?= $pageHead ?? '' ?>

<?php if ($activePage === 'home'): ?>
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'NGO',
    'name' => $siteName,
    'url' => public_base_url() . SITE_URL . '/index.php',
    'logo' => full_asset_url($logo),
    'description' => $pageDescription,
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => setting($pdo, 'address')],
    'email' => setting($pdo, 'email'),
    'telephone' => setting($pdo, 'phone'),
    'sameAs' => array_values(array_filter([
        setting($pdo, 'facebook'), setting($pdo, 'twitter'), setting($pdo, 'instagram'), setting($pdo, 'linkedin'), setting($pdo, 'youtube'),
    ])),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>
</head>
<body class="<?= h($activePage) ?>">
<?php if ($activePage === 'home'): ?><div class="home-shell"><?php endif; ?>

<?php
$phone     = setting($pdo, 'phone');
$email     = setting($pdo, 'email');
$tagline   = setting($pdo, 'tagline');
$cartCount = !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0;
$socials   = array_filter([
    'facebook'  => ['Facebook', setting($pdo, 'facebook')],
    'x-twitter' => ['X (Twitter)', setting($pdo, 'twitter')],
    'instagram' => ['Instagram', setting($pdo, 'instagram')],
    'linkedin'  => ['LinkedIn', setting($pdo, 'linkedin')],
    'youtube'   => ['YouTube', setting($pdo, 'youtube')],
], fn($s) => $s[1] !== '');
$cartLink = function (int $size) use ($cartCount): string {
    $badge = $cartCount ? '<span class="cart-count">' . $cartCount . '</span>' : '';
    return '<a href="' . SITE_URL . '/cart.php" class="cart-link" aria-label="Cart' . ($cartCount ? " ($cartCount items)" : '') . '">' . icon('shopping-bag', $size) . $badge . '</a>';
};
?>
<!-- Utility bar: contact details, social links, shop & cart -->
<div class="topbar">
  <div class="container">
    <div class="topbar-contact">
      <?php if ($phone): ?><a href="tel:<?= h(preg_replace('/\s+/', '', $phone)) ?>"><?= icon('phone', 14) ?> <?= h($phone) ?></a><?php endif; ?>
      <?php if ($email): ?><a href="mailto:<?= h($email) ?>"><?= icon('mail', 14) ?> <?= h($email) ?></a><?php endif; ?>
    </div>
    <div class="topbar-links">
      <?php if ($socials): ?>
        <div class="topbar-social">
          <?php foreach ($socials as $iconName => [$label, $url]): ?>
            <a href="<?= h($url) ?>" target="_blank" rel="noopener" aria-label="<?= h($label) ?>"><?= icon($iconName, 14) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <a href="<?= SITE_URL ?>/products.php" class="topbar-shop"><?= icon('basket', 14) ?> Shop the Farm</a>
      <?= $cartLink(17) ?>
    </div>
  </div>
</div>

<!-- Masthead: logo + tagline + primary call to action -->
<header class="site-header">
  <div class="container">
    <a href="<?= SITE_URL ?>/index.php" class="brand">
      <img src="<?= asset_url($logo) ?>" alt="<?= h($siteName) ?>" width="700" height="144">
      <?php if ($tagline): ?><span class="brand-tagline"><?= h($tagline) ?></span><?php endif; ?>
    </a>
    <div class="header-actions">
      <span class="mobile-cart"><?= $cartLink(21) ?></span>
      <a href="<?= SITE_URL ?>/contact.php?subject=Partnership enquiry" class="btn-pill-accent header-cta"><?= icon('heart', 15) ?> Support Our Work</a>
      <button type="button" class="nav-toggle" id="navToggle" aria-label="Open menu" aria-controls="mainNav" aria-expanded="false"><?= icon('menu', 24) ?></button>
    </div>
  </div>
</header>

<!-- Main navigation band -->
<nav class="nav-bar" aria-label="Main">
  <div class="container">
    <div class="main-nav" id="mainNav">
      <div class="main-nav-head">
        <span>Menu</span>
        <button type="button" class="nav-close" aria-label="Close menu"><?= icon('x', 22) ?></button>
      </div>
      <a href="<?= SITE_URL ?>/index.php" class="<?= $activePage === 'home' ? 'active' : '' ?>">Home</a>
      <a href="<?= SITE_URL ?>/about.php" class="<?= $activePage === 'about' ? 'active' : '' ?>">About Us</a>
      <div class="nav-dropdown <?= in_array($activePage, ['programs', 'impact', 'partners']) ? 'active' : '' ?>">
        <button type="button" class="nav-dropdown-toggle" aria-haspopup="true">Our Work <?= icon('chevron-down', 15) ?></button>
        <div class="nav-dropdown-menu">
          <a href="<?= SITE_URL ?>/programs.php"><?= icon('leaf', 17) ?> Our Programmes</a>
          <a href="<?= SITE_URL ?>/projects.php"><?= icon('grid', 17) ?> Projects</a>
          <a href="<?= SITE_URL ?>/partners.php"><?= icon('heart', 17) ?> Our Partners</a>
          <a href="<?= SITE_URL ?>/impact-reports.php"><?= icon('trending-up', 17) ?> Impact &amp; Reports</a>
        </div>
      </div>
      <a href="<?= SITE_URL ?>/farm.php" class="<?= in_array($activePage, ['farm', 'products']) ? 'active' : '' ?>">BetterLife Farm</a>
      <a href="<?= SITE_URL ?>/team.php" class="<?= $activePage === 'team' ? 'active' : '' ?>">Our Team</a>
      <a href="<?= SITE_URL ?>/blog.php" class="<?= $activePage === 'blog' ? 'active' : '' ?>">Stories</a>
      <a href="<?= SITE_URL ?>/contact.php" class="<?= $activePage === 'contact' ? 'active' : '' ?>">Contact</a>
      <div class="main-nav-foot">
        <a href="<?= SITE_URL ?>/contact.php?subject=Partnership enquiry" class="btn-pill-accent"><?= icon('heart', 15) ?> Support Our Work</a>
        <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary btn-sm"><?= icon('basket', 15) ?> Shop the Farm</a>
        <?php if ($phone): ?><a href="tel:<?= h(preg_replace('/\s+/', '', $phone)) ?>" class="main-nav-contact"><?= icon('phone', 15) ?> <?= h($phone) ?></a><?php endif; ?>
        <?php if ($email): ?><a href="mailto:<?= h($email) ?>" class="main-nav-contact"><?= icon('mail', 15) ?> <?= h($email) ?></a><?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div class="nav-backdrop" id="navBackdrop" hidden></div>
