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

/** Resized copies made by tools/make_sized_images.php live in assets/img/sized/. */
function ab_variants(string $path): array
{
    static $cache = [];
    if (isset($cache[$path])) return $cache[$path];
    $slug = preg_replace('~\.[a-z0-9]+$~i', '', str_replace('/', '__', preg_replace('~^assets/img/~', '', $path)));
    $out = [];
    foreach ([480, 960, 1600] as $w) {
        $rel = "assets/img/sized/{$slug}-{$w}.webp";
        if (is_file(__DIR__ . '/' . $rel)) $out[$w] = $rel;
    }
    return $cache[$path] = $out;
}

/** <img> with intrinsic size (no layout shift), responsive srcset and lazy loading by default. */
function ab_img(string $path, string $alt, string $class = '', bool $lazy = true, string $extra = '', string $sizes = '100vw'): string
{
    static $dims = [];
    $dims[$path] ??= @getimagesize(__DIR__ . '/' . $path) ?: [null, null];
    [$w, $h] = $dims[$path];
    $v = ab_variants($path);
    $src = $v[960] ?? $path;
    // Offer the original too when it is larger than the biggest resized copy
    if ($v && $w && $w > max(array_keys($v))) $v[$w] = $path;
    $srcset = $v ? implode(', ', array_map(fn($vw, $rel) => asset_url($rel) . " {$vw}w", array_keys($v), $v)) : '';
    return '<img src="' . h(asset_url($src)) . '"' . ($srcset ? ' srcset="' . h($srcset) . '" sizes="' . h($sizes) . '"' : '')
        . ' alt="' . h($alt) . '"'
        . ($w ? ' width="' . $w . '" height="' . $h . '"' : '')
        . ($class ? ' class="' . h($class) . '"' : '')
        . ($lazy ? ' loading="lazy" decoding="async"' : ' fetchpriority="high"')
        . ($extra ? ' ' . $extra : '') . '>';
}

/** Split text into its first sentence and the remainder. */
function ab_split(string $text): array
{
    $text = trim($text);
    if (preg_match('/^(.+?[.!?])\s+(.+)$/su', $text, $m)) return [$m[1], $m[2]];
    return [$text, ''];
}

/** Photo that opens in the lightbox (a plain link to the full image without JavaScript). */
function ab_photo(string $path, string $alt, string $caption, string $gallery, string $class = '', string $sizes = '400px'): string
{
    return '<a href="' . h(asset_url($path)) . '" class="ab-lb ' . h($class) . '" data-gallery="' . h($gallery) . '" data-caption="' . h($caption) . '" aria-label="View larger: ' . h($caption) . '">'
        . ab_img($path, $alt, '', true, '', $sizes) . '<span class="ab-lb-icon" aria-hidden="true">' . icon('search', 16) . '</span></a>';
}


// Figures (verified). Shown separately; never summed.
$figures = [
    ['value' => '110,000', 'count' => 110000, 'label' => 'people reached',               'prefix' => '',      'img' => 'assets/img/team-conference-group-photo.webp',                  'alt' => 'Participants gathered at a BetterLife event'],
    ['value' => '122',     'count' => 122,    'label' => 'farms established',            'prefix' => 'About', 'img' => 'assets/img/farm-aerial-view-2.webp',                           'alt' => 'Aerial view of cultivated farm plots and a greenhouse'],
    ['value' => '5',       'count' => 5,      'label' => 'community farms established',  'prefix' => '',      'img' => 'assets/img/betterlifeint-source/programs/program-photo-2.jpg', 'alt' => 'A field of cabbages'],
    ['value' => '310',     'count' => 310,    'label' => 'households supported',         'prefix' => '',      'img' => 'assets/img/vendor-and-children-food-stall.webp',               'alt' => 'A woman preparing food at a stall with children nearby'],
    ['value' => '5',       'count' => 5,      'label' => 'schools supported with meals', 'prefix' => '',      'img' => 'assets/img/betterlifeint-source/programs/program-photo-8.jpg', 'alt' => 'Pupils gathered outdoors for a session'],
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
    ['assets/img/field-team-conversation.webp', 'BetterLife team members in conversation with a community member'],
    ['assets/img/betterlifeint-source/programs/program-photo-9.jpg', 'A raised sack garden planted with seedlings'],
    ['assets/img/grain-milling-machine.webp', 'Grain being processed with a milling machine'],
    ['assets/img/betterlifeint-source/programs/program-photo-11.jpg', 'Women meeting together in a community group'],
    ['assets/img/soil-sample-in-hand.webp', 'Soil being examined by hand in a field'],
];

