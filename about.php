<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us';
$activePage = 'about';
$pageDescription = 'BetterLife International was founded in Uganda in 2021 by a refugee-led team of young people. It now works across five African countries on food security and sustainable livelihoods.';

// Editable content (Admin → Page Content / Site Settings / Team)
$howWeWork     = content_items($pdo, 'about', 'how_we_work');
$whoWeWorkWith = content_items($pdo, 'about', 'who_we_work_with');
$whereWeWork   = content_items($pdo, 'about', 'where_we_work');
$journey       = content_items($pdo, 'about', 'journey');
$guides        = content_items($pdo, 'about', 'guides');
$mission       = setting($pdo, 'mission_text');
$vision        = setting($pdo, 'vision_text');
$slogan        = setting($pdo, 'tagline', 'Building Hope');
$leaders = $pdo->query("SELECT name, role, photo FROM team_members WHERE status = 1 AND category = 'leadership' ORDER BY sort_order, id")->fetchAll();
$managerCountries = array_map(
    fn($r) => trim(preg_replace('/^Country Manager,\s*/i', '', $r['role'])),
    $pdo->query("SELECT role FROM team_members WHERE status = 1 AND role LIKE 'Country Manager%' ORDER BY sort_order, id")->fetchAll()
);
// Leadership faces appear automatically once every leader has a photo (Admin → Team)
$leadersHavePhotos = $leaders && !array_filter($leaders, fn($l) => empty($l['photo']));

$map = require __DIR__ . '/includes/map-paths.php';
require_once __DIR__ . '/includes/media.php';

// Figures (verified). Shown separately; never summed.
$figures = [
    ['value' => '112,430', 'count' => 112430, 'label' => 'people reached in 2025',       'prefix' => '',      'img' => 'assets/img/about/yumbe-women-celebrating.jpg',                 'alt' => 'Programme participants and BetterLife staff celebrating together under a tree in Yumbe'],
    ['value' => '122',     'count' => 122,    'label' => 'farms established',            'prefix' => 'About', 'img' => 'assets/img/about/yumbe-farm-aerial.jpg',                       'alt' => 'Aerial view of farm plots and a greenhouse in Yumbe'],
    ['value' => '5',       'count' => 5,      'label' => 'community farms established',  'prefix' => '',      'img' => 'assets/img/about/rukungiri-community-field.jpg',               'alt' => 'A group of people working a field together in Rukungiri'],
    ['value' => '310',     'count' => 310,    'label' => 'households supported',         'prefix' => '',      'img' => 'assets/img/about/yumbe-shopkeeper.jpg',                        'alt' => 'A smiling woman at her market stall'],
    ['value' => '5',       'count' => 5,      'label' => 'schools supported with meals', 'prefix' => '',      'img' => 'assets/img/about/rukungiri-school-cup.jpg',                    'alt' => 'A pupil drinking from a cup in a classroom'],
];

// How we see the work: food connects to seven things (drawn from the supplied narrative and programme content)
$connections = [
    'water'       => ['Water',       'A harvest depends on water close enough to use. Our work includes water access and water-efficient production, and solar-powered irrigation in South Sudan.'],
    'energy'      => ['Energy',      'Firewood collection takes hours. Clean energy, from biogas to solar, eases that load and can power farming itself.'],
    'time'        => ['Time',        'When much of the day goes on water and firewood, little is left for farming, learning or earning.'],
    'skills'      => ['Skills',      'People adopt what they can see working. We teach through demonstration sites, local-language facilitation and peer learning.'],
    'savings'     => ['Savings',     'Without money for inputs, good knowledge stays unused. Savings groups and credit co-operatives help members plan and invest.'],
    'information' => ['Information', 'A phone and timely information change decisions. Soilla brings soil, crop, climate and price information closer to farmers.'],
    'markets'     => ['Markets',     'For farmers producing for sale, access to buyers helps turn a harvest into income. Buyer connections and Agribusiness Connekt link producers to markets.'],
];

$howPhotos = [
    ['assets/img/about/yumbe-listening-circle.jpg', 'BetterLife team members seated in a circle with community members'],
    ['assets/img/about/yumbe-planting-demo.jpg', 'A man demonstrating planting while others watch'],
    ['assets/img/about/rukungiri-weighing.jpg', 'A woman weighing a sack of produce on a hanging scale'],
    ['assets/img/about/yumbe-group-under-tree.jpg', 'A community group meeting under a tree'],
    ['assets/img/about/yumbe-field-conversation.jpg', 'A BetterLife team member talking with community members'],
];

$whoPhotos = [
    'Women and Girls'                 => ['assets/img/about/rukungiri-two-women.jpg', 'Two women smiling in a maize field', '18% 50%'],
    'Children and Young People'       => ['assets/img/about/rukungiri-pupils.jpg', 'Four smiling pupils in school uniform'],
    'Refugees and Displaced Families' => ['assets/img/about/yumbe-four-women.jpg', 'Four women smiling together in Yumbe'],
    'Smallholder Farmers'             => ['assets/img/about/rukungiri-maize-smile.jpg', 'A smiling farmer standing in her maize crop'],
];

// Where we work: places named in the supplied text only (no addresses)
$pr = $map['proj'];
$project = fn(float $lon, float $lat): array => [
    round(deg2rad($lon) * $pr['scale'] + $pr['ox'], 1),
    round(-log(tan(M_PI / 4 + deg2rad($lat) / 2)) * $pr['scale'] + $pr['oy'], 1),
];
$countryMeta = [
    'Uganda' => ['key' => 'uganda', 'mapKey' => 'Uganda', 'note' => 'Where BetterLife began',
        'places' => [['Yumbe and Bidi Bidi', 31.3, 3.47, 'field', 'West Nile coordination'], ['Rukungiri', 29.92, -0.79, 'field', 'Communal farm', 'left']],
        'photo' => ['assets/img/about/yumbe-greenhouse-aerial.jpg', 'Aerial view of a greenhouse and farm plots in Yumbe, Uganda']],
    'South Sudan' => ['key' => 'south-sudan', 'mapKey' => 'South Sudan', 'note' => '',
        'places' => [['Juba', 31.58, 4.85, 'office', 'Office'], ['Yambio', 28.40, 4.57, 'field', 'Field presence', 'left']],
        'photo' => null],
    'Tanzania' => ['key' => 'tanzania', 'mapKey' => 'Tanzania', 'note' => 'With FADECO',
        'places' => [['Kayanga, Karagwe', 31.13, -1.6, 'office', 'Base']],
        'photo' => null],
    'Ghana' => ['key' => 'ghana', 'mapKey' => 'Ghana', 'note' => 'Programme activity',
        'places' => [],
        'photo' => ['assets/img/betterlifeint-source/projects/project-climate-education-alt.jpg', 'A climate education session in Ghana']],
    'Democratic Republic of Congo' => ['key' => 'drc', 'mapKey' => 'Democratic Republic of the Congo', 'note' => 'Programme activity',
        'places' => [],
        'photo' => null],
];

