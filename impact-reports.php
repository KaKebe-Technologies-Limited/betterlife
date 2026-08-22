<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Impact & Reports';
$activePage = 'impact';

$stats = $pdo->query("SELECT * FROM stats WHERE status = 1 ORDER BY sort_order")->fetchAll();
$stories = $pdo->query("SELECT * FROM impact_stories WHERE status = 1 ORDER BY sort_order")->fetchAll();
$reports = $pdo->query("SELECT * FROM reports WHERE status = 1 ORDER BY sort_order")->fetchAll();
$logo = setting($pdo, 'logo', 'assets/img/logo.png');

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Work<span>/</span>Impact &amp; Reports</div>
    <h1>Our Impact</h1>
    <p style="max-width:640px;color:#e2f0e9;">Since 2021, we've empowered women, trained farmers, and promoted biogas, organic farming, climate education, tree-planting and zero-waste projects to foster sustainability and community development.</p>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="impact-stats">
      <?php foreach ($stats as $s): ?>
        <div class="stat-item fade-up">
          <strong data-count="<?= h($s['value']) ?>">0</strong>
          <span><?= h($s['label']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($stories): ?>
<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Stories From the Ground</span>
      <h2>Impact, Told by the People Living It</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($stories as $s): ?>
        <div class="impact-photo fade-up" style="aspect-ratio:4/3.4;">
          <img src="<?= asset_url($s['image']) ?>" alt="<?= h($s['title']) ?>">
          <span class="cap"><?= h($s['title']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($reports): ?>
<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Our Annual Reports</span>
      <h2>Read the Full Story, Year by Year</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($reports as $r): ?>
        <div class="card report-card fade-up">
          <div class="report-icon"><?= icon('file-text', 26) ?></div>
          <img src="<?= asset_url($logo) ?>" alt="<?= h(setting($pdo,'site_name')) ?>" class="report-logo">
          <h3><?= h($r['title']) ?></h3>
          <span class="cat-badge"><?= h($r['year']) ?></span>
          <a href="<?= h($r['file_url']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="margin-top:16px;">Download PDF <?= icon('arrow-right', 15) ?></a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section>
  <div class="container">
    <div class="cta-banner fade-up">
      <div>
        <h3>Explore the programs and projects behind these numbers</h3>
        <p>See exactly where and how this impact is created.</p>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="<?= SITE_URL ?>/programs.php" class="btn btn-white">Our Programs</a>
        <a href="<?= SITE_URL ?>/projects.php" class="btn btn-outline">Our Projects</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
