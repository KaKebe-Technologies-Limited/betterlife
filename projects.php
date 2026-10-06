<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
$pageTitle = 'Projects';
$activePage = 'programs';
$pageDescription = 'Every BetterLife project, grouped by programme area and filterable by country: practical routes to food security, livelihoods and climate resilience.';

$areas = pp_areas();
$projects = pp_projects();
$film = pp_film();

// Filters are plain links (they work without JavaScript, with keyboard and touch); projects.js filters in place
$area = isset($areas[$_GET['area'] ?? '']) ? $_GET['area'] : '';
$countries = array_values(array_unique(array_filter(array_map(fn($p) => $p['country'] ?? '', $projects))));
usort($countries, fn($x, $y) => ($x === 'Regional') <=> ($y === 'Regional') ?: strcmp($x, $y));
$country = in_array($_GET['country'] ?? '', $countries, true) ? $_GET['country'] : '';
$placeName = fn(string $c): string => $c === 'Regional' ? 'Regional and virtual' : $c;

$inArea = fn($p, $a) => $p['area'] === $a || in_array($a, $p['also'] ?? [], true);
$matches = fn($p, string $a, string $c) => (!$a || $inArea($p, $a)) && (!$c || ($p['country'] ?? '') === $c);
$shown = count(array_filter($projects, fn($p) => $matches($p, $area, $country)));
$filterUrl = function (string $a, string $c): string {
    $q = array_filter(['area' => $a, 'country' => $c]);
    return SITE_URL . '/projects.php' . ($q ? '?' . http_build_query($q) : '') . '#directory';
};
$countFor = fn(string $a, string $c) => count(array_filter($projects, fn($p) => $matches($p, $a, $c)));

// The opening: what the directory holds, counted from the projects themselves
$words = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen', 'Twenty'];
$byPlace = array_count_values(array_filter(array_map(fn($p) => $p['country'] ?? '', $projects), fn($c) => $c !== '' && $c !== 'Regional'));
arsort($byPlace);
$places = array_keys($byPlace);
$listJoin = fn(array $xs): string => count($xs) > 1 ? implode(', ', array_slice($xs, 0, -1)) . ' and ' . end($xs) : ($xs[0] ?? '');
$online = in_array('Regional', $countries, true);

// Photographs for the opening, each one a way into its project and none repeating a card below
$tiles = [
    ['womens-climate-resilience-yumbe', 'hero'],
    ['tanzania-climate-education', 'hero'],
    ['apala-youth-centre', 'hero'],
    ['betterlife-agro-tourism-farm', ['assets/img/farm/maize-women.jpg', '', '55% 40%']],
];

// Short names for the filter buttons; each group below carries the full programme name
$chipNames = [
    'climate-resilient-agriculture'      => 'Food Security',
    'green-skills-livelihoods'           => 'RISE',
    'climate-education-youth-leadership' => 'Climate Education',
    'clean-energy-water-restoration'     => 'Clean Energy and Water',
    'digital-innovation'                 => 'Digital Tools',
];

// Projects grouped under their home programme area, in the order the areas are presented
$groups = [];
foreach ($areas as $as => $a) $groups[$as] = [];
foreach ($projects as $ps => $p) $groups[$p['area']][$ps] = $p;
$groups = array_filter($groups);

