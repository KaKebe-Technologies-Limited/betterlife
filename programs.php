<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Programs & Projects';
$activePage = 'programs';
$pageDescription = 'Explore BetterLife International\'s programs and on-the-ground projects: sustainable agriculture, climate education, nature-based solutions and livelihoods for refugees and displaced communities across Africa.';

$programs = $pdo->query("SELECT * FROM programs WHERE status = 1 ORDER BY sort_order")->fetchAll();
$projects = $pdo->query("SELECT * FROM projects WHERE status = 1 ORDER BY sort_order")->fetchAll();
$stats = $pdo->query("SELECT * FROM stats WHERE status = 1 ORDER BY sort_order")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Programs &amp; Projects</div>
    <h1>Bringing Innovation to the Community</h1>
    <p style="max-width:640px;color:#e2f0e9;">From irrigation and plastic recycling to climate education and smart farming, this is where our long-term programs meet the projects turning them into results on the ground.</p>
  </div>
</section>

<section class="section-cream" style="padding-top:56px;padding-bottom:56px;">
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

<?php foreach ($programs as $i => $p): ?>
<section id="<?= h($p['slug']) ?>">
  <div class="container">
    <div class="split">
      <?php if ($i % 2 === 1): ?>
        <div class="fade-up img-frame"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['title']) ?>"></div>
      <?php endif; ?>
      <div class="fade-up">
        <span class="eyebrow"><?= h($p['tagline']) ?></span>
        <h2><?= h($p['title']) ?></h2>
        <div class="muted"><?= nl2p($p['content']) ?></div>
      </div>
      <?php if ($i % 2 === 0): ?>
        <div class="fade-up img-frame"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['title']) ?>"></div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php if ($projects): ?>
<section id="our-projects" class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">On The Ground</span>
      <h2>Our Projects</h2>
      <p class="muted">Real initiatives turning these programs into measurable results across five countries.</p>
    </div>
    <div class="grid grid-4">
      <?php foreach ($projects as $p): ?>
        <div class="card project-card fade-up">
          <div class="thumb"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['title']) ?>"></div>
          <div class="body">
            <?php if ($p['category']): ?><span class="cat-badge"><?= h($p['category']) ?></span><?php endif; ?>
            <h3><?= h($p['title']) ?></h3>
            <p class="muted" style="font-size:14px;"><?= h(excerpt($p['description'], 120)) ?></p>
          </div>
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
        <h3>See our farm-to-market programme in action</h3>
        <p>Our sustainable agriculture programme also produces honey, ghee and yoghurt sold to support our work.</p>
      </div>
      <a href="<?= SITE_URL ?>/products.php" class="btn btn-white">Visit BetterLife Farm →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
