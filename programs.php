<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
$pageTitle = 'Our Programmes';
$activePage = 'programs';
$pageDescription = 'BetterLife’s programmes help families grow food, earn a steady income and cope with a changing climate across five connected areas of work.';

$areas = pp_areas();
$projects = pp_projects();
$featured = ['womens-climate-resilience-yumbe', 'smiles', 'betterlife-spring'];

// Three documented results, each kept with its project, group and measurement context
$evidence = [
    ['72%', 'Adopted sack or box gardening', 'Women in the programme, reported after training', 'womens-climate-resilience-yumbe', 'confirm'],
    ['78%', 'Moved into sustainable income pathways', 'Refugee and host-community participants, reported across target groups', 'smiles', 'confirm'],
    ['3,000+', 'Books for young people in Alebtong', 'Opened in December 2023 with ten computers and space for around 400 young people', 'apala-youth-centre'],
];

// How the work connects: four petals and two circles
$petals = [
    ['Farming', 'assets/img/about/rukungiri-community-field.jpg', 'People preparing a field together in Rukungiri', '50% 55%'],
    ['Water',   'assets/img/children-at-borehole.webp', 'Children collecting water at a borehole in Yumbe', '45% 40%'],
    ['Energy',  'assets/img/solar-panel-installation-2.webp', 'A solar panel in Rukungiri', '50% 45%'],
    ['Markets', 'assets/img/market-stall-vendor.webp', 'A woman standing at her market stall', '72% 45%'],
];
$dots = [
    ['Skills', 'assets/img/betterlifeint-source/programs/program-photo-10.jpg', 'Women taking notes during a training session', '55% 40%'],
    ['Information', 'assets/img/soilla-app-field-demo.webp', 'The Soilla app open on a phone in a field', '50% 50%'],
];

$strip = [
    ['assets/img/about/yumbe-cabbages.jpg', 'Cabbages growing at a BetterLife-supported site in Yumbe'],
    ['assets/img/about/yumbe-listening-circle.jpg', 'BetterLife staff and community members seated in a circle for a session in Yumbe'],
    ['assets/img/about/rukungiri-pupils-desks.jpg', 'Pupils at their desks in a classroom in Rukungiri'],
    ['assets/img/woman-winnowing-grain.webp', 'Winnowing grain in Rukungiri'],
    ['assets/img/solar-panel-farm-sky.webp', 'A solar panel under an open sky in Rukungiri'],
    ['assets/img/about/soroti-wac-gathering.jpg', 'Women gathered for a Women’s Action Circle session in Soroti'],
    ['assets/img/grain-milling-machine.webp', 'Grain being processed with a milling machine in Rukungiri'],
    ['assets/img/about/yumbe-poultry-feeders.jpg', 'Participants celebrating with new poultry feeders in Yumbe'],
    ['assets/img/betterlifeint-source/projects/project-renewable-pathways-alt.jpg', 'Adding a bottle to a plastic bank for recycling'],
    ['assets/img/about/yumbe-beehives.jpg', 'Beehives at a BetterLife-supported site in Yumbe'],
    ['assets/img/about/rukungiri-poultry.jpg', 'A woman feeding chickens in a poultry house in Rukungiri'],
    ['assets/img/about/yumbe-greenhouse-aerial.jpg', 'Aerial view of a greenhouse and farm plots in Yumbe'],
];