// Each year: a photograph, and optionally a second one set into its corner. [photo, description, caption, keep natural shape]
$journeyPhotos = [
    '2021' => [
        ['assets/img/about/journey-2021-medicinal-garden.jpg', 'Pupils holding their lunch boxes with our team beside a sign that reads Welcome to Medicinal Garden', 'With pupils at a school medicinal garden, 2021'],
        ['assets/img/about/journey-2021-tree-posters.jpg', 'Pupils and our team holding up posters that read Plant and Grow a Tree', 'Tree-planting posters with pupils, 2021'],
    ],
    '2022' => [['assets/img/about/journey-2022-nature-hub.jpg', 'Poster for the NATURE for Life Hub under A Better Life Uganda, showing two young women with their arms folded', 'The NATURE for Life Hub, December 2022', true]],
    '2023' => [['assets/img/soilla-app-field-demo.webp', 'The Soilla app open on a phone in a field', 'Soilla in use in the field']],
    '2024' => [['assets/img/solar-panel-installation-2.webp', 'A solar panel installed above a raised water tank', 'Clean energy installation']],
    '2025' => [['assets/img/about/yumbe-2025-women.jpg', 'Women laughing together at a programme session', 'Women in the Foundation S-supported programme in Yumbe, 2025']],
    '2026' => [['assets/img/about/rukungiri-communal-farm.jpg', 'A wide maize field below forested hills', 'Our 20-acre communal farm in Rukungiri, 2026']],
];

// Photographs from our work: two rows that glide in opposite directions (see the strip below)
$stripRows = [
    [
        ['assets/img/about/yumbe-staff-child.jpg', 'A BetterLife team member holding a child in Yumbe'],
        ['assets/img/about/yumbe-elder-smile.jpg', 'An older woman smiling outside her shop in Yumbe'],
        ['assets/img/about/rukungiri-poultry.jpg', 'A woman feeding chickens in a poultry house in Rukungiri'],
        ['assets/img/about/yumbe-poultry-feeders.jpg', 'Programme participants celebrating with new poultry feeders in Yumbe'],
        ['assets/img/about/yumbe-girl-running.jpg', 'A girl running along a path'],
        ['assets/img/about/yumbe-children-game.jpg', 'Children playing a game outside their homes in Yumbe'],
        ['assets/img/about/rukungiri-classroom.jpg', 'Pupils at their desks in a classroom in Rukungiri'],
    ],
    [
        ['assets/img/about/yumbe-2025-shirt.jpg', 'A smiling woman holding a programme T-shirt in Yumbe'],
        ['assets/img/children-at-borehole.webp', 'Children fetching water at a borehole'],
        ['assets/img/about/rukungiri-staff-maize.jpg', 'A BetterLife team member in a maize field in Rukungiri'],
        ['assets/img/about/yumbe-laughing-notes.jpg', 'A woman laughing as she writes during a training session'],
        ['assets/img/about/yumbe-man-child.jpg', 'A BetterLife team member laughing with a child in Yumbe'],
        ['assets/img/about/soroti-wac-2026.jpg', 'A Women’s Action Circle session in Soroti'],
        ['assets/img/about/yumbe-girl-jerrycan.jpg', 'A girl carrying a jerrycan on her head'],
    ],
];

// Natural shape of each photograph, so the strip never crops a face into an awkward box
$shape = function (string $p): float { $s = @getimagesize(__DIR__ . '/' . $p); return $s ? round($s[0] / $s[1], 3) : 1.0; };

$contact = fn(string $subject): string => SITE_URL . '/contact.php?subject=' . rawurlencode($subject);

