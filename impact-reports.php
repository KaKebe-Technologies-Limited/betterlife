<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Impact & Reports';
$activePage = 'impact';
$pageDescription = 'BetterLife tracks whether people are using what they learnt, whether families are growing more food, whether incomes are becoming more stable and whether communities are better placed to face the next shock.';

$stats = $pdo->query("SELECT * FROM stats WHERE status = 1 ORDER BY sort_order")->fetchAll();
$reports = $pdo->query("SELECT * FROM reports WHERE status = 1 ORDER BY sort_order")->fetchAll();
$logo = setting($pdo, 'logo', 'assets/img/logo.png');

$changeLooksLike = [
  'Impact is a woman harvesting vegetables from a sack garden beside her home instead of buying everything at the market.',
  'It is a young refugee starting a poultry business and trading with the host community.',
  'It is a farmer checking market information before deciding where to sell.',
  'It is a child using a computer or opening a climate book for the first time.',
  'It is a household cooking with biogas instead of spending hours looking for firewood.',
];
$selectedResults = [
  ['Women&rsquo;s Climate Resilience in Yumbe', 'Knowledge of climate-smart agriculture among the structured training cohort rose from 22 per cent to 92 per cent. Women adopted sack and box gardening, composting, mulching and drought-tolerant crops, while participating households reported lower spending on vegetables.', 'Read the Yumbe Case Study', 'assets/img/betterlifeint-source/programs/program-photo-3.jpg'],
  ['SMILES', 'Seventy-eight per cent of participants moved into sustainable income pathways, 85 per cent reported stronger refugee-host relationships and food insecurity in target groups fell by 40 per cent.', 'Read the SMILES Case Study', 'assets/img/project-smiles.jpg'],
  ['Green Libraries and Eco Labs', 'More than 4,500 learners have taken part in climate education, practical environmental action, debate and digital learning through BetterLife-supported schools and learning spaces.', 'Read the Education Case Study', 'assets/img/betterlifeint-source/programs/program-photo-8.jpg'],
];

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Work<span>/</span>Impact &amp; Reports</div>
    <h1>We ask what changed, not only what was delivered.</h1>
    <p style="max-width:660px;color:#e2f0e9;">What Happens After the Training Ends?</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="prose-narrow fade-up">
      <p>BetterLife tracks whether people are using what they learnt, whether families are growing more food, whether incomes are becoming more stable and whether communities are better placed to face the next shock.</p>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Our Reach</span>
    </div>
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

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow">What Change Looks Like</span>
        <?php foreach ($changeLooksLike as $line): ?>
          <p class="muted"><?= $line ?></p>
        <?php endforeach; ?>
        <p class="muted">These changes may begin with one activity. Their value lies in what becomes possible afterwards.</p>
      </div>
      <div class="fade-up img-frame" style="position:sticky;top:100px;">
        <img src="<?= asset_url('assets/img/impact-story-2.jpg') ?>" alt="A woman laughing during a BetterLife community session">
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Selected Results</span>
    </div>
    <div class="grid grid-3">
      <?php foreach ($selectedResults as $r): ?>
        <div class="card project-card fade-up">
          <div class="thumb"><img src="<?= asset_url($r[3]) ?>" alt="<?= h(html_entity_decode(strip_tags($r[0]), ENT_QUOTES)) ?>"></div>
          <div class="body">
            <h3 style="font-size:17px;"><?= $r[0] ?></h3>
            <p class="muted" style="font-size:13.5px;"><?= h($r[1]) ?></p>
            <a href="<?= SITE_URL ?>/blog.php" class="readmore"><?= h($r[2]) ?></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Reports and Accountability</span>
    </div>
    <div class="prose-narrow fade-up">
      <p>We publish reports so that communities, partners and the public can see what we did, what changed and where we still need to improve.</p>
    </div>
    <?php if ($reports): ?>
      <div class="grid grid-3" style="margin-top:34px;">
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
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
