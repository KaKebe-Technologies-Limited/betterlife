<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Projects';
$activePage = 'projects';

$projects = $pdo->query("SELECT * FROM projects WHERE status = 1 ORDER BY sort_order")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Work<span>/</span>Projects</div>
    <h1>Our Projects</h1>
    <p style="max-width:640px;color:#e2f0e9;">From irrigation and plastic recycling to smart farming apps, these are the projects turning our programs into results on the ground.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="grid grid-3">
      <?php foreach ($projects as $p): ?>
        <div class="card project-card fade-up">
          <div class="thumb"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['title']) ?>"></div>
          <div class="body">
            <?php if ($p['category']): ?><span class="cat-badge"><?= h($p['category']) ?></span><?php endif; ?>
            <h3><?= h($p['title']) ?></h3>
            <p class="muted" style="font-size:14px;"><?= h(excerpt($p['description'], 130)) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (!$projects): ?>
        <div class="empty-state">Projects will be listed here soon.</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="cta-banner fade-up">
      <div>
        <h3>Want to see the bigger picture?</h3>
        <p>Explore our long-term programs or read our full impact reports.</p>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="<?= SITE_URL ?>/programs.php" class="btn btn-white">Our Programs</a>
        <a href="<?= SITE_URL ?>/impact-reports.php" class="btn btn-outline">Impact &amp; Reports</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
