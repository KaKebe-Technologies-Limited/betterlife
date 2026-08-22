<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Team';
$activePage = 'team';

$all = $pdo->query("SELECT * FROM team_members WHERE status = 1 ORDER BY sort_order")->fetchAll();
$leadership = array_filter($all, fn($m) => $m['category'] === 'leadership');
$staff      = array_filter($all, fn($m) => $m['category'] === 'staff');
$volunteers = array_filter($all, fn($m) => $m['category'] === 'volunteer');
$board      = array_filter($all, fn($m) => $m['category'] === 'board');

function avatar_src(array $m): string {
    return $m['photo'] ? asset_url($m['photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($m['name']) . '&background=16593f&color=fff&size=240';
}

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Team</div>
    <h1>The People Behind BetterLife</h1>
    <p style="max-width:640px;color:#e2f0e9;">A dedicated multidisciplinary team committed to empowering individuals and communities through innovative solutions.</p>
  </div>
</section>

<?php if ($leadership): ?>
<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Leadership</span>
      <h2>Meet Our Leadership Team</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($leadership as $m): ?>
        <div class="card team-card fade-up">
          <div class="avatar"><img src="<?= avatar_src($m) ?>" alt="<?= h($m['name']) ?>"></div>
          <h4><?= h($m['name']) ?></h4>
          <div class="role"><?= h($m['role']) ?></div>
          <?php if ($m['bio']): ?><p class="muted" style="font-size:13px;"><?= h(excerpt($m['bio'], 90)) ?></p><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($staff): ?>
<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Our Staff</span>
      <h2>Country &amp; Programme Teams</h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($staff as $m): ?>
        <div class="card team-card fade-up">
          <div class="avatar"><img src="<?= avatar_src($m) ?>" alt="<?= h($m['name']) ?>"></div>
          <h4><?= h($m['name']) ?></h4>
          <div class="role"><?= h($m['role']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($board): ?>
<section id="board">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Governance</span>
      <h2>Meet the Board of Directors</h2>
      <p class="muted">Global leaders and advisors who guide our strategy and hold us accountable to our mission.</p>
    </div>
    <div class="grid grid-2">
      <?php foreach ($board as $m): ?>
        <div class="card board-card fade-up">
          <div class="avatar"><img src="<?= avatar_src($m) ?>" alt="<?= h($m['name']) ?>"></div>
          <div>
            <h4><?= h($m['name']) ?></h4>
            <span class="role"><?= h($m['role']) ?></span>
            <p><?= h(excerpt($m['bio'], 220)) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($volunteers): ?>
<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Volunteers</span>
      <h2>Meet Our Volunteers</h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($volunteers as $m): ?>
        <div class="card team-card fade-up" style="padding:22px 14px;">
          <div class="avatar" style="width:88px;height:88px;"><img src="<?= avatar_src($m) ?>" alt="<?= h($m['name']) ?>"></div>
          <h4 style="font-size:15px;"><?= h($m['name']) ?></h4>
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
        <h3>Want to join the BetterLife team?</h3>
        <p>We're always looking for passionate people to volunteer or work with us.</p>
      </div>
      <a href="<?= SITE_URL ?>/contact.php?subject=Careers" class="btn btn-white">Get In Touch →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
