<?php
/**
 * Landing-page DESIGN preview (placeholder content only) — recreation of the
 * approved reference layout. Stage 1: static nav + hero Africa-mask
 * composition + icon row. Not linked from the live site.
 */
$map = require __DIR__ . '/map-paths.php';
require __DIR__ . '/brush.php';
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
</main>

</body>
</html>
