<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
$activePage = 'programs';

$areas = pp_areas();
$projects = pp_projects();
$slug = (string) ($_GET['slug'] ?? '');

if (!isset($areas[$slug])) {
    http_response_code(404);
    $pageTitle = 'Programme Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="container-narrow" style="padding:100px 24px;text-align:center;"><h1>Programme Not Found</h1><p class="muted">This programme area may have moved.</p><a href="' . SITE_URL . '/programs.php" class="btn btn-primary">See all programmes</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$a = $areas[$slug];
$own = array_filter($projects, fn($p) => $p['area'] === $slug);
$linked = array_filter($projects, fn($p) => in_array($slug, $p['also'] ?? [], true));
$partners = array_values(array_unique(array_filter(array_map(fn($p) => $p['partner'] ?? '', $own))));
$blocks = array_map(fn($b) => ['title' => $b[1], 'paras' => pp_paragraphs($pdo, [$b])], $a['blocks'] ?? []);
$index = array_search($slug, array_keys($areas), true) + 1;

// One project leads the page as a feature; the others follow as cards
$featureSlug = isset($own[$a['feature'] ?? '']) ? $a['feature'] : array_key_first($own);
$feature = $featureSlug ? $own[$featureSlug] : null;
$others = array_filter($own, fn($k) => $k !== $featureSlug, ARRAY_FILTER_USE_KEY);
$collage = $a['collage'] ?? [];
// Voices for this area (illustrative placeholders show on a local preview only; see pp_voices())
$voiceKeys = ['climate-resilient-agriculture' => ['grace', 'mariam'], 'green-skills-livelihoods' => ['peter', 'josephine', 'ahmed', 'esther']][$slug] ?? [];
$voices = pp_visible_voices($voiceKeys);
$strip = $a['gallery'] ?? [];

$pageTitle = $a['short'];
$pageDescription = $a['card'];
[$heroImg, $heroAlt, $heroPos] = $a['hero'];
$heroV = ab_variants($heroImg);
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/programmes.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="100vw" fetchpriority="high">'
        : '')
    . pp_voice_head($voices)
    . '<script>document.documentElement.classList.add("ab-js")</script>';