// One project card. The project with the film is shown wide, with its results and a way straight to the film.
$card = function (string $ps, array $p) use ($areas, $film, $matches, $area, $country): string {
    $feature = $film['project'] === $ps && is_file(__DIR__ . '/' . $film['src']);
    $url = pp_project_url($ps, $p);
    $pic = $p['directory_photo'] ?? $p['image'] ?? null;
    $results = $p['results'] ?? [];
    if (isset($p['proof'], $results[$p['proof']])) $results = array_merge([$results[$p['proof']]], array_diff_key($results, [$p['proof'] => 1]));   // its chosen result first
    ob_start(); ?>
    <article class="pg-project pg-dcard<?= $feature ? ' is-feature' : '' ?> ab-reveal" data-areas="<?= h(implode(' ', array_merge([$p['area']], $p['also'] ?? []))) ?>" data-country="<?= h($p['country'] ?? '') ?>"<?= $matches($p, $area, $country) ? '' : ' hidden' ?>>
      <div class="pg-project-media<?= $pic ? '' : ' is-text' ?>">
        <?php if ($pic): ?>
          <?= ab_img($pic[0], $pic[1], '', true, 'style="object-position: ' . h($pic[2] ?? '50% 50%') . '"', $feature ? '(max-width: 900px) 100vw, 640px' : '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 380px') ?>
        <?php else: ?>
          <span class="pg-project-mono" aria-hidden="true"><?= icon($areas[$p['area']]['icon'] ?? 'leaf', 30) ?></span>
        <?php endif; ?>
        <?php if (!empty($p['location'])): ?><span class="pg-dcard-place"><?= icon('map-pin', 13) ?> <?= h($p['location']) ?></span><?php endif; ?>
        <?php if ($feature): ?>
          <a class="pg-dcard-film" href="<?= h($url) ?>#film"><span class="pg-dcard-play" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg></span><span>Watch the film <small><?= (int) $film['minutes'] ?> minutes</small></span></a>
        <?php endif; ?>
      </div>
      <div class="pg-project-body">
        <?php if ($feature): ?><span class="pg-kicker">Featured project</span><?php endif; ?>
        <h3><a href="<?= h($url) ?>"><?= h($p['title']) ?></a></h3>
        <?php if (!empty($p['status'])): ?><p class="pg-dcard-status"><?= icon('calendar', 13) ?> <?= h($p['status']) ?></p><?php endif; ?>
        <p class="pg-dcard-sum"><?= h($p['summary']) ?></p>
        <?php if ($results): ?>
          <ul class="pg-dcard-results">
            <?php foreach (array_slice($results, 0, 4) as $r): ?><li><strong><?= h($r[0]) ?></strong><span><?= h($r[1]) ?></span></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <?php if (!empty($p['partner'])): ?><p class="pg-partner"><?= icon('heart', 14) ?> With <?= h($p['partner']) ?></p><?php endif; ?>
        <span class="pg-more" aria-hidden="true"><?= !empty($p['href']) ? 'Visit the farm' : 'Read the project' ?> <?= icon('arrow-right', 15) ?></span>
      </div>
    </article>
    <?php return ob_get_clean();
};

$tileSizes = ['(max-width: 900px) 60vw, 360px', '(max-width: 900px) 50vw, 380px', '(max-width: 900px) 30vw, 200px', '(max-width: 900px) 30vw, 200px'];

