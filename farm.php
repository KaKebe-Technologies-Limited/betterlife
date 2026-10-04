<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'BetterLife Farm';
$activePage = 'farm';
$pageDescription = 'BetterLife Agro Tourism Farm in Rukungiri: seedlings for families’ own gardens, a market for their surplus, and value added on the farm as flour, ghee, yoghurt, honey and organic manure.';

// What visitors see (Admin → Page Content, page "farm", section "on_the_farm")
$onTheFarm = content_items($pdo, 'farm', 'on_the_farm');
// The shop's products, so the value-addition diagram always matches the shop (Admin → Products)
$products = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY featured DESC, sort_order, id")->fetchAll();

// The opening film: one shot per part of the farm, in the order of the index under the headline; each opens its part of the ring
$filmIndex = [['Fodder', 'fodder'], ['Dairy', 'dairy'], ['Biogas', 'biogas'], ['Solar water', 'water'], ['Harvest', 'crops']];

// Seedlings and starter inputs that go out to households
$seedlings = [
    ['Vegetables', 'Seedlings for home and sack gardens, so families can grow the greens they would otherwise buy.', 'assets/img/programmes/yumbe-seedling-trays.jpg', 'Vegetable seedlings growing in nursery trays', '50% 50%'],
    ['Maize', 'Seed for the staple that fills most plates, planted with practical training on preparing the ground and caring for the crop.', 'assets/img/farm/maize-seed-bucket.jpg', 'A bucket of maize seed ready for planting', '50% 62%'],
    ['Soya', 'A protein-rich crop that feeds the household and, once milled, becomes flour.', 'assets/img/farm/young-crops.jpg', 'Young crops coming up in rows', '50% 50%'],
];

// From support to supplier: how a participant moves through the farm
$model = [
    ['Learn and receive', 'Participants who are rebuilding their livelihoods can choose to spend up to two flexible hours at the farm. They receive practical agricultural training, food support, free seedlings and starter inputs.', 'assets/img/farm/planting-maize.jpg', 'A woman and a man planting maize together', '50% 40%'],
    ['Grow at home', 'The arrangement leaves time for family care, study, job-seeking and other income. Families grow food to eat first; what they do not need becomes surplus.', 'assets/img/farm/preparing-land.jpg', 'Women preparing the ground with hoes', '45% 40%'],
    ['Supply the farm', 'As people become more stable, they move into independent production. BetterLife Agro Tourism Farm Ltd buys their surplus, turning a good season into cash and capital for the next one.', 'assets/img/home/farm-weighing.jpg', 'A woman weighing a sack of produce on a hanging scale', '45% 40%'],
];

// Nothing on the farm works alone: six parts that feed each other
$cycle = [
    'fodder'  => ['Fodder', 'Grass goes through the chaff cutter, which chops it into feed for the cattle.', 'assets/img/farm/chaff-cutter-chute.jpg', 'A young man feeding grass into the chaff cutter', '70% 45%'],
    'dairy'   => ['Dairy', 'Livestock supports food, manure, household income and the farm’s dairy value chain, which ends in BetterLife Ghee and Yoghurt.', 'assets/img/farm/calves-shed.jpg', 'Young cattle feeding in the farm’s wooden shed', '55% 55%'],
    'biogas'  => ['Biogas', 'Dung from the cattle shed is wheeled to the biogas digester, which turns it into a clean fuel.', 'assets/img/farm/biogas-inlet-dung.jpg', 'Dung tipped from a wheelbarrow into the inlet of the biogas digester', '50% 50%'],
    'water'   => ['Solar water', 'Solar energy pumps water and feeds the drip lines, so crops keep growing when rainfall is unreliable.', 'assets/img/farm/solar-panels.jpg', 'Solar panels above banana plants at the farm', '45% 45%'],
    'crops'   => ['Crops', 'Maize, soya and vegetables grow in the farm’s fields and demonstration gardens, where farmers learn water-efficient production, soil management and crop care.', 'assets/img/farm/maize-field.jpg', 'A field of young maize below forested hills', '50% 60%'],
    'poultry' => ['Poultry and bees', 'Poultry and beekeeping spread the risk, so no household depends on one crop or one season. Beekeeping also protects trees and flowering plants.', 'assets/img/farm/hen.jpg', 'A speckled hen in the poultry house', '55% 40%'],
];

