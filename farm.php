<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'BetterLife Farm';
$activePage = 'farm';
$pageDescription = 'BetterLife Agro Tourism Farm in Rukungiri: seedlings for families’ own gardens, a market for their surplus, and value added on the farm as flour, ghee, yoghurt, honey and organic manure.';

// What visitors see (Admin → Page Content, page "farm", section "on_the_farm")
$onTheFarm = content_items($pdo, 'farm', 'on_the_farm');
// The shop's products, so prices here always match the shop (Admin → Products)
$products = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY featured DESC, sort_order, id")->fetchAll();

// The opening film: one shot per part of the farm, in the order of the index under the headline
$filmIndex = ['Fodder', 'Dairy', 'Biogas', 'Solar water', 'Harvest'];

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
    'fodder'  => ['Fodder', 'Grass grown on the farm goes through the chaff cutter, which chops it into feed for the cattle.', 'assets/img/farm/chaff-cutter-feeding.jpg', 'A young man feeding grass into a chaff cutter driven by a diesel engine', '62% 40%'],
    'dairy'   => ['Dairy', 'The dairy herd gives the milk that becomes BetterLife Ghee and Yoghurt.', 'assets/img/farm/calves-shed.jpg', 'Young cattle feeding in the farm’s wooden shed', '55% 55%'],
    'biogas'  => ['Biogas', 'Dung from the cattle shed is wheeled to the biogas digester, which turns it into a clean fuel.', 'assets/img/farm/biogas-inlet-dung.jpg', 'Dung tipped from a wheelbarrow into the inlet of the biogas digester', '50% 50%'],
    'water'   => ['Solar water', 'Solar energy pumps water and feeds the drip lines, so crops keep growing when rainfall is unreliable.', 'assets/img/farm/solar-pump.jpg', 'A solar panel above the farm’s water tank', '50% 45%'],
    'crops'   => ['Crops', 'Maize, soya and vegetables grow in the farm’s fields and demonstration gardens, where farmers learn water-efficient production, soil management and crop care.', 'assets/img/farm/maize-field.jpg', 'A field of young maize below forested hills', '50% 60%'],
    'poultry' => ['Poultry and bees', 'Poultry and beekeeping spread the risk, so no household depends on one crop or one season. Beekeeping also protects trees and flowering plants.', 'assets/img/farm/poultry-feeding.jpg', 'Feeding chickens in the poultry house', '45% 50%'],
];

