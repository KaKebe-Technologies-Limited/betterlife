<?php
/**
 * Landing-page DESIGN preview (placeholder content only) — recreation of the
 * reference layout. Static stage: all 7 sections (nav, Africa-mask hero, icon
 * row, collage, story cards, partners + second map, CTA banner); animation
 * comes after approval. Not linked from the live site.
 */
$map = require __DIR__ . '/map-paths.php';
require __DIR__ . '/brush.php';

// Same Web Mercator projection as the generated Africa path (art-box coords)
$pr = $map['proj'];
$project = fn(float $lon, float $lat): array => [
    round(deg2rad($lon) * $pr['scale'] + $pr['ox'], 1),
    round(-log(tan(M_PI / 4 + deg2rad($lat) / 2)) * $pr['scale'] + $pr['oy'], 1),
];
// Placeholder location markers on the second map (real coordinates, so they sit correctly)
$markers = [
    [-17.4, 14.7], [-8.0, 12.6], [2.1, 13.5], [-0.2, 5.6], [3.4, 6.5], [3.0, 36.7], [31.2, 30.0],
    [32.5, 15.6], [38.7, 9.0], [32.6, 0.3], [36.8, -1.3], [15.3, -4.3], [28.3, -15.4], [28.0, -26.2],
];
$img = '../assets/img/';
$v   = @filemtime(__DIR__ . '/landing.css') ?: time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Landing Design Preview | BetterLife International</title>
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

<!-- 1. Slim navigation bar -->
<header class="lp-nav">
  <div class="lp-nav-inner">
    <a href="#" class="lp-logo"><img src="<?= $img ?>logo.png" alt="BetterLife International" width="700" height="144"></a>
    <nav class="lp-links" aria-label="Main">
      <a href="#">About</a>
      <a href="#">Our Work</a>
      <a href="#">Stories</a>
      <a href="#">Contact</a>
    </nav>
    <a href="#" class="lp-btn lp-btn-dark lp-btn-sm">Button</a>
  </div>
</header>

