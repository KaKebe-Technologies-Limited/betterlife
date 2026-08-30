<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect(ADMIN_URL . '/index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin'] = ['id' => $admin['id'], 'name' => $admin['name'], 'username' => $admin['username'], 'email' => $admin['email']];
            redirect(ADMIN_URL . '/index.php');
        }
        $error = 'Invalid username or password.';
    }
}

$logo = setting($pdo, 'logo', 'assets/img/logo.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | BetterLife International</title>
<link rel="icon" href="<?= asset_url(setting($pdo, 'favicon', 'assets/img/favicon.png')) ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= ADMIN_URL ?>/assets/css/admin.css?v=<?= @filemtime(__DIR__ . '/assets/css/admin.css') ?: time() ?>">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="logo"><img src="<?= asset_url($logo) ?>" alt="Logo"><strong></strong></div>
    <h1>Welcome back</h1>
    <p class="sub">Sign in to manage your site's content.</p>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
      <?= csrf_field() ?>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required autofocus value="<?= h($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Sign In →</button>
    </form>
    <p class="help-text" style="margin-top:20px;text-align:center;"><a href="<?= SITE_URL ?>/index.php">← Back to website</a></p>
  </div>
</div>
</body>
</html>
