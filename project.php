<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
$activePage = 'programs';

$areas = pp_areas();
$projects = pp_projects();
$slug = (string) ($_GET['slug'] ?? '');
// Renamed projects: their old addresses move to the new ones
$renamed = ['smiles' => 'rise', 'pre-cop-climate-academy' => 'climate-leadership-academy'];
if (isset($renamed[$slug])) { header('Location: ' . SITE_URL . '/project.php?slug=' . $renamed[$slug], true, 301); exit; }
$p = $projects[$slug] ?? null;

// Projects with an established page of their own (the farm) go there
if ($p && !empty($p['href'])) redirect(SITE_URL . '/' . $p['href']);

if (!$p) {
    http_response_code(404);
    $pageTitle = 'Project Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="container-narrow" style="padding:100px 24px;text-align:center;"><h1>Project Not Found</h1><p class="muted">This project may have moved.</p><a href="' . SITE_URL . '/projects.php" class="btn btn-primary">See all projects</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$area = $areas[$p['area']];
$story = pp_paragraphs($pdo, $p['blocks'] ?? []) ?: [$p['summary']];
$others = array_slice(array_filter($projects, fn($o, $s) => $s !== $slug && $o['area'] === $p['area'], ARRAY_FILTER_USE_BOTH), 0, 3, true);
$gallery = $p['gallery'] ?? [];

$pageTitle = $p['title'];
$pageDescription = $p['summary'];
$heroPic = $p['hero'] ?? $p['image'] ?? null;   // the page photograph differs from the card photograph where there is one
$heroImg = $heroPic[0] ?? null;
$heroV = $heroImg ? ab_variants($heroImg) : [];
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/programmes.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="100vw" fetchpriority="high">'
        : '')
    . '<script>document.documentElement.classList.add("ab-js")</script>';
