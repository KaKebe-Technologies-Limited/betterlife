<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
$pageTitle = 'Our Programmes';
$activePage = 'programs';
$pageDescription = 'BetterLife’s programmes help families grow food, earn a steady income and cope with a changing climate across five connected areas of work.';

$areas = pp_areas();
$projects = pp_projects();

// How many projects sit behind each programme area (home area or linked)
$projectCount = [];
foreach ($areas as $slug => $a) {
    $projectCount[$slug] = count(array_filter($projects, fn($p) => $p['area'] === $slug || in_array($slug, $p['also'] ?? [], true)));
}

// Three documented results, each kept with its project, group and measurement context
$evidence = [
    ['72%', 'Adopted sack or box gardening', 'Women in the programme, reported after training', 'womens-climate-resilience-yumbe',
        ['assets/img/programmes/yumbe-participant-smile.jpg', 'A smiling participant in the Yumbe programme', '50% 22%']],
    ['78%', 'Moved into sustainable income pathways', 'Refugee and host-community participants, reported across target groups', 'rise',
        ['assets/img/programmes/yumbe-stall-woman.jpg', 'A woman at her stall beside a brick shop', '72% 50%']],
    ['4,500+', 'Students engaged through school climate education', 'Learners at partner schools, across BetterLife’s school work', 'green-libraries-eco-labs',
        ['assets/img/betterlifeint-source/programs/program-photo-8.jpg', 'Pupils gathered outdoors for a school session', '50% 45%']],
];

// How the work connects: four petals and two circles (photographs not used on the About page)
$petals = [
    ['Farming', 'assets/img/programmes/yumbe-sack-tower.jpg', 'A tiered sack garden planted with seedlings in Yumbe', '50% 55%'],
    ['Water',   'assets/img/programmes/yumbe-water-carrying.jpg', 'A woman carrying water past a maize field in Yumbe', '50% 30%'],
    ['Energy',  'assets/img/solar-panel-installation-1.webp', 'A BetterLife team member checking a solar panel in Yumbe', '45% 35%'],
    ['Markets', 'assets/img/market-stall-vendor.webp', 'A woman standing at her market stall in Yumbe', '72% 45%'],
];
$dots = [
    ['Skills', 'assets/img/programmes/yumbe-training-listener.jpg', 'A participant listening during a training session in Yumbe', '35% 40%'],
    ['Information', 'assets/img/programmes/yumbe-session-phones.jpg', 'Participants at a training session in Yumbe, one checking a phone', '40% 40%'],
];

// Farming, training, school learning, food processing, clean energy and community organising
$strip = [
    ['assets/img/programmes/yumbe-fish-pond.jpg', 'A fish pond at a BetterLife-supported site in Yumbe'],
    ['assets/img/programmes/yumbe-circle-mat.jpg', 'A group meeting on a mat under a tree in Yumbe'],
    ['assets/img/programmes/bucket-garden.jpg', 'Vegetables growing in hanging buckets'],
    ['assets/img/programmes/rukungiri-pupils-writing.jpg', 'A pupil writing in class in Rukungiri'],
    ['assets/img/grain-milling-machine.webp', 'Grain being processed with a milling machine in Rukungiri'],
    ['assets/img/programmes/rukungiri-solar-banana.jpg', 'A solar panel among banana plants in Rukungiri'],
    ['assets/img/programmes/yumbe-shared-meal.jpg', 'Preparing a meal at a training day in Yumbe'],
    ['assets/img/programmes/rukungiri-sowing.jpg', 'Sowing seed by hand in Rukungiri'],
    ['assets/img/programmes/bottle-tower-garden.jpg', 'A tower garden built from plastic bottles'],
    ['assets/img/programmes/yumbe-circle-staff.jpg', 'BetterLife team members with a women’s group in Yumbe'],
    ['assets/img/programmes/rukungiri-grass-bundle.jpg', 'Carrying cut grass across a field in Rukungiri'],
    ['assets/img/programmes/yumbe-trellis-rows.jpg', 'Trellised vegetables in Yumbe'],
];

// The strip in two rows that glide in opposite directions; each photograph at its natural shape
$stripRows = [array_slice($strip, 0, 6), array_slice($strip, 6)];
$shape = function (string $p): float { $s = @getimagesize(__DIR__ . '/' . $p); return $s ? round($s[0] / $s[1], 3) : 1.0; };

// Participants in their own words (see pp_quotes()): three voices from community and savings groups, one per language
$voices = ['amina', 'esther', 'fatima'];

$feature = 'womens-climate-resilience-yumbe';
$side = ['rise', 'betterlife-spring'];

