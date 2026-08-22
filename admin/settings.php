<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'settings';
$pageTitle = 'Site Settings';

$textFields = [
    'site_name', 'tagline', 'founded_year',
    'hero_title', 'hero_subtitle',
    'about_who_title', 'about_who_text', 'mission_text', 'vision_text',
    'farm_title', 'farm_text',
    'address', 'phone', 'email', 'shop_email',
    'facebook', 'twitter', 'instagram', 'linkedin', 'youtube',
    'footer_about', 'board_quote', 'board_quote_author', 'map_embed',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/settings.php');
    }

    if (($_POST['form'] ?? '') === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $admin = current_admin();
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$admin['id']]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($current, $row['password'])) {
            flash_set('error', 'Current password is incorrect.');
        } elseif (strlen($new) < 8) {
            flash_set('error', 'New password must be at least 8 characters.');
        } elseif ($new !== $confirm) {
            flash_set('error', 'New password and confirmation do not match.');
        } else {
            $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?")->execute([password_hash($new, PASSWORD_BCRYPT), $admin['id']]);
            flash_set('success', 'Password updated successfully.');
        }
        redirect(ADMIN_URL . '/settings.php');
    }

    $stmtUpsert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach ($textFields as $field) {
        $value = trim($_POST[$field] ?? '');
        $stmtUpsert->execute([$field, $value]);
    }

    $errors = [];
    if ($logo = handle_image_upload($_FILES['logo'] ?? [], 'settings', $errors)) {
        $stmtUpsert->execute(['logo', $logo]);
    }
    if ($hero = handle_image_upload($_FILES['hero_image'] ?? [], 'settings', $errors)) {
        $stmtUpsert->execute(['hero_image', $hero]);
    }
    if ($about = handle_image_upload($_FILES['about_image'] ?? [], 'settings', $errors)) {
        $stmtUpsert->execute(['about_image', $about]);
    }
    if ($farm = handle_image_upload($_FILES['farm_image'] ?? [], 'settings', $errors)) {
        $stmtUpsert->execute(['farm_image', $farm]);
    }

    flash_set('success', 'Site settings updated successfully.');
    redirect(ADMIN_URL . '/settings.php');
}

require __DIR__ . '/includes/header.php';
$v = fn($k) => h(setting($pdo, $k));
?>
<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="panel">
    <div class="panel-head"><h3>General</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group"><label>Site Name</label><input type="text" name="site_name" class="form-control" value="<?= $v('site_name') ?>"></div>
        <div class="form-group"><label>Tagline</label><input type="text" name="tagline" class="form-control" value="<?= $v('tagline') ?>"></div>
        <div class="form-group"><label>Founded Year</label><input type="text" name="founded_year" class="form-control" value="<?= $v('founded_year') ?>"></div>
        <div class="form-group">
          <label>Logo</label>
          <img src="<?= asset_url(setting($pdo, 'logo')) ?>" class="current-image"><br>
          <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Homepage Hero</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group full"><label>Hero Title</label><input type="text" name="hero_title" class="form-control" value="<?= $v('hero_title') ?>"></div>
        <div class="form-group full"><label>Hero Subtitle</label><textarea name="hero_subtitle" class="form-control"><?= h(setting($pdo, 'hero_subtitle')) ?></textarea></div>
        <div class="form-group">
          <label>Hero Background Image</label>
          <img src="<?= asset_url(setting($pdo, 'hero_image')) ?>" class="current-image"><br>
          <input type="file" name="hero_image" class="form-control" accept="image/*">
        </div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>About Page</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group"><label>Section Title</label><input type="text" name="about_who_title" class="form-control" value="<?= $v('about_who_title') ?>"></div>
        <div class="form-group">
          <label>About Image</label>
          <img src="<?= asset_url(setting($pdo, 'about_image')) ?>" class="current-image"><br>
          <input type="file" name="about_image" class="form-control" accept="image/*">
        </div>
        <div class="form-group full"><label>Who We Are (paragraphs separated by blank lines)</label><textarea name="about_who_text" class="form-control" style="min-height:180px;"><?= h(setting($pdo, 'about_who_text')) ?></textarea></div>
        <div class="form-group"><label>Mission Statement</label><textarea name="mission_text" class="form-control"><?= h(setting($pdo, 'mission_text')) ?></textarea></div>
        <div class="form-group"><label>Vision Statement</label><textarea name="vision_text" class="form-control"><?= h(setting($pdo, 'vision_text')) ?></textarea></div>
        <div class="form-group full"><label>Board Quote</label><textarea name="board_quote" class="form-control"><?= h(setting($pdo, 'board_quote')) ?></textarea></div>
        <div class="form-group"><label>Board Quote Author</label><input type="text" name="board_quote_author" class="form-control" value="<?= $v('board_quote_author') ?>"></div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>BetterLife Farm</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group"><label>Farm Section Title</label><input type="text" name="farm_title" class="form-control" value="<?= $v('farm_title') ?>"></div>
        <div class="form-group">
          <label>Farm Image</label>
          <img src="<?= asset_url(setting($pdo, 'farm_image')) ?>" class="current-image"><br>
          <input type="file" name="farm_image" class="form-control" accept="image/*">
        </div>
        <div class="form-group full"><label>Farm Description</label><textarea name="farm_text" class="form-control" style="min-height:140px;"><?= h(setting($pdo, 'farm_text')) ?></textarea></div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Contact &amp; Social</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group"><label>Address</label><input type="text" name="address" class="form-control" value="<?= $v('address') ?>"></div>
        <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= $v('phone') ?>"></div>
        <div class="form-group"><label>General Email</label><input type="email" name="email" class="form-control" value="<?= $v('email') ?>"></div>
        <div class="form-group"><label>Farm Orders Email</label><input type="email" name="shop_email" class="form-control" value="<?= $v('shop_email') ?>"></div>
        <div class="form-group"><label>Facebook URL</label><input type="text" name="facebook" class="form-control" value="<?= $v('facebook') ?>"></div>
        <div class="form-group"><label>Twitter / X URL</label><input type="text" name="twitter" class="form-control" value="<?= $v('twitter') ?>"></div>
        <div class="form-group"><label>Instagram URL</label><input type="text" name="instagram" class="form-control" value="<?= $v('instagram') ?>"></div>
        <div class="form-group"><label>LinkedIn URL</label><input type="text" name="linkedin" class="form-control" value="<?= $v('linkedin') ?>"></div>
        <div class="form-group"><label>YouTube URL</label><input type="text" name="youtube" class="form-control" value="<?= $v('youtube') ?>"></div>
        <div class="form-group full"><label>Footer About Text</label><textarea name="footer_about" class="form-control"><?= h(setting($pdo, 'footer_about')) ?></textarea></div>
        <div class="form-group full"><label>Google Maps Embed Code (optional, &lt;iframe&gt;)</label><textarea name="map_embed" class="form-control"><?= h(setting($pdo, 'map_embed')) ?></textarea></div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">💾 Save All Settings</button>
</form>

<div class="panel" style="margin-top:26px;">
  <div class="panel-head"><h3>Change Admin Password</h3></div>
  <div class="panel-body">
    <form method="post" style="max-width:420px;">
      <?= csrf_field() ?>
      <input type="hidden" name="form" value="password">
      <div class="form-group"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
      <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control" required minlength="8"></div>
      <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" class="form-control" required minlength="8"></div>
      <button type="submit" class="btn btn-accent">Update Password</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