$contactUrl = SITE_URL . '/contact.php?subject=' . rawurlencode('Partnership enquiry: ' . $p['title']);

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg" id="top">
  <?= ab_brush_defs() ?>

  <section class="pg-hero is-compact<?= $heroImg ? '' : ' is-text' ?>" aria-labelledby="pgTitle">
    <?php if ($heroImg): ?>
      <div class="pg-hero-media"><?= ab_img($heroImg, $heroPic[1], '', false, 'style="object-position: ' . h($heroPic[2] ?? '50% 50%') . '"', '100vw') ?></div>
    <?php else: ?>
      <svg class="pg-hero-strokes" viewBox="0 0 1200 500" preserveAspectRatio="none" aria-hidden="true" focusable="false"><g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(860, 120, 1260, 80, 60, 81) ?>"/><path class="f-blue" d="<?= lp_brush_d(940, 420, 1260, 390, 50, 83) ?>"/></g></svg>
    <?php endif; ?>
    <div class="container pg-hero-inner">
      <nav class="pg-crumb-trail" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/programs.php">Programmes</a><span aria-hidden="true">/</span><a href="<?= h(pp_area_url($p['area'])) ?>"><?= h($area['short']) ?></a><span aria-hidden="true">/</span><span aria-current="page"><?= h($p['title']) ?></span></nav>
      <p class="pg-hero-kicker">Project</p>
      <h1 id="pgTitle"><?= h($p['title']) ?></h1>
      <?php if (!empty($p['formal'])): ?><p class="pg-hero-lead"><?= h($p['formal']) ?></p><?php endif; ?>
      <ul class="pg-hero-facts">
        <?php if (!empty($p['location'])): ?><li><?= icon('map-pin', 15) ?> <?= h($p['location']) ?></li><?php endif; ?>
        <?php if (!empty($p['partner'])): ?><li><?= icon('heart', 15) ?> With <?= h($p['partner']) ?></li><?php endif; ?>
        <?php if (!empty($p['status'])): ?><li><?= icon('calendar', 15) ?> <?= h($p['status']) ?></li><?php endif; ?>
      </ul>
    </div>
  </section>

  <!-- The project -->
  <section aria-labelledby="pgStoryTitle">
    <div class="container pg-intro-grid">
      <div class="pg-story ab-reveal">
        <span class="ab-eyebrow">The project</span>
        <h2 id="pgStoryTitle" class="sr-only">About the project</h2>
        <?php foreach ($story as $para): ?><p><?= h($para) ?></p><?php endforeach; ?>
      </div>
      <aside class="pg-aside ab-reveal" aria-label="Project facts">
        <dl class="pg-facts">
          <div><dt>Programme area</dt><dd><a href="<?= h(pp_area_url($p['area'])) ?>"><?= h($area['short']) ?></a><?php foreach ($p['also'] ?? [] as $al): ?>, <a href="<?= h(pp_area_url($al)) ?>"><?= h($areas[$al]['short']) ?></a><?php endforeach; ?></dd></div>
          <?php if (!empty($p['location'])): ?><div><dt>Location</dt><dd><?= h($p['location']) ?></dd></div><?php endif; ?>
          <div><dt>Who is involved</dt><dd><?= h($p['who']) ?></dd></div>
          <?php if (!empty($p['partner'])): ?><div><dt>Partner</dt><dd><?= h($p['partner']) ?></dd></div><?php endif; ?>
          <?php if (!empty($p['status'])): ?><div><dt>Status</dt><dd><?= h($p['status']) ?></dd></div><?php endif; ?>
          <?php if (!empty($p['launch'])): ?><div><dt>Launch</dt><dd><?= h($p['launch']) ?></dd></div><?php endif; ?>
        </dl>
      </aside>
    </div>
  </section>

  <!-- The film from this project -->
  <?php $film = pp_film(); if ($film['project'] === $slug && is_file(__DIR__ . '/' . $film['src'])): ?>
    <section class="pg-section-cream" aria-labelledby="pgFilmTitle">
      <div class="container">
        <div class="ab-head ab-reveal">
          <span class="ab-eyebrow">Watch the film</span>
          <h2 id="pgFilmTitle"><?= h($film['title']) ?></h2>
        </div>
        <div class="pg-film-inline ab-reveal"><video controls playsinline preload="none" poster="<?= h(asset_url($film['poster'])) ?>" aria-label="<?= h($film['title']) ?>"><source src="<?= h(pp_film_src($film)) ?>" type="video/mp4"></video></div>
        <p class="pg-film-caption ab-reveal"><?= h($film['summary']) ?> <?= (int) $film['minutes'] ?> minutes, with sound.</p>
      </div>
    </section>
  <?php endif; ?>

  <!-- Voices from the project: shown only when real, consented quotes have been added -->
  <?php foreach ($p['voices'] ?? [] as $vi => [$vq, $vn, $vr]): ?>
    <section class="pg-voice-band<?= $vi ? ' is-white' : '' ?>" aria-label="In their words">
      <div class="container"><?= pp_voice($vq, $vn, $vr, 170 + $vi) ?></div>
    </section>
  <?php endforeach; ?>

  <!-- Documented results -->
  <?php if (!empty($p['results'])): ?>
    <section class="pg-section-cream" aria-labelledby="pgResultsTitle">
      <div class="container">
        <div class="ab-head ab-reveal">
          <span class="ab-eyebrow">Documented results</span>
          <h2 id="pgResultsTitle">What the project recorded</h2>
        </div>
        <ul class="pg-results is-light<?= count($p['results']) === 4 ? ' is-four' : '' ?>">
          <?php foreach ($p['results'] as $r): ?><?= pp_result($r) ?><?php endforeach; ?>
        </ul>
        <p class="pg-evidence-note ab-reveal" style="color: var(--ab-soft) !important;">These figures describe <?= h($p['title']) ?> only, for the group named with each one. Fuller reporting is available in our <a href="<?= SITE_URL ?>/impact-reports.php">impact reports</a>.</p>
      </div>
    </section>
  <?php endif; ?>

  <!-- Photographs -->
  <?php if ($gallery): ?>
    <section aria-labelledby="pgGalleryTitle">
      <div class="container">
        <div class="ab-head ab-reveal">
          <span class="ab-eyebrow">In pictures</span>
          <h2 id="pgGalleryTitle">From the project</h2>
        </div>
        <ul class="pg-gallery ab-reveal<?= count($gallery) <= 2 ? ' is-two' : '' ?>">
          <?php foreach ($gallery as $i => [$gp, $cap]): ?>
            <li><?= ab_photo($gp, $cap, $cap, 'project', '', $i === 0 && count($gallery) > 2 ? '(max-width: 720px) 100vw, 800px' : '(max-width: 720px) 50vw, 600px') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>

  <!-- Related -->
  <section class="pg-section-cream" aria-labelledby="pgRelatedTitle">
    <div class="container">
      <div class="pg-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">Keep exploring</span>
          <h2 id="pgRelatedTitle">Related work, stories and reports</h2>
        </div>
        <ul class="pg-related">
          <?php foreach ($p['related'] ?? [] as [$href, $label]): ?><li><a href="<?= h(SITE_URL . '/' . $href) ?>"><?= h($label) ?> <?= icon('arrow-right', 15) ?></a></li><?php endforeach; ?>
          <li><a href="<?= SITE_URL ?>/impact-reports.php">Our impact reports <?= icon('arrow-right', 15) ?></a></li>
          <li><a href="<?= SITE_URL ?>/projects.php">All projects <?= icon('arrow-right', 15) ?></a></li>
        </ul>
      </div>
      <?php if ($others): ?>
        <!-- More projects as text links, so their photographs are not repeated here -->
        <h3 class="ab-subhead" style="margin-bottom: 18px;">More in <?= h($area['short']) ?></h3>
        <ul class="pg-more-list">
          <?php foreach ($others as $os => $op): ?>
            <li><a href="<?= h(pp_project_url($os, $op)) ?>"><span><strong><?= h($op['title']) ?></strong><?php if (!empty($op['location'])): ?><small><?= icon('map-pin', 13) ?> <?= h($op['location']) ?></small><?php endif; ?></span><?= icon('arrow-right', 18) ?></a></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </section>

  <section class="pg-invite" aria-labelledby="pgInviteTitle">
    <div class="container">
      <div class="pg-invite-card ab-reveal">
        <svg class="ab-strokes" viewBox="0 0 1200 300" preserveAspectRatio="none" aria-hidden="true" focusable="false"><g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(980, 40, 1260, 10, 44, 75) ?>"/><path class="f-blue" d="<?= lp_brush_d(-60, 280, 220, 262, 40, 77) ?>"/></g></svg>
        <div>
          <h2 id="pgInviteTitle">Support <?= h($p['title']) ?></h2>
          <p><?= h($area['invite']) ?></p>
        </div>
        <a href="<?= h($contactUrl) ?>" class="pg-btn">Talk to our team <?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </section>
</main>

<?= ab_lightbox() ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
