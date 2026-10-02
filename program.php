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

$pageTitle = $a['short'];
$pageDescription = $a['card'];
[$heroImg, $heroAlt, $heroPos] = $a['hero'];
$heroV = ab_variants($heroImg);
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/programmes.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="100vw" fetchpriority="high">'
        : '')
    . '<script>document.documentElement.classList.add("ab-js")</script>';
$contactUrl = SITE_URL . '/contact.php?subject=' . rawurlencode('Partnership enquiry: ' . $a['short']);

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg" id="top">
  <?= ab_brush_defs() ?>

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

  <!-- Introduction and who takes part -->
  <section aria-labelledby="pgIntroTitle">
    <div class="container pg-intro-grid">
      <div class="pg-intro-copy ab-reveal">
        <span class="ab-eyebrow">About this programme</span>
        <h2 id="pgIntroTitle" class="sr-only">About this programme</h2>
        <?php foreach ($a['intro'] as $i => $para): ?>
          <p<?= $i === 0 ? ' class="pg-intro-lead"' : '' ?>><?= h($para) ?></p>
        <?php endforeach; ?>
      </div>
      <aside class="pg-aside ab-reveal" aria-label="At a glance">
        <h3>Who participates</h3>
        <p><?= h($a['who']) ?></p>
        <?php if ($own): ?>
          <h3>Projects in this area</h3>
          <p><?= count($own) ?> <?= count($own) === 1 ? 'project' : 'projects' ?><?php if ($linked): ?>, plus <?= count($linked) ?> connected from other areas<?php endif; ?></p>
        <?php endif; ?>
        <?php if ($partners): ?>
          <h3>Partners</h3>
          <p><?= h(implode(' · ', $partners)) ?></p>
        <?php endif; ?>
      </aside>
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
          <?php foreach ($blocks as $b): if (!$b['paras']) continue; ?>
            <article class="pg-block ab-reveal">
              <h3><?= h($b['title']) ?></h3>
              <?php foreach ($b['paras'] as $para): ?><p><?= h($para) ?></p><?php endforeach; ?>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Projects -->
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
        <?php if ($own): ?>
          <div class="pg-project-grid">
            <?php foreach ($own as $ps => $p): ?><?= pp_project_card($ps, $p, $areas, '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 380px', false) ?><?php endforeach; ?>
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

  <!-- Selected evidence -->
  <?php if ($a['evidence']): ?>
    <section class="pg-section-cream" aria-labelledby="pgEvidenceTitle">
      <div class="container">
        <div class="ab-head ab-reveal">
          <span class="ab-eyebrow">Selected evidence</span>
          <h2 id="pgEvidenceTitle">What the figures show</h2>
        </div>
        <ul class="pg-results is-light<?= count($a['evidence']) === 4 ? ' is-four' : '' ?>">
          <?php foreach ($a['evidence'] as $r): ?><?= pp_result($r, null, $projects) ?><?php endforeach; ?>
        </ul>
        <p class="pg-evidence-note ab-reveal" style="color: var(--ab-soft) !important;">Each figure belongs to the project or activity named with it. Figures are shown separately and should not be added together.</p>
      </div>
    </section>
  <?php endif; ?>

  <!-- Photographs -->
  <?php if ($a['gallery']): ?>
    <section aria-labelledby="pgGalleryTitle">
      <div class="container">
        <div class="ab-head ab-reveal">
          <span class="ab-eyebrow">In pictures</span>
          <h2 id="pgGalleryTitle">From the field</h2>
        </div>
        <ul class="pg-gallery ab-reveal">
          <?php foreach ($a['gallery'] as $i => [$p, $cap]): ?>
            <li><?= ab_photo($p, $cap, $cap, 'area', '', $i === 0 ? '(max-width: 720px) 100vw, 800px' : '(max-width: 720px) 50vw, 400px') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>

  <!-- Partnership invitation -->
  <section class="pg-invite" aria-labelledby="pgInviteTitle">
    <div class="container">
      <div class="pg-invite-card ab-reveal">
        <svg class="ab-strokes" viewBox="0 0 1200 300" preserveAspectRatio="none" aria-hidden="true" focusable="false"><g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(980, 40, 1260, 10, 44, 71) ?>"/><path class="f-blue" d="<?= lp_brush_d(-60, 280, 220, 262, 40, 73) ?>"/></g></svg>
        <div>
          <h2 id="pgInviteTitle">Partner on <?= h($a['short']) ?></h2>
          <p><?= h($a['invite']) ?></p>
        </div>
        <a href="<?= h($contactUrl) ?>" class="pg-btn">Talk to our team <?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </section>

  <!-- Other programme areas -->
  <section class="pg-section-cream" aria-labelledby="pgOtherTitle" style="padding-top: clamp(40px, 5vw, 64px); padding-bottom: clamp(48px, 6vw, 80px);">
    <div class="container">
      <h2 id="pgOtherTitle" class="ab-subhead" style="border-top: 0; padding-top: 0; margin-bottom: 20px;">Explore the other programme areas</h2>
      <ul class="pg-area-nav">
        <?php foreach ($areas as $os => $oa): if ($os === $slug) continue; ?>
          <li><a href="<?= h(pp_area_url($os)) ?>"><?= icon($oa['icon'], 18) ?> <?= h($oa['short']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
</main>

<?= ab_lightbox() ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
