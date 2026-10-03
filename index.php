<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'Home';
$activePage = 'home';
$pageDescription = 'BetterLife International is a refugee-led organisation founded in Uganda in 2021. We work with refugees, displaced families, women, young people and farming communities in five African countries on food, skills, savings and markets.';

$map = require __DIR__ . '/includes/map-paths.php';
$areas = pp_areas();
$film = pp_film();
$voice = pp_quotes()['josephine'];

// Editable in Admin → Settings; "begin again" carries the painted underline when present
$heroTitle = setting($pdo, 'hero_title', 'Led by people who understand what it means to begin again.');
$heroLead  = setting($pdo, 'hero_subtitle', 'BetterLife works with women, young people, refugees, displaced families and farming communities to turn climate pressure into practical action.');
$heroKick  = setting($pdo, 'hero_kicker', 'Founded in Uganda. Working across five African countries.');
$heroTitleHtml = preg_replace('/\b(begin again)\b/i', '<span class="ab-mark">$1<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="' . lp_brush_d(4, 17, 238, 14, 22, 17, 0.02) . '"/></svg></span>', h($heroTitle), 1);

// The hero art: one coordinate system for the map, the film window and the labels
// (map-paths.php art box; Africa occupies x 40 to 591.8, y 53 to 673)
$art = ['x' => 0, 'y' => 40, 'w' => 720, 'h' => 650];
[$bx, $by, $bw, $bh] = $map['africa_box'];
$win = sprintf('left:%.3f%%;top:%.3f%%;width:%.3f%%;height:%.3f%%', ($bx - $art['x']) / $art['w'] * 100, ($by - $art['y']) / $art['h'] * 100, $bw / $art['w'] * 100, $bh / $art['h'] * 100);
$heroLabels = [
    'South Sudan'                      => ['South Sudan', 612, 292, 'start'],
    'Uganda'                           => ['Uganda',      612, 352, 'start', 'where we began'],
    'Tanzania'                         => ['Tanzania',    612, 420, 'start'],
    'Democratic Republic of the Congo' => ['DR Congo',    262, 472, 'end'],
    'Ghana'                            => ['Ghana',       150, 404, 'middle'],
];

// Figures (the same verified set as the About page and the Programmes evidence). Shown separately; never summed.
$figures = [
    ['110,000', 'people reached since 2021'],
    ['About 122', 'farms established'],
    ['310', 'households supported'],
    ['5', 'community farms established'],
    ['5', 'schools supported with meals'],
    ['65', 'community boreholes supported'],
    ['50,000+', 'tree seedlings raised in supported nurseries'],
    ['5', 'countries'],
];

// Five programme areas, each with a portrait not used on the Programmes overview
$areaPhotos = [
    'climate-resilient-agriculture'      => ['assets/img/programmes/yumbe-garden-woman.jpg', 'A woman standing in a vegetable garden in Yumbe', '50% 30%'],
    'green-skills-livelihoods'           => ['assets/img/programmes/yumbe-poultry-house.jpg', 'A woman standing in the doorway of her poultry house', '50% 35%'],
    'climate-education-youth-leadership' => ['assets/img/classroom-climate-club.webp', 'Pupils raising their hands in a school climate club', '50% 40%'],
    'clean-energy-water-restoration'     => ['assets/img/programmes/yumbe-water-girl.jpg', 'A girl at a water point in Yumbe', '50% 40%'],
    'digital-innovation'                 => ['assets/img/programmes/rukungiri-phone-solar.jpg', 'A young woman using her phone beside a solar panel in Rukungiri', '50% 30%'],
];

// Where we work: the places named on the About page, projected onto the same map
$pr = $map['proj'];
$project = fn(float $lon, float $lat): array => [
    round(deg2rad($lon) * $pr['scale'] + $pr['ox'], 1),
    round(-log(tan(M_PI / 4 + deg2rad($lat) / 2)) * $pr['scale'] + $pr['oy'], 1),
];
$places = [
    ['Yumbe and Bidi Bidi', 31.3, 3.47], ['Rukungiri', 29.92, -0.79], ['Juba', 31.58, 4.85], ['Yambio', 28.40, 4.57], ['Kayanga', 31.13, -1.6],
];
$countries = [
    'uganda'      => ['Uganda',      'Uganda',                           'Where BetterLife began. Yumbe and Bidi Bidi in West Nile, and our communal farm in Rukungiri.'],
    'south-sudan' => ['South Sudan', 'South Sudan',                      'An office in Juba and field presence in Yambio, including solar-powered irrigation.'],
    'tanzania'    => ['Tanzania',    'Tanzania',                         'A base in Kayanga, Karagwe, working with FADECO.'],
    'ghana'       => ['Ghana',       'Ghana',                            'Programme activity, including climate education.'],
    'drc'         => ['DR Congo',    'Democratic Republic of the Congo', 'Programme activity.'],
];
$uganda = $map['countries']['Uganda']['c'];

