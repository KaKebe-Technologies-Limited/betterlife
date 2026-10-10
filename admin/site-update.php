<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/site-sync.php';
$activeNav = 'site-update';
$pageTitle = 'Site Update';
$self = ADMIN_URL . '/site-update.php';

// Downloading a backup
if (isset($_GET['download'])) {
    $path = site_sync_backup_path((string) $_GET['download']);
    if (!$path) { http_response_code(404); exit('Backup not found.'); }
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . basename($path) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect($self);
    }
    $upsert = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');

    if (($_POST['action'] ?? '') === 'apply') {
        if (!isset($_POST['confirm'])) {
            flash_set('error', 'Please tick the box to confirm first.');
            redirect($self);
        }
        $stamp = site_sync_stamp(SITE_SYNC_FILE);
        $sql = $stamp ? (string) file_get_contents(SITE_SYNC_FILE) : '';
        $r = $sql !== '' ? site_sync_apply($pdo, $sql) : ['ok' => false, 'error' => 'There is no update file on this site yet.'];
        if ($r['ok']) {
            $upsert->execute(['content_sync_applied', $stamp]);
            $upsert->execute(['content_sync_applied_at', date('Y-m-d H:i')]);
        }
        $_SESSION['site_update_report'] = $r + ['kind' => 'apply'];
    }

    if (($_POST['action'] ?? '') === 'restore') {
        $path = site_sync_backup_path((string) ($_POST['backup'] ?? ''));
        $r = $path ? site_sync_apply($pdo, (string) file_get_contents($path)) : ['ok' => false, 'error' => 'That backup could not be found.'];
        if ($r['ok']) {
            $upsert->execute(['content_sync_applied', '']);   // the update file is no longer what the site shows
            $upsert->execute(['content_sync_applied_at', date('Y-m-d H:i')]);
        }
        $_SESSION['site_update_report'] = $r + ['kind' => 'restore', 'from' => basename((string) $path)];
    }
    redirect($self);
}

$report = $_SESSION['site_update_report'] ?? null;
unset($_SESSION['site_update_report']);

$stamp = site_sync_stamp(SITE_SYNC_FILE);
$applied = setting($pdo, 'content_sync_applied', '');
$appliedAt = setting($pdo, 'content_sync_applied_at', '');
$plan = null;
$planError = null;
if ($stamp) {
    try {
        $plan = site_sync_plan((string) file_get_contents(SITE_SYNC_FILE));
    } catch (Throwable $e) {
        $planError = $e->getMessage();
    }
}
$when = fn(string $s) => $s !== '' ? date('j F Y, H:i', strtotime($s)) : '';
$backupWhen = fn(string $f) => preg_match('/(\d{4}-\d{2}-\d{2})-(\d{2})(\d{2})(\d{2})/', $f, $m) ? date('j F Y, H:i', strtotime("$m[1] $m[2]:$m[3]:$m[4]")) : $f;
$backups = site_sync_backups();

require __DIR__ . '/includes/header.php';
?>

