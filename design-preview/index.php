<?php
/**
 * Landing page preview: approved reference design with final copy.
 * Sections: nav, Africa-mask hero, approach row, origins collage, work in
 * practice cards, where we work (map + figures + partners), closing banner,
 * footer. Not linked from the live site yet.
 */
$map = require __DIR__ . '/map-paths.php';
require __DIR__ . '/brush.php';

$img  = '../assets/img/';
$site = '..';
$v    = @filemtime(__DIR__ . '/landing.css') ?: time();

// Second map: the five countries where BetterLife works, with label placements
// (art-box coordinates, same Web Mercator projection as the outline).
$countryLabels = [
    'Ghana'                            => ['lx' => 166, 'ly' => 384, 'anchor' => 'middle', 'lines' => ['Ghana']],
    'Democratic Republic of the Congo' => ['lx' => 270, 'ly' => 452, 'anchor' => 'end',    'lines' => ['Democratic Republic', 'of the Congo']],
    'South Sudan'                      => ['lx' => 612, 'ly' => 298, 'anchor' => 'start',  'lines' => ['South Sudan']],
    'Uganda'                           => ['lx' => 612, 'ly' => 354, 'anchor' => 'start',  'lines' => ['Uganda']],
    'Tanzania'                         => ['lx' => 612, 'ly' => 414, 'anchor' => 'start',  'lines' => ['Tanzania']],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>BetterLife International | Refugee-led food security and livelihoods</title>
<meta name="description" content="BetterLife International is a refugee-led organisation working with refugees, displaced people and host communities in five African countries to grow food, build skills and start enterprises.">
<link rel="icon" href="<?= $img ?>favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="landing.css?v=<?= $v ?>">
</head>
<body>

<!-- Shared SVG defs: painted brush-stroke filter (rough edge + dry bristle streaks) -->
<svg class="lp-defs" width="0" height="0" aria-hidden="true" focusable="false">
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

<!-- 1. Navigation -->
<header class="lp-nav">
  <div class="lp-nav-inner">
    <a href="./" class="lp-logo"><img src="<?= $img ?>logo.png" alt="BetterLife International" width="700" height="144"></a>
    <nav class="lp-links" id="lpLinks" aria-label="Main">
      <a href="<?= $site ?>/about.php">About Us</a>
      <a href="<?= $site ?>/programs.php">Our Work</a>
      <a href="<?= $site ?>/impact-reports.php">Our Impact</a>
      <a href="#where">Where We Work</a>
      <a href="#get-involved">Get Involved</a>
      <a href="<?= $site ?>/contact.php?subject=Partnership enquiry" class="lp-btn lp-btn-dark lp-links-cta">Partner With Us</a>
    </nav>
    <button type="button" class="lp-menu-toggle" aria-expanded="false" aria-controls="lpLinks">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg><span>Menu</span>
    </button>
    <a href="<?= $site ?>/contact.php?subject=Partnership enquiry" class="lp-btn lp-btn-dark lp-btn-sm lp-nav-cta">Partner With Us</a>
  </div>
</header>

<main>
  <!-- 2. Hero -->
  <section class="lp-hero">
    <div class="lp-container lp-hero-inner">
      <div class="lp-art">
        <svg class="lp-art-svg" viewBox="0 0 1700 720" preserveAspectRatio="xMinYMin meet" role="img" aria-label="Children standing in a village, shown inside a map of Africa">
          <defs>
            <!-- Natural Earth 1:50m, Web Mercator, mainland + Madagascar, single dissolved outline -->
            <clipPath id="lpAfricaClip" clipPathUnits="userSpaceOnUse"><path d="<?= $map['africa'] ?>"/></clipPath>
          </defs>
          <path class="lp-world" d="<?= $map['world'] ?>"/>
          <!-- The clip is on this fixed group; only the photos inside it fade and zoom,
               so the outline of Africa never moves. Each photo is placed so faces sit
               in the upper middle of the continent and watermarks fall below the coast;
               --o is the zoom origin (the faces). -->
          <g clip-path="url(#lpAfricaClip)">
            <rect x="0" y="0" width="640" height="720" fill="#d9cbb8"/>
            <?php
            $africaPhotos = [
                ['village-children-portrait.webp', -85,  -215, 960,  '42% 33%'],
                ['about-real-1.jpg',               -80,  -194, 820,  '50% 33%'],
                ['hero-real-1.jpg',                -99,  -208, 900,  '51% 31%'],
                ['village-girl-portrait.webp',     -479, -807, 1500, '53% 47%'],
            ];
            foreach ($africaPhotos as $i => [$file, $x, $y, $w, $origin]): ?>
              <image class="lp-africa-photo" href="<?= $img . $file ?>" x="<?= $x ?>" y="<?= $y ?>" width="<?= $w ?>" height="<?= $w * 1.5 ?>" preserveAspectRatio="xMidYMid slice" style="--o: <?= $origin ?>; --i: <?= $i ?>"/>
            <?php endforeach; ?>
          </g>
          <g class="lp-brushes" filter="url(#lpBrush)">
            <path class="b-green" d="<?= lp_brush_d(-42, 250, 108, 216, 50, 3) ?>"/>
            <path class="b-blue"  d="<?= lp_brush_d(414, 376, 572, 430, 44, 7, -0.04) ?>"/>
            <path class="b-green" d="<?= lp_brush_d(462, 396, 512, 406, 20, 11) ?>"/>
            <path class="b-blue"  d="<?= lp_brush_d(168, 474, 298, 482, 50, 5) ?>"/>
            <path class="b-green" d="<?= lp_brush_d(250, 508, 292, 556, 30, 13) ?>"/>
          </g>
        </svg>

        <button type="button" class="lp-motion-toggle" aria-pressed="false" aria-label="Pause photo animation">
          <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6v12M15 6v12"/></svg>
          <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l10.5-6.5Z"/></svg>
        </button>

        <div class="lp-stat">
          <svg class="lp-stat-brush" viewBox="0 0 130 80" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(6, 48, 126, 34, 40, 21) ?>"/></svg>
          <span class="lp-stat-circle" aria-hidden="true"></span>
          <strong class="lp-stat-num">110,000</strong>
          <span class="lp-stat-label">people reached<br>since 2021</span>
        </div>
      </div>

      <div class="lp-hero-copy">
        <h1 class="lp-display">From the next meal<br>to <span class="lp-mark">a living<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 17, 0.02) ?>"/></svg></span> families<br>can plan around.</h1>
        <p class="lp-lead">BetterLife International is refugee-led. We work with refugees, displaced people and host communities in five African countries to grow food, build skills and start enterprises.</p>
        <div class="lp-actions">
          <a href="<?= $site ?>/programs.php" class="lp-btn lp-btn-dark">Explore Our Work</a>
          <a href="#get-involved" class="lp-textlink">Support Our Work <span aria-hidden="true">&rarr;</span></a>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. How the work moves: four steps -->
  <section class="lp-pillars" aria-label="Our approach">
    <div class="lp-container lp-pillars-row">
      <span class="lp-rule lp-rule-l" aria-hidden="true"></span>
      <div class="lp-pillar">
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5Z"/><path d="M4 19.5V4.5"/></svg>
        <h3>Learn and practise</h3>
        <p>Climate-smart farming and trades, from tailoring to solar technology.</p>
      </div>
      <div class="lp-pillar is-active">
        <svg class="lp-pillar-wash" viewBox="0 0 200 130" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(8, 68, 194, 60, 104, 31, 0.03) ?>"/></svg>
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 20A7 7 0 0 1 4 13c0-5 4-9 15-11-2 11-6 15-11 15Z"/><path d="M4 13c3.5 3.5 7 4 11 3"/></svg>
        <h3>Produce and build</h3>
        <p>Skills put to work on household farms, community farms and small enterprises.</p>
      </div>
      <div class="lp-pillar">
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v4c0 1.7 3.1 3 7 3s7-1.3 7-3V6"/><path d="M5 10v4c0 1.7 3.1 3 7 3s7-1.3 7-3v-4"/><path d="M5 14v4c0 1.7 3.1 3 7 3s7-1.3 7-3v-4"/></svg>
        <h3>Save and organise</h3>
        <p>Savings groups and credit co-operatives that let members plan and invest.</p>
      </div>
      <div class="lp-pillar">
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/></svg>
        <h3>Connect and grow</h3>
        <p>Markets, finance and farm advice, including our Soilla and Agribusiness Connekt tools.</p>
      </div>
      <span class="lp-rule lp-rule-r" aria-hidden="true"></span>
    </div>
  </section>

  <!-- 4. Who we are: the beginning -->
  <section class="lp-impact" id="about">
    <div class="lp-narrow lp-impact-inner">
      <div class="lp-collage">
        <svg class="lp-collage-brush is-back" viewBox="0 0 620 521" aria-hidden="true">
          <g filter="url(#lpBrush)">
            <path class="f-blue" d="<?= lp_brush_d(386, 24, 584, 6, 50, 41) ?>"/>
            <path class="f-blue" d="<?= lp_brush_d(404, 436, 562, 426, 30, 43) ?>"/>
          </g>
        </svg>
        <!-- Each frame holds two photos that slowly swap; frames float a few pixels -->
        <figure class="lp-ph ph-b"><img src="<?= $img ?>smiles-group-under-tree-2.webp" alt="A large community group gathered under a tree"><img class="lp-ph-alt" src="<?= $img ?>smiles-group-under-tree-1.webp" alt="" loading="lazy"></figure>
        <figure class="lp-ph ph-a"><img src="<?= $img ?>program-trees.jpg" alt="A woman with a baby on her back planting crops"><img class="lp-ph-alt" src="<?= $img ?>farm-field-2.jpg" alt="" loading="lazy"></figure>
        <figure class="lp-ph ph-d"><img src="<?= $img ?>soilla-app-portrait.webp" alt="A young man holding up a phone with the Soilla app"><img class="lp-ph-alt" src="<?= $img ?>agribusiness-connekt-app.webp" alt="" loading="lazy"></figure>
        <figure class="lp-ph ph-c"><img src="<?= $img ?>impact-story-2.jpg" alt="Women writing in notebooks at a group meeting"><img class="lp-ph-alt" src="<?= $img ?>impact-story-1.jpg" alt="" loading="lazy"></figure>
        <svg class="lp-collage-brush is-front" viewBox="0 0 620 521" aria-hidden="true">
          <g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(168, 326, 368, 312, 46, 45) ?>"/></g>
        </svg>
      </div>

      <div class="lp-impact-copy">
        <h2 class="lp-h2">Started in 2021<br>with USD 200 and<br><span class="lp-mark">lived experience.<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 47, 0.02) ?>"/></svg></span></h2>
        <p class="lp-body is-strong">BetterLife was founded by a refugee-led team who grew up facing many of the challenges our communities face today.</p>
        <p class="lp-body">For a family living with displacement, unreliable rain and few ways to earn, finding the next meal can take up much of the day. So we work on what changes that: land to farm, skills to use and enterprises that bring in money.</p>
        <p class="lp-body">From that start, our work has reached 110,000 people. We are still refugee-led, youth-led and women-led, with community members, staff, volunteers and partners carrying the work forward.</p>
        <div class="lp-actions lp-actions-end">
          <a href="<?= $site ?>/team.php" class="lp-textlink">Meet the team <span aria-hidden="true">&rarr;</span></a>
          <a href="<?= $site ?>/about.php" class="lp-btn lp-btn-dark">Read Our Story</a>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. Our work in practice -->
  <section class="lp-stories" id="work">
    <div class="lp-narrow">
      <div class="lp-stories-head">
        <h2 class="lp-h2">From a <span class="lp-mark">family plot<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 51, 0.02) ?>"/></svg></span><br>to a 20-acre farm.</h2>
        <div class="lp-arrows">
          <button type="button" class="lp-arrow" data-dir="-1" aria-label="Previous"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg></button>
          <button type="button" class="lp-arrow" data-dir="1" aria-label="Next"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg></button>
        </div>
      </div>
      <div class="lp-cards-wrap">
        <svg class="lp-cards-brush" viewBox="0 0 832 420" aria-hidden="true">
          <g filter="url(#lpBrush)">
            <path class="f-green" d="<?= lp_brush_d(-70, 66, 24, 52, 40, 53) ?>"/>
            <path class="f-green" d="<?= lp_brush_d(806, 104, 882, 214, 34, 55) ?>"/>
          </g>
        </svg>
        <div class="lp-cards" id="lpCards">
          <article class="lp-card">
            <div class="lp-card-img"><img src="<?= $img ?>farm-field-3.jpg" alt="A mother with her baby tending crops" loading="lazy"></div>
            <h3>Household farms</h3>
            <span class="lp-card-meta">Food and income close to home</span>
            <p>Families put their training to work on farms near home. Alongside savings and small enterprise, the farm feeds the household and adds to what it earns.</p>
            <a href="<?= $site ?>/programs.php" class="lp-textlink lp-textlink-sm">Explore our livelihoods work <span aria-hidden="true">&rarr;</span></a>
          </article>
          <article class="lp-card">
            <div class="lp-card-img"><img src="<?= $img ?>farm-field-1.jpg" alt="A farmer preparing soil beside a wheelbarrow" loading="lazy"></div>
            <h3>A 20-acre community farm</h3>
            <span class="lp-card-meta">Under way</span>
            <p>With the community, we are preparing 20 acres for maize and soybean, with clean energy supporting the farm's operations. It is being built with the capacity to help feed about 1,000 refugees and displaced people.</p>
            <a href="<?= $site ?>/projects.php" class="lp-textlink lp-textlink-sm">Follow the farm's progress <span aria-hidden="true">&rarr;</span></a>
          </article>
          <article class="lp-card">
            <div class="lp-card-img"><img src="<?= $img ?>classroom-climate-club.webp" alt="Pupils in a classroom" loading="lazy"></div>
            <h3>Meals in five schools</h3>
            <span class="lp-card-meta">School feeding</span>
            <p>We support feeding in five schools, so our food security work also reaches children and their school communities during the school day.</p>
            <a href="<?= $site ?>/programs.php" class="lp-textlink lp-textlink-sm">Learn about school feeding <span aria-hidden="true">&rarr;</span></a>
          </article>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. Where we work: figures, partners and the map -->
  <section class="lp-reach" id="where">
    <div class="lp-narrow lp-reach-inner">
      <div class="lp-reach-copy">
        <h2 class="lp-h2">Five countries,<br>five <span class="lp-mark">starting points.<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 57, 0.02) ?>"/></svg></span></h2>
        <p class="lp-body lp-reach-lead">The aim is the same in every community: food on the table and a way to earn. The starting point is not. Displacement, land, water, skills and local work all differ, so each programme begins with what a community already has.</p>

        <div class="lp-figures">
          <div class="lp-figure"><strong><small>About</small> <span class="num"><span class="lp-partner-dot is-green" aria-hidden="true"></span>122</span></strong><p>farms established</p></div>
          <div class="lp-figure"><strong><span class="num"><span class="lp-partner-dot is-blue" aria-hidden="true"></span>310</span></strong><p>households supported</p></div>
          <div class="lp-figure"><strong><span class="num"><span class="lp-partner-dot is-blue" aria-hidden="true"></span>5</span></strong><p>community farms established</p></div>
          <div class="lp-figure"><strong><span class="num"><span class="lp-partner-dot is-green" aria-hidden="true"></span>5</span></strong><p>schools supported with meals</p></div>
        </div>

      </div>

      <div class="lp-reach-map">
        <svg viewBox="22 35 700 656" role="img" aria-label="Map of Africa highlighting Uganda, South Sudan, Tanzania, the Democratic Republic of the Congo and Ghana">
          <defs>
            <clipPath id="lpAfricaClip2" clipPathUnits="userSpaceOnUse"><path d="<?= $map['africa'] ?>"/></clipPath>
          </defs>
          <g clip-path="url(#lpAfricaClip2)">
            <rect x="0" y="0" width="640" height="720" class="lp-map-base"/>
            <path class="lp-map-band2" d="M0 262 C 90 238, 190 300, 300 276 S 500 246, 640 284 L 640 720 L 0 720 Z"/>
            <path class="lp-map-band1" d="M0 300 C 110 280, 210 338, 320 312 S 520 284, 640 322 L 640 720 L 0 720 Z"/>
          </g>
          <g class="lp-countries">
            <?php foreach ($map['countries'] as $c): ?><path d="<?= $c['d'] ?>"/><?php endforeach; ?>
          </g>
          <g filter="url(#lpBrush)" class="lp-map-brush">
            <path d="<?= lp_brush_d(436, 196, 566, 222, 34, 61) ?>"/>
            <path d="<?= lp_brush_d(28, 214, 166, 240, 36, 63) ?>"/>
            <path d="<?= lp_brush_d(192, 512, 338, 538, 32, 65) ?>"/>
          </g>
          <g class="lp-map-labels">
            <?php foreach ($countryLabels as $name => $l): [$cx, $cy] = $map['countries'][$name]['c'];
              $tx = $l['anchor'] === 'start' ? $l['lx'] - 8 : ($l['anchor'] === 'end' ? $l['lx'] + 8 : $l['lx']);
              $ty = $l['anchor'] === 'middle' ? $l['ly'] - 20 : $l['ly'] - 6; ?>
              <path class="lead" d="M<?= $cx ?> <?= $cy ?> L<?= $tx ?> <?= $ty ?>"/>
              <circle class="pin-halo" cx="<?= $cx ?>" cy="<?= $cy ?>" r="11"/><circle class="pin" cx="<?= $cx ?>" cy="<?= $cy ?>" r="5.5"/>
              <text x="<?= $l['lx'] ?>" y="<?= $l['ly'] ?>" text-anchor="<?= $l['anchor'] ?>"><?php foreach ($l['lines'] as $i => $line): ?><tspan x="<?= $l['lx'] ?>" dy="<?= $i ? '1.2em' : 0 ?>"><?= htmlspecialchars($line) ?></tspan><?php endforeach; ?></text>
            <?php endforeach; ?>
          </g>
        </svg>
      </div>
    </div>
  </section>

  <!-- Partners: logo band leading into the closing invitation -->
  <section class="lp-partners-band" aria-labelledby="lpPartnersTitle">
    <div class="lp-narrow">
      <div class="lp-partners-head">
        <h2 id="lpPartnersTitle">Working alongside</h2>
        <a href="<?= $site ?>/contact.php?subject=Partnership enquiry" class="lp-textlink lp-textlink-sm">Become a partner <span aria-hidden="true">&rarr;</span></a>
      </div>
      <ul class="lp-logos">
        <?php
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
        foreach ($partners as [$file, $name]): ?>
          <li class="lp-logo-<?= pathinfo($file, PATHINFO_FILENAME) ?>"><img src="<?= $img ?>partners/<?= $file ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy"></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- 7. Closing invitation -->
  <section class="lp-cta" id="get-involved">
    <div class="lp-cta-box">
      <svg class="lp-cta-brush is-back" viewBox="0 0 1199 331" aria-hidden="true">
        <g filter="url(#lpBrush)">
          <path class="f-blue"  d="<?= lp_brush_d(140, -6, 250, -14, 26, 71) ?>"/>
          <path class="f-blue"  d="<?= lp_brush_d(-92, 96, 12, 70, 40, 73) ?>"/>
          <path class="f-green" d="<?= lp_brush_d(-100, 214, 14, 176, 46, 75) ?>"/>
        </g>
      </svg>
      <img class="lp-cta-photo" src="<?= $img ?>woman-winnowing-grain.webp" alt="A woman winnowing grain" loading="lazy">
      <div class="lp-cta-shade" aria-hidden="true"></div>
      <div class="lp-cta-content">
        <h2>Help families plan<br>beyond the next meal.</h2>
        <p>Funding, technical expertise, equipment and market connections all help communities build on work that is already under way.</p>
        <div class="lp-cta-actions">
          <a href="<?= $site ?>/contact.php?subject=Support" class="lp-btn lp-btn-blue">Support Our Work</a>
          <a href="<?= $site ?>/contact.php" class="lp-btn lp-btn-green">Talk to Our Team</a>
        </div>
      </div>
      <svg class="lp-cta-brush is-front" viewBox="0 0 1199 331" aria-hidden="true">
        <g filter="url(#lpBrush)">
          <path class="f-green" d="<?= lp_brush_d(1004, 252, 1300, 214, 54, 77) ?>"/>
          <path class="f-blue"  d="<?= lp_brush_d(1040, 318, 1170, 300, 30, 79) ?>"/>
        </g>
      </svg>
    </div>
  </section>