// School feeding (Rukungiri): a mosaic of five tiles. Tile 2 is a short silent film from the school kitchen
// (the washing-up photograph stands in until the film exists); tile 3 is the head teacher.
$schoolPhotos = [
    1 => ['assets/img/home/school-cup-up.jpg', 'A pupil drinking from a cup at school in Rukungiri', '45% 40%'],
    2 => ['assets/img/home/school-washing-cups.jpg', 'Pupils washing their cups in a basin at school', '50% 55%'],
    3 => ['assets/img/home/head-teacher.jpg', 'The head teacher of a school BetterLife supports in Rukungiri', '50% 22%', 'The head teacher'],
    4 => ['assets/img/home/school-friends.jpg', 'Four pupils smiling together at school in Rukungiri', '50% 35%'],
    5 => ['assets/img/home/school-pink-cups.jpg', 'Pupils with their pink cups in class', '55% 45%'],
];
$schoolFilm = ['src' => 'assets/video/school-kitchen.mp4', 'poster' => 'assets/img/home/school-kitchen.jpg',
    'alt' => 'Cooks and pupils preparing and serving a meal in a school kitchen in Rukungiri', 'label' => 'In the school kitchen'];
$hasSchoolFilm = is_file(__DIR__ . '/' . $schoolFilm['src']) && is_file(__DIR__ . '/' . $schoolFilm['poster']);

// The farm model as a loop (supplied by BetterLife; see also farm.php)
$loop = [
    ['short' => 'Seedlings and manure', 'title' => 'Seedlings and organic manure',
        'text' => 'Refugee, displaced and vulnerable host-community families receive seedlings for vegetables, maize and other crops, organic manure and practical training to start producing at home.',
        'img' => ['assets/img/programmes/yumbe-seedling-trays.jpg', 'Seedlings growing in nursery trays', '50% 50%']],
    ['short' => 'A harvest at home', 'title' => 'A harvest at home',
        'text' => 'Families grow food to eat first. What the household does not need becomes surplus it can sell.',
        'img' => ['assets/img/home/harvest-tomatoes.jpg', 'A woven basket full of ripe tomatoes', '50% 62%']],
    ['short' => 'We buy the surplus', 'title' => 'We buy the surplus',
        'text' => 'BetterLife Agro Tourism Farm buys surplus produce from participating farmers, turning a good season into cash and capital for the next one.',
        'img' => ['assets/img/home/farm-weighing.jpg', 'A woman weighing a sack of produce on a hanging scale', '45% 45%']],
    ['short' => 'Value added, sold on', 'title' => 'Value added, sold on',
        'text' => 'The farm processes, packages and sells ghee, yoghurt, honey and organic manure, creating a market that keeps buying.',
        'img' => ['assets/img/home/farm-mill.jpg', 'A woman feeding grain into a milling machine', '50% 40%']],
];

// Products from the farm, each photographed by BetterLife
$products = [
    ['assets/img/product-yoghurt-real.jpg', 'BetterLife Yoghurt', 'Strawberry and vanilla', 'Bottles of BetterLife strawberry and vanilla yoghurt'],
    ['assets/img/product-honey-real.jpg', 'BetterLife Honey', 'Pure honey in jars', 'Stacked jars of BetterLife Honey'],
    ['assets/img/product-ghee-real.jpg', 'BetterLife Ghee', 'Organic, from 100% pure cow butter', 'Jars of BetterLife Organic Ghee'],
    ['assets/img/product-organic-boost.jpg', 'Organic manure', 'BetterLife Organic Boost liquid fertiliser', 'Containers of BetterLife Organic Boost organic fertiliser'],
];

$partners = [
    ['foundation-s.png',                'Foundation S, The Sanofi Collective'],
    ['farm-radio-international.jpg',    'Farm Radio International'],
    ['dovetail-impact-foundation.webp', 'Dovetail Impact Foundation'],
    ['world-food-programme.svg',        'World Food Programme'],
    ['hbcu-green-fund.png',             'HBCU Green Fund'],
    ['fadeco.png',                      'FADECO'],
    ['icpac.svg',                       'ICPAC, IGAD Climate Prediction and Applications Centre'],
    ['moonshot.svg',                    'Moonshot'],
];

