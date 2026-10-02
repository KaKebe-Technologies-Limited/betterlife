<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
$pageTitle = 'Projects';
$activePage = 'programs';
$pageDescription = 'Every BetterLife project, filterable by programme area and country.';

$areas = pp_areas();
$projects = pp_projects();

// Filters are plain links (work without JavaScript, with keyboard and touch)
$area = isset($areas[$_GET['area'] ?? '']) ? $_GET['area'] : '';
$countries = array_values(array_unique(array_filter(array_map(fn($p) => $p['country'] ?? '', $projects))));
usort($countries, fn($x, $y) => ($x === 'Regional') <=> ($y === 'Regional') ?: strcmp($x, $y));
$country = in_array($_GET['country'] ?? '', $countries, true) ? $_GET['country'] : '';

$inArea = fn($p, $a) => $p['area'] === $a || in_array($a, $p['also'] ?? [], true);
$list = array_filter($projects, fn($p) => (!$area || $inArea($p, $area)) && (!$country || ($p['country'] ?? '') === $country));
$filterUrl = function (string $a, string $c): string {
    $q = array_filter(['area' => $a, 'country' => $c]);
    return SITE_URL . '/projects.php' . ($q ? '?' . http_build_query($q) : '') . '#directory';
};
$countFor = fn(string $a, string $c) => count(array_filter($projects, fn($p) => (!$a || $inArea($p, $a)) && (!$c || ($p['country'] ?? '') === $c)));

$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg" id="top">
  <?= ab_brush_defs() ?>

  <section class="pg-page-head" aria-labelledby="pgTitle">
    <div class="container">
      <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/programs.php">Programmes</a><span aria-hidden="true">/</span><span aria-current="page">Projects</span></nav>
      <h1 id="pgTitle">Our <?= ab_mark('projects', 89) ?></h1>
      <p>Projects show how our five programme areas are delivered in particular places, with particular communities and partners. Filter by programme area or country.</p>
    </div>
  </section>

  <section id="directory" aria-labelledby="pgDirTitle" style="padding-top: clamp(32px, 4vw, 48px);">
    <div class="container">
      <h2 id="pgDirTitle" class="sr-only">Project directory</h2>
      <div class="pg-filters">
        <nav class="pg-filter-row" aria-label="Filter by programme area">
          <span>Programme area</span>
          <a href="<?= h($filterUrl('', $country)) ?>" class="pg-pill"<?= !$area ? ' aria-current="true"' : '' ?>>All <small>(<?= $countFor('', $country) ?>)</small></a>
          <?php foreach ($areas as $as => $a): $c = $countFor($as, $country); ?>
            <a href="<?= h($filterUrl($as, $country)) ?>" class="pg-pill"<?= $area === $as ? ' aria-current="true"' : '' ?>><?= h($a['short']) ?> <small>(<?= $c ?>)</small></a>
          <?php endforeach; ?>
        </nav>
        <nav class="pg-filter-row" aria-label="Filter by country">
          <span>Country</span>
          <a href="<?= h($filterUrl($area, '')) ?>" class="pg-pill"<?= !$country ? ' aria-current="true"' : '' ?>>All <small>(<?= $countFor($area, '') ?>)</small></a>
          <?php foreach ($countries as $c): ?>
            <a href="<?= h($filterUrl($area, $c)) ?>" class="pg-pill"<?= $country === $c ? ' aria-current="true"' : '' ?>><?= h($c === 'Regional' ? 'Regional and virtual' : $c) ?> <small>(<?= $countFor($area, $c) ?>)</small></a>
          <?php endforeach; ?>
        </nav>
      </div>

      <p class="pg-count" role="status">Showing <?= count($list) ?> <?= count($list) === 1 ? 'project' : 'projects' ?><?= $area ? ' in ' . h($areas[$area]['short']) : '' ?><?= $country ? ' · ' . h($country === 'Regional' ? 'regional and virtual' : $country) : '' ?><?php if ($area || $country): ?> · <a href="<?= h($filterUrl('', '')) ?>">Clear filters</a><?php endif; ?></p>

      <?php if ($list): ?>
        <div class="pg-project-grid">
          <?php foreach ($list as $ps => $p): ?><?= pp_project_card($ps, $p, $areas) ?><?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="pg-empty">No projects match these filters yet. <a href="<?= h($filterUrl('', '')) ?>">Show all projects</a>.</p>
      <?php endif; ?>
    </div>
  </section>

  <section class="pg-invite" aria-labelledby="pgInviteTitle">
    <div class="container">
      <div class="pg-invite-card ab-reveal">
        <div>
          <h2 id="pgInviteTitle">Bring a project to more people</h2>
          <p>Funding, equipment, technical expertise and market connections all help a project reach further. Tell us what you could offer.</p>
        </div>
        <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Partnership enquiry') ?>" class="pg-btn">Partner with us <?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
