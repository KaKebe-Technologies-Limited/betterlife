<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'impact-reports';
$pageTitle = 'Impact & Reports';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/impact-reports.php');
    }
    $form = $_POST['form'] ?? '';

    /* ---------------- Impact stories ---------------- */
    if ($form === 'story') {
        if (($_POST['action'] ?? '') === 'delete') {
            $delId = (int) $_POST['id'];
            $img = $pdo->prepare("SELECT image FROM impact_stories WHERE id = ?");
            $img->execute([$delId]);
            if ($path = $img->fetchColumn()) { $full = __DIR__ . '/../' . $path; if (is_file($full)) @unlink($full); }
            $pdo->prepare("DELETE FROM impact_stories WHERE id = ?")->execute([$delId]);
            flash_set('success', 'Story removed.');
            redirect(ADMIN_URL . '/impact-reports.php');
        }
        $title = trim($_POST['title'] ?? '');
        $caption = trim($_POST['caption'] ?? '');
        $status = isset($_POST['status']) ? 1 : 0;
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($title === '') { flash_set('error', 'Story title is required.'); redirect(ADMIN_URL . '/impact-reports.php'); }
        $errors = [];
        $image = handle_image_upload($_FILES['image'] ?? [], 'stories', $errors);
        if (!$image) { flash_set('error', 'Please choose a photo for the story.'); redirect(ADMIN_URL . '/impact-reports.php'); }
        $pdo->prepare("INSERT INTO impact_stories (title, caption, image, status, sort_order) VALUES (?,?,?,?,?)")
            ->execute([$title, $caption, $image, $status, $sort]);
        flash_set('success', 'Impact story added.');
        redirect(ADMIN_URL . '/impact-reports.php');
    }

    /* ---------------- Reports ---------------- */
    if ($form === 'report') {
        if (($_POST['action'] ?? '') === 'delete') {
            $pdo->prepare("DELETE FROM reports WHERE id = ?")->execute([(int) $_POST['id']]);
            flash_set('success', 'Report removed.');
            redirect(ADMIN_URL . '/impact-reports.php');
        }
        $title = trim($_POST['title'] ?? '');
        $year = trim($_POST['year'] ?? '');
        $fileUrl = trim($_POST['file_url'] ?? '');
        $sort = (int) ($_POST['sort_order'] ?? 0);

        $errors = [];
        if (!empty($_FILES['file']['name'])) {
            if ($_FILES['file']['size'] > 15 * 1024 * 1024) {
                flash_set('error', 'PDF must be smaller than 15MB.');
                redirect(ADMIN_URL . '/impact-reports.php');
            }
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                flash_set('error', 'Please upload a PDF file.');
                redirect(ADMIN_URL . '/impact-reports.php');
            }
            $dir = __DIR__ . '/../uploads/reports';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
            move_uploaded_file($_FILES['file']['tmp_name'], $dir . '/' . $filename);
            $fileUrl = 'uploads/reports/' . $filename;
        }

        if ($title === '' || $fileUrl === '') {
            flash_set('error', 'Title and a PDF (file or URL) are required.');
            redirect(ADMIN_URL . '/impact-reports.php');
        }
        $pdo->prepare("INSERT INTO reports (title, year, file_url, sort_order) VALUES (?,?,?,?)")
            ->execute([$title, $year, $fileUrl, $sort]);
        flash_set('success', 'Report added.');
        redirect(ADMIN_URL . '/impact-reports.php');
    }
}

$stories = $pdo->query("SELECT * FROM impact_stories ORDER BY sort_order")->fetchAll();
$reports = $pdo->query("SELECT * FROM reports ORDER BY sort_order")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="form-grid">
  <div class="panel">
    <div class="panel-head"><h3>Add Impact Story</h3></div>
    <div class="panel-body">
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="story">
        <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" required></div>
        <div class="form-group"><label>Caption</label><input type="text" name="caption" class="form-control" placeholder="Short supporting line"></div>
        <div class="form-group"><label>Photo *</label><input type="file" name="image" class="form-control" accept="image/*" required></div>
        <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        <div class="checkbox-row" style="margin-bottom:18px;"><input type="checkbox" name="status" id="storyStatus" checked><label for="storyStatus" style="margin:0;">Visible on website</label></div>
        <button type="submit" class="btn btn-primary btn-block">+ Add Story</button>
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Impact Stories (<?= count($stories) ?>)</h3></div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th></th><th>Title</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php if (!$stories): ?><tr class="empty-row"><td colspan="4">No stories yet.</td></tr><?php endif; ?>
          <?php foreach ($stories as $s): ?>
            <tr>
              <td><img src="<?= asset_url($s['image']) ?>" class="row-thumb"></td>
              <td><strong><?= h($s['title']) ?></strong><br><span class="help-text"><?= h($s['caption']) ?></span></td>
              <td><span class="badge <?= $s['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $s['status'] ? 'Visible' : 'Hidden' ?></span></td>
              <td><form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="form" value="story"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $s['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Remove this story?">Delete</button></form></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="form-grid">
  <div class="panel">
    <div class="panel-head"><h3>Add Annual Report</h3></div>
    <div class="panel-body">
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="report">
        <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" required value="BetterLife International Annual Report"></div>
        <div class="form-group"><label>Year</label><input type="text" name="year" class="form-control" placeholder="e.g. 2024"></div>
        <div class="form-group"><label>Upload PDF</label><input type="file" name="file" class="form-control" accept="application/pdf"></div>
        <div class="form-group"><label>...or paste a PDF URL instead</label><input type="text" name="file_url" class="form-control" placeholder="https://"></div>
        <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        <button type="submit" class="btn btn-primary btn-block">+ Add Report</button>
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Annual Reports (<?= count($reports) ?>)</h3></div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Title</th><th>Year</th><th></th></tr></thead>
        <tbody>
          <?php if (!$reports): ?><tr class="empty-row"><td colspan="3">No reports yet.</td></tr><?php endif; ?>
          <?php foreach ($reports as $r): ?>
            <tr>
              <td><a href="<?= h($r['file_url']) ?>" target="_blank"><?= h($r['title']) ?></a></td>
              <td><?= h($r['year']) ?></td>
              <td><form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="form" value="report"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Remove this report?">Delete</button></form></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