// Opening: three candid photographs crossfade slowly behind the headline (first one is the LCP image)
$heroSlides = [
    ['assets/img/woman-winnowing-grain.webp',     'A woman winnowing grain',                         '56% 28%'],
    ['assets/img/farmers-planting-together.webp', 'Two people planting seedlings together',          '50% 46%'],
    ['assets/img/about/rukungiri-women-hoeing.jpg', 'Women preparing a field together with hoes in Rukungiri', '50% 55%'],
];
$heroImg = $heroSlides[0][0];
$heroV = ab_variants($heroImg);
$pageStyles  = ['assets/css/about.css'];
$pageScripts = ['assets/js/about.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="100vw" fetchpriority="high">'
        : '<link rel="preload" as="image" href="' . h(asset_url($heroImg)) . '" fetchpriority="high">')
    . '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab" id="top">

  <!-- 1. Opening -->
  <!-- Painted brush-stroke filter shared by every stroke on the page (same as the homepage) -->
  <svg class="ab-defs" width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
      <filter id="lpBrush" x="-15%" y="-60%" width="130%" height="220%" color-interpolation-filters="sRGB">
        <feTurbulence type="fractalNoise" baseFrequency="0.035" numOctaves="3" seed="4" result="edgeNoise"/>
        <feDisplacementMap in="SourceGraphic" in2="edgeNoise" scale="6" xChannelSelector="R" yChannelSelector="G" result="rough"/>
        <feTurbulence type="fractalNoise" baseFrequency="0.006 0.42" numOctaves="2" seed="9" result="bristles"/>
        <feColorMatrix in="bristles" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  4.2 0 0 0 -1.55" result="streaks"/>
        <feComposite in="rough" in2="streaks" operator="in" result="dry"/>
        <feMorphology in="rough" operator="erode" radius="3" result="core"/>
        <feMerge><feMergeNode in="dry"/><feMergeNode in="core"/></feMerge>
      </filter>
    </defs>
  </svg>

  <section class="ab-hero" aria-labelledby="abHeroTitle">
    <div class="ab-hero-media">
      <?php foreach ($heroSlides as $i => $slide): [$p, $alt, $pos] = $slide; $w = $slide[3] ?? '100%'; ?>
        <?= ab_img($p, $i === 0 ? $alt : '', 'ab-hero-img', $i > 0, 'style="--i: ' . $i . '; --pos: ' . $pos . '; --w: ' . $w . '"') ?>
      <?php endforeach; ?>
      <?php if (is_file(__DIR__ . '/assets/video/about-hero-720.mp4')): ?>
        <!-- Silent film from Rukungiri; loaded after the page on wider screens only (see about.js), photographs remain the fallback -->
        <video class="ab-hero-video" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-src="<?= h(asset_url('assets/video/about-hero-720.mp4')) ?>" data-src-hd="<?= h(asset_url('assets/video/about-hero-1080.mp4')) ?>"></video>
      <?php endif; ?>
    </div>
    <div class="container ab-hero-inner">
      <div class="ab-hero-copy">
        <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">About Us</span></nav>
        <p class="ab-kicker"><span>Refugee-led</span> <i aria-hidden="true">&middot;</i> <span>Youth-led</span> <i aria-hidden="true">&middot;</i> <span>Women-led</span></p>
        <h1 id="abHeroTitle">It began with the <span class="ab-mark">lives we knew<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 17, 0.02) ?>"/></svg></span></h1>
        <p class="ab-hero-lead">This work did not begin in a conference room. It began with young people who knew that poverty, displacement and climate change do not arrive one at a time, and who wanted solutions that make sense in the whole of a person&rsquo;s life.</p>
        <a href="#beginnings" class="ab-hero-scroll">Read our story <?= icon('chevron-down', 16) ?></a>
      </div>
      <!-- Brand mark from the homepage: a photograph inside an accurate outline of Africa -->
      <figure class="ab-hero-africa" aria-hidden="true">
        <svg viewBox="10 30 600 660" preserveAspectRatio="xMidYMid meet" focusable="false">
          <defs><clipPath id="abHeroAfrica" clipPathUnits="userSpaceOnUse"><path d="<?= $map['africa'] ?>"/></clipPath></defs>
          <g clip-path="url(#abHeroAfrica)">
            <rect x="0" y="0" width="640" height="720" fill="#d9cbb8"/>
            <image href="<?= h(asset_url(ab_variants('assets/img/about-real-1.jpg')[960] ?? 'assets/img/about-real-1.jpg')) ?>" x="-80" y="-194" width="820" height="1230" preserveAspectRatio="xMidYMid slice"/>
          </g>
          <!-- crisp white coastline so the continent reads clearly over the photographs behind it -->
          <path class="ab-hero-africa-edge" d="<?= $map['africa'] ?>"/>
          <g filter="url(#lpBrush)">
            <path class="f-green" d="<?= lp_brush_d(-30, 250, 118, 218, 46, 3) ?>"/>
            <path class="f-blue"  d="<?= lp_brush_d(414, 376, 572, 430, 40, 7, -0.04) ?>"/>
            <path class="f-blue"  d="<?= lp_brush_d(168, 474, 298, 482, 44, 5) ?>"/>
          </g>
        </svg>
      </figure>
    </div>
    <button type="button" class="ab-motion-toggle" aria-pressed="false" aria-label="Pause background motion">
      <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6v12M15 6v12"/></svg>
      <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l10.5-6.5Z"/></svg>
    </button>
  </section>

  <!-- 2. Our beginnings, then the team -->
  <section class="ab-begin" id="beginnings" aria-labelledby="abBeginTitle">
    <div class="container ab-begin-grid">
      <div class="ab-collage ab-reveal">
        <svg class="ab-strokes" viewBox="0 0 600 648" preserveAspectRatio="none" aria-hidden="true" focusable="false">
          <g filter="url(#lpBrush)">
            <path class="f-green" d="<?= lp_brush_d(-34, 452, 150, 430, 46, 41) ?>"/>
            <path class="f-blue"  d="<?= lp_brush_d(300, 18, 520, -4, 40, 43) ?>"/>
            <path class="f-green" d="<?= lp_brush_d(360, 628, 560, 604, 34, 45) ?>"/>
          </g>
        </svg>
        <?= ab_photo('assets/img/about/yumbe-team-members.jpg', 'Two BetterLife team members smiling together outdoors', 'BetterLife team members in Yumbe, Uganda', 'beginnings', 'ab-col ab-col-a', '(max-width: 900px) 50vw, 300px') ?>
        <?= ab_photo('assets/img/about/denise-with-group.jpg', 'Denise Ayebare standing with a group of people outdoors', 'Denise Ayebare with others at a community gathering', 'beginnings', 'ab-col ab-col-b ab-col-group', '(max-width: 900px) 50vw, 320px') ?>
        <?= ab_photo('assets/img/betterlifeint-source/programs/program-photo-10.jpg', 'Women taking notes during a training session', 'Women taking notes during a BetterLife training session', 'beginnings', 'ab-col ab-col-c', '(max-width: 900px) 45vw, 260px') ?>
        <?= ab_photo('assets/img/village-girl-portrait.webp', 'A girl standing outside a thatched home', 'A girl standing outside a thatched home', 'beginnings', 'ab-col ab-col-d', '(max-width: 900px) 45vw, 260px') ?>
        <div class="ab-stamp ab-stamp-year" aria-hidden="true"><span>Founded</span><strong>2021</strong></div>
        <div class="ab-stamp ab-stamp-money" aria-hidden="true"><span>Started with</span><strong>USD 200</strong></div>
      </div>

      <div class="ab-begin-copy ab-reveal">
        <span class="ab-eyebrow">Our beginnings</span>
        <h2 id="abBeginTitle">Started by people who had <span class="ab-mark">lived it<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 47, 0.02) ?>"/></svg></span></h2>
        <p class="ab-lead">Denise Ayebare founded BetterLife International in Uganda in 2021, at nineteen, with USD&nbsp;200 and a small team of young people. As a refugee-led team, we had grown up experiencing many of the challenges facing the communities we wanted to work alongside.</p>
        <p>We knew the uncertainty of providing for a household when food, income and opportunity were difficult to secure. We had also seen the knowledge and determination within communities. Our commitment was to help people build on that ability, with practical support to grow food, earn, save and access markets.</p>
        <figure class="ab-founder-quote">
          <blockquote><p>We were tired of watching communities receive short-term help while the conditions keeping them vulnerable stayed the same. BetterLife was created to work differently.</p></blockquote>
          <figcaption><strong>Denise Ayebare</strong>, Founder and Executive Director <span>Forbes Africa 30 Under 30, Class of 2026</span></figcaption>
        </figure>
        <p>That beginning has grown into an organisation that reached 112,430 people in 2025, with work in five African countries. The work continues through community members, staff, volunteers and partners who help shape it every day.</p>
        <p class="ab-facts"><span><strong>2021</strong> founded in Uganda</span><span><strong>USD 200</strong> starting budget</span><span><strong>5</strong> countries today</span></p>
      </div>
    </div>

    <?php if ($leaders):
      $others = array_values(array_filter($leaders, fn($l) => stripos($l['role'], 'Founder') === false));
      $listJoin = function (array $items): string {
          if (count($items) < 2) return implode('', $items);
          return implode(', ', array_slice($items, 0, -1)) . ' and ' . end($items);
      }; ?>
      <div class="container">
        <div class="ab-team ab-reveal">
          <?php if ($leadersHavePhotos): ?>
            <span class="ab-eyebrow">Who leads BetterLife</span>
            <ul class="ab-team-faces">
              <?php foreach ($leaders as $l): ?>
                <li><?= ab_img($l['photo'], $l['name'], 'ab-team-photo', true, '', '(max-width: 720px) 72px, 104px') ?><strong><?= h($l['name']) ?></strong><small><?= h($l['role']) ?></small></li>
              <?php endforeach; ?>
            </ul>
            <?php if ($managerCountries): ?><p class="ab-team-line">Our leadership works alongside country managers in <?= h($listJoin($managerCountries)) ?>.</p><?php endif; ?>
          <?php else: ?>
            <p class="ab-team-line">BetterLife is led by its founder and Executive Director, Denise Ayebare<?php if ($others): ?>, with <?= h($listJoin(array_map(fn($l) => $l['name'] . ' (' . $l['role'] . ')', $others))) ?><?php endif; ?><?php if ($managerCountries): ?>, alongside country managers in <?= h($listJoin($managerCountries)) ?><?php endif; ?>.</p>
          <?php endif; ?>
          <a href="<?= SITE_URL ?>/team.php" class="ab-link">Meet the full team and board <?= icon('arrow-right', 15) ?></a>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <!-- 3. Impact: figures, then results from one programme -->
  <section class="ab-impact" id="impact" aria-labelledby="abImpactTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Impact in pictures</span>
        <h2 id="abImpactTitle">Since 2021, counted in people, farms, homes and schools</h2>
      </div>
      <div class="ab-figures-wrap">
      <svg class="ab-strokes" viewBox="0 0 1200 400" preserveAspectRatio="none" aria-hidden="true" focusable="false">
        <g filter="url(#lpBrush)">
          <path class="f-green" d="<?= lp_brush_d(-60, 70, 70, 50, 44, 53) ?>"/>
          <path class="f-blue"  d="<?= lp_brush_d(1140, 330, 1262, 300, 40, 55) ?>"/>
        </g>
      </svg>
      <ul class="ab-figures">
        <?php foreach ($figures as $i => $f): ?>
          <li class="ab-figure ab-reveal">
            <?= ab_img($f['img'], $f['alt'], 'ab-figure-img', true, '', $i === 0 ? '(max-width: 720px) 100vw, 360px' : '(max-width: 720px) 50vw, (max-width: 1100px) 33vw, 240px') ?>
            <div class="ab-figure-text">
              <strong><?php if ($f['prefix']): ?><small><?= h($f['prefix']) ?></small> <?php endif; ?><span class="ab-count" data-count="<?= $f['count'] ?>"><?= h($f['value']) ?></span></strong>
              <span><?= h($f['label']) ?></span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      </div>
      <p class="ab-figures-note ab-reveal">Each figure counts a different part of the work, so they should not be added together. The people we reach take part in different activities and receive different kinds of support.</p>

      <!-- One programme as proof; the full account lives on the Programmes page -->
      <article class="ab-proof ab-reveal" aria-labelledby="abProofTitle">
        <div class="ab-proof-head">
          <span class="ab-eyebrow">Proof from one programme</span>
          <h3 id="abProofTitle">Strengthening the capacity of refugees and IDPs in agriculture, food security and climate action</h3>
          <p class="ab-results-meta"><?= icon('map-pin', 15) ?> Yumbe, Uganda <i aria-hidden="true">&middot;</i> With support from Foundation S, The Sanofi Collective</p>
        </div>
        <div class="ab-proof-stats">
          <p class="ab-proof-stat"><strong>22% <span aria-hidden="true">&rarr;</span><span class="sr-only">to</span> 92%</strong><span>Knowledge of climate-smart agriculture among the 72 women who completed structured training</span></p>
          <p class="ab-proof-stat"><strong>35%</strong><span>Average reduction in household spending on vegetables, reported by participating households</span></p>
        </div>
        <figure class="ab-proof-faces">
          <ul>
            <?php for ($i = 1; $i <= 8; $i++): ?>
              <li><?= ab_img("assets/img/about/yumbe-portrait-$i.jpg", '', '', true, '', '(max-width: 720px) 25vw, 140px') ?></li>
            <?php endfor; ?>
          </ul>
          <figcaption>Some of the women taking part in the programme in Yumbe</figcaption>
        </figure>
        <a href="<?= SITE_URL ?>/project.php?slug=womens-climate-resilience-yumbe" class="ab-link">Read the full programme story <?= icon('arrow-right', 15) ?></a>
      </article>
    </div>
  </section>

  <!-- 4. Vision and mission -->
  <section class="ab-purpose" aria-labelledby="abPurposeTitle">
    <div class="ab-purpose-bg"><?= ab_img('assets/img/solar-panel-farm-sky.webp', '', '', true, '', '60vw') ?></div>
    <div class="container">
      <div class="ab-purpose-head ab-reveal">
        <span class="ab-eyebrow">Vision, mission and values</span>
        <h2 id="abPurposeTitle"><?= h($slogan) ?></h2>
      </div>
      <div class="ab-purpose-grid">
        <article class="ab-purpose-card ab-reveal">
          <span class="ab-purpose-label"><?= icon('eye', 18) ?> Our vision</span>
          <p><?= h($vision) ?></p>
        </article>
        <article class="ab-purpose-card ab-reveal">
          <span class="ab-purpose-label"><?= icon('target', 18) ?> Our mission</span>
          <p><?= h($mission) ?></p>
        </article>
        <article class="ab-purpose-card ab-practice ab-reveal">
          <span class="ab-purpose-label"><?= icon('leaf', 18) ?> In practice</span>
          <p>Our focus is food security and sustainable livelihoods. Alongside refugees, displaced people and host communities, we help families grow food, earn an income, save and reach markets, and build the climate resilience that keeps those gains in place.</p>
        </article>
      </div>
      <?php if ($guides): ?>
        <div class="ab-values">
          <div class="ab-values-intro ab-reveal">
            <h3 class="ab-purpose-label" id="abValuesTitle"><?= icon('heart', 18) ?> What guides us</h3>
            <p class="ab-statement">People closest to a problem must have a real hand in defining it, designing the response and deciding what success looks like.</p>
          </div>
          <ol class="ab-guides-grid" aria-labelledby="abValuesTitle">
            <?php foreach ($guides as $i => $g): ?>
              <li class="ab-guide ab-reveal">
                <span class="ab-guide-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <h4><?= h($g['title']) ?></h4>
                <p><?= h($g['body']) ?></p>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- 5. How we work: the failed-harvest view, the five principles, the values -->
  <section class="ab-systems" id="approach" aria-labelledby="abSystemsTitle">
    <div class="container ab-systems-grid">
      <div class="ab-systems-copy ab-reveal">
        <span class="ab-eyebrow">How we work</span>
        <h2 id="abSystemsTitle">A failed harvest is never just a farming problem</h2>
        <p class="ab-lead">When a woman tells us her harvest failed, seeds may appear to be the answer. Listen longer and the picture changes. She may have no water nearby. She may spend much of the day collecting firewood. She may lack money for inputs, access to a phone or a buyer for what she grows.</p>
        <p class="ab-pull ab-pull-sm">Giving her seeds alone leaves most of the problem untouched.</p>
        <p>We take a systems approach because people live in systems. One programme may include a demonstration garden, a savings group, a digital tool and a buyer connection, shaped by the barriers people actually face.</p>
      </div>

      <div class="ab-web ab-reveal">
        <p class="ab-web-hint" id="abWebHint">Food sits at the centre. Select a connection to see how it shapes the harvest.</p>
        <div class="ab-web-stage">
          <svg class="ab-web-lines" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
            <?php $n = count($connections); $i = 0; foreach ($connections as $key => $c):
              $a = -M_PI / 2 + 2 * M_PI * $i / $n; $x = round(50 + 38 * cos($a), 2); $y = round(50 + 38 * sin($a), 2); ?>
              <line x1="50" y1="50" x2="<?= $x ?>" y2="<?= $y ?>" data-for="<?= $key ?>"/>
            <?php $i++; endforeach; ?>
          </svg>
          <div class="ab-web-core" aria-hidden="true"><?= icon('leaf', 22) ?><span>Food</span></div>
          <div class="ab-web-nodes" role="tablist" aria-label="What food connects to" aria-describedby="abWebHint">
            <?php $i = 0; foreach ($connections as $key => $c):
              $a = -M_PI / 2 + 2 * M_PI * $i / $n; $x = round(50 + 38 * cos($a), 2); $y = round(50 + 38 * sin($a), 2); ?>
              <button type="button" role="tab" class="ab-web-node" id="abTab-<?= $key ?>" aria-controls="abPanel-<?= $key ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" tabindex="<?= $i === 0 ? '0' : '-1' ?>" data-key="<?= $key ?>" style="--x: <?= $x ?>%; --y: <?= $y ?>%"><?= h($c[0]) ?></button>
            <?php $i++; endforeach; ?>
          </div>
        </div>
        <div class="ab-web-panels">
          <?php foreach ($connections as $key => $c): ?>
            <div class="ab-web-panel" role="tabpanel" id="abPanel-<?= $key ?>" aria-labelledby="abTab-<?= $key ?>" data-key="<?= $key ?>">
              <h3>Food and <?= h(strtolower($c[0])) ?></h3>
              <p><?= h($c[1]) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="container ab-how">
      <div class="ab-how-intro ab-reveal">
        <h3 class="ab-subhead">Five principles, learned in the field</h3>
      </div>
      <!-- One principle open at a time; the photograph beside the list follows it -->
      <div class="ab-how-grid">
        <div class="ab-how-frame ab-reveal" aria-hidden="true">
          <?php foreach ($howWeWork as $i => $s): [$p] = $howPhotos[$i] ?? $howPhotos[0]; ?>
            <?= ab_img($p, '', 'ab-how-img' . ($i === 0 ? ' is-active' : ''), true, 'data-step="' . $i . '"', '(max-width: 900px) 100vw, 580px') ?>
          <?php endforeach; ?>
          <span class="ab-how-count"><b>01</b> / <?= str_pad((string) count($howWeWork), 2, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="ab-how-steps ab-reveal">
          <?php foreach ($howWeWork as $i => $s): [$p, $alt] = $howPhotos[$i] ?? $howPhotos[0]; ?>
            <details class="ab-how-step" name="abPrinciples" data-step="<?= $i ?>"<?= $i === 0 ? ' open' : '' ?>>
              <summary>
                <span class="ab-how-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <h4><?= h($s['title']) ?></h4>
                <span class="ab-how-chev" aria-hidden="true"><?= icon('chevron-down', 18) ?></span>
              </summary>
              <div class="ab-how-body">
                <figure class="ab-how-step-photo"><?= ab_img($p, $alt, '', true, '', '100vw') ?></figure>
                <p><?= h($s['body']) ?></p>
                <?php if ($i === 3): ?>
                  <p class="ab-trust"><?= icon('users', 20) ?><span>People learn faster in groups they already trust.</span></p>
                <?php endif; ?>
              </div>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. Who we work with -->
  <section class="ab-who" aria-labelledby="abWhoTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Who we work with</span>
        <h2 id="abWhoTitle">The people we work alongside</h2>
      </div>
      <div class="ab-who-grid">
        <?php foreach ($whoWeWorkWith as $i => $w):
          [$p, $alt] = $whoPhotos[$w['title']] ?? [null, '']; $pos = $whoPhotos[$w['title']][2] ?? '';
          [$short, $more] = ab_split(str_replace('; they', '. They', $w['body'])); ?>
          <article class="ab-who-card ab-reveal">
            <?php if ($p): ?><div class="ab-who-photo"><?= ab_img($p, $alt, '', true, $pos ? 'style="object-position: ' . h($pos) . '"' : '', '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 300px') ?></div><?php endif; ?>
            <div class="ab-who-body">
              <h3><?= h($w['title']) ?></h3>
              <p><?= h($short) ?></p>
              <?php if ($more): ?>
                <p class="ab-who-more" id="abWhoMore<?= $i ?>"><?= h($more) ?></p>
                <button type="button" class="ab-who-toggle" aria-expanded="false" aria-controls="abWhoMore<?= $i ?>" hidden><span>Read more</span> <?= icon('chevron-down', 15) ?></button>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 7. Where we work: interactive map -->
  <section class="ab-where" id="where" aria-labelledby="abWhereTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Where we work</span>
        <h2 id="abWhereTitle">Rooted in Uganda, working across <span class="ab-mark">five countries<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 57, 0.02) ?>"/></svg></span></h2>
      </div>
      <div class="ab-where-grid" data-map>
        <div class="ab-map-wrap ab-reveal">
          <svg class="ab-map" viewBox="30 43 572 640" data-base="30 43 572 640" role="img" aria-label="Map of Africa highlighting Uganda, South Sudan, Tanzania, Ghana and the Democratic Republic of Congo">
            <path class="ab-map-land" d="<?= $map['africa'] ?>"/>
            <?php foreach ($countryMeta as $name => $c): $d = $map['countries'][$c['mapKey']]['d'] ?? ''; ?>
              <path class="ab-map-country" d="<?= $d ?>" data-key="<?= $c['key'] ?>"><title><?= h($name) ?></title></path>
            <?php endforeach; ?>
            <?php foreach ($countryMeta as $name => $c): foreach ($c['places'] as $place): [$label, $lon, $lat, $type] = $place; $left = ($place[5] ?? '') === 'left'; [$px, $py] = $project($lon, $lat); ?>
              <g class="ab-map-place is-<?= $type ?>" data-key="<?= $c['key'] ?>" data-x="<?= $px ?>" data-y="<?= $py ?>" transform="translate(<?= $px ?> <?= $py ?>)">
                <?php if ($type === 'office'): ?><rect x="-4" y="-4" width="8" height="8" rx="1.5"/><?php else: ?><circle r="4"/><?php endif; ?>
                <text x="<?= $left ? -7 : 7 ?>" y="3.5"<?= $left ? ' text-anchor="end"' : '' ?>><?= h($label) ?></text>
              </g>
            <?php endforeach; endforeach; ?>
          </svg>
          <p class="ab-map-legend"><span class="lg-office" aria-hidden="true"></span> Office or base <span class="lg-field" aria-hidden="true"></span> Field presence <span class="lg-country" aria-hidden="true"></span> Programme country</p>
        </div>

        <div class="ab-where-panel ab-reveal">
          <div class="ab-country-tabs" role="tablist" aria-label="Countries">
            <?php $first = true; foreach ($countryMeta as $name => $c): ?>
              <button type="button" role="tab" class="ab-country-tab" id="abCTab-<?= $c['key'] ?>" aria-controls="abCPanel-<?= $c['key'] ?>" aria-selected="<?= $first ? 'true' : 'false' ?>" tabindex="<?= $first ? '0' : '-1' ?>" data-key="<?= $c['key'] ?>"><?= h($name === 'Democratic Republic of Congo' ? 'DR Congo' : $name) ?></button>
            <?php $first = false; endforeach; ?>
          </div>
          <?php $byTitle = []; foreach ($whereWeWork as $w) $byTitle[$w['title']] = $w['body']; ?>
          <?php foreach ($countryMeta as $name => $c): ?>
            <div class="ab-country-panel" role="tabpanel" id="abCPanel-<?= $c['key'] ?>" aria-labelledby="abCTab-<?= $c['key'] ?>" data-key="<?= $c['key'] ?>">
              <?php if ($c['photo']): ?>
                <div class="ab-country-photo"><?= ab_img($c['photo'][0], $c['photo'][1], '', true, '', '(max-width: 900px) 100vw, 520px') ?></div>
              <?php endif; ?>
              <h3><?= h($name) ?></h3>
              <?php if ($c['note'] || $c['places']): ?>
                <ul class="ab-country-tags">
                  <?php if ($c['note']): ?><li><?= h($c['note']) ?></li><?php endif; ?>
                  <?php foreach ($c['places'] as $place): [$label, , , $type, $role] = $place; ?><li class="is-<?= $type ?>"><?= h($role) ?>: <?= h($label) ?></li><?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <p><?= h($byTitle[$name] ?? '') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- 8. Photographic journey -->
  <section class="ab-journey" aria-labelledby="abJourneyTitle">
    <div class="container ab-rail-head ab-reveal">
      <div>
        <span class="ab-eyebrow">Our journey</span>
        <h2 id="abJourneyTitle">From a local idea to work across five countries</h2>
      </div>
      <div class="ab-rail-nav" hidden>
        <button type="button" class="ab-rail-btn" data-dir="-1" aria-label="Earlier years"><?= icon('arrow-right', 18) ?></button>
        <button type="button" class="ab-rail-btn" data-dir="1" aria-label="Later years"><?= icon('arrow-right', 18) ?></button>
      </div>
    </div>
    <!-- Years run left to right; the rail scrolls sideways (swipe, trackpad, arrow buttons or keyboard) -->
    <ol class="ab-timeline" tabindex="0" aria-label="<?= h($journey ? 'Our journey, ' . $journey[0]['title'] . ' to ' . $journey[count($journey) - 1]['title'] : 'Our journey') ?>">
      <?php foreach ($journey as $i => $j): [$firstLine, $rest] = ab_split($j['body']); $ph = $journeyPhotos[$j['title']] ?? []; ?>
        <li class="ab-milestone<?= $ph ? '' : ' is-text' ?>">
          <span class="ab-year"><?= h($j['title']) ?></span>
          <div class="ab-milestone-card">
            <?php if ($ph): ?>
              <div class="ab-milestone-media<?= isset($ph[1]) ? ' has-inset' : '' ?>">
                <?= ab_photo($ph[0][0], $ph[0][1], $ph[0][2], 'journey', 'ab-milestone-photo', '(max-width: 720px) 80vw, 360px', empty($ph[0][3]) ? '' : 'style="--ar: ' . $shape($ph[0][0]) . '"') ?>
                <?php if (isset($ph[1])): ?><?= ab_photo($ph[1][0], $ph[1][1], $ph[1][2], 'journey', 'ab-milestone-inset', '(max-width: 720px) 40vw, 180px') ?><?php endif; ?>
              </div>
            <?php endif; ?>
            <p><?= h($firstLine) ?></p>
            <?php if ($rest): ?>
              <p class="ab-milestone-more" id="abMs<?= $i ?>"><?= h($rest) ?></p>
              <button type="button" class="ab-who-toggle" aria-expanded="false" aria-controls="abMs<?= $i ?>" hidden><span>Read more</span> <?= icon('chevron-down', 15) ?></button>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>
  </section>

  <!-- 9. Tools that extend the work -->
  <section class="ab-tools" aria-labelledby="abToolsTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Tools that extend the work</span>
          <h2 id="abToolsTitle">Practical guidance. Connections to opportunity.</h2>
        </div>
        <p class="ab-head-sub">Two BetterLife platforms help farmers make production decisions and reach buyers, always paired with field training.</p>
      </div>
      <div class="ab-tools-grid">
        <article class="ab-tool ab-reveal">
          <?= ab_photo('assets/img/soilla-app-portrait.webp', 'A young man holding up a phone showing the Soilla app', 'The Soilla app in use', 'tools', 'ab-tool-photo', '(max-width: 720px) 100vw, 240px') ?>
          <div class="ab-tool-body">
            <span class="ab-status">Launched in 2023</span>
            <h3>Soilla</h3>
            <p>Our digital agricultural advisory platform. Farmers use it for soil and crop guidance, climate information, market prices and agricultural services, and to find suppliers, experts and other producers.</p>
            <a href="<?= SITE_URL ?>/project.php?slug=soilla" class="ab-link">How Soilla fits our programmes <?= icon('arrow-right', 15) ?></a>
          </div>
        </article>
        <article class="ab-tool ab-reveal">
          <?= ab_photo('assets/img/agribusiness-connekt-app.webp', 'A person holding up a phone showing the Agribusiness Connekt app', 'The Agribusiness Connekt app', 'tools', 'ab-tool-photo', '(max-width: 720px) 100vw, 240px') ?>
          <div class="ab-tool-body">
            <span class="ab-status ab-status-soft">Work continuing in 2026</span>
            <h3>Agribusiness Connekt</h3>
            <p>Where Soilla supports production decisions, Agribusiness Connekt focuses on the business around the farm. It links farmers and small agricultural enterprises to buyers, finance, services and market information.</p>
            <a href="<?= SITE_URL ?>/project.php?slug=agribusiness-connekt" class="ab-link">Explore Agribusiness Connekt <?= icon('arrow-right', 15) ?></a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- 10. Community photo strip -->
  <section class="ab-strip" aria-labelledby="abStripTitle">
    <h2 class="sr-only" id="abStripTitle">Photographs from our work</h2>
    <!-- Two rows at the photographs' natural shape, gliding in opposite directions as the page scrolls -->
    <div class="ab-glide ab-reveal">
      <?php foreach ($stripRows as $r => $row): ?>
        <div class="ab-glide-row<?= $r === 0 ? ' is-tall' : '' ?>" data-glide="<?= $r % 2 ? -1 : 1 ?>">
          <?php foreach ($row as [$p, $cap]): ?>
            <?= ab_photo($p, $cap, $cap, 'strip', 'ab-glide-item', $r === 0 ? '(max-width: 720px) 60vw, 460px' : '(max-width: 720px) 50vw, 400px', 'style="--ar: ' . $shape($p) . '"') ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Partners and accountability -->
  <section class="ab-partners" aria-labelledby="abPartnersTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Our partners</span>
          <h2 id="abPartnersTitle">Stronger in partnership</h2>
        </div>
        <p class="ab-head-sub">We work with organisations that bring resources, knowledge and reach while respecting the experience of the communities at the centre of the work.</p>
      </div>
      <?php // Partners with a logo, from the shared list (includes/partners-list.php)