$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/projects.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg pg-dir" id="top">
  <?= ab_brush_defs() ?>

  <section class="pg-dir-hero" aria-labelledby="pgTitle">
    <svg class="pg-hero-strokes" viewBox="0 0 1200 500" preserveAspectRatio="none" aria-hidden="true" focusable="false"><g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(-80, 470, 360, 440, 70, 97) ?>"/><path class="f-blue" d="<?= lp_brush_d(980, 30, 1280, 6, 44, 99) ?>"/></g></svg>
    <div class="container pg-dir-hero-grid">
      <div class="pg-dir-hero-copy">
        <nav class="pg-crumb-trail" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/programs.php">Programmes</a><span aria-hidden="true">/</span><span aria-current="page">Projects</span></nav>
        <p class="pg-hero-kicker">Our projects</p>
        <h1 id="pgTitle">Where the work <?= ab_mark('takes root', 89) ?></h1>
        <p class="pg-dir-lead"><?= h($words[count($projects)] ?? (string) count($projects)) ?> projects across <?= h($listJoin($online ? array_merge($places, ['online']) : $places)) ?>. Each one is a practical route to food security, livelihoods and climate resilience.</p>
        <ul class="pg-dir-stats">
          <li><strong><?= count($projects) ?></strong> projects</li>
          <li><strong><?= count($groups) ?></strong> programme areas</li>
          <li><strong><?= count($places) ?></strong> countries<?= $online ? ', and online' : '' ?></li>
        </ul>
      </div>
      <div class="pg-dir-mosaic">
        <?php foreach ($tiles as $i => [$ts, $which]): $tp = $projects[$ts] ?? null; $pic = is_array($which) ? $which : ($tp[$which] ?? $tp['image'] ?? null); if (!$tp || !$pic) continue; ?>
          <a class="pg-dir-tile pg-dir-tile-<?= $i + 1 ?>" href="<?= h(pp_project_url($ts, $tp)) ?>" aria-label="<?= h($tp['title']) ?>">
            <?= ab_img($pic[0], '', '', $i > 1, 'style="object-position: ' . h($pic[2] ?? '50% 50%') . '"', $tileSizes[$i]) ?>
            <span class="pg-dir-tile-tag" aria-hidden="true"><?= icon('map-pin', 12) ?> <?= h(($tp['location'] ?? '') ?: $tp['title']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="directory" class="pg-dir-list" aria-labelledby="pgDirTitle" data-area="<?= h($area) ?>" data-country="<?= h($country) ?>">
    <div class="container">
      <h2 id="pgDirTitle" class="sr-only">Project directory</h2>
      <div class="pg-toolbar">
        <div class="pg-toolbar-row">
          <span class="pg-toolbar-label" id="pgFilterArea">Programme area</span>
          <nav class="pg-chips" aria-labelledby="pgFilterArea">
            <a href="<?= h($filterUrl('', $country)) ?>" class="pg-fchip" data-kind="area" data-value="" data-label=""<?= !$area ? ' aria-current="true"' : '' ?>>All areas</a>
            <?php foreach ($areas as $as => $a): ?>
              <a href="<?= h($filterUrl($as, $country)) ?>" class="pg-fchip<?= $countFor($as, $country) ? '' : ' is-empty' ?>" data-kind="area" data-value="<?= h($as) ?>" data-label="<?= h($a['short']) ?>" title="<?= h($a['short']) ?>"<?= $area === $as ? ' aria-current="true"' : '' ?>><?= icon($a['icon'] ?? 'leaf', 15) ?> <?= h($chipNames[$as] ?? $a['short']) ?></a>
            <?php endforeach; ?>
          </nav>
        </div>
        <div class="pg-toolbar-row">
          <span class="pg-toolbar-label" id="pgFilterPlace">Country</span>
          <nav class="pg-chips" aria-labelledby="pgFilterPlace">
            <a href="<?= h($filterUrl($area, '')) ?>" class="pg-fchip" data-kind="country" data-value="" data-label=""<?= !$country ? ' aria-current="true"' : '' ?>>Everywhere <small>(<?= $countFor($area, '') ?>)</small></a>
            <?php foreach ($countries as $c): ?>
              <a href="<?= h($filterUrl($area, $c)) ?>" class="pg-fchip" data-kind="country" data-value="<?= h($c) ?>" data-label="<?= h($placeName($c)) ?>"<?= $country === $c ? ' aria-current="true"' : '' ?>><?= icon($c === 'Regional' ? 'globe' : 'map-pin', 14) ?> <?= h($placeName($c)) ?> <small>(<?= $countFor($area, $c) ?>)</small></a>
            <?php endforeach; ?>
          </nav>
        </div>
      </div>

      <p class="pg-count" role="status">Showing <?= $shown ?> <?= $shown === 1 ? 'project' : 'projects' ?><?= $area ? ' in ' . h($areas[$area]['short']) : '' ?><?= $country ? ' · ' . h($placeName($country)) : '' ?><?php if ($area || $country): ?> · <a href="<?= h($filterUrl('', '')) ?>" data-clear>Clear filters</a><?php endif; ?></p>

      <?php foreach ($groups as $as => $gp): $a = $areas[$as]; $n = count(array_filter($gp, fn($p) => $matches($p, $area, $country))); ?>
        <section class="pg-group<?= $n === 1 ? ' is-one' : ($n === 2 ? ' is-two' : '') ?>" aria-labelledby="pgGroup-<?= h($as) ?>"<?= $n ? '' : ' hidden' ?>>
          <header class="pg-group-head ab-reveal">
            <span class="pg-group-icon" aria-hidden="true"><?= icon($a['icon'] ?? 'leaf', 26) ?></span>
            <div>
              <h3 id="pgGroup-<?= h($as) ?>"><?= h($a['short']) ?></h3>
              <p><?= h($a['card']) ?></p>
            </div>
            <div class="pg-group-side">
              <span class="pg-group-n"><?= $n ?> <?= $n === 1 ? 'project' : 'projects' ?></span>
              <a class="pg-link" href="<?= h(pp_area_url($as)) ?>">About this programme <?= icon('arrow-right', 15) ?></a>
            </div>
          </header>
          <div class="pg-project-grid">
            <?php foreach ($gp as $ps => $p): ?><?= $card($ps, $p) ?><?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <p class="pg-empty"<?= $shown ? ' hidden' : '' ?>>No projects match these filters yet. <a href="<?= h($filterUrl('', '')) ?>" data-clear>Show all projects</a>.</p>
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