// Around the farm: two gliding rows of photographs
$stripRows = [
    [
        ['assets/img/farm/chopped-fodder.jpg', 'Chopped fodder falling from the chaff cutter onto a tarpaulin'],
        ['assets/img/woman-winnowing-grain.webp', 'A woman winnowing grain with a woven tray'],
        ['assets/img/farm/biogas-mixing.jpg', 'Mixing dung and water at the biogas inlet beside the cattle shed'],
        ['assets/img/farm/community-field.jpg', 'Farmers clearing and preparing a field together'],
        ['assets/img/farm/poultry-house.jpg', 'A woman refilling feeders in the poultry house'],
        ['assets/img/farm/drip-line.jpg', 'A drip irrigation line running along a prepared bed'],
    ],
    [
        ['assets/img/farm/maize-woman.jpg', 'A woman smiling among tall maize'],
        ['assets/img/farm/chaff-cutter-chute.jpg', 'The chaff cutter’s curved chute'],
        ['assets/img/farm/biogas-digester.jpg', 'The covered biogas digester'],
        ['assets/img/about/rukungiri-communal-farm.jpg', 'A wide maize field below forested hills'],
        ['assets/img/farm/flour-bagging.jpg', 'Bagging flour at the farm’s mill'],
        ['assets/img/farm/rukungiri-farm-sign.jpg', 'The roadside sign for BetterLife Agro-Tourism Farm in Rukungiri'],
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
        <?php foreach ($filmIndex as $i => $label): ?>
          <a href="#cycle" data-i="<?= $i ?>"><b><?= sprintf('%02d', $i + 1) ?></b><span><?= h($label) ?></span></a>
        <?php endforeach; ?>
      </nav>
    </div>
  </section>

  <!-- 2. Food security first -->
  <section class="fm-intro" aria-labelledby="fmIntroTitle">
    <div class="container fm-intro-grid">
      <div class="ab-reveal">
        <span class="ab-eyebrow">Food security first</span>
        <h2 id="fmIntroTitle">A farm that feeds families before it feeds a market</h2>
        <p class="ab-lead">BetterLife Agro Tourism Farm supports refugees, women and vulnerable households to move from emergency assistance towards producing their own food and earning stable incomes.</p>
        <p>Solar energy powers the farm’s irrigation, water pumping and key production activities, so it keeps producing through dry periods and shows farmers how clean energy can lower both climate risk and running costs.</p>
        <ul class="fm-facts">
          <li><strong>2023</strong><span>founded in Rukungiri</span></li>
          <li><strong>2 hours</strong><span>flexible time at the farm, at most</span></li>
          <li><strong>Solar</strong><span>pumping and irrigation</span></li>
        </ul>
      </div>
      <figure class="fm-intro-photo ab-reveal">
        <?= ab_photo('assets/img/farm/maize-woman.jpg', 'A woman smiling among tall maize', 'Maize at the farm in Rukungiri', 'farm', 'fm-intro-img', '(max-width: 900px) 100vw, 460px', 'style="--pos: 40% 30%"') ?>
        <?= ab_photo('assets/img/farm/maize-seed-bucket.jpg', 'A bucket of maize seed ready for planting', 'Maize seed ready for planting', 'farm', 'fm-intro-inset', '(max-width: 900px) 40vw, 200px') ?>
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
        <p>Grass becomes feed, feed becomes milk, dung becomes fuel and the sun moves the water. Choose a part of the farm to see how it feeds the next.</p>
        <div class="fm-cycle-panels">
          <?php $k = 0; foreach ($cycle as $key => [$title, $text, $p, $alt, $pos]): ?>
            <div class="fm-cycle-panel<?= $k === 0 ? ' is-active' : '' ?>" id="fmPanel-<?= $key ?>" role="tabpanel" aria-labelledby="fmTab-<?= $key ?>" tabindex="0">
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
              <span class="fm-ring-photo<?= $k === 0 ? ' is-on' : '' ?>" data-key="<?= $key ?>"><?= ab_img($p, '', '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 720px) 60vw, 300px') ?></span>
            <?php $k++; endforeach; ?>
          </div>
          <div class="fm-ring-nodes" role="tablist" aria-label="Parts of the farm">
            <?php $n = count($cycle); $k = 0; foreach ($cycle as $key => [$title, $text, $p, $alt, $pos]):
              $a = -M_PI / 2 + 2 * M_PI * $k / $n; $x = round(50 + 38 * cos($a), 2); $y = round(50 + 38 * sin($a), 2); ?>
              <button type="button" role="tab" class="fm-node" id="fmTab-<?= $key ?>" aria-controls="fmPanel-<?= $key ?>" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>" tabindex="<?= $k === 0 ? '0' : '-1' ?>" data-key="<?= $key ?>" style="--x: <?= $x ?>%; --y: <?= $y ?>%">
                <span class="fm-node-img"><?= ab_img($p, '', '', true, 'style="object-position: ' . h($pos) . '"', '88px') ?></span>
                <span class="fm-node-label"><?= h($title) ?></span>
              </button>
            <?php $k++; endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. Value addition -->
  <section class="fm-value" id="products" aria-labelledby="fmValueTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Value addition</span>
          <h2 id="fmValueTitle">What the harvest becomes</h2>
        </div>
        <p class="ab-head-sub">Training has limited value if a farmer produces and cannot sell. BetterLife Agro Tourism Farm Ltd buys, processes and markets produce from the farm and from participating farmers.</p>
      </div>
      <div class="fm-mill ab-reveal">
        <div class="fm-mill-photos">
          <?= ab_photo('assets/img/farm/mill-shed.jpg', 'The mill in the farm’s processing shed', 'The mill in the farm’s processing shed', 'farm', 'fm-mill-a', '(max-width: 720px) 100vw, 520px') ?>
          <?= ab_photo('assets/img/farm/flour-bagging.jpg', 'A woman bagging flour as it leaves the mill', 'Bagging flour at the mill', 'farm', 'fm-mill-b', '(max-width: 720px) 50vw, 240px') ?>
        </div>
        <div class="fm-mill-copy">
          <span class="fm-tag">Milled at the farm</span>
          <h3>Maize and soya flour</h3>
          <p>Maize and soya bought from farmers are milled and bagged in the farm’s own shed, so a harvest leaves the farm worth more than when it arrived.</p>
        </div>
      </div>
      <?php if ($products): ?>
        <ul class="fm-shelf">
          <?php foreach ($products as $i => $p): $price = $p['price'] !== null ? (float) $p['price'] : null; $usd = format_usd($pdo, $price); ?>
            <li class="fm-product ab-reveal" style="--i: <?= $i ?>">
              <a href="<?= SITE_URL ?>/product.php?slug=<?= h($p['slug']) ?>">
                <span class="fm-product-img"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['name']) ?>" loading="lazy" decoding="async"></span>
                <span class="fm-product-body">
                  <strong><?= h($p['name']) ?></strong>
                  <span class="fm-product-unit"><?= h($p['unit']) ?></span>
                  <span class="fm-product-price"><?= h(format_price($price)) ?><?php if ($usd): ?> <small><?= h($usd) ?></small><?php endif; ?></span>
                </span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="fm-shelf-cta ab-reveal"><a href="<?= SITE_URL ?>/products.php" class="pg-btn">Visit the shop <?= icon('arrow-right', 16) ?></a></div>
      <?php endif; ?>
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
    <div class="pg-close-bg"><?= ab_img('assets/img/impact/cabbage-rows.jpg', '', '', true, 'style="object-position: 50% 55%"', '100vw') ?></div>
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