$heroImg = 'assets/img/farm-field-3.jpg';
$heroV = ab_variants($heroImg);
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css', 'assets/css/home.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/programmes.js', 'assets/js/home.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="(max-width: 900px) 90vw, 46vw" fetchpriority="high">'
        : '')
    . pp_quotes_head(['josephine'])
    . '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab hm" id="top">
  <?= ab_brush_defs() ?>

  <!-- 1. Opening: the continent as a window onto the work -->
  <section class="hm-hero" aria-labelledby="hmTitle">
    <div class="container hm-hero-grid">
      <div class="hm-hero-copy">
        <p class="hm-kicker"><span class="hm-kicker-dot" aria-hidden="true"></span><?= h($heroKick) ?></p>
        <h1 id="hmTitle"><?= $heroTitleHtml ?></h1>
        <p class="hm-lead"><?= h($heroLead) ?></p>
        <div class="hm-actions">
          <a href="<?= SITE_URL ?>/programs.php" class="pg-btn">Explore our work <?= icon('arrow-right', 16) ?></a>
          <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Partnership enquiry') ?>" class="hm-btn-line">Partner with us</a>
        </div>
        <ul class="hm-led" aria-label="How BetterLife is led">
          <li>Refugee-led</li><li>Youth-led</li><li>Women-led</li>
        </ul>
      </div>

      <div class="hm-art">
        <svg class="hm-art-back" viewBox="<?= "{$art['x']} {$art['y']} {$art['w']} {$art['h']}" ?>" aria-hidden="true" focusable="false">
          <path class="hm-world" d="<?= $map['world'] ?>"/>
          <g filter="url(#lpBrush)">
            <path class="f-green" d="<?= lp_brush_d(-30, 330, 120, 300, 54, 3) ?>"/>
            <path class="f-blue"  d="<?= lp_brush_d(470, 600, 650, 640, 44, 7, -0.04) ?>"/>
          </g>
        </svg>

        <!-- The film plays inside the outline of Africa (CSS mask); the photograph stays underneath as the fallback -->
        <div class="hm-window" style="<?= $win ?>">
          <?= ab_img($heroImg, 'A mother carrying her baby in a farm field', 'hm-window-img', false, 'style="object-position: 62% 40%"', '(max-width: 900px) 90vw, 46vw') ?>
          <?php if (is_file(__DIR__ . '/assets/video/home-africa.mp4')): ?>
            <!-- Silent loop from BetterLife's Yumbe film: a woman carrying a basin, women at a training session,
                 peppers on the plant, two girls laughing, a young man in Yumbe town. Loaded after the page on wider screens. -->
            <video class="hm-window-video" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-src="<?= h(asset_url('assets/video/home-africa.mp4')) ?>?v=<?= filemtime(__DIR__ . '/assets/video/home-africa.mp4') ?>"></video>
          <?php endif; ?>
        </div>

        <svg class="hm-art-front" viewBox="<?= "{$art['x']} {$art['y']} {$art['w']} {$art['h']}" ?>" role="img" aria-label="Map of Africa showing the five countries where BetterLife works: Uganda, South Sudan, Tanzania, the Democratic Republic of the Congo and Ghana">
          <path class="hm-outline" d="<?= $map['africa'] ?>" pathLength="1"/>
          <g class="hm-countries">
            <?php foreach ($heroLabels as $key => $l): ?>
              <path class="<?= $key === 'Uganda' ? 'is-home' : '' ?>" d="<?= $map['countries'][$key]['d'] ?>"/>
            <?php endforeach; ?>
          </g>
          <g class="hm-labels">
            <?php foreach ($heroLabels as $key => [$name, $lx, $ly, $anchor]): [$cx, $cy] = $map['countries'][$key]['c'];
              $tx = $anchor === 'start' ? $lx - 8 : ($anchor === 'end' ? $lx + 8 : $lx);
              $ty = $anchor === 'middle' ? $ly - 20 : $ly - 6; ?>
              <path class="hm-lead-line" d="M<?= $cx ?> <?= $cy ?> L<?= $tx ?> <?= $ty ?>"/>
            <?php endforeach; ?>
            <?php $i = 0; foreach ($heroLabels as $key => $l): [$cx, $cy] = $map['countries'][$key]['c']; ?>
              <circle class="hm-pulse" cx="<?= $cx ?>" cy="<?= $cy ?>" r="6" style="--d: <?= $i++ * .5 ?>s"/>
              <circle class="hm-pin<?= $key === 'Uganda' ? ' is-home' : '' ?>" cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $key === 'Uganda' ? 7 : 5.5 ?>"/>
            <?php endforeach; ?>
            <?php foreach ($heroLabels as $key => $l): [$name, $lx, $ly, $anchor] = $l; ?>
              <text x="<?= $lx ?>" y="<?= $ly ?>" text-anchor="<?= $anchor ?>"><?= h($name) ?><?php if (!empty($l[4])): ?><tspan class="hm-label-note" x="<?= $lx ?>" dy="1.25em"><?= h($l[4]) ?></tspan><?php endif; ?></text>
            <?php endforeach; ?>
          </g>
        </svg>

        <div class="hm-stat">
          <svg class="hm-stat-brush" viewBox="0 0 130 80" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(6, 48, 126, 34, 40, 21) ?>"/></svg>
          <strong><span class="ab-count" data-count="110000">110,000</span></strong>
          <span>people reached<br>since 2021</span>
        </div>

        <button type="button" class="hm-motion" aria-pressed="false" aria-label="Pause the film" hidden>
          <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6v12M15 6v12"/></svg>
          <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l10.5-6.5Z"/></svg>
        </button>
      </div>
    </div>
    <p class="hm-legend container" aria-hidden="true">Uganda · South Sudan · Tanzania · DR Congo · Ghana</p>
  </section>

  <!-- 2. Figures, moving slowly across the page -->
  <section class="hm-ribbon" aria-label="BetterLife in figures">
    <div class="hm-ribbon-track">
      <?php foreach ([false, true] as $copy): ?>
        <ul class="hm-ribbon-list"<?= $copy ? ' aria-hidden="true"' : '' ?>>
          <?php foreach ($figures as [$num, $label]): ?>
            <li><strong><?= h($num) ?></strong> <span><?= h($label) ?></span><i aria-hidden="true"><?= icon('leaf', 18) ?></i></li>
          <?php endforeach; ?>
        </ul>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 3. Who we are -->
  <section class="hm-origin" aria-labelledby="hmOriginTitle">
    <div class="container hm-origin-grid">
      <div class="hm-origin-copy ab-reveal">
        <span class="ab-eyebrow">Who we are</span>
        <h2 id="hmOriginTitle">Started in 2021 with USD 200 and <?= ab_mark('lived experience', 47) ?></h2>
        <p class="ab-lead">BetterLife International was founded in Uganda by a refugee-led team of young people who had grown up facing many of the challenges our communities face today.</p>
        <p>For a family living with displacement, unreliable rain and few ways to earn, finding the next meal can take up much of the day. So we work on what changes that: land to farm, skills to use and enterprises that bring in money.</p>
        <div class="hm-links">
          <a href="<?= SITE_URL ?>/about.php" class="pg-btn">Read our story <?= icon('arrow-right', 16) ?></a>
          <a href="<?= SITE_URL ?>/team.php" class="pg-link">Meet the team <?= icon('arrow-right', 15) ?></a>
        </div>
      </div>
      <div class="hm-polaroids ab-reveal" aria-label="Photographs of the BetterLife team and communities">
        <svg class="ab-strokes" viewBox="0 0 600 560" preserveAspectRatio="none" aria-hidden="true" focusable="false">
          <g filter="url(#lpBrush)">
            <path class="f-blue"  d="<?= lp_brush_d(360, 30, 600, 6, 48, 41) ?>"/>
            <path class="f-green" d="<?= lp_brush_d(-20, 470, 220, 500, 44, 45) ?>"/>
          </g>
        </svg>
        <figure class="hm-polaroid p-1"><?= ab_img('assets/img/field-team-group-under-tree.webp', 'The BetterLife field team standing together under a tree', '', true, 'style="object-position: 50% 45%"', '(max-width: 900px) 70vw, 360px') ?><figcaption>The field team</figcaption></figure>
        <figure class="hm-polaroid p-2"><?= ab_img('assets/img/smiles-group-under-tree-2.webp', 'A large community group celebrating together under a tree', '', true, '', '(max-width: 900px) 60vw, 320px') ?><figcaption>Together under the tree</figcaption></figure>
        <figure class="hm-polaroid p-3"><?= ab_img('assets/img/yumbe-greenhouse-group.webp', 'Women and the BetterLife team in front of a greenhouse in Yumbe', '', true, '', '(max-width: 900px) 60vw, 300px') ?><figcaption>A greenhouse in Yumbe</figcaption></figure>
        <div class="hm-stamp s-year" aria-hidden="true"><span>Founded</span><strong>2021</strong></div>
        <div class="hm-stamp s-money" aria-hidden="true"><span>Started with</span><strong>USD 200</strong></div>
      </div>
    </div>
  </section>

  <!-- 4. Five areas of work: tall panels that open as you point at or focus them (a swipeable row on touch screens) -->
  <section class="hm-areas" aria-labelledby="hmAreasTitle">
    <div class="container">
      <div class="pg-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">What we do</span>
          <h2 id="hmAreasTitle">Five ways into a better season</h2>
        </div>
        <a href="<?= SITE_URL ?>/programs.php" class="pg-link">All programmes <?= icon('arrow-right', 15) ?></a>
      </div>
      <ol class="hm-panels ab-reveal" data-panels>
        <?php $n = 0; foreach ($areas as $slug => $a): $n++; [$img, $alt, $pos] = $areaPhotos[$slug]; ?>
          <li class="hm-panel<?= $n === 1 ? ' is-on' : '' ?>">
            <?= ab_img($img, $alt, 'hm-panel-img', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 720px) 84vw, (max-width: 1000px) 42vw, 560px') ?>
            <span class="hm-panel-num" aria-hidden="true"><?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
            <span class="hm-panel-tab" aria-hidden="true"><?= h($a['short']) ?></span>
            <div class="hm-panel-body">
              <span class="hm-panel-ico" aria-hidden="true"><?= icon($a['icon'], 18) ?></span>
              <h3><a href="<?= h(pp_area_url($slug)) ?>"><?= h($a['short']) ?></a></h3>
              <p><?= h($a['card']) ?></p>
              <span class="hm-panel-cta" aria-hidden="true">Explore <?= icon('arrow-right', 15) ?></span>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
      <p class="hm-swipe" aria-hidden="true">Swipe to see all five <?= icon('arrow-right', 14) ?></p>
    </div>
  </section>

  <!-- 5. School feeding: why a meal at school matters -->
  <section class="hm-school" aria-labelledby="hmSchoolTitle">
    <div class="container hm-school-grid">
      <div class="hm-school-copy ab-reveal">
        <span class="ab-eyebrow">School feeding</span>
        <h2 id="hmSchoolTitle">Feeding children so they can <?= ab_mark('learn', 81) ?></h2>
        <p class="ab-lead">Most of our work in schools begins with food. We support school feeding in five schools, so the food security work that starts on family farms also reaches children during the school day.</p>
        <ul class="hm-why">
          <li><span class="hm-why-ico" aria-hidden="true"><?= icon('sun', 18) ?></span><span><b>Energy to learn.</b> Hungry children struggle to concentrate. A meal at school helps them stay focused through the day.</span></li>
          <li><span class="hm-why-ico" aria-hidden="true"><?= icon('heart', 18) ?></span><span><b>Less pressure at home.</b> When a harvest falls short, a school meal takes some of the weight off families.</span></li>
          <li><span class="hm-why-ico" aria-hidden="true"><?= icon('book', 18) ?></span><span><b>More than a meal.</b> The same schools are where our climate clubs, Green Libraries and Eco Labs reach young people.</span></li>
        </ul>
        <div class="hm-school-foot">
          <p class="hm-school-fig"><strong>5</strong><span>schools supported<br>with meals</span></p>
          <a href="<?= h(pp_area_url('climate-education-youth-leadership')) ?>" class="pg-link">Our work with schools <?= icon('arrow-right', 15) ?></a>
        </div>
      </div>
      <div class="hm-mosaic ab-reveal" aria-label="Photographs of school feeding in Rukungiri">
        <?php foreach ($schoolPhotos as $t => $ph): if ($t === 2 && $hasSchoolFilm): ?>
          <div class="hm-tile t-2 hm-tile-film" data-tile-film>
            <?= ab_img($schoolFilm['poster'], $schoolFilm['alt'], '', true, '', '(max-width: 900px) 100vw, 440px') ?>
            <!-- Silent loop: a meal stirred in the pot, served, the cups, the washing-up. Loads when near the screen on wider screens only. -->
            <video muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-src="<?= h(asset_url($schoolFilm['src'])) ?>?v=<?= filemtime(__DIR__ . '/' . $schoolFilm['src']) ?>"></video>
            <span class="hm-tile-label" aria-hidden="true"><i></i><?= h($schoolFilm['label']) ?></span>
          </div>
        <?php else: [$p, $alt, $pos] = $ph; $label = $ph[3] ?? ''; ?>
          <?= ab_photo($p, $alt, $alt, 'school', 'hm-tile t-' . $t, $t === 1 ? '(max-width: 900px) 50vw, 300px' : '(max-width: 900px) 50vw, 260px', 'style="--pos: ' . h($pos) . '"' . ($label ? ' data-label="' . h($label) . '"' : '')) ?>
        <?php endif; endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 6. The farm model: inputs out, surplus bought back, value added, sold on -->
  <section class="hm-farm" aria-labelledby="hmFarmTitle">
    <div class="container">
      <div class="hm-farm-head ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">BetterLife Agro Tourism Farm</span>
          <h2 id="hmFarmTitle">From a farmer&rsquo;s surplus to the <?= ab_mark('market shelf', 83) ?></h2>
        </div>
        <p class="ab-head-sub">The farm is our engine for food security and value addition. It is a stepping-stone, not a destination: the aim is for families to grow, sell and plan for themselves.</p>
      </div>
      <div class="hm-loop-grid">
        <div class="hm-loop ab-reveal" data-loop>
          <svg class="hm-loop-ring" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
            <defs><marker id="hmArrow" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="4.2" markerHeight="4.2" orient="auto-start-reverse"><path d="M1 1L9 5L1 9Z"/></marker></defs>
            <?php // Four quarter arcs, clockwise from the top node, each ending in an arrow before the next node
            $arc = fn(float $a1, float $a2): string => sprintf('M%.2f %.2f A38 38 0 0 1 %.2f %.2f', 50 + 38 * cos(deg2rad($a1)), 50 + 38 * sin(deg2rad($a1)), 50 + 38 * cos(deg2rad($a2)), 50 + 38 * sin(deg2rad($a2)));
            foreach ([[-62, -28], [28, 62], [118, 152], [208, 242]] as [$a1, $a2]): ?>
              <path class="hm-loop-arc" d="<?= $arc($a1, $a2) ?>" marker-end="url(#hmArrow)"/>
            <?php endforeach; ?>
          </svg>
          <div class="hm-loop-core"><span aria-hidden="true"><?= icon('leaf', 22) ?></span><strong>The farm model</strong><small>Every season builds on the last</small></div>
          <?php foreach ($loop as $i => $s): ?>
            <button type="button" class="hm-node n-<?= $i + 1 ?><?= $i === 0 ? ' is-on' : '' ?>" data-i="<?= $i ?>" aria-label="Step <?= $i + 1 ?>: <?= h($s['title']) ?>">
              <span class="hm-node-img"><?= ab_img($s['img'][0], '', '', true, 'style="object-position: ' . h($s['img'][2]) . '"', '(max-width: 720px) 34vw, 200px') ?></span>
              <span class="hm-node-cap" aria-hidden="true"><b><?= $i + 1 ?></b><span><?= h($s['short']) ?></span></span>
            </button>
          <?php endforeach; ?>
        </div>
        <ol class="hm-steps ab-reveal">
          <?php foreach ($loop as $i => $s): ?>
            <li class="<?= $i === 0 ? 'is-on' : '' ?>" data-i="<?= $i ?>">
              <span class="hm-step-num" aria-hidden="true"><?= $i + 1 ?></span>
              <div><h3><?= h($s['title']) ?></h3><p><?= h($s['text']) ?></p></div>
            </li>
          <?php endforeach; ?>
          <li class="hm-steps-again" aria-hidden="true"><?= icon('trending-up', 16) ?> And the loop starts again.</li>
        </ol>
      </div>

      <div class="hm-shelf-head ab-reveal">
        <h3>Made from what community farmers produce</h3>
        <a href="<?= SITE_URL ?>/products.php" class="pg-link">Shop our products <?= icon('arrow-right', 15) ?></a>
      </div>
      <ul class="hm-shelf ab-reveal">
        <?php foreach ($products as [$img, $name, $line, $alt]): ?>
          <li class="hm-product">
            <div class="hm-product-img"><?= ab_img($img, $alt, '', true, '', '(max-width: 720px) 70vw, 280px') ?></div>
            <h4><a href="<?= SITE_URL ?>/products.php"><?= h($name) ?></a></h4>
            <p><?= h($line) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="hm-links hm-farm-links ab-reveal">
        <a href="<?= SITE_URL ?>/farm.php" class="pg-btn">Discover the farm <?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </section>

  <!-- 7. Featured work and the Yumbe film -->
  <section class="hm-film" aria-labelledby="hmFilmTitle">
    <div class="container hm-film-grid">
      <div class="hm-film-copy ab-reveal">
        <span class="ab-eyebrow">Featured work &middot; Yumbe, Uganda</span>
        <h2 id="hmFilmTitle">What women in Yumbe taught us about climate resilience</h2>
        <p>Before introducing a single farming technique, we asked women how the changing climate was affecting their day. Their answers went far beyond crops: hours spent looking for water, the distance travelled for firewood and the choices families make when a harvest fails.</p>
        <p class="hm-film-fig"><strong><span class="ab-count" data-count="72">72</span>%</strong> <span>of women in the programme adopted sack or box gardening after training</span></p>
        <a href="<?= h(pp_project_url($film['project'], pp_projects()[$film['project']])) ?>" class="pg-link">Read the Yumbe story <?= icon('arrow-right', 15) ?></a>
      </div>
      <div class="hm-cinema ab-reveal">
        <?= ab_img('assets/img/home/yumbe-film-still.jpg', 'Two women in conversation at a training session in Yumbe', 'hm-cinema-img', true, '', '(max-width: 900px) 100vw, 720px') ?>
        <?php if (is_file(__DIR__ . '/' . $film['src'])): ?>
          <button type="button" class="hm-cinema-play" data-film="<?= h(pp_film_src($film)) ?>" data-film-title="<?= h($film['title']) ?>">
            <span class="pg-film-play-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 5.5v13l10.5-6.5Z"/></svg></span>
            <span class="hm-cinema-label">Watch the film<small><?= h($film['title']) ?> &middot; <?= (int) $film['minutes'] ?> minutes, with sound</small></span>
          </button>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- 8. In their words -->
  <section class="hm-voice" aria-labelledby="hmVoiceTitle">
    <div class="container">
      <figure class="hm-voice-fig ab-reveal">
        <h2 class="sr-only" id="hmVoiceTitle">In their words</h2>
        <span class="hm-voice-mark" aria-hidden="true">&ldquo;</span>
        <blockquote lang="<?= h($voice['lang']) ?>"><p><?= h($voice['q']) ?></p></blockquote>
        <p class="hm-voice-en" lang="en"><span class="sr-only">In English: </span><?= h($voice['en']) ?></p>
        <figcaption><b><?= h($voice['name']) ?></b><span><?= h($voice['role']) ?></span></figcaption>
        <svg class="hm-voice-stroke" viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path filter="url(#lpBrush)" class="f-green" d="<?= lp_brush_d(4, 17, 238, 14, 22, 61, 0.02) ?>"/></svg>
      </figure>
    </div>
  </section>

  <!-- 9. Where we work: a night map with lines from Uganda, where it began -->
  <section class="hm-reach" aria-labelledby="hmReachTitle">
    <div class="container hm-reach-grid">
      <div class="hm-map ab-reveal" data-reach-map>
        <svg viewBox="28 45 580 638" role="img" aria-label="Map of Africa with lines from Uganda to South Sudan, Tanzania, the Democratic Republic of the Congo and Ghana">
          <defs>
            <pattern id="hmDots" width="9" height="9" patternUnits="userSpaceOnUse"><circle cx="4.5" cy="4.5" r="1.5"/></pattern>
            <clipPath id="hmAfricaClip"><path d="<?= $map['africa'] ?>"/></clipPath>
          </defs>
          <path class="hm-map-land" d="<?= $map['africa'] ?>"/>
          <rect class="hm-map-dots" x="0" y="0" width="720" height="720" fill="url(#hmDots)" clip-path="url(#hmAfricaClip)"/>
          <g class="hm-map-countries">
            <?php foreach ($countries as $key => [$short, $mapKey]): ?>
              <path d="<?= $map['countries'][$mapKey]['d'] ?>" data-key="<?= $key ?>" class="<?= $key === 'uganda' ? 'is-home' : '' ?>"/>
            <?php endforeach; ?>
          </g>
          <g class="hm-arcs">
            <?php $k = 0; foreach ($countries as $key => [$short, $mapKey]): if ($key === 'uganda') continue; [$cx, $cy] = $map['countries'][$mapKey]['c'];
              $mx = ($uganda[0] + $cx) / 2; $my = min($uganda[1], $cy) - max(40, abs($cx - $uganda[0]) * 0.35); ?>
              <path d="M<?= $uganda[0] ?> <?= $uganda[1] ?> Q<?= round($mx, 1) ?> <?= round($my, 1) ?> <?= $cx ?> <?= $cy ?>" pathLength="1" data-key="<?= $key ?>" style="--d: <?= $k++ * .25 ?>s"/>
            <?php endforeach; ?>
          </g>
          <g class="hm-places">
            <?php foreach ($places as [$name, $lon, $lat]): [$px, $py] = $project($lon, $lat); ?>
              <circle cx="<?= $px ?>" cy="<?= $py ?>" r="3"><title><?= h($name) ?></title></circle>
            <?php endforeach; ?>
          </g>
          <g class="hm-map-pins">
            <?php foreach ($countries as $key => [$short, $mapKey]): [$cx, $cy] = $map['countries'][$mapKey]['c']; ?>
              <circle class="hm-map-glow" cx="<?= $cx ?>" cy="<?= $cy ?>" r="16" data-key="<?= $key ?>"/>
              <circle class="hm-map-pin" cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $key === 'uganda' ? 7 : 5 ?>" data-key="<?= $key ?>"/>
              <text class="hm-map-name" x="<?= $key === 'ghana' ? $cx : $cx + 18 ?>" y="<?= $key === 'ghana' ? $cy - 22 : $cy + 6 ?>" text-anchor="<?= $key === 'ghana' ? 'middle' : 'start' ?>" data-key="<?= $key ?>" aria-hidden="true"><?= h($short) ?></text>
            <?php endforeach; ?>
          </g>
        </svg>
      </div>
      <div class="hm-reach-copy ab-reveal">
        <span class="ab-eyebrow">Where we work</span>
        <h2 id="hmReachTitle">Five countries, five <?= ab_mark('starting points', 57) ?></h2>
        <p>The aim is the same in every community: food on the table and a way to earn. The starting point is not. Displacement, land, water, skills and local work all differ, so each programme begins with what a community already has.</p>
        <ul class="hm-countries-list">
          <?php foreach ($countries as $key => [$short, $mapKey, $note]): ?>
            <li data-key="<?= $key ?>"><b><?= h($short) ?></b><span><?= h($note) ?></span></li>
          <?php endforeach; ?>
        </ul>
        <a href="<?= SITE_URL ?>/about.php#where" class="pg-link">See where we work <?= icon('arrow-right', 15) ?></a>
      </div>
    </div>
  </section>

  <!-- 10. Partners -->
  <section class="hm-partners" aria-labelledby="hmPartnersTitle">
    <div class="container">
      <div class="hm-partners-head ab-reveal">
        <h2 id="hmPartnersTitle">Working alongside</h2>
        <a href="<?= SITE_URL ?>/partners.php" class="pg-link">Meet our partners <?= icon('arrow-right', 15) ?></a>
      </div>
      <ul class="hm-logos ab-reveal">
        <?php foreach ($partners as [$file, $name]): ?>
          <li class="lg-<?= h(pathinfo($file, PATHINFO_FILENAME)) ?>"><img src="<?= h(asset_url('assets/img/partners/' . $file)) ?>" alt="<?= h($name) ?>" loading="lazy"></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- 11. Closing invitation -->
  <section class="hm-close" aria-labelledby="hmCloseTitle">
    <div class="hm-close-bg"><?= ab_img('assets/img/smiles-group-under-tree-1.webp', '', '', true, 'style="object-position: 50% 55%"', '100vw') ?></div>
    <div class="container">
      <div class="hm-close-inner ab-reveal">
        <span class="ab-eyebrow">Get involved</span>
        <h2 id="hmCloseTitle">Help families plan beyond the <?= ab_mark('next meal', 77) ?></h2>
        <p>Funding, technical expertise, equipment and market connections all help communities build on work that is already under way.</p>
        <div class="hm-actions">
          <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Partnership enquiry') ?>" class="pg-btn">Support our work <?= icon('arrow-right', 16) ?></a>
          <a href="<?= SITE_URL ?>/contact.php" class="pg-btn pg-btn-ghost">Talk to our team</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?= ab_lightbox() ?>
<?= pp_film_dialog() ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