$heroImg = 'assets/img/about/rukungiri-farmer-maize.jpg';
$heroV = ab_variants($heroImg);
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/programmes.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="100vw" fetchpriority="high">'
        : '')
    . '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg" id="top">
  <?= ab_brush_defs() ?>

  <!-- 1. Photographic hero -->
  <section class="pg-hero" aria-labelledby="pgTitle">
    <div class="pg-hero-media"><?= ab_img($heroImg, 'A farmer tending a tall maize crop in Rukungiri, Uganda', '', false, 'style="object-position: 58% 42%"', '100vw') ?></div>
    <div class="container pg-hero-inner">
      <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">Programmes</span></nav>
      <p class="pg-hero-kicker">Our programmes</p>
      <h1 id="pgTitle">Food security and sustainable <?= ab_mark('livelihoods', 31) ?></h1>
      <p class="pg-hero-lead">Our programmes help families grow more reliable food, earn a steady income and cope with a changing climate. A family facing drought may also face hunger, unemployment and weak access to markets, so our work connects around the person.</p>
      <div class="pg-hero-actions">
        <a href="#areas" class="pg-btn">Explore the five programme areas <?= icon('chevron-down', 16) ?></a>
        <a href="<?= SITE_URL ?>/projects.php" class="pg-btn pg-btn-ghost">See all projects</a>
      </div>
    </div>
  </section>

  <!-- 2. How the work connects -->
  <section class="pg-connect" aria-labelledby="pgConnectTitle">
    <div class="container pg-connect-grid">
      <div class="pg-clover ab-reveal">
        <svg class="ab-strokes" viewBox="0 0 600 600" preserveAspectRatio="none" aria-hidden="true" focusable="false">
          <g filter="url(#lpBrush)">
            <path class="f-green" d="<?= lp_brush_d(-40, 300, 120, 270, 46, 61) ?>"/>
            <path class="f-blue"  d="<?= lp_brush_d(430, 40, 610, 18, 40, 63) ?>"/>
            <path class="f-green" d="<?= lp_brush_d(380, 590, 590, 560, 34, 65) ?>"/>
          </g>
        </svg>
        <div class="pg-clover-petals">
          <?php foreach ($petals as $i => [$tag, $p, $alt, $pos]): ?>
            <figure class="pg-petal pg-petal-<?= $i + 1 ?>"><?= ab_img($p, $alt, '', true, 'style="object-position: ' . $pos . '"', '(max-width: 900px) 45vw, 280px') ?><span class="pg-tag"><?= h($tag) ?></span></figure>
          <?php endforeach; ?>
        </div>
        <?php foreach ($dots as $i => [$tag, $p, $alt, $pos]): ?>
          <figure class="pg-dot pg-dot-<?= $i + 1 ?>"><span class="pg-dot-inner"><?= ab_img($p, $alt, '', true, 'style="object-position: ' . $pos . '"', '(max-width: 900px) 30vw, 170px') ?></span><span class="pg-tag"><?= h($tag) ?></span></figure>
        <?php endforeach; ?>
        <div class="pg-clover-core" aria-hidden="true"><?= icon('leaf', 20) ?><span>Food</span></div>
      </div>
      <div class="pg-connect-copy ab-reveal">
        <span class="ab-eyebrow">How the work connects</span>
        <h2 id="pgConnectTitle">A harvest depends on more than <?= ab_mark('seed', 67) ?></h2>
        <p>Farmers need skills they have seen working, water close enough to use and energy that does not cost hours of firewood collection. Information helps them decide what to plant, and markets turn a good season into income. Our programmes are designed so these pieces support each other.</p>
        <a href="<?= SITE_URL ?>/about.php#approach" class="pg-link">Read about our approach <?= icon('arrow-right', 15) ?></a>
      </div>
    </div>
  </section>

  <!-- 3. Five programme areas -->
  <section class="pg-areas" id="areas" aria-labelledby="pgAreasTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Programme areas</span>
        <h2 id="pgAreasTitle">Five areas of work</h2>
        <p class="ab-head-sub">Each area brings together related activities, projects and partners. Choose one to see how the work is delivered and what it has achieved.</p>
      </div>
      <ol class="pg-area-grid">
        <?php $n = 0; foreach ($areas as $slug => $a): $n++; [$img, $alt, $pos] = $a['image']; ?>
          <li class="pg-area ab-reveal" id="<?= h($slug) ?>">
            <article class="pg-area-card">
              <div class="pg-area-media">
                <?= ab_img($img, $alt, '', true, 'style="object-position: ' . h($pos) . '"', $n <= 2 ? '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 600px' : '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 400px') ?>
                <span class="pg-area-num"><?= icon($a['icon'], 15) ?> <?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
              </div>
              <div class="pg-area-body">
                <h3><?= h($a['short']) ?></h3>
                <p><?= h($a['card']) ?></p>
                <a href="<?= h(pp_area_url($slug)) ?>" class="pg-link">Explore Programme<span class="sr-only">: <?= h($a['short']) ?></span> <?= icon('arrow-right', 15) ?></a>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <!-- 4. Selected projects -->
  <section class="pg-projects" aria-labelledby="pgProjectsTitle">
    <div class="container">
      <div class="pg-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">Selected projects</span>
          <h2 id="pgProjectsTitle">How the work looks in practice</h2>
        </div>
        <a href="<?= SITE_URL ?>/projects.php" class="pg-link">Explore all projects <?= icon('arrow-right', 15) ?></a>
      </div>
      <div class="pg-project-grid">
        <?php foreach ($featured as $slug): ?>
          <?= pp_project_card($slug, $projects[$slug], $areas) ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 5. Evidence -->
  <section class="pg-evidence" aria-labelledby="pgEvidenceTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Evidence from the field</span>
        <h2 id="pgEvidenceTitle">What changed, project by project</h2>
      </div>
      <ul class="pg-results">
        <?php foreach ($evidence as $r): ?><?= pp_result($r, null, $projects) ?><?php endforeach; ?>
      </ul>
      <p class="pg-evidence-note ab-reveal">Each result belongs to the project and group named with it, and should not be read as an organisation-wide figure. Fuller context is on each project page and in our <a href="<?= SITE_URL ?>/impact-reports.php">impact reports</a>.</p>
    </div>
  </section>

  <!-- 6. Community photo strip -->
  <section class="pg-strip" aria-labelledby="pgStripTitle" data-strip>
    <div class="container pg-strip-head ab-reveal">
      <div>
        <span class="ab-eyebrow">Around the programmes</span>
        <h2 id="pgStripTitle">Fields, classrooms, circles and solar panels</h2>
      </div>
      <div class="pg-strip-nav" hidden>
        <button type="button" class="pg-round" data-dir="-1" aria-controls="pgStripRow" aria-label="Previous photographs"><?= icon('arrow-right', 18) ?></button>
        <button type="button" class="pg-round" data-dir="1" aria-controls="pgStripRow" aria-label="Next photographs"><?= icon('arrow-right', 18) ?></button>
      </div>
    </div>
    <ul class="pg-strip-row" id="pgStripRow" tabindex="0" aria-label="Photographs from our programmes. Select one to see it larger.">
      <?php foreach ($strip as [$p, $cap]): ?>
        <li><?= ab_photo($p, $cap, $cap, 'strip', 'pg-strip-item', '(max-width: 720px) 200px, 280px') ?></li>
      <?php endforeach; ?>
    </ul>
  </section>

  <!-- 7. Closing invitation -->
  <section class="pg-close" aria-labelledby="pgCloseTitle">
    <div class="pg-close-bg"><?= ab_img('assets/img/about/rukungiri-communal-farm.jpg', '', '', true, '', '100vw') ?></div>
    <div class="container">
      <div class="pg-close-inner ab-reveal">
        <span class="ab-eyebrow">Partner with us</span>
        <h2 id="pgCloseTitle">Help deliver the next season of this work</h2>
        <p>Partners help us reach more farmers, families and schools, and help results last after a project ends. These are some of the ways to contribute.</p>
      </div>
      <ul class="pg-ways">
        <li class="pg-way ab-reveal"><span class="pg-way-ico" aria-hidden="true"><?= icon('heart', 20) ?></span><h3>Funding</h3><p>Support a programme area or a specific project, from one season to several years.</p></li>
        <li class="pg-way ab-reveal"><span class="pg-way-ico" aria-hidden="true"><?= icon('box', 20) ?></span><h3>Equipment</h3><p>Irrigation and solar systems, seedlings, farm tools, computers or books.</p></li>
        <li class="pg-way ab-reveal"><span class="pg-way-ico" aria-hidden="true"><?= icon('award', 20) ?></span><h3>Technical expertise</h3><p>Agronomy, engineering, data, enterprise coaching or training for our teams.</p></li>
        <li class="pg-way ab-reveal"><span class="pg-way-ico" aria-hidden="true"><?= icon('basket', 20) ?></span><h3>Market connections</h3><p>Buyers, processors and retailers who can turn farmers’ produce into income.</p></li>
      </ul>
      <div class="pg-hero-actions ab-reveal">
        <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Partnership enquiry') ?>" class="pg-btn">Start a partnership conversation <?= icon('arrow-right', 16) ?></a>
        <a href="<?= SITE_URL ?>/partners.php" class="pg-btn pg-btn-ghost">Meet our partners</a>
      </div>
    </div>
  </section>
</main>

<?= ab_lightbox() ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