// Value addition: what comes in, and what it leaves the farm as. Products come from the shop; one not listed in
// $productFrom is drawn as made on the farm itself.
$flowIn = [
    'grain' => ['Maize and soya', 'From the farm and participating farmers', '<path d="M12 3c2.6 2 3.6 5 3.6 8.4S14.1 17.9 12 20c-2.1-2.1-3.6-5.2-3.6-8.6S9.4 5 12 3Z"/><path d="M12 6.5v11M9.6 9.5h4.8M9.2 12.5h5.6M9.6 15.5h4.8"/><path d="M12 20c-3.4-.9-6.3-3.4-7-7.3 3 .2 5.4 2.3 6.6 5M12 20c3.4-.9 6.3-3.4 7-7.3-3 .2-5.4 2.3-6.6 5"/>'],
    'milk'  => ['Milk and butter', 'Surplus bought from community farmers', '<path d="M9 3h6M10 3v3.2L7.4 9.6A2 2 0 0 0 7 10.8V19a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-8.2a2 2 0 0 0-.4-1.2L14 6.2V3"/><path d="M7 13.5h10"/>'],
    'honey' => ['Honey', 'From community beekeepers', '<path d="m8 3.5 3 1.7v3.5L8 10.4 5 8.7V5.2Z"/><path d="m16 3.5 3 1.7v3.5l-3 1.7-3-1.7V5.2Z"/><path d="m12 10.6 3 1.7v3.5l-3 1.7-3-1.7v-3.5Z"/><path d="M12 17.5v2.2"/>'],
];
$productFrom = ['betterlife-ghee' => 'milk', 'betterlife-yoghurt-strawberry' => 'milk', 'betterlife-yoghurt-vanilla' => 'milk', 'betterlife-honey' => 'honey'];
$flowOut = [['name' => 'Maize and soya flour', 'unit' => 'Milled and bagged at the farm', 'image' => 'assets/img/farm/flour-bagging.jpg', 'from' => 'grain', 'price' => null, 'slug' => null]];
foreach ($products as $p) {
    $flowOut[] = ['name' => $p['name'], 'unit' => $p['unit'], 'image' => $p['image'], 'from' => $productFrom[$p['slug']] ?? 'farm', 'price' => $p['price'] !== null ? (float) $p['price'] : null, 'slug' => $p['slug']];
}
$fromOrder = ['grain' => 0, 'milk' => 1, 'honey' => 2, 'farm' => 3];
usort($flowOut, fn($a, $b) => $fromOrder[$a['from']] <=> $fromOrder[$b['from']]);

// Around the farm: two gliding rows of photographs, none of them used elsewhere on the page
$stripRows = [
    [
        ['assets/img/farm/chopped-fodder.jpg', 'Chopped fodder falling from the chaff cutter onto a tarpaulin'],
        ['assets/img/about/rukungiri-communal-farm.jpg', 'A wide maize field below forested hills'],
        ['assets/img/farm/biogas-mixing.jpg', 'Mixing dung and water at the biogas inlet beside the cattle shed'],
        ['assets/img/farm/community-field.jpg', 'Farmers clearing and preparing a field together'],
        ['assets/img/farm/poultry-house.jpg', 'A woman refilling feeders in the poultry house'],
        ['assets/img/farm/mill-shed.jpg', 'The mill in the farm’s processing shed'],
    ],
    [
        ['assets/img/farm/seed-scoop.jpg', 'A hand scooping maize seed from a bucket'],
        ['assets/img/farm/biogas-digester.jpg', 'The covered biogas digester'],
        ['assets/img/farm/crop-spraying.jpg', 'A farmer spraying a tall crop with a knapsack sprayer'],
        ['assets/img/farm/maize-two-women.jpg', 'Two women laughing in a maize field'],
        ['assets/img/farm/solar-pump.jpg', 'A solar panel above the farm’s water tank'],
        ['assets/img/farm/maize-woman.jpg', 'A woman smiling among tall maize'],
    ],
];
$shape = function (string $p): float { $s = @getimagesize(__DIR__ . '/' . $p); return $s ? round($s[0] / $s[1], 3) : 1.0; };