</main>

<footer class="lp-footer">
  <div class="lp-narrow lp-footer-inner">
    <div class="lp-footer-brand">
      <img src="<?= $img ?>logo.png" alt="BetterLife International" width="700" height="144">
      <p>Food security and sustainable livelihoods, built alongside refugees, displaced people and host communities.</p>
      <p class="lp-footer-since">Refugee-led, youth-led and women-led since 2021.</p>
    </div>
    <nav class="lp-footer-col" aria-label="Explore">
      <h4>Explore</h4>
      <a href="<?= $site ?>/about.php">About Us</a>
      <a href="<?= $site ?>/programs.php">Our Work</a>
      <a href="<?= $site ?>/impact-reports.php">Our Impact</a>
      <a href="#where">Where We Work</a>
    </nav>
    <nav class="lp-footer-col" aria-label="Connect">
      <h4>Connect</h4>
      <a href="<?= $site ?>/contact.php?subject=Partnership enquiry">Partner With Us</a>
      <a href="<?= $site ?>/contact.php">Contact Us</a>
      <span class="lp-footer-social">
        <a href="https://facebook.com/betterlifeintl" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 9V6.5A1.5 1.5 0 0 1 15.5 5H17V2h-2.5A4.5 4.5 0 0 0 10 6.5V9H7v3h3v10h4V12h2.6l.4-3H14Z"/></svg></a>
        <a href="https://twitter.com/betterlifeintl" target="_blank" rel="noopener" aria-label="X (Twitter)"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 7.5 9.4L3.4 21H6l6-6.5 4.8 6.5H21l-8-9.9L20.2 3H17.6l-5.5 6L7.4 3H3Z"/></svg></a>
        <a href="https://instagram.com/betterlifeintl" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/></svg></a>
        <a href="https://linkedin.com/company/betterlifeintl" target="_blank" rel="noopener" aria-label="LinkedIn"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7.5 10v7M7.5 7v.01M11.5 17v-4a2.2 2.2 0 0 1 4.4 0v4M11.5 10v7"/></svg></a>
      </span>
    </nav>
    <nav class="lp-footer-col" aria-label="Accountability">
      <h4>Accountability</h4>
      <a href="<?= $site ?>/impact-reports.php">Reports</a>
      <a href="<?= $site ?>/about.php">Governance</a>
      <a href="#">Privacy Policy</a>
    </nav>
  </div>
  <div class="lp-narrow lp-footer-base">&copy; <?= date('Y') ?> BetterLife International</div>
</footer>

<script src="landing.js?v=<?= @filemtime(__DIR__ . '/landing.js') ?: time() ?>"></script>
</body>
</html>
