<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us';
$activePage = 'about';
$pageDescription = 'BetterLife International was founded in Uganda in 2021 with USD 200. It now works across Uganda, South Sudan, Tanzania, Ghana and the Democratic Republic of Congo on climate-resilient agriculture, livelihoods, clean energy, education and market access.';

// These lists (and the photo strips below) are stored in the content_items
// table and managed from Admin → Page Content, so BetterLife staff can edit
// the wording or add/remove cards without touching code.
$howWeWork = content_items($pdo, 'about', 'how_we_work');
$whoWeWorkWith = content_items($pdo, 'about', 'who_we_work_with');
$whoWeWorkWithGallery = content_items($pdo, 'about', 'who_we_work_with_gallery');
$whereWeWork = content_items($pdo, 'about', 'where_we_work');
$whereWeWorkGallery = content_items($pdo, 'about', 'where_we_work_gallery');
$journey = content_items($pdo, 'about', 'journey');
$guides = content_items($pdo, 'about', 'guides');

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>About Us</div>
    <h1>It Began with the Lives We Knew</h1>
    <p style="max-width:640px;color:#e2f0e9;">This work did not begin in a conference room.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <p class="muted">BetterLife International was founded by young people who understood that poverty, displacement and climate change do not happen one at a time. Our work grew from the need for solutions that make sense in the whole of a person&rsquo;s life.</p>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url(setting($pdo, 'about_image')) ?>" alt="BetterLife International team">
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Our Story</span>
      <h2>From USD 200 to Work Across Five Countries</h2>
    </div>
    <div class="split" style="align-items:start;">
      <div class="prose-narrow fade-up"><?= nl2p(setting($pdo, 'about_who_text')) ?></div>
      <div class="fade-up img-frame" style="position:sticky;top:100px;">
        <img src="<?= asset_url('assets/img/betterlifeint-source/impact-reports/impact-photo-2.jpeg') ?>" alt="A BetterLife team member with a child in the community">
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/farm-field-3.jpg') ?>" alt="BetterLife International in the field">
      </div>
      <div class="fade-up" style="display:flex;flex-direction:column;gap:18px;">
        <span class="eyebrow">Purpose</span>
        <div class="card" style="padding:24px;">
          <div class="icon-badge" style="width:38px;height:38px;margin-bottom:10px;"><?= icon('target', 20) ?></div>
          <h3>Mission</h3>
          <p class="muted" style="font-size:14px;margin:0;"><?= h(setting($pdo, 'mission_text')) ?></p>
        </div>
        <div class="card" style="padding:24px;">
          <div class="icon-badge" style="width:38px;height:38px;margin-bottom:10px;"><?= icon('eye', 20) ?></div>
          <h3>Vision</h3>
          <p class="muted" style="font-size:14px;margin:0;"><?= h(setting($pdo, 'vision_text')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-10.jpg') ?>" alt="A woman taking notes during a BetterLife training session">
      </div>
      <div class="fade-up">
        <span class="eyebrow">How We See the Work</span>
        <h2>A Failed Harvest Is Never Just a Farming Problem</h2>
        <p class="muted">When a woman tells us her harvest failed, seeds may appear to be the answer. But listen longer and the picture changes. She may have no water nearby. She may spend much of the day collecting firewood. She may lack money for inputs, access to a phone or a buyer for what she grows.</p>
        <p class="muted">Giving her seeds alone leaves most of the problem untouched.</p>
        <p class="muted">BetterLife takes a systems approach because people live in systems. We connect food to water, time, energy, income, finance, information and markets. One programme may therefore include a demonstration garden, a savings group, a digital tool and a buyer connection. The combination is shaped by the barriers people are actually facing.</p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">How We Work</span>
    </div>
    <div class="detail-grid fade-up">
      <?php foreach ($howWeWork as $b): ?>
        <div class="detail-block"><h4><?= h($b['title']) ?></h4><p><?= h($b['body']) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="media-band">
  <div class="container">
    <figure class="fade-up">
      <img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-11.jpg') ?>" alt="Women meeting together in a BetterLife community group">
      <figcaption>People learn faster in groups they already trust.</figcaption>
    </figure>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Who We Work With</span>
    </div>
    <div class="detail-grid fade-up">
      <?php foreach ($whoWeWorkWith as $b): ?>
        <div class="detail-block"><h4><?= h($b['title']) ?></h4><p><?= h($b['body']) ?></p></div>
      <?php endforeach; ?>
    </div>
    <div class="impact-photos fade-up" style="margin-top:36px;grid-template-columns:repeat(<?= count($whoWeWorkWithGallery) ?>,1fr);max-width:<?= count($whoWeWorkWithGallery) * 300 ?>px;">
      <?php foreach ($whoWeWorkWithGallery as $g): ?>
        <div class="impact-photo"><img src="<?= asset_url($g['image']) ?>" alt="<?= h($g['extra'] ?: $g['title']) ?>"><span class="cap"><?= h($g['title']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Where We Work</span>
    </div>
    <div class="detail-grid fade-up">
      <?php foreach ($whereWeWork as $b): ?>
        <div class="detail-block"><h4><?= h($b['title']) ?></h4><p><?= h($b['body']) ?></p></div>
      <?php endforeach; ?>
    </div>
    <div class="impact-photos fade-up" style="margin-top:36px;grid-template-columns:repeat(<?= count($whereWeWorkGallery) ?>,1fr);max-width:<?= count($whereWeWorkGallery) * 300 ?>px;">
      <?php foreach ($whereWeWorkGallery as $g): ?>
        <div class="impact-photo"><img src="<?= asset_url($g['image']) ?>" alt="<?= h($g['extra'] ?: $g['title']) ?>"><span class="cap"><?= h($g['title']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="media-band">
  <div class="container">
    <figure class="fade-up">
      <img src="<?= asset_url('assets/img/betterlifeint-source/projects/project-agro-tourism-alt.jpeg') ?>" alt="A BetterLife farmer walking through a banana plantation">
    </figure>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div style="max-width:760px;margin:0 auto;">
      <div class="fade-up">
        <span class="eyebrow">Our Journey</span>
        <h2>From a Local Idea to Work Across Five Countries</h2>
        <div class="journey-list">
          <?php foreach ($journey as $j): ?>
            <div class="journey-row">
              <div class="year"><?= h($j['title']) ?></div>
              <p><?= h($j['body']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="media-band">
  <div class="container">
    <figure class="fade-up">
      <img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-12.jpg') ?>" alt="A BetterLife community session in progress">
      <figcaption>Every principle here was learned in the field, not written first in an office.</figcaption>
    </figure>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">What Guides Us</span>
    </div>
    <div class="grid grid-3">
      <?php foreach ($guides as $i => $g): ?>
        <div class="card value-card fade-up">
          <div class="num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
          <h4><?= h($g['title']) ?></h4>
          <p><?= h($g['body']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
