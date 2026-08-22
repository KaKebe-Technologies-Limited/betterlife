<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Programs';
$activePage = 'programs';

$programs = $pdo->query("SELECT * FROM programs WHERE status = 1 ORDER BY sort_order")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Programs</div>
    <h1>Bringing Innovation to the Community</h1>
    <p style="max-width:640px;color:#e2f0e9;">Our recent projects include turning plastic into fuel, climate education, empowering women in displaced communities, and planting trees to protect the environment.</p>
  </div>
</section>

<?php foreach ($programs as $i => $p): ?>
<section id="<?= h($p['slug']) ?>" class="<?= $i % 2 === 1 ? 'section-cream' : '' ?>">
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
