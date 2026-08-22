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

    if (($_POST['form'] ?? '') === 'payments') {
        $stmtUpsert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmtUpsert->execute(['admin_alert_email', trim($_POST['admin_alert_email'] ?? '')]);
        $stmtUpsert->execute(['smtp_host', trim($_POST['smtp_host'] ?? '')]);
        $stmtUpsert->execute(['smtp_port', trim($_POST['smtp_port'] ?? '587')]);
        $stmtUpsert->execute(['smtp_username', trim($_POST['smtp_username'] ?? '')]);
        $stmtUpsert->execute(['smtp_from_name', trim($_POST['smtp_from_name'] ?? '')]);
        $stmtUpsert->execute(['pesapal_sandbox', isset($_POST['pesapal_sandbox']) ? '1' : '0']);
        if (trim($_POST['smtp_app_password'] ?? '') !== '') {
            $stmtUpsert->execute(['smtp_app_password', trim($_POST['smtp_app_password'])]);
        }
        if (trim($_POST['pesapal_consumer_key'] ?? '') !== '') {
            $stmtUpsert->execute(['pesapal_consumer_key', trim($_POST['pesapal_consumer_key'])]);
        }
        if (trim($_POST['pesapal_consumer_secret'] ?? '') !== '') {
            $stmtUpsert->execute(['pesapal_consumer_secret', trim($_POST['pesapal_consumer_secret'])]);
        }
        flash_set('success', 'Payment & email settings updated.');
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
    foreach (range(1, 12) as $n) {
        if ($heroImg = handle_image_upload($_FILES["hero_image_$n"] ?? [], 'settings', $errors)) {
            $stmtUpsert->execute(["hero_image_$n", $heroImg]);
        }
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
      </div>
      <label style="display:block;margin:8px 0 12px;">Hero Photos (12 shown scrolling across the full-width homepage banner)</label>
      <div class="form-grid">
        <?php for ($n = 1; $n <= 12; $n++): ?>
          <div class="form-group">
            <label>Photo <?= $n ?></label>
            <img src="<?= asset_url(setting($pdo, "hero_image_$n")) ?>" class="current-image"><br>
            <input type="file" name="hero_image_<?= $n ?>" class="form-control" accept="image/*">
          </div>
        <?php endfor; ?>
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

  <button type="submit" class="btn btn-primary ico-text"><?= icon('save', 16) ?> Save All Settings</button>
</form>

<div class="panel" style="margin-top:26px;">
  <div class="panel-head"><h3>Payments &amp; Email</h3></div>
  <div class="panel-body">
    <p class="help-text" style="margin-bottom:20px;">Powers online checkout (card &amp; mobile money via Pesapal) and order emails. Secret fields show blank for security — leave them blank to keep the current saved value.</p>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="form" value="payments">

      <h4 style="margin-bottom:14px;">Order Alerts</h4>
      <div class="form-grid">
        <div class="form-group"><label>Admin Alert Email</label><input type="email" name="admin_alert_email" class="form-control" value="<?= $v('admin_alert_email') ?>"><p class="hint">Receives an email every time a customer places or pays for an order.</p></div>
      </div>

      <h4 style="margin:22px 0 14px;">Outgoing Email (SMTP)</h4>
      <div class="form-grid">
        <div class="form-group"><label>SMTP Host</label><input type="text" name="smtp_host" class="form-control" value="<?= $v('smtp_host') ?>"></div>
        <div class="form-group"><label>SMTP Port</label><input type="number" name="smtp_port" class="form-control" value="<?= $v('smtp_port') ?>"></div>
        <div class="form-group"><label>SMTP Username / Email</label><input type="text" name="smtp_username" class="form-control" value="<?= $v('smtp_username') ?>"></div>
        <div class="form-group"><label>SMTP App Password</label><input type="password" name="smtp_app_password" class="form-control" placeholder="•••••••• (leave blank to keep current)" autocomplete="new-password"><p class="hint">Use a Gmail <strong>App Password</strong>, not your normal password — generate one at myaccount.google.com/apppasswords.</p></div>
        <div class="form-group"><label>"From" Name</label><input type="text" name="smtp_from_name" class="form-control" value="<?= $v('smtp_from_name') ?>"></div>
      </div>

      <h4 style="margin:22px 0 14px;">Pesapal (Card &amp; Mobile Money)</h4>
      <div class="form-grid">
        <div class="form-group"><label>Consumer Key</label><input type="password" name="pesapal_consumer_key" class="form-control" placeholder="•••••••• (leave blank to keep current)" autocomplete="new-password"></div>
        <div class="form-group"><label>Consumer Secret</label><input type="password" name="pesapal_consumer_secret" class="form-control" placeholder="•••••••• (leave blank to keep current)" autocomplete="new-password"></div>
        <div class="form-group full">
          <div class="checkbox-row"><input type="checkbox" name="pesapal_sandbox" id="pesapal_sandbox" <?= setting($pdo, 'pesapal_sandbox') === '1' ? 'checked' : '' ?>><label for="pesapal_sandbox" style="margin:0;">Sandbox / test mode (no real money moves)</label></div>
          <p class="hint">Leave unchecked for LIVE payments. Get your keys from pay.pesapal.com under Settings → API.</p>
        </div>
      </div>
      <button type="submit" class="btn btn-accent ico-text"><?= icon('save', 16) ?> Save Payment &amp; Email Settings</button>
    </form>
  </div>
</div>

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
