<?php
/**
 * Shown to the public when maintenance mode is ON (Admin -> Site Settings).
 * Logged-in admins bypass this and see the live site. Rendered by
 * includes/header.php before any page output; expects $pdo in scope.
 */
$siteName = setting($pdo, 'site_name', 'BetterLife International');
$logo     = setting($pdo, 'logo', 'assets/img/logo.png');
$message  = setting($pdo, 'maintenance_message', 'We are carrying out a short update to improve the site. Please check back again shortly.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= h($siteName) ?> &middot; We&rsquo;ll be back soon</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
    background:#0b3d2e;color:#eaf3ee;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
  .box{max-width:520px;text-align:center}
  .box img{height:54px;width:auto;margin-bottom:28px}
  h1{font-size:26px;line-height:1.25;margin-bottom:14px;color:#fff}
  p{font-size:16px;line-height:1.6;color:#cfe3d8}
  .dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#7bdcb5;margin-right:8px;vertical-align:middle}
  .tag{font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:#7bdcb5;margin-bottom:20px}
  .foot{margin-top:34px;font-size:13px;color:#8fb3a5}
</style>
</head>
<body>
  <div class="box">
    <img src="<?= asset_url($logo) ?>" alt="<?= h($siteName) ?>">
    <div class="tag"><span class="dot"></span>Maintenance in progress</div>
    <h1>We&rsquo;ll be back soon</h1>
    <p><?= nl2br(h($message)) ?></p>
    <div class="foot">&copy; <?= date('Y') ?> <?= h($siteName) ?></div>
  </div>
</body>
</html>
