<?php
require_once __DIR__ . '/includes/functions.php';
http_response_code(404);
$pageTitle = 'Page Not Found';
$pageDescription = 'The page you are looking for could not be found.';
$activePage = '';
require __DIR__ . '/includes/header.php';
?>

<section class="container-narrow" style="padding:120px 24px;text-align:center;">
  <span class="eyebrow" style="justify-content:center;">404</span>
  <h1>Page Not Found</h1>
  <p class="muted" style="margin-bottom:30px;">The page you're looking for may have moved or no longer exists.</p>
  <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
    <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">Back to Home</a>
    <a href="<?= SITE_URL ?>/products.php" class="btn btn-outline-dark">Shop BetterLife Farm</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
