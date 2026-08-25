<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Team';
$activePage = 'team';
$pageDescription = 'Meet the leadership, country teams and board of directors behind BetterLife International — a youth-led organisation working across five African countries.';

$all = $pdo->query("SELECT * FROM team_members WHERE status = 1 ORDER BY sort_order")->fetchAll();
$leadership = array_filter($all, fn($m) => $m['category'] === 'leadership');
$staff      = array_filter($all, fn($m) => $m['category'] === 'staff');
$board      = array_filter($all, fn($m) => $m['category'] === 'board');

function avatar_src(array $m): string {
    return $m['photo'] ? asset_url($m['photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($m['name']) . '&background=16593f&color=fff&size=240';
}

$teamData = [];
foreach ($all as $m) {
    $teamData[$m['id']] = [
        'name'  => $m['name'],
        'role'  => $m['role'],
        'bio'   => $m['bio'] ?: 'Bio coming soon.',
        'photo' => avatar_src($m),
    ];
}

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Team</div>
    <h1>The People Behind BetterLife</h1>
    <p style="max-width:640px;color:#e2f0e9;">A dedicated multidisciplinary team committed to empowering individuals and communities through innovative solutions. Tap anyone below to read their full bio.</p>
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
        <div class="card team-card fade-up js-open-bio" data-member-id="<?= $m['id'] ?>" role="button" tabindex="0">
          <div class="avatar"><img src="<?= avatar_src($m) ?>" alt="<?= h($m['name']) ?>"></div>
          <h4><?= h($m['name']) ?></h4>
          <div class="role"><?= h($m['role']) ?></div>
          <?php if ($m['bio']): ?><p class="muted" style="font-size:13px;"><?= h(excerpt($m['bio'], 90)) ?></p><?php endif; ?>
          <span class="read-bio-link"><?= icon('file-text', 14) ?> Read full bio</span>
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
        <div class="card team-card fade-up js-open-bio" data-member-id="<?= $m['id'] ?>" role="button" tabindex="0">
          <div class="avatar"><img src="<?= avatar_src($m) ?>" alt="<?= h($m['name']) ?>"></div>
          <h4><?= h($m['name']) ?></h4>
          <div class="role"><?= h($m['role']) ?></div>
          <span class="read-bio-link"><?= icon('file-text', 14) ?> Read full bio</span>
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
      <p class="muted">Leaders and advisors who guide our strategy and hold us accountable to our mission.</p>
    </div>
    <div class="grid grid-2">
      <?php foreach ($board as $m): ?>
        <div class="card board-card fade-up js-open-bio" data-member-id="<?= $m['id'] ?>" role="button" tabindex="0">
          <div class="avatar"><img src="<?= avatar_src($m) ?>" alt="<?= h($m['name']) ?>"></div>
          <div>
            <h4><?= h($m['name']) ?></h4>
            <span class="role"><?= h($m['role']) ?></span>
            <p><?= h(excerpt($m['bio'], 180)) ?></p>
            <span class="read-bio-link"><?= icon('file-text', 14) ?> Read full bio</span>
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
        <h3>Want to join the BetterLife team?</h3>
        <p>We're always looking for passionate people to work with us.</p>
      </div>
      <a href="<?= SITE_URL ?>/contact.php?subject=Careers" class="btn btn-white">Get In Touch →</a>
    </div>
  </div>
</section>

<!-- Bio modal -->
<div class="member-modal" id="memberModal" aria-hidden="true">
  <div class="member-modal-backdrop" data-close-modal></div>
  <div class="member-modal-card">
    <button type="button" class="member-modal-close" data-close-modal aria-label="Close"><?= icon('x', 20) ?></button>
    <img id="modalPhoto" src="" alt="">
    <h3 id="modalName"></h3>
    <span id="modalRole" class="role"></span>
    <p id="modalBio"></p>
  </div>
</div>
<script>window.__teamData = <?= json_encode($teamData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