$heroImg = 'assets/img/farm/chaff-cutter-feeding.jpg';
$heroV = ab_variants($heroImg);
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css', 'assets/css/farm.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/programmes.js', 'assets/js/farm.js'];
$pageHead = ($heroV
        ? '<link rel="preload" as="image" imagesrcset="' . h(implode(', ', array_map(fn($w, $r) => asset_url($r) . " {$w}w", array_keys($heroV), $heroV))) . '" imagesizes="100vw" fetchpriority="high">'
        : '')
    . '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg fm" id="top">
  <?= ab_brush_defs() ?>

  <!-- 1. Opening: a silent film of the working farm, its parts indexed under the headline -->
  <section class="pg-hero has-index fm-hero" aria-labelledby="fmTitle">
    <div class="pg-hero-media">
      <?= ab_img($heroImg, 'A young man feeding grass into the chaff cutter at BetterLife Agro Tourism Farm', '', false, 'style="object-position: 62% 40%"', '100vw') ?>
      <?php if (is_file(__DIR__ . '/assets/video/farm-hero-720.mp4')): ?>
        <!-- Silent film from the farm in Rukungiri, one shot per index item; loaded after the page on wider screens only
             (see programmes.js); the photograph stays as the fallback -->
        <video class="pg-hero-video" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-cues="0,3.2,6,8.8,11.6" data-src="<?= h(asset_url('assets/video/farm-hero-720.mp4')) ?>?v=<?= filemtime(__DIR__ . '/assets/video/farm-hero-720.mp4') ?>"<?php if (is_file(__DIR__ . '/assets/video/farm-hero-1080.mp4')): ?> data-src-hd="<?= h(asset_url('assets/video/farm-hero-1080.mp4')) ?>?v=<?= filemtime(__DIR__ . '/assets/video/farm-hero-1080.mp4') ?>"<?php endif; ?>></video>
      <?php endif; ?>
    </div>
    <button type="button" class="ab-motion-toggle pg-film-toggle" aria-pressed="false" aria-label="Pause background film" hidden>
      <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6v12M15 6v12"/></svg>
      <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l10.5-6.5Z"/></svg>
    </button>
    <div class="container pg-hero-inner">
      <div class="pg-hero-copy">
        <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">BetterLife Farm</span></nav>
        <p class="pg-hero-kicker">BetterLife Agro Tourism Farm, Rukungiri</p>
        <h1 id="fmTitle">Food today. A route to <?= ab_mark('independence', 23) ?> tomorrow.</h1>
        <p class="pg-hero-lead">A working, solar-powered farm built around food security. Families receive seedlings for their own gardens, the farm buys the surplus they grow, and the harvest becomes flour, ghee, yoghurt, honey and organic manure.</p>
        <div class="pg-hero-actions">
          <a href="#model" class="pg-btn">How the farm works <?= icon('chevron-down', 16) ?></a>
          <a href="<?= SITE_URL ?>/products.php" class="pg-btn pg-btn-ghost">Shop farm products</a>
        </div>
      </div>
      <nav class="pg-hero-index" aria-label="Parts of the farm">
        <?php foreach ($filmIndex as $i => [$label, $part]): ?>
          <a href="#cycle" data-i="<?= $i ?>" data-part="<?= $part ?>"><b><?= sprintf('%02d', $i + 1) ?></b><span><?= h($label) ?></span></a>
        <?php endforeach; ?>
      </nav>
    </div>
  </section>

  <!-- 2. Food security first, and who does what -->
  <section class="fm-intro" aria-labelledby="fmIntroTitle">
    <div class="container fm-intro-grid">
      <div class="ab-reveal">
        <span class="ab-eyebrow">Food security first</span>
        <h2 id="fmIntroTitle">A farm that feeds families before it feeds a market</h2>
        <p class="ab-lead">BetterLife Agro Tourism Farm supports refugees, women and vulnerable households to move from emergency assistance towards producing their own food and earning stable incomes.</p>
        <p>Solar energy powers the farm’s irrigation, water pumping and key production activities, so it keeps producing through dry periods and shows farmers how clean energy can lower both climate risk and running costs.</p>
        <div class="fm-orgs">
          <div class="fm-org">
            <span class="fm-org-tag">Trains and supports</span>
            <strong>BetterLife International</strong>
            <span>Farmers, refugees, women and vulnerable households</span>
          </div>
          <span class="fm-org-join" aria-hidden="true">+</span>
          <div class="fm-org is-ltd">
            <span class="fm-org-tag">Produces and sells</span>
            <strong>BetterLife Agro Tourism Farm Ltd</strong>
            <span>Production, processing, packaging and sales</span>
          </div>
        </div>
      </div>
      <figure class="fm-intro-photo ab-reveal">
        <?= ab_photo('assets/img/farm/maize-smile.jpg', 'A woman laughing in a maize field', 'Maize at the farm in Rukungiri', 'farm', 'fm-intro-img', '(max-width: 900px) 100vw, 460px', 'style="--pos: 50% 35%"') ?>
        <?= ab_photo('assets/img/farm/drip-line.jpg', 'A drip irrigation line running along a prepared bed', 'Drip irrigation, fed by solar pumping', 'farm', 'fm-intro-inset', '(max-width: 900px) 40vw, 200px', 'style="--pos: 55% 60%"') ?>
      </figure>
    </div>
  </section>

  <!-- 3. What goes out: seedlings and starter inputs -->
  <section class="fm-seed" id="seedlings" aria-labelledby="fmSeedTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">What goes out to communities</span>
          <h2 id="fmSeedTitle">Not only vegetables. Staples too.</h2>
        </div>
        <p class="ab-head-sub">Refugee, displaced and vulnerable host-community families receive seedlings, organic manure and practical training to start producing at home.</p>
      </div>
      <ul class="fm-seed-grid">
        <?php foreach ($seedlings as $i => [$name, $text, $p, $alt, $pos]): ?>
          <li class="fm-seed-card ab-reveal" style="--i: <?= $i ?>">
            <div class="fm-seed-photo"><?= ab_img($p, $alt, '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 720px) 100vw, 380px') ?></div>
            <div class="fm-seed-body">
              <h3><?= h($name) ?></h3>
              <p><?= h($text) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
        <li class="fm-seed-card is-plus ab-reveal" style="--i: 3">
          <span class="fm-plus" aria-hidden="true">+</span>
          <h3>With every household</h3>
          <p>Organic manure to feed the soil, and practical training so the first harvest is not left to chance.</p>
        </li>
      </ul>
    </div>
  </section>

  <!-- 4. From support to supplier -->
  <section class="fm-model" id="model" aria-labelledby="fmModelTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">How the model works</span>
        <h2 id="fmModelTitle">From support to supplier</h2>
      </div>
      <ol class="fm-steps">
        <?php foreach ($model as $i => [$title, $text, $p, $alt, $pos]): ?>
          <li class="fm-step ab-reveal" style="--i: <?= $i ?>">
            <div class="fm-step-photo"><?= ab_img($p, $alt, '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 720px) 100vw, 380px') ?></div>
            <span class="fm-step-no"><?= sprintf('%02d', $i + 1) ?></span>
            <h3><?= h($title) ?></h3>
            <p><?= h($text) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
      <p class="fm-pull ab-reveal">The point is not to keep people working at the farm. It is to help them reach a place where they <?= ab_mark('no longer need to', 41) ?>.</p>
    </div>
  </section>

  <!-- 5. Nothing on the farm works alone: six parts around a centre; choose one (or watch them turn) -->
  <section class="fm-cycle" id="cycle" aria-labelledby="fmCycleTitle">
    <div class="container fm-cycle-grid">
      <div class="fm-cycle-copy ab-reveal">
        <span class="ab-eyebrow">One connected farm</span>
        <h2 id="fmCycleTitle">Nothing on the farm works alone</h2>
        <p>Grass becomes feed, cattle give milk and manure, dung becomes fuel and the sun moves the water. Choose a part of the farm to see how it feeds the next.</p>
        <div class="fm-cycle-panels">
          <?php $k = 0; foreach ($cycle as $key => [$title, $text, $p, $alt, $pos]): ?>
            <div class="fm-cycle-panel<?= $k === 0 ? ' is-active' : '' ?>" id="fmPanel-<?= $key ?>" role="tabpanel" aria-labelledby="fmTab-<?= $key ?>" tabindex="0">
              <span class="fm-cycle-no"><?= sprintf('%02d', $k + 1) ?> / <?= sprintf('%02d', count($cycle)) ?></span>
              <h3><?= h($title) ?></h3>
              <p><?= h($text) ?></p>
            </div>
          <?php $k++; endforeach; ?>
        </div>
      </div>
      <div class="fm-ring ab-reveal">
        <div class="fm-ring-stage">
          <svg class="fm-ring-line" viewBox="0 0 100 100" aria-hidden="true" focusable="false"><circle cx="50" cy="50" r="38"/></svg>
          <div class="fm-ring-core" aria-hidden="true">
            <?php $k = 0; foreach ($cycle as $key => [$title, $text, $p, $alt, $pos]): ?>
              <span class="fm-ring-photo<?= $k === 0 ? ' is-on' : '' ?>" data-key="<?= $key ?>"><?= ab_img($p, '', '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 720px) 60vw, 320px') ?></span>
            <?php $k++; endforeach; ?>
          </div>
          <div class="fm-ring-nodes" role="tablist" aria-label="Parts of the farm">
            <?php $n = count($cycle); $k = 0; foreach ($cycle as $key => [$title, $text, $p, $alt, $pos]):
              $a = -M_PI / 2 + 2 * M_PI * $k / $n; $x = round(50 + 38 * cos($a), 2); $y = round(50 + 38 * sin($a), 2); ?>
              <button type="button" role="tab" class="fm-node" id="fmTab-<?= $key ?>" aria-controls="fmPanel-<?= $key ?>" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>" tabindex="<?= $k === 0 ? '0' : '-1' ?>" data-key="<?= $key ?>" style="--x: <?= $x ?>%; --y: <?= $y ?>%">
                <span class="fm-node-img"><?= ab_img($p, '', '', true, 'style="object-position: ' . h($pos) . '"', '96px') ?></span>
                <span class="fm-node-label"><?= h($title) ?></span>
              </button>
            <?php $k++; endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. Value addition: what comes in, what the farm does, what leaves (lines drawn by farm.js on wide screens) -->
  <section class="fm-value" id="products" aria-labelledby="fmValueTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Value addition</span>
          <h2 id="fmValueTitle">What the harvest becomes</h2>
        </div>
        <p class="ab-head-sub">Training has limited value if a farmer produces and cannot sell. BetterLife Agro Tourism Farm Ltd buys, processes and markets produce from the farm and from participating farmers.</p>
      </div>
      <div class="fm-flow ab-reveal">
        <svg class="fm-flow-lines" aria-hidden="true" focusable="false"></svg>
        <div class="fm-flow-col">
          <h3 class="fm-flow-label">Comes in</h3>
          <ul class="fm-flow-in">
            <?php foreach ($flowIn as $key => [$name, $from, $svg]): ?>
              <li class="fm-in" data-in="<?= $key ?>">
                <span class="fm-in-text"><strong><?= h($name) ?></strong><small><?= h($from) ?></small></span>
                <span class="fm-in-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><?= $svg ?></svg></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="fm-flow-hub">
          <div class="fm-hub-photo"><?= ab_img('assets/img/farm/winnowing.jpg', 'A woman winnowing grain, the chaff blowing from her tray', '', true, 'style="object-position: 45% 45%"', '(max-width: 900px) 120px, 260px') ?></div>
          <p class="fm-hub-text"><strong>BetterLife Agro Tourism Farm Ltd</strong><span>Buys, processes, packs and sells</span></p>
        </div>
        <div class="fm-flow-col">
          <h3 class="fm-flow-label">Goes out</h3>
          <ul class="fm-flow-out">
            <?php foreach ($flowOut as $o): $usd = format_usd($pdo, $o['price']); $tag = $o['slug'] ? 'a' : 'div'; ?>
              <li class="fm-out" data-in="<?= $o['from'] ?>">
                <<?= $tag ?> class="fm-out-card"<?php if ($o['slug']): ?> href="<?= SITE_URL ?>/product.php?slug=<?= h($o['slug']) ?>"<?php endif; ?>>
                  <span class="fm-out-img"><?= ab_img($o['image'], '', '', true, '', '72px') ?></span>
                  <span class="fm-out-text"><strong><?= h($o['name']) ?></strong><small><?= h($o['unit']) ?></small></span>
                  <?php if ($o['price'] !== null): ?>
                    <span class="fm-out-price"><?= h(format_price($o['price'])) ?><?php if ($usd): ?><small><?= h($usd) ?></small><?php endif; ?></span>
                  <?php endif; ?>
                </<?= $tag ?>>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="fm-flow-foot ab-reveal">
        <p><span class="fm-flow-back" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 12a8 8 0 1 0 2.6-5.9M4 4v4.5h4.5"/></svg></span>The farm buys their surplus, adds value and sells it, so every purchase helps keep that market open for refugee, displaced and host-community families.</p>
        <a href="<?= SITE_URL ?>/products.php" class="pg-btn">Visit the shop <?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </section>

  <!-- 7. Around the farm: two rows that glide in opposite directions as the page scrolls (about.js) -->
  <section class="pg-strip fm-strip" aria-labelledby="fmStripTitle">
    <div class="container pg-strip-head ab-reveal">
      <div>
        <span class="ab-eyebrow">Around the farm</span>
        <h2 id="fmStripTitle">Fodder, fuel, flour and fields</h2>
      </div>
    </div>
    <div class="ab-glide ab-reveal" role="group" aria-label="Photographs from the farm. Select one to see it larger.">
      <?php foreach ($stripRows as $r => $row): ?>
        <div class="ab-glide-row<?= $r === 0 ? ' is-tall' : '' ?>" data-glide="<?= $r % 2 ? -1 : 1 ?>">
          <?php foreach ($row as [$p, $cap]): ?>
            <?= ab_photo($p, $cap, $cap, 'farm-strip', 'ab-glide-item', $r === 0 ? '(max-width: 720px) 60vw, 460px' : '(max-width: 720px) 50vw, 400px', 'style="--ar: ' . $shape($p) . '"') ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 8. Visit the farm -->
  <section class="fm-visit" id="visit" aria-labelledby="fmVisitTitle">
    <div class="container fm-visit-grid">
      <figure class="fm-visit-photo ab-reveal">
        <?= ab_photo('assets/img/farm/rukungiri-farm-sign.jpg', 'The roadside sign for BetterLife Agro-Tourism Farm and BetterLife International Organisation in Rukungiri', 'The farm’s roadside sign in Rukungiri', 'farm', 'fm-visit-img', '(max-width: 900px) 100vw, 420px') ?>
      </figure>
      <div class="ab-reveal">
        <span class="ab-eyebrow">Agro-tourism</span>
        <h2 id="fmVisitTitle">Come and see it working</h2>
        <p class="ab-lead">Schools, farmers, community groups, development partners and visitors can experience how solar energy, irrigation, livestock, beekeeping and food processing work together.</p>
        <?php if ($onTheFarm): ?>
          <ul class="fm-see">
            <?php foreach ($onTheFarm as $b): ?>
              <li><strong><?= h($b['title']) ?></strong><span><?= h($b['body']) ?></span></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <div class="fm-visit-actions">
          <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Farm visit') ?>" class="pg-btn">Book a farm visit <?= icon('arrow-right', 16) ?></a>
          <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Partnership enquiry') ?>" class="fm-link">Partner with the farm <?= icon('arrow-right', 15) ?></a>
        </div>
      </div>
    </div>
  </section>

  <!-- 9. Partner with the farm -->
  <section class="pg-close fm-close" aria-labelledby="fmCloseTitle">
    <div class="pg-close-bg"><?= ab_img('assets/img/farm/maize-women.jpg', '', '', true, 'style="object-position: 62% 40%"', '100vw') ?></div>
    <div class="container">
      <div class="pg-close-inner ab-reveal">
        <span class="ab-eyebrow">Partner with us</span>
        <h2 id="fmCloseTitle">Help more families grow their own food</h2>
        <p>Fund seedlings and starter inputs for a season, back processing at the farm, or buy what the farm makes. Every route keeps the harvest moving.</p>
      </div>
      <div class="pg-hero-actions ab-reveal">
        <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Partnership enquiry') ?>" class="pg-btn">Talk to our team <?= icon('arrow-right', 16) ?></a>
        <a href="<?= SITE_URL ?>/products.php" class="pg-btn pg-btn-ghost">Shop farm products</a>
      </div>
    </div>
  </section>
</main>

<?= ab_lightbox() ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
