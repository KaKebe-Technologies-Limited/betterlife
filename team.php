<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Team';
$activePage = 'team';
$pageDescription = 'Meet the leadership, country teams and board of directors behind BetterLife International — a youth-led organisation working across five African countries.';

$all = $pdo->query("SELECT * FROM team_members WHERE status = 1 ORDER BY sort_order")->fetchAll();
$leadership = array_filter($all, fn($m) => $m['category'] === 'leadership');
$staff      = array_filter($all, fn($m) => $m['category'] === 'staff');
$board      = array_filter($all, fn($m) => $m['category'] === 'board');
$volunteers = array_filter($all, fn($m) => $m['category'] === 'volunteer');

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
    <h1>People who know the work and the places where it happens.</h1>
    <p style="max-width:640px;color:#e2f0e9;">The Team Behind BetterLife.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="prose-narrow fade-up">
      <p>BetterLife is led by an African team working across community development, agriculture, law, climate action, finance, monitoring, communications and youth leadership.</p>
      <p>Our country and programme teams bring professional knowledge together with a close understanding of the communities where we work. Our board provides oversight, experience and accountability as the organisation grows.</p>
    </div>
  </div>
</section>

<?php if ($leadership): ?>
<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Executive Leadership</span>
      <h2>Executive Leadership</h2>
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
      <span class="eyebrow" style="justify-content:center;">Country and Programme Teams</span>
      <h2>Country and Programme Teams</h2>
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
      <span class="eyebrow" style="justify-content:center;">Board of Directors</span>
      <h2>Board of Directors</h2>
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

<?php if ($volunteers): ?>
<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Volunteers and Community Champions</span>
      <h2>Volunteers and Community Champions</h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($volunteers as $m): ?>
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

<section>
  <div class="container">
    <div class="cta-banner fade-up">
      <div>
        <h3>Want to join the BetterLife team?</h3>
        <p>We're always looking for passionate people to work with us.</p>
      </div>
      <a href="<?= SITE_URL ?>/contact.php?subject=Careers" class="btn btn-white">Get In Touch</a>
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