$whoPhotos = [
    'Women and Girls'                 => ['assets/img/betterlifeint-source/programs/program-photo-3.jpg', 'A woman in a BetterLife shirt holding a young plant'],
    'Children and Young People'       => ['assets/img/classroom-climate-club.webp', 'Pupils raising their hands in a classroom'],
    'Refugees and Displaced Families' => ['assets/img/smiles-group-under-tree-2.webp', 'Community members gathered under a tree'],
    'Smallholder Farmers'             => ['assets/img/farmer-spraying-crops.webp', 'A farmer tending a maize crop'],
];

// Where we work: places named in the supplied text only (no addresses)
$pr = $map['proj'];
$project = fn(float $lon, float $lat): array => [
    round(deg2rad($lon) * $pr['scale'] + $pr['ox'], 1),
    round(-log(tan(M_PI / 4 + deg2rad($lat) / 2)) * $pr['scale'] + $pr['oy'], 1),
];
$countryMeta = [
    'Uganda' => ['key' => 'uganda', 'mapKey' => 'Uganda', 'note' => 'Where BetterLife began',
        'places' => [['Yumbe and Bidi Bidi', 31.3, 3.47, 'field', 'West Nile coordination']],
        'photo' => ['assets/img/yumbe-greenhouse-interior.webp', 'Inside a greenhouse in Yumbe, Uganda']],
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

$journeyPhotos = [
    '2023' => ['assets/img/soilla-app-field-demo.webp', 'The Soilla app open on a phone in a field', 'Soilla in use in the field'],
    '2024' => ['assets/img/solar-panel-installation-2.webp', 'A solar panel installed above a raised water tank', 'Clean energy installation'],
    '2025' => ['assets/img/betterlifeint-source/projects/project-agro-tourism-alt.jpeg', 'A farmer walking through a banana plantation at BetterLife Agro Tourism Farm', 'BetterLife Agro Tourism Farm'],
];

$strip = [
    ['assets/img/betterlifeint-source/programs/program-photo-1.jpg', 'A tray of seedlings ready for transplanting'],
    ['assets/img/betterlifeint-source/projects/project-spring-alt.jpeg', 'Collecting water at a spring'],
    ['assets/img/children-at-borehole.webp', 'Children fetching water at a borehole'],
    ['assets/img/betterlifeint-source/impact-reports/impact-photo-3.jpeg', 'Leafy greens growing in a hydroponic system'],
    ['assets/img/solar-panel-installation-1.webp', 'Installing a solar panel'],
    ['assets/img/betterlifeint-source/projects/project-renewable-pathways-alt.jpg', 'Plastic bottles collected for reuse'],
    ['assets/img/market-stall-vendor.webp', 'A vendor at her market stall'],
    ['assets/img/betterlifeint-source/programs/program-photo-7.jpg', 'A newly planted field'],
    ['assets/img/betterlifeint-source/programs/program-photo-5.jpg', 'Students holding placards at a school event'],
    ['assets/img/betterlifeint-source/programs/program-photo-6.jpg', 'Young women at a BetterLife event'],
];

$contact = fn(string $subject): string => SITE_URL . '/contact.php?subject=' . rawurlencode($subject);

$heroImg = 'assets/img/smiles-group-under-tree-1.webp';
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
  <section class="ab-hero" aria-labelledby="abHeroTitle">
    <div class="ab-hero-media"><?= ab_img($heroImg, 'Members of a BetterLife community programme gathered under a large tree', 'ab-hero-img', false) ?></div>
    <div class="container ab-hero-inner">
      <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">About Us</span></nav>
      <p class="ab-kicker"><span>Refugee-led</span> <i aria-hidden="true">&middot;</i> <span>Youth-led</span> <i aria-hidden="true">&middot;</i> <span>Women-led</span></p>
      <h1 id="abHeroTitle">It Began with the Lives We Knew</h1>
      <p class="ab-hero-lead">This work did not begin in a conference room. It began with young people who knew that poverty, displacement and climate change do not arrive one at a time, and who wanted solutions that make sense in the whole of a person&rsquo;s life.</p>
      <a href="#beginnings" class="ab-hero-scroll">Read our story <?= icon('chevron-down', 16) ?></a>
    </div>
  </section>

  <!-- 2. Our beginnings, then the team -->
  <section class="ab-begin" id="beginnings" aria-labelledby="abBeginTitle">
    <div class="container ab-begin-grid">
      <div class="ab-collage ab-reveal">
        <?= ab_photo('assets/img/field-team-group-under-tree.webp', 'Members of the BetterLife field team standing together outdoors', 'Members of the BetterLife field team', 'beginnings', 'ab-col ab-col-a', '(max-width: 900px) 50vw, 300px') ?>
        <?= ab_photo('assets/img/yumbe-greenhouse-group.webp', 'A community group outside a greenhouse', 'A community group outside a greenhouse in Yumbe, Uganda', 'beginnings', 'ab-col ab-col-b', '(max-width: 900px) 50vw, 320px') ?>
        <?= ab_photo('assets/img/betterlifeint-source/programs/program-photo-10.jpg', 'Women taking notes during a training session', 'Women taking notes during a BetterLife training session', 'beginnings', 'ab-col ab-col-c', '(max-width: 900px) 45vw, 260px') ?>
        <?= ab_photo('assets/img/betterlifeint-source/projects/project-smiles-alt.jpg', 'Women gathered at a community session', 'Women gathered at a community session', 'beginnings', 'ab-col ab-col-d', '(max-width: 900px) 45vw, 260px') ?>
        <div class="ab-stamp ab-stamp-year" aria-hidden="true"><span>Founded</span><strong>2021</strong></div>
        <div class="ab-stamp ab-stamp-money" aria-hidden="true"><span>Started with</span><strong>USD 200</strong></div>
      </div>

      <div class="ab-begin-copy ab-reveal">
        <span class="ab-eyebrow">Our beginnings</span>
        <h2 id="abBeginTitle">Started by people who had lived it</h2>
        <p class="ab-lead">Denise Ayebare founded BetterLife International in Uganda in 2021, at nineteen, with USD&nbsp;200 and a small team of young people. As a refugee-led team, we had grown up experiencing many of the challenges facing the communities we wanted to work alongside.</p>
        <p>We knew the uncertainty of providing for a household when food, income and opportunity were difficult to secure. We had also seen the knowledge and determination within communities. Our commitment was to help people build on that ability, with practical support to grow food, earn, save and access markets.</p>
        <p>That beginning has grown into an organisation reaching 110,000 people across five African countries. The work continues through community members, staff, volunteers and partners who help shape it every day.</p>
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
            <ul class="ab-team-faces">
              <?php foreach ($leaders as $l): ?>
                <li><?= ab_img($l['photo'], $l['name'], 'ab-team-photo', true, '', '96px') ?><strong><?= h($l['name']) ?></strong><small><?= h($l['role']) ?></small></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <p class="ab-team-line">BetterLife is led by its founder and Executive Director, Denise Ayebare<?php if ($others): ?>, with <?= h($listJoin(array_map(fn($l) => $l['name'] . ' (' . $l['role'] . ')', $others))) ?><?php endif; ?><?php if ($managerCountries): ?>, alongside country managers in <?= h($listJoin($managerCountries)) ?><?php endif; ?>.</p>
          <a href="<?= SITE_URL ?>/team.php" class="ab-link">Meet the full team and board <?= icon('arrow-right', 15) ?></a>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <!-- 3. Impact: figures, then results from one programme -->
  <section class="ab-impact" aria-labelledby="abImpactTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Impact in pictures</span>
        <h2 id="abImpactTitle">Since 2021, counted in people, farms, homes and schools</h2>
      </div>
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
        <a href="<?= SITE_URL ?>/programs.php#climate-resilient-agriculture" class="ab-link">Read the full programme story <?= icon('arrow-right', 15) ?></a>
      </article>
    </div>
  </section>

  <!-- 4. Vision and mission -->
  <section class="ab-purpose" aria-labelledby="abPurposeTitle">
    <div class="ab-purpose-bg"><?= ab_img('assets/img/solar-panel-farm-sky.webp', '', '', true, '', '60vw') ?></div>
    <div class="container">
      <div class="ab-purpose-head ab-reveal">
        <span class="ab-eyebrow">Our vision and mission</span>
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
      </div>
      <div class="ab-practice ab-reveal">
        <span class="ab-purpose-label"><?= icon('leaf', 18) ?> What this means in practice</span>
        <p>Our focus is food security and sustainable livelihoods. Alongside refugees, displaced people and host communities, we help families grow food, earn an income, save and reach markets, and build the climate resilience that keeps those gains in place.</p>
      </div>
    </div>
  </section>

  <!-- 5. How we work: the failed-harvest view, the five principles, the values -->
  <section class="ab-systems" aria-labelledby="abSystemsTitle">
    <div class="container ab-systems-grid">
      <div class="ab-systems-copy ab-reveal">
        <span class="ab-eyebrow">How we work</span>
        <h2 id="abSystemsTitle">A Failed Harvest Is Never Just a Farming Problem</h2>
        <p class="ab-lead">When a woman tells us her harvest failed, seeds may appear to be the answer. Listen longer and the picture changes. She may have no water nearby. She may spend much of the day collecting firewood. She may lack money for inputs, access to a phone or a buyer for what she grows.</p>
        <p class="ab-pull ab-pull-sm">Giving her seeds alone leaves most of the problem untouched.</p>
        <p>We take a systems approach because people live in systems. One programme may include a demonstration garden, a savings group, a digital tool and a buyer connection, shaped by the barriers people actually face.</p>
        <figure class="ab-systems-photo"><?= ab_img('assets/img/farmers-planting-together.webp', 'Two people planting seedlings together in a field', '', true, '', '(max-width: 900px) 100vw, 560px') ?></figure>
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
      <div class="ab-how-grid">
        <div class="ab-how-sticky" aria-hidden="true">
          <div class="ab-how-frame">
            <?php foreach ($howWeWork as $i => $s): [$p] = $howPhotos[$i] ?? $howPhotos[0]; ?>
              <?= ab_img($p, '', 'ab-how-img' . ($i === 0 ? ' is-active' : ''), true, 'data-step="' . $i . '"', '560px') ?>
            <?php endforeach; ?>
            <span class="ab-how-count"><b>01</b> / <?= str_pad((string) count($howWeWork), 2, '0', STR_PAD_LEFT) ?></span>
          </div>
        </div>
        <ol class="ab-how-steps">
          <?php foreach ($howWeWork as $i => $s): [$p, $alt] = $howPhotos[$i] ?? $howPhotos[0]; ?>
            <li class="ab-how-step" data-step="<?= $i ?>">
              <figure class="ab-how-step-photo"><?= ab_img($p, $alt, '', true, '', '100vw') ?></figure>
              <span class="ab-how-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
              <h4><?= h($s['title']) ?></h4>
              <p><?= h($s['body']) ?></p>
              <?php if ($i === 3): ?>
                <p class="ab-trust"><?= icon('users', 20) ?><span>People learn faster in groups they already trust.</span></p>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <section class="ab-guides" aria-labelledby="abGuidesTitle">
    <div class="container">
      <div class="ab-guides-intro ab-reveal">
        <h2 class="ab-eyebrow" id="abGuidesTitle">What guides us</h2>
        <p class="ab-statement">People closest to a problem must have a real hand in defining it, designing the response and deciding what success looks like.</p>
      </div>
      <ol class="ab-guides-grid">
        <?php foreach ($guides as $i => $g): ?>
          <li class="ab-guide ab-reveal">
            <span class="ab-guide-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h3><?= h($g['title']) ?></h3>
            <p><?= h($g['body']) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
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
          [$p, $alt] = $whoPhotos[$w['title']] ?? [null, ''];
          [$short, $more] = ab_split(str_replace('; they', '. They', $w['body'])); ?>
          <article class="ab-who-card ab-reveal">
            <?php if ($p): ?><div class="ab-who-photo"><?= ab_img($p, $alt, '', true, '', '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 300px') ?></div><?php endif; ?>
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
        <h2 id="abWhereTitle">Rooted in Uganda, working across five countries</h2>
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
              <div class="ab-country-photo">
                <?php if ($c['photo']): ?>
                  <?= ab_img($c['photo'][0], $c['photo'][1], '', true, '', '(max-width: 900px) 100vw, 520px') ?>
                <?php else: ?>
                  <div class="ab-photo-needed"><?= icon('map-pin', 20) ?><span>Photograph from our <?= h($name) ?> work needed</span></div>
                <?php endif; ?>
              </div>
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
    <div class="container">
      <div class="ab-head ab-head-center ab-reveal">
        <span class="ab-eyebrow">Our journey</span>
        <h2 id="abJourneyTitle">From a local idea to work across five countries</h2>
      </div>
      <ol class="ab-timeline">
        <?php foreach ($journey as $i => $j): [$firstLine, $rest] = ab_split($j['body']); $ph = $journeyPhotos[$j['title']] ?? null; ?>
          <li class="ab-milestone ab-reveal">
            <span class="ab-year"><?= h($j['title']) ?></span>
            <div class="ab-milestone-card">
              <?php if ($ph): ?><?= ab_photo($ph[0], $ph[1], $ph[2], 'journey', 'ab-milestone-photo', '(max-width: 720px) 90vw, 440px') ?><?php endif; ?>
              <p><?= h($firstLine) ?></p>
              <?php if ($rest): ?>
                <p class="ab-milestone-more" id="abMs<?= $i ?>"><?= h($rest) ?></p>
                <button type="button" class="ab-who-toggle" aria-expanded="false" aria-controls="abMs<?= $i ?>" hidden><span>Read more</span> <?= icon('chevron-down', 15) ?></button>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <!-- 9. Tools that extend the work -->
  <section class="ab-tools" aria-labelledby="abToolsTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Tools that extend the work</span>
        <h2 id="abToolsTitle">Practical guidance. Connections to opportunity.</h2>
        <p class="ab-head-sub">Two BetterLife platforms help farmers make production decisions and reach buyers, always paired with field training.</p>
      </div>
      <div class="ab-tools-grid">
        <article class="ab-tool ab-reveal">
          <div class="ab-tool-media">
            <?= ab_photo('assets/img/betterlifeint-source/projects/project-soilla-app-alt.jpeg', 'Screens from the Soilla app', 'Screens from the Soilla app', 'tools', 'ab-tool-screens', '400px') ?>
            <figure class="ab-tool-person"><?= ab_img('assets/img/soilla-app-portrait.webp', 'A young man holding up a phone showing the Soilla app', '', true, '', '200px') ?></figure>
          </div>
          <span class="ab-status">Launched in 2023</span>
          <h3>Soilla</h3>
          <p>Our digital agricultural advisory platform. Farmers use it for soil and crop guidance, climate information, market prices and agricultural services, and to find suppliers, experts and other producers.</p>
          <a href="<?= SITE_URL ?>/programs.php#digital-innovation" class="ab-link">How Soilla fits our programmes <?= icon('arrow-right', 15) ?></a>
        </article>
        <article class="ab-tool ab-reveal">
          <div class="ab-tool-media ab-tool-media-single">
            <figure class="ab-tool-person"><?= ab_img('assets/img/agribusiness-connekt-app.webp', 'A person holding up a phone showing the Agribusiness Connekt app', '', true, '', '(max-width: 900px) 100vw, 540px') ?></figure>
          </div>
          <span class="ab-status ab-status-soft">Work continuing in 2026</span>
          <h3>Agribusiness Connekt</h3>
          <p>Where Soilla supports production decisions, Agribusiness Connekt focuses on the business around the farm. It links farmers and small agricultural enterprises to buyers, finance, services and market information.</p>
          <a href="<?= SITE_URL ?>/programs.php#digital-innovation" class="ab-link">Explore our digital work <?= icon('arrow-right', 15) ?></a>
        </article>
      </div>
    </div>
  </section>

  <!-- 10. Community photo strip -->
  <section class="ab-strip" aria-labelledby="abStripTitle">
    <div class="container ab-strip-head ab-reveal">
      <div>
        <span class="ab-eyebrow">Around the work</span>
        <h2 id="abStripTitle">Seedlings, springs and solar panels</h2>
      </div>
      <p>Select any photograph to see it larger.</p>
    </div>
    <ul class="ab-strip-row">
      <?php foreach ($strip as [$p, $cap]): ?>
        <li><?= ab_photo($p, $cap, $cap, 'strip', 'ab-strip-item', '300px') ?><span class="ab-strip-cap"><?= h($cap) ?></span></li>
      <?php endforeach; ?>
    </ul>
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
