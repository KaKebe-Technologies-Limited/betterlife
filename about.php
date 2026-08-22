<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us';
$activePage = 'about';

$leadership = $pdo->query("SELECT * FROM team_members WHERE status = 1 AND category = 'leadership' ORDER BY sort_order LIMIT 6")->fetchAll();
$values = [
  ['num' => '01', 'title' => 'Sustainability', 'text' => 'We focus on long-term solutions that protect the environment and can be maintained by communities themselves.'],
  ['num' => '02', 'title' => 'Empowerment', 'text' => 'We believe in building local capacity and leadership, especially among young people and women.'],
  ['num' => '03', 'title' => 'Innovation', 'text' => 'We embrace technology and creative solutions to address complex environmental and social challenges.'],
  ['num' => '04', 'title' => 'Inclusivity', 'text' => 'We work with all community members regardless of background, ensuring no one is left behind.'],
];
$journey = [
  ['2021', 'Founded in Uganda by young leaders and refugees.'],
  ['2022', 'Expanded to South Sudan and Tanzania.'],
  ['2023', 'Launched the Soillla app; expanded to Ghana and DR Congo.'],
  ['2024', 'Reached 1M trees planted and 90,000+ people impacted.'],
];

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>About Us</div>
    <h1>Who We Are</h1>
    <p style="max-width:600px;color:#e2f0e9;">Youth-led. Refugee-inspired. Working across five countries to turn adversity into opportunity.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow"><?= h(setting($pdo,'about_who_title','Who We Are')) ?></span>
        <h2>A youth-led movement for lasting change</h2>
        <div class="muted"><?= nl2p(setting($pdo, 'about_who_text')) ?></div>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url(setting($pdo, 'about_image')) ?>" alt="BetterLife International team">
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="grid grid-2" style="gap:30px;">
      <div class="card fade-up" style="padding:38px;">
        <div class="icon-badge" style="margin-bottom:16px;">🎯</div>
        <h3>Our Mission</h3>
        <p class="muted"><?= h(setting($pdo, 'mission_text')) ?></p>
      </div>
      <div class="card fade-up" style="padding:38px;">
        <div class="icon-badge" style="margin-bottom:16px;">🔭</div>
        <h3>Our Vision</h3>
        <p class="muted"><?= h(setting($pdo, 'vision_text')) ?></p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Our Journey</span>
      <h2>From a Local Idea to a Regional Movement</h2>
    </div>
    <div class="timeline">
      <?php foreach ($journey as $j): ?>
        <div class="timeline-item fade-up">
          <div class="year"><?= h($j[0]) ?></div>
          <p><?= h($j[1]) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Core Values</span>
      <h2>The Principles That Guide Everything We Do</h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($values as $v): ?>
        <div class="card value-card fade-up">
          <div class="num"><?= h($v['num']) ?></div>
          <h4><?= h($v['title']) ?></h4>
          <p><?= h($v['text']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($leadership): ?>
<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Leadership</span>
      <h2>Meet Our Leadership Team</h2>
      <p class="muted">Our leadership team combines local expertise with international experience.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($leadership as $m): ?>
        <div class="card team-card fade-up">
          <div class="avatar">
            <img src="<?= $m['photo'] ? asset_url($m['photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($m['name']) . '&background=16593f&color=fff&size=240' ?>" alt="<?= h($m['name']) ?>">
          </div>
          <h4><?= h($m['name']) ?></h4>
          <div class="role"><?= h($m['role']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:40px;">
      <a href="<?= SITE_URL ?>/team.php" class="btn btn-outline-dark">Meet the Full Team →</a>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section-dark">
  <div class="container">
    <div class="container-narrow" style="text-align:center;">
      <div class="quote-mark" style="color:rgba(255,255,255,.15);font-size:80px;font-family:Georgia,serif;line-height:.4;">&ldquo;</div>
      <p style="font-size:22px;font-style:italic;color:#eaf3ee;margin:20px 0 24px;"><?= h(setting($pdo,'board_quote')) ?></p>
      <strong style="color:#fff;">— <?= h(setting($pdo,'board_quote_author')) ?></strong>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="cta-banner fade-up">
      <div>
        <h3>Want to know more about our board of directors?</h3>
        <p>Meet the global leaders and advisors who guide our strategy.</p>
      </div>
      <a href="<?= SITE_URL ?>/team.php#board" class="btn btn-white">Meet the Board →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