<?php if ($report): ?>
  <div class="panel" style="border-left:4px solid <?= $report['ok'] ? 'var(--a-green-600)' : 'var(--a-danger)' ?>;">
    <div class="panel-head">
      <h3><?= $report['ok']
            ? ($report['kind'] === 'restore' ? 'The backup has been put back' : 'The site content is up to date')
            : ($report['kind'] === 'restore' ? 'The backup was not put back' : 'The update did not go through') ?></h3>
    </div>
    <div class="panel-body">
      <?php if (!$report['ok']): ?>
        <p style="margin:0 0 10px;"><?= h($report['error']) ?></p>
      <?php endif; ?>
      <?php if (!empty($report['tables']) && $report['ok']): ?>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Section</th><th>Before</th><th>Now</th></tr></thead>
            <tbody>
              <?php foreach ($report['tables'] as $t => [$before, $after]): ?>
                <tr><td><?= h(SITE_SYNC_TABLES[$t] ?? $t) ?></td><td><?= $before === null ? 'new' : (int) $before ?></td><td><?= (int) $after ?></td></tr>
              <?php endforeach; ?>
              <tr><td>Site settings</td><td></td><td><?= (int) $report['settings'] ?> set<?= !empty($report['cleared']) ? ', ' . count($report['cleared']) . ' emptied (' . h(implode(', ', $report['cleared'])) . ')' : '' ?></td></tr>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
      <?php if (!empty($report['backup'])): ?>
        <p class="help-text" style="margin:12px 0 0;">What the site showed before was saved as a backup (<?= h($backupWhen($report['backup'])) ?>). It is listed below if you need to go back.</p>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class="panel" style="border-left:4px solid <?= $stamp && $stamp !== $applied ? '#c98a12' : 'var(--a-green-600)' ?>;">
  <div class="panel-head">
    <h3>Bring the site content up to date</h3>
    <?php if ($stamp): ?>
      <span class="badge <?= $stamp !== $applied ? 'badge-warn' : 'badge-green' ?>"><?= $stamp !== $applied ? 'New content waiting' : 'Up to date' ?></span>
    <?php endif; ?>
  </div>
  <div class="panel-body">
    <p style="margin:0 0 14px;max-width:72ch;">The website's content (page text, projects, team, farm products, stories, reports and site settings) is prepared on the computer where the site is built and arrives here with each code update, in an update file. This loads that file into the site, so visitors see everything exactly as it was prepared.</p>

    <?php if (!$stamp): ?>
      <p class="alert alert-error" style="margin:0;">There is no update file on this site yet (live-content-sync.sql). It arrives with the code.</p>
    <?php elseif ($planError): ?>
      <p class="alert alert-error" style="margin:0;"><?= h($planError) ?></p>
    <?php else: ?>
      <ul style="margin:0 0 16px;padding-left:18px;line-height:1.8;">
        <li>Update file prepared: <strong><?= h($when($stamp)) ?></strong></li>
        <li>Last loaded on this site: <strong><?= $applied !== '' ? h($when($applied)) . ' file, loaded ' . h($when($appliedAt)) : 'not yet' ?></strong></li>
      </ul>

      <div class="table-wrap" style="margin-bottom:16px;">
        <table class="data-table">
          <thead><tr><th>Section</th><th>On the site now</th><th>In the update</th></tr></thead>
          <tbody>
            <?php foreach ($plan['tables'] as $t => $p):
              $now = site_sync_count($pdo, $t); ?>
              <tr>
                <td><?= h(SITE_SYNC_TABLES[$t]) ?></td>
                <td><?= $now === null ? 'none yet' : $now ?></td>
                <td><?= (int) ($plan['expected'][$t] ?? 0) ?></td>
              </tr>
            <?php endforeach; ?>
            <tr><td>Site settings</td><td></td><td><?= count($plan['settings']) ?><?= $plan['clear'] ? ', and ' . h(implode(', ', $plan['clear'])) . ' left empty' : '' ?></td></tr>
          </tbody>
        </table>
      </div>

      <p class="help-text" style="margin:0 0 6px;">These sections are replaced in full, so anything changed in this admin since <?= h($when($stamp)) ?> in them will be replaced too. A backup of what the site shows now is saved first, and you can put it back below.</p>
      <p class="help-text" style="margin:0 0 18px;">Never touched: orders, donations, contact messages, newsletter subscribers, admin accounts, email and payment passwords, order alerts and maintenance mode.</p>

      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="apply">
        <div class="checkbox-row" style="margin-bottom:16px;">
          <input type="checkbox" name="confirm" id="confirm" required>
          <label for="confirm" style="margin:0;">I understand the sections above will be replaced with the update</label>
        </div>
        <button type="submit" class="btn btn-primary ico-text"><?= icon('check', 16) ?> Update the site content</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Backups</h3></div>
  <div class="panel-body">
    <?php if (!$backups): ?>
      <p class="help-text" style="margin:0;">None yet. One is saved each time the site content is updated; the ten most recent are kept.</p>
    <?php else: ?>
      <p class="help-text" style="margin:0 0 14px;">Each is what the site showed just before an update. Putting one back saves a backup of the current content first, so it can be undone too.</p>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Saved</th><th>Size</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($backups as $b): ?>
              <tr>
                <td><?= h($backupWhen($b)) ?></td>
                <td><?= number_format(filesize(SITE_SYNC_BACKUPS . '/' . $b) / 1024) ?> KB</td>
                <td style="text-align:right;white-space:nowrap;">
                  <a href="<?= $self ?>?download=<?= urlencode($b) ?>" class="btn btn-outline btn-sm">Download</a>
                  <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="restore">
                    <input type="hidden" name="backup" value="<?= h($b) ?>">
                    <button type="submit" class="btn btn-outline btn-sm" data-confirm="Put back the site content from <?= h($backupWhen($b)) ?>? What the site shows now will be replaced (and saved as a backup first).">Put back</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