$contactUrl = SITE_URL . '/contact.php?subject=' . rawurlencode('Partnership enquiry: ' . $a['short']);
$seed = 100 + $index * 7;   // each area gets its own brush strokes

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg" id="top">
  <?= ab_brush_defs() ?>

  <!-- Opening -->
  <section class="pg-hero is-compact<?= ['right' => ' is-right', 'narrow' => ' is-narrow'][$a['hero_side'] ?? ''] ?? '' ?>" aria-labelledby="pgTitle">
    <div class="pg-hero-media"><?= ab_img($heroImg, $heroAlt, '', false, 'style="object-position: ' . h($heroPos) . '"', '100vw') ?></div>
    <div class="container pg-hero-inner">
     <div class="pg-hero-copy">
      <nav class="pg-crumb-trail" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/programs.php">Programmes</a><span aria-hidden="true">/</span><span aria-current="page"><?= h($a['short']) ?></span></nav>
      <p class="pg-hero-kicker">Programme area <?= str_pad((string) $index, 2, '0', STR_PAD_LEFT) ?></p>
      <h1 id="pgTitle"><?= h($a['formal']) ?></h1>
      <p class="pg-hero-lead"><?= h($a['lead']) ?></p>
      <div class="pg-hero-actions">
        <?php if ($own || $linked): ?><a href="#projects" class="pg-btn">See the projects <?= icon('chevron-down', 16) ?></a><?php endif; ?>
        <a href="<?= h($contactUrl) ?>" class="pg-btn pg-btn-ghost">Partner on this programme</a>
      </div>
     </div>
    </div>
  </section>

  <!-- Introduction beside an organic photo collage -->
  <section class="pg-area-intro" aria-labelledby="pgIntroTitle">
    <div class="container pg-area-intro-grid">
      <div class="pg-intro-copy ab-reveal">
        <span class="ab-eyebrow">About this programme</span>
        <h2 id="pgIntroTitle" class="sr-only">About this programme</h2>
        <?php foreach ($a['intro'] as $i => $para): ?>
          <p<?= $i === 0 ? ' class="pg-intro-lead"' : '' ?>><?= h($para) ?></p>
        <?php endforeach; ?>
        <dl class="pg-glance">
          <div><dt><?= icon('users', 16) ?> Who participates</dt><dd><?= h($a['who']) ?></dd></div>
          <?php if ($own || $linked): ?><div><dt><?= icon('grid', 16) ?> Projects</dt><dd><?= count($own) ?> in this area<?php if ($linked): ?>, <?= count($linked) ?> connected<?php endif; ?></dd></div><?php endif; ?>
          <?php if ($partners): ?><div><dt><?= icon('heart', 16) ?> Partners</dt><dd><?= h(implode(' · ', $partners)) ?></dd></div><?php endif; ?>
        </dl>
      </div>
      <?php if (count($collage) >= 3): ?>
        <div class="pg-trio ab-reveal">
          <svg class="ab-strokes" viewBox="0 0 600 640" preserveAspectRatio="none" aria-hidden="true" focusable="false">
            <g filter="url(#lpBrush)">
              <path class="f-green" d="<?= lp_brush_d(-30, 470, 150, 446, 46, $seed) ?>"/>
              <path class="f-blue"  d="<?= lp_brush_d(330, 22, 560, 0, 40, $seed + 2) ?>"/>
            </g>
          </svg>
          <?php foreach (array_slice($collage, 0, 3) as $i => [$p, $cap]): ?>
            <?= ab_photo($p, $cap, $cap, 'trio', 'pg-trio-' . ($i + 1), '(max-width: 900px) 50vw, 320px') ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- What we do -->
  <section class="pg-section-cream" aria-labelledby="pgActivitiesTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">What we do</span>
        <h2 id="pgActivitiesTitle"><?= $blocks ? 'Practical solutions, close to home' : 'Activities in this programme' ?></h2>
      </div>
      <?php if (!empty($a['activities'])): ?>
        <ul class="pg-activity-list">
          <?php foreach ($a['activities'] as $act): ?><li class="ab-reveal"><?= icon('check', 16) ?> <?= h($act) ?></li><?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if ($blocks): ?>
        <div class="pg-blocks">
          <?php foreach ($blocks as $n => $b): if (!$b['paras']) continue; ?>
            <article class="pg-block ab-reveal">
              <span class="pg-block-num"><?= str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) ?></span>
              <h3><?= h($b['title']) ?></h3>
              <?php foreach ($b['paras'] as $para): ?><p><?= h($para) ?></p><?php endforeach; ?>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- A voice from BetterLife (draft for Denise Ayebare's approval) -->
  <?php if (!empty($a['quote'])): ?>
    <section class="pg-voice-band is-white" aria-label="From our founder">
      <div class="container"><?= pp_voice($a['quote'], PP_FOUNDER[0], PP_FOUNDER[1], 150 + $index) ?></div>
    </section>
  <?php endif; ?>

  <!-- Projects: one feature, then the rest -->
  <?php if ($own || $linked): ?>
    <section id="projects" aria-labelledby="pgProjectsTitle">
      <div class="container">
        <div class="pg-head-row ab-reveal">
          <div class="ab-head">
            <span class="ab-eyebrow">Projects</span>
            <h2 id="pgProjectsTitle">Where this programme is delivered</h2>
          </div>
          <a href="<?= SITE_URL ?>/projects.php?area=<?= h(rawurlencode($slug)) ?>" class="pg-link">Filter the project directory <?= icon('arrow-right', 15) ?></a>
        </div>
        <?php if ($feature): ?>
          <?php $fPic = $feature['hero'] ?? $feature['image'] ?? null; ?>
          <article class="pg-feature pg-feature-wide ab-reveal<?= $fPic ? '' : ' is-text' ?>">
            <?php if ($fPic): ?>
              <?= ab_img($fPic[0], $fPic[1], 'pg-feature-img', true, 'style="object-position: ' . h($fPic[2] ?? '50% 50%') . '"', '(max-width: 900px) 100vw, 1240px') ?>
            <?php endif; ?>
            <div class="pg-feature-panel">
              <span class="pg-kicker">Featured project</span>
              <h3><a href="<?= h(pp_project_url($featureSlug, $feature)) ?>"><?= h($feature['title']) ?></a></h3>
              <?php if (!empty($feature['location'])): ?><p class="pg-feature-place"><?= icon('map-pin', 14) ?> <?= h($feature['location']) ?></p><?php endif; ?>
              <p><?= h($feature['summary']) ?></p>
              <?php if (!empty($feature['partner'])): ?><p class="pg-partner"><?= icon('heart', 14) ?> With <?= h($feature['partner']) ?></p><?php endif; ?>
              <span class="pg-more" aria-hidden="true">Read the project <?= icon('arrow-right', 15) ?></span>
            </div>
          </article>
        <?php endif; ?>
        <?php if ($others): ?>
          <div class="pg-project-grid" style="margin-top: clamp(18px, 2.2vw, 26px);">
            <?php foreach ($others as $ps => $p): ?><?= pp_project_card($ps, $p, $areas, '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 380px', false) ?><?php endforeach; ?>
          </div>
        <?php endif; ?>
        <?php if ($linked): ?>
          <h3 class="ab-subhead" style="margin-top: clamp(40px, 5vw, 64px); margin-bottom: 24px;">Connected from other programme areas</h3>
          <div class="pg-project-grid">
            <?php foreach ($linked as $ps => $p): ?><?= pp_project_card($ps, $p, $areas) ?><?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- Selected evidence beside a photograph -->
  <?php if ($a['evidence']): ?>
    <section class="pg-evidence" aria-labelledby="pgEvidenceTitle">
      <div class="container<?= !empty($a['evidence_photo']) ? ' pg-evidence-grid' : '' ?>">
        <?php if (!empty($a['evidence_photo'])): ?>
          <figure class="pg-evidence-photo ab-reveal">
            <span class="pg-evidence-frame"><?= ab_img($a['evidence_photo'][0], $a['evidence_photo'][1], '', true, '', '(max-width: 900px) 100vw, 520px') ?></span>
            <figcaption><?= h($a['evidence_photo'][1]) ?></figcaption>
          </figure>
        <?php endif; ?>
        <div>
          <div class="ab-head ab-reveal">
            <span class="ab-eyebrow">Selected evidence</span>
            <h2 id="pgEvidenceTitle">What the figures show</h2>
          </div>
          <ul class="pg-results is-stack">
            <?php foreach ($a['evidence'] as $r): ?><?= pp_result($r, null, $projects) ?><?php endforeach; ?>
          </ul>
          <p class="pg-evidence-note ab-reveal">Each figure belongs to the project or activity named with it. Figures are shown separately and should not be added together.</p>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?= pp_voice_section($voices) ?>

  <!-- Photographs: a strip with controls, or a pair when there are only a few -->
  <?php if (count($strip) >= 4): ?>
    <section class="pg-strip" aria-labelledby="pgGalleryTitle" data-strip>
      <div class="container pg-strip-head ab-reveal">
        <div>
          <span class="ab-eyebrow">In pictures</span>
          <h2 id="pgGalleryTitle">From the field</h2>
        </div>
        <div class="pg-strip-nav" hidden>
          <button type="button" class="pg-round" data-dir="-1" aria-controls="pgStripRow" aria-label="Previous photographs"><?= icon('arrow-right', 18) ?></button>
          <button type="button" class="pg-round" data-dir="1" aria-controls="pgStripRow" aria-label="Next photographs"><?= icon('arrow-right', 18) ?></button>
        </div>
      </div>
      <ul class="pg-strip-row" id="pgStripRow" tabindex="0" aria-label="Photographs from this programme. Select one to see it larger.">
        <?php foreach ($strip as [$p, $cap]): ?>
          <li><?= ab_photo($p, $cap, $cap, 'area', 'pg-strip-item', '(max-width: 720px) 200px, 280px') ?></li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php elseif ($strip): ?>
    <section aria-labelledby="pgGalleryTitle">
      <div class="container">
        <div class="ab-head ab-reveal">
          <span class="ab-eyebrow">In pictures</span>
          <h2 id="pgGalleryTitle">From the field</h2>
        </div>
        <ul class="pg-gallery is-two ab-reveal">
          <?php foreach ($strip as [$p, $cap]): ?><li><?= ab_photo($p, $cap, $cap, 'area', '', '(max-width: 720px) 50vw, 600px') ?></li><?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>

  <!-- Partnership invitation on a wide photograph -->
  <section class="pg-close is-area" aria-labelledby="pgInviteTitle">
    <?php if (!empty($a['invite_bg'])): ?><div class="pg-close-bg"><?= ab_img($a['invite_bg'], '', '', true, '', '100vw') ?></div><?php endif; ?>
    <div class="container">
      <div class="pg-close-inner ab-reveal">
        <span class="ab-eyebrow">Partner with us</span>
        <h2 id="pgInviteTitle">Partner on <?= h($a['short']) ?></h2>
        <p><?= h($a['invite']) ?></p>
      </div>
      <div class="pg-hero-actions ab-reveal" style="margin-top: 28px;">
        <a href="<?= h($contactUrl) ?>" class="pg-btn">Talk to our team <?= icon('arrow-right', 16) ?></a>
        <a href="<?= SITE_URL ?>/partners.php" class="pg-btn pg-btn-ghost">Meet our partners</a>
      </div>
    </div>
  </section>

  <!-- Other programme areas, as photo cards -->
  <section class="pg-section-cream" aria-labelledby="pgOtherTitle" style="padding-top: clamp(44px, 5vw, 68px); padding-bottom: clamp(48px, 6vw, 80px);">
    <div class="container">
      <h2 id="pgOtherTitle" class="ab-subhead" style="border-top: 0; padding-top: 0; margin-bottom: 22px;">Explore the other programme areas</h2>
      <ul class="pg-area-nav has-icons">
        <?php $n = 0; foreach ($areas as $os => $oa): $n++; if ($os === $slug) continue; ?>
          <li><a href="<?= h(pp_area_url($os)) ?>">
            <span class="pg-area-nav-ico" aria-hidden="true"><?= icon($oa['icon'], 20) ?></span>
            <span><small><?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></small><?= h($oa['short']) ?></span>
          </a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
</main>

<?= ab_lightbox() ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