$partners = array_values(array_map(fn($p) => [$p[0], $p[1]], array_filter(require __DIR__ . '/includes/partners-list.php', fn($p) => $p[0] && is_file(__DIR__ . '/assets/img/partners/' . $p[0])))); ?>
      <ul class="ab-logos ab-reveal" style="--n: <?= count($partners) ?>">
        <?php foreach ($partners as [$file, $pname]): ?>
          <li class="ab-logo-<?= h(pathinfo($file, PATHINFO_FILENAME)) ?>"><img src="<?= h(asset_url('assets/img/partners/' . $file)) ?>" alt="<?= h($pname) ?>" loading="lazy" decoding="async"></li>
        <?php endforeach; ?>
      </ul>
      <p class="ab-partners-links ab-reveal">
        <a href="<?= SITE_URL ?>/impact-reports.php" class="ab-link">Read our impact reports <?= icon('arrow-right', 15) ?></a>
        <a href="<?= SITE_URL ?>/partners.php" class="ab-link">How each partner supports the work <?= icon('arrow-right', 15) ?></a>
        <a href="<?= h($contact('Partnership enquiry')) ?>" class="ab-link">Become a partner <?= icon('arrow-right', 15) ?></a>
      </p>

      <?php if ($regNo = setting($pdo, 'ngo_reg_no')): ?>
        <!-- Legal standing, as on the Certificate of Registration and the Permit to Operate -->
        <div class="ab-legal ab-reveal" aria-labelledby="abLegalTitle">
          <div class="ab-legal-seal" aria-hidden="true"><?= icon('check', 30) ?><span>Registered<br>NGO</span></div>
          <div class="ab-legal-body">
            <h3 id="abLegalTitle">Registered and accountable</h3>
            <p><?= h(setting($pdo, 'legal_name') ?: setting($pdo, 'site_name')) ?> is an indigenous non-governmental organisation, registered with Uganda’s National Bureau for Non-Governmental Organisations under the NGO Act, Cap. 109, and holding a permit to operate countrywide. Our permit covers innovative environmental solutions that reduce carbon footprint, sustainable practices, preserving biodiversity, and climate change education and awareness.</p>
            <dl class="ab-legal-facts">
              <div><dt>Registration number</dt><dd><?= h($regNo) ?></dd></div>
              <?php if ($permit = setting($pdo, 'ngo_permit_no')): ?><div><dt>Permit to operate</dt><dd><?= h($permit) ?><?php if ($until = setting($pdo, 'ngo_permit_until')): ?> <small>Valid to <?= h($until) ?></small><?php endif; ?></dd></div><?php endif; ?>
              <?php if ($postal = setting($pdo, 'postal_address')): ?><div><dt>Postal address</dt><dd><?= h($postal) ?></dd></div><?php endif; ?>
            </dl>
            <p class="ab-legal-links">
              <a href="<?= SITE_URL ?>/impact-reports.php#imYears" class="ab-link">Annual reports <?= icon('arrow-right', 15) ?></a>
              <a href="<?= SITE_URL ?>/team.php#board" class="ab-link">Our board <?= icon('arrow-right', 15) ?></a>
            </p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- 11. Join us: a path for each visitor -->
  <section class="ab-close" id="get-involved" aria-labelledby="abCloseTitle">
    <div class="ab-close-bg"><?= ab_img('assets/img/farm-aerial-view-1.webp', '', '', true, '', '100vw') ?></div>
    <div class="container">
      <div class="ab-close-inner ab-reveal">
        <span class="ab-eyebrow"><?= h($slogan) ?></span>
        <h2 id="abCloseTitle">Work with people who know the problem firsthand</h2>
        <p>Choose the way you would like to be involved, and our team will take it from there.</p>
      </div>
      <ul class="ab-paths">
        <li class="ab-path ab-reveal">
          <span class="ab-path-ico" aria-hidden="true"><?= icon('heart', 22) ?></span>
          <h3>Fund or partner</h3>
          <p>Support community farming, school meals, households and enterprise, or build a programme with us.</p>
          <a href="<?= h($contact('Partnership enquiry')) ?>" class="btn btn-white">Partner With Us <?= icon('arrow-right', 16) ?></a>
        </li>
        <li class="ab-path ab-reveal">
          <span class="ab-path-ico" aria-hidden="true"><?= icon('users', 22) ?></span>
          <h3>Volunteer</h3>
          <p>Offer your time, skills or expertise to strengthen the work already under way.</p>
          <a href="<?= h($contact('Volunteer enquiry')) ?>" class="btn btn-outline">Volunteer With Us</a>
        </li>
        <li class="ab-path ab-reveal">
          <span class="ab-path-ico" aria-hidden="true"><?= icon('message', 22) ?></span>
          <h3>Media and speaking</h3>
          <p>Interview requests, panels and speaking invitations for our team.</p>
          <a href="<?= h($contact('Media and speaking')) ?>" class="btn btn-outline">Get in Touch</a>
        </li>
      </ul>
      <p class="ab-close-more ab-reveal"><a href="<?= SITE_URL ?>/impact-reports.php" class="ab-close-link">Read our reports <?= icon('arrow-right', 15) ?></a><a href="<?= SITE_URL ?>/team.php" class="ab-close-link">Meet the team <?= icon('arrow-right', 15) ?></a></p>
    </div>
  </section>
</main>

<!-- Lightbox (native dialog: focus is contained and Escape closes it) -->
<dialog class="ab-lightbox" id="abLightbox" aria-label="Photo viewer">
  <figure>
    <img src="" alt="">
    <figcaption><span class="ab-lb-caption"></span><span class="ab-lb-count"></span></figcaption>
  </figure>
  <button type="button" class="ab-lb-btn ab-lb-close" aria-label="Close photo viewer"><?= icon('x', 22) ?></button>
  <button type="button" class="ab-lb-btn ab-lb-prev" aria-label="Previous photo"><?= icon('arrow-right', 20) ?></button>
  <button type="button" class="ab-lb-btn ab-lb-next" aria-label="Next photo"><?= icon('arrow-right', 20) ?></button>
</dialog>

<?php require __DIR__ . '/includes/footer.php'; ?>