<main>
  <!-- 2. Hero: editorial headline left, Africa-masked photograph right -->
  <section class="lp-hero">
    <div class="lp-container lp-hero-inner">
      <div class="lp-art">
        <svg class="lp-art-svg" viewBox="0 0 1700 720" preserveAspectRatio="xMinYMin meet" role="img" aria-label="Photograph shown inside an accurate outline of Africa">
          <defs>
            <!-- Natural Earth 1:50m, Web Mercator, mainland + Madagascar, single dissolved outline -->
            <clipPath id="lpAfricaClip" clipPathUnits="userSpaceOnUse"><path d="<?= $map['africa'] ?>"/></clipPath>
          </defs>

          <!-- Surrounding world, as in the reference: pale land behind the nav -->
          <path class="lp-world" d="<?= $map['world'] ?>"/>

          <!-- Photograph clipped INSIDE Africa (box 40,53 → 592,673) -->
          <g clip-path="url(#lpAfricaClip)">
            <rect x="0" y="0" width="640" height="720" fill="#d9cbb8"/>
            <image class="lp-africa-photo" href="<?= $img ?>village-children-portrait.webp" x="-85" y="-215" width="960" height="1440" preserveAspectRatio="xMidYMid slice"/>
          </g>

          <!-- Brush strokes in BetterLife colours, placed where the reference has them -->
          <g class="lp-brushes" filter="url(#lpBrush)">
            <path class="b-green" d="<?= lp_brush_d(-42, 250, 108, 216, 50, 3) ?>"/>
            <path class="b-blue"  d="<?= lp_brush_d(414, 376, 572, 430, 44, 7, -0.04) ?>"/>
            <path class="b-green" d="<?= lp_brush_d(462, 396, 512, 406, 20, 11) ?>"/>
            <path class="b-blue"  d="<?= lp_brush_d(168, 474, 298, 482, 50, 5) ?>"/>
            <path class="b-green" d="<?= lp_brush_d(250, 508, 292, 556, 30, 13) ?>"/>
          </g>
        </svg>

        <!-- Stat badge (placeholder figure) -->
        <div class="lp-stat" aria-hidden="false">
          <svg class="lp-stat-brush" viewBox="0 0 130 80" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(6, 48, 126, 34, 40, 21) ?>"/></svg>
          <span class="lp-stat-circle" aria-hidden="true"></span>
          <strong class="lp-stat-num">000</strong>
          <span class="lp-stat-label">Placeholder stat<br>label text</span>
        </div>
      </div>

      <div class="lp-hero-copy">
        <h1 class="lp-display">Lorem ipsum dolor<br><span class="lp-mark">sit amet<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 17, 0.02) ?>"/></svg></span> tempor<br>incididunt ut.</h1>
        <p class="lp-lead">Placeholder paragraph text for the hero introduction. It runs to roughly two short lines, as in the reference.</p>
        <div class="lp-actions">
          <a href="#" class="lp-btn lp-btn-dark">Button</a>
          <a href="#" class="lp-textlink">Text link <span aria-hidden="true">&rarr;</span></a>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. Compact row of small icons + placeholder labels -->
  <section class="lp-pillars">
    <div class="lp-container lp-pillars-row">
      <span class="lp-rule lp-rule-l" aria-hidden="true"></span>
      <div class="lp-pillar">
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 20A7 7 0 0 1 4 13c0-5 4-9 15-11-2 11-6 15-11 15Z"/><path d="M4 13c3.5 3.5 7 4 11 3"/></svg>
        <h3>Label one</h3>
        <p>Placeholder text for this item, about three short lines long.</p>
      </div>
      <div class="lp-pillar is-active">
        <svg class="lp-pillar-wash" viewBox="0 0 200 130" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(8, 68, 194, 60, 104, 31, 0.03) ?>"/></svg>
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5Z"/><path d="M4 19.5V4.5"/></svg>
        <h3>Label two</h3>
        <p>Placeholder text for this item, about three short lines long.</p>
      </div>
      <div class="lp-pillar">
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5s7 8 7 12.5a7 7 0 0 1-14 0c0-4.5 7-12.5 7-12.5Z"/></svg>
        <h3>Label three</h3>
        <p>Placeholder text for this item, about three short lines long.</p>
      </div>
      <div class="lp-pillar">
        <svg class="lp-ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/></svg>
        <h3>Label four</h3>
        <p>Placeholder text for this item, about three short lines long.</p>
      </div>
      <span class="lp-rule lp-rule-r" aria-hidden="true"></span>
    </div>
  </section>

  <!-- 4. Overlapping photo collage left, heading + text + buttons right -->
  <section class="lp-impact">
    <div class="lp-narrow lp-impact-inner">
      <div class="lp-collage">
        <svg class="lp-collage-brush is-back" viewBox="0 0 620 521" aria-hidden="true">
          <g filter="url(#lpBrush)">
            <path class="f-blue" d="<?= lp_brush_d(386, 24, 584, 6, 50, 41) ?>"/>
            <path class="f-blue" d="<?= lp_brush_d(404, 436, 562, 426, 30, 43) ?>"/>
          </g>
        </svg>
        <figure class="lp-ph ph-b"><img src="<?= $img ?>classroom-climate-club.webp" alt="Placeholder photo"></figure>
        <figure class="lp-ph ph-a"><img src="<?= $img ?>school-child-drinking-water.webp" alt="Placeholder photo"></figure>
        <figure class="lp-ph ph-d"><img src="<?= $img ?>soilla-app-portrait.webp" alt="Placeholder photo"></figure>
        <figure class="lp-ph ph-c"><img src="<?= $img ?>impact-story-2.jpg" alt="Placeholder photo"></figure>
        <svg class="lp-collage-brush is-front" viewBox="0 0 620 521" aria-hidden="true">
          <g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(168, 326, 368, 312, 46, 45) ?>"/></g>
        </svg>
      </div>

      <div class="lp-impact-copy">
        <h2 class="lp-h2">Lorem ipsum<br>dolor sit amet<br><span class="lp-mark">tempor ut?<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 47, 0.02) ?>"/></svg></span></h2>
        <p class="lp-body is-strong">Placeholder introduction paragraph set slightly darker, running to about three lines in this column.</p>
        <p class="lp-body">Placeholder body paragraph. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
        <p class="lp-body">Placeholder closing line, about two lines long in the reference layout.</p>
        <div class="lp-actions lp-actions-end">
          <a href="#" class="lp-textlink">Text link <span aria-hidden="true">&rarr;</span></a>
          <a href="#" class="lp-btn lp-btn-dark">Button</a>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. Three photographic story cards -->
  <section class="lp-stories">
    <div class="lp-narrow">
      <div class="lp-stories-head">
        <h2 class="lp-h2">Lorem <span class="lp-mark">ipsum dolor<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 51, 0.02) ?>"/></svg></span> sit amet<br>consectetur elit</h2>
        <div class="lp-arrows">
          <button type="button" class="lp-arrow" data-dir="-1" aria-label="Previous stories"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg></button>
          <button type="button" class="lp-arrow" data-dir="1" aria-label="Next stories"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg></button>
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
          <?php
          $cards = ['village-girl-portrait.webp', 'about-real-1.jpg', 'market-stall-vendor.webp', 'farmers-planting-together.webp', 'field-team-conversation.webp'];
          foreach ($cards as $i => $photo): ?>
            <article class="lp-card">
              <div class="lp-card-img"><img src="<?= $img . $photo ?>" alt="Placeholder story photo" loading="lazy"></div>
              <h3>Name <?= $i + 1 ?></h3>
              <span class="lp-card-meta">Placeholder</span>
              <p>Placeholder story summary for this card, about three short lines of text.</p>
              <a href="#" class="lp-textlink lp-textlink-sm">Learn more <span aria-hidden="true">&rarr;</span></a>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. Text + partner logos left, second accurate Africa map right -->
  <section class="lp-reach">
    <div class="lp-narrow lp-reach-inner">
      <div class="lp-reach-copy">
        <h2 class="lp-h2">Lorem ipsum dolor<br>sit amet <span class="lp-mark">tempor ut.<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="<?= lp_brush_d(4, 17, 238, 14, 22, 57, 0.02) ?>"/></svg></span></h2>
        <div class="lp-partners">
          <?php
          $partners = ['hbcu-green-fund-logo.png', 'iccb-logo.jpeg', 'mentor-me-360-logo.jpeg', 'tuzu-logo.png'];
          foreach ($partners as $i => $logo): ?>
            <div class="lp-partner">
              <span class="lp-partner-dot <?= $i % 3 === 0 ? 'is-green' : 'is-blue' ?>" aria-hidden="true"></span>
              <img src="<?= $img ?>betterlifeint-source/partner-logos/<?= $logo ?>" alt="Partner logo placeholder" loading="lazy">
              <p>Placeholder partner text, two short lines.</p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="lp-reach-map">
        <svg viewBox="22 35 588 656" role="img" aria-label="Map of Africa with placeholder location markers">
          <defs>
            <clipPath id="lpAfricaClip2" clipPathUnits="userSpaceOnUse"><path d="<?= $map['africa'] ?>"/></clipPath>
          </defs>
          <g clip-path="url(#lpAfricaClip2)">
            <rect x="0" y="0" width="640" height="720" class="lp-map-base"/>
            <!-- two-tone painted bands, as in the reference -->
            <path class="lp-map-band2" d="M0 262 C 90 238, 190 300, 300 276 S 500 246, 640 284 L 640 720 L 0 720 Z"/>
            <path class="lp-map-band1" d="M0 300 C 110 280, 210 338, 320 312 S 520 284, 640 322 L 640 720 L 0 720 Z"/>
          </g>
          <g class="lp-markers">
            <?php foreach ($markers as [$lon, $lat]): [$mx, $my] = $project($lon, $lat); ?>
              <circle cx="<?= $mx ?>" cy="<?= $my ?>" r="11" class="halo"/><circle cx="<?= $mx ?>" cy="<?= $my ?>" r="5"/>
            <?php endforeach; ?>
          </g>
          <g filter="url(#lpBrush)" class="lp-map-brush">
            <path d="<?= lp_brush_d(436, 196, 566, 222, 34, 61) ?>"/>
            <path d="<?= lp_brush_d(34, 306, 228, 332, 40, 63) ?>"/>
            <path d="<?= lp_brush_d(192, 512, 338, 538, 32, 65) ?>"/>
          </g>
        </svg>
      </div>
    </div>
  </section>

  <!-- 7. Wide photographic call-to-action banner -->
  <section class="lp-cta">
    <div class="lp-cta-box">
      <svg class="lp-cta-brush is-back" viewBox="0 0 1199 331" aria-hidden="true">
        <g filter="url(#lpBrush)">
          <path class="f-blue"  d="<?= lp_brush_d(140, -6, 250, -14, 26, 71) ?>"/>
          <path class="f-blue"  d="<?= lp_brush_d(-92, 96, 12, 70, 40, 73) ?>"/>
          <path class="f-green" d="<?= lp_brush_d(-100, 214, 14, 176, 46, 75) ?>"/>
        </g>
      </svg>
      <img class="lp-cta-photo" src="<?= $img ?>woman-winnowing-grain.webp" alt="" loading="lazy">
      <div class="lp-cta-shade" aria-hidden="true"></div>
      <div class="lp-cta-content">
        <h2>Lorem ipsum dolor!<br>Sit amet consectetur.</h2>
        <div class="lp-cta-actions">
          <a href="#" class="lp-btn lp-btn-blue">Button</a>
          <a href="#" class="lp-btn lp-btn-green">Second button</a>
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

<script src="landing.js?v=<?= @filemtime(__DIR__ . '/landing.js') ?: time() ?>"></script>

</body>
</html>