$heroImg = 'assets/img/about/rukungiri-farmer-maize.jpg';
$heroV = ab_variants($heroImg);
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/programmes.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="100vw" fetchpriority="high">'
        : '')
    . pp_quotes_head($voices)
    . '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg" id="top">
  <?= ab_brush_defs() ?>

  <!-- 1. Photographic hero, with an index of the five programme areas -->
  <section class="pg-hero is-narrow has-index" aria-labelledby="pgTitle">
    <div class="pg-hero-media">
      <?= ab_img($heroImg, 'A farmer tending a tall maize crop in Rukungiri, Uganda', '', false, 'style="object-position: 58% 42%"', '100vw') ?>
      <?php if (is_file(__DIR__ . '/assets/video/programmes-hero-720.mp4')): ?>
        <!-- Silent film, one shot per programme area in index order (sowing, milling, a classroom, solar, a phone);
             loaded after the page on wider screens only (see programmes.js); the photograph stays as the fallback -->
        <video class="pg-hero-video" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-cues="0,3.2,6,8.8,11.6" data-src="<?= h(asset_url('assets/video/programmes-hero-720.mp4')) ?>" data-src-hd="<?= h(asset_url('assets/video/programmes-hero-1080.mp4')) ?>"></video>
      <?php endif; ?>
    </div>
    <button type="button" class="ab-motion-toggle pg-film-toggle" aria-pressed="false" aria-label="Pause background film" hidden>
      <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6v12M15 6v12"/></svg>
      <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l10.5-6.5Z"/></svg>
    </button>
    <div class="container pg-hero-inner">
      <div class="pg-hero-copy">
        <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">Programmes</span></nav>
        <p class="pg-hero-kicker">Our programmes</p>
        <h1 id="pgTitle">Food security and sustainable <?= ab_mark('livelihoods', 31) ?></h1>
        <p class="pg-hero-lead">Our programmes help families grow more reliable food, earn a steady income and cope with a changing climate. A family facing drought may also face hunger, unemployment and weak access to markets, so our work connects around the person.</p>
        <div class="pg-hero-actions">
          <a href="#areas" class="pg-btn">Explore the five programme areas <?= icon('chevron-down', 16) ?></a>
          <a href="<?= SITE_URL ?>/projects.php" class="pg-btn pg-btn-ghost">See all projects</a>
        </div>
      </div>
      <nav class="pg-hero-index" aria-label="Programme areas">
        <?php $n = 0; foreach ($areas as $slug => $a): $n++; ?>
          <a href="#<?= h($slug) ?>" data-i="<?= $n - 1 ?>"><b><?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></b><span><?= h($a['short']) ?></span></a>
        <?php endforeach; ?>
      </nav>
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

  <!-- 3. Five programme areas: a list beside one large photograph that follows the area pointed at or focused
       (on touch screens and narrow windows each area is a card with its own photograph) -->
  <section class="pg-areas" id="areas" aria-labelledby="pgAreasTitle">
    <div class="container">
      <div class="pg-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">Programme areas</span>
          <h2 id="pgAreasTitle">Five areas of work</h2>
        </div>
        <p class="ab-head-sub pg-head-note">Each area brings together related activities, projects and partners. Choose one to see how the work is delivered and what it has achieved.</p>
      </div>
      <div class="pg-atlas ab-reveal" data-atlas>
        <ol class="pg-atlas-list">
          <?php $n = 0; foreach ($areas as $slug => $a): $n++; [$img, $alt, $pos] = $a['image']; $num = str_pad((string) $n, 2, '0', STR_PAD_LEFT); ?>
            <li class="pg-atlas-item<?= $n === 1 ? ' is-on' : '' ?>" id="<?= h($slug) ?>" data-i="<?= $n - 1 ?>">
              <div class="pg-atlas-thumb"><?= ab_img($img, $alt, '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 720px) 100vw, 50vw') ?><span aria-hidden="true"><?= $num ?></span></div>
              <span class="pg-atlas-n" aria-hidden="true"><?= $num ?></span>
              <div class="pg-atlas-body">
                <p class="pg-area-meta"><?= icon($a['icon'], 15) ?> <?= $projectCount[$slug] ?> <?= $projectCount[$slug] === 1 ? 'project' : 'projects' ?></p>
                <h3><a href="<?= h(pp_area_url($slug)) ?>"><?= h($a['short']) ?></a></h3>
                <p class="pg-atlas-desc"><?= h($a['card']) ?></p>
                <span class="pg-atlas-cta" aria-hidden="true">Explore Programme <?= icon('arrow-right', 15) ?></span>
              </div>
              <span class="pg-atlas-go" aria-hidden="true"><?= icon('arrow-right', 18) ?></span>
            </li>
          <?php endforeach; ?>
        </ol>
        <p class="pg-atlas-hint" aria-hidden="true">Swipe to see all five areas <?= icon('arrow-right', 14) ?></p>
        <div class="pg-atlas-stage" aria-hidden="true">
          <?php $n = 0; foreach ($areas as $slug => $a): $n++; [$img, , $pos] = $a['image']; ?>
            <div class="pg-atlas-slide<?= $n === 1 ? ' is-on' : '' ?>">
              <div class="pg-atlas-shot">
                <?= ab_img($img, '', '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 1000px) 1px, 680px') ?>
                <span class="pg-atlas-big"><?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
              </div>
              <div class="pg-atlas-panel">
                <span class="pg-kicker"><?= h($a['short']) ?></span>
                <p><?= h($a['card']) ?></p>
                <a href="<?= h(pp_area_url($slug)) ?>" class="pg-link" tabindex="-1">Explore Programme <?= icon('arrow-right', 15) ?></a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. Selected projects: one feature and two companions -->
  <section class="pg-projects" aria-labelledby="pgProjectsTitle">
    <div class="container">
      <div class="pg-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">Selected projects</span>
          <h2 id="pgProjectsTitle">How the work looks in practice</h2>
        </div>
        <a href="<?= SITE_URL ?>/projects.php" class="pg-link">Explore all projects <?= icon('arrow-right', 15) ?></a>
      </div>
      <div class="pg-feature-grid">
        <?php $f = $projects[$feature]; ?>
        <article class="pg-feature ab-reveal">
          <?= ab_img($f['image'][0], $f['image'][1], 'pg-feature-img', true, 'style="object-position: ' . h($f['image'][2]) . '"', '(max-width: 900px) 100vw, 700px') ?>
          <?= $feature === pp_film()['project'] ? pp_film_button() : '' ?>
          <div class="pg-feature-panel">
            <span class="pg-kicker"><?= h($areas[$f['area']]['short']) ?></span>
            <h3><a href="<?= h(pp_project_url($feature, $f)) ?>"><?= h($f['title']) ?></a></h3>
            <p class="pg-feature-place"><?= icon('map-pin', 14) ?> <?= h($f['location']) ?></p>
            <p><?= h($f['summary']) ?></p>
            <p class="pg-partner"><?= icon('heart', 14) ?> With <?= h($f['partner']) ?></p>
            <span class="pg-more" aria-hidden="true">Read the project <?= icon('arrow-right', 15) ?></span>
          </div>
        </article>
        <div class="pg-side">
          <?php foreach ($side as $slug): ?><?= pp_project_card($slug, $projects[$slug], $areas, '(max-width: 900px) 100vw, 260px') ?><?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. Evidence: three results, each with its project and a photograph -->
  <section class="pg-proof" aria-labelledby="pgEvidenceTitle">
    <div class="container">
      <div class="pg-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">Evidence from the field</span>
          <h2 id="pgEvidenceTitle">What changed, project by project</h2>
        </div>
        <p class="ab-head-sub pg-head-note">Each figure belongs to the project and group named with it, not to the organisation as a whole. Fuller context is on each project page and in our <a href="<?= SITE_URL ?>/impact-reports.php">impact reports</a>.</p>
      </div>
      <?= pp_tiles($evidence, $projects) ?>
    </div>
  </section>

  <?= pp_quotes_section($voices) ?>

  <!-- 6. Around the programmes: two rows of photographs that glide in opposite directions as the page scrolls (about.js) -->
  <section class="pg-strip pg-glide" aria-labelledby="pgStripTitle">
    <div class="container pg-strip-head ab-reveal">
      <div>
        <span class="ab-eyebrow">Around the programmes</span>
        <h2 id="pgStripTitle">Ponds, classrooms, circles and seedlings</h2>
      </div>
    </div>
    <div class="ab-glide ab-reveal" role="group" aria-label="Photographs from our programmes. Select one to see it larger.">
      <?php foreach ($stripRows as $r => $row): ?>
        <div class="ab-glide-row<?= $r === 0 ? ' is-tall' : '' ?>" data-glide="<?= $r % 2 ? -1 : 1 ?>">
          <?php foreach ($row as [$p, $cap]): ?>
            <?= ab_photo($p, $cap, $cap, 'strip', 'ab-glide-item', $r === 0 ? '(max-width: 720px) 60vw, 460px' : '(max-width: 720px) 50vw, 400px', 'style="--ar: ' . $shape($p) . '"') ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 7. Closing invitation -->
  <section class="pg-close" aria-labelledby="pgCloseTitle">
    <div class="pg-close-bg"><?= ab_img('assets/img/programmes/yumbe-women-walking.jpg', '', '', true, 'style="object-position: 50% 60%"', '100vw') ?></div>
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
<?= pp_film_dialog() ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
