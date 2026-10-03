<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'Impact & Reports';
$activePage = 'impact';
$pageDescription = 'What changed after the training ended: BetterLife’s reach year by year, the results behind it and our annual reports from 2022 to 2025.';

// Headline figures (Admin → Stats) and the annual reports (Admin → Impact & Reports)
$stats = $pdo->query("SELECT * FROM stats WHERE status = 1 ORDER BY sort_order")->fetchAll();
$reports = $pdo->query("SELECT * FROM reports WHERE status = 1 ORDER BY year, sort_order")->fetchAll();

// Each year as its annual report tells it. Reach is counted per year: the years sit side by side and are never added together.
$years = [
    '2022' => [
        'reach' => 27400,
        'line'  => 'Climate education, green learning spaces and recycling in schools and refugee-hosting communities.',
        'notes' => [
            ['7,000+', 'students in climate education across more than 20 rural primary schools'],
            ['5', 'new BetterLife Green Libraries, including in refugee settlements'],
            ['3', 'Green Eco Labs in South Sudan, where more than 3,000 people learnt practical skills'],
            ['20', 'Plastic Banks, with 25 local cooperatives formed around them'],
        ],
        'mark'  => 'Green Libraries, Eco Labs and 20 Plastic Banks',
        'photo' => ['assets/img/impact/classroom-hand-up.jpg', 'A pupil raising his hand in class in Rukungiri', '38% 52%'],
    ],
    '2023' => [
        'reach' => 37550,
        'line'  => 'A turn towards smart and sustainable agriculture, including the founding of the BetterLife Agro Tourism Farm.',
        'notes' => [
            ['48', 'biogas plants, bringing clean energy to more than 1,200 households'],
            ['10', 'boreholes, giving 3,500 people clean water in refugee-hosting communities in South Sudan'],
            ['1,500+', 'refugees trained in sustainable farming'],
            ['30', 'schools holding environmental debates, with more than 2,500 students'],
        ],
        'mark'  => 'BetterLife Agro Tourism Farm founded',
        'photo' => ['assets/img/woman-winnowing-grain.webp', 'A woman winnowing grain at the farm in Rukungiri', '60% 33%'],
    ],
    '2024' => [
        'reach' => 68500,
        'line'  => 'A year of consolidation and scale-up across Uganda, South Sudan, Tanzania and Ghana.',
        'notes' => [
            ['120+', 'schools reached with climate education'],
            ['9,600+', 'farmers and young people trained at climate-smart demonstration farms'],
            ['5,400+', 'young people and women in SMILES skills training and mentoring'],
            ['2,300+', 'young people in climate leadership and community action'],
        ],
        'mark'  => '9,600+ farmers trained at demonstration farms',
        'photo' => ['assets/img/impact/maize-woman-smile.jpg', 'A farmer smiling among her maize in Rukungiri', '45% 30%'],
    ],
    '2025' => [
        'reach' => 112430,
        'line'  => 'The year our work moved from programme expansion to regional scale, with women, young people and displaced families at the centre.',
        'notes' => [
            ['18,900', 'farmers using the Soilla platform, with more than 420,000 tailored advisory messages sent'],
            ['37%', 'higher crop yields and 28% lower input costs among farmers using Soilla'],
            ['41,200', 'refugees and host-community members reached'],
            ['60+', 'youth dialogues on climate action'],
        ],
        'mark'  => '18,900 farmers on Soilla',
        'photo' => ['assets/img/programmes/rukungiri-soilla-phone.jpg', 'A young farmer smiling as he shows the Soilla app on his phone in Rukungiri', '50% 30%'],
    ],
];
$first = array_key_first($years); $last = array_key_last($years);
$growth = (int) floor($years[$last]['reach'] / $years[$first]['reach']);

// The curve: an SVG plot (viewBox 1000 × 500); the photographs and labels sit over it at the same percentages
$yMax = 120000; [$px0, $px1, $py0, $py1] = [150, 870, 40, 440];
$pts = []; $i = 0; $n = count($years);
foreach ($years as $y => $d) {
    $pts[$y] = [$px0 + ($px1 - $px0) * $i++ / max(1, $n - 1), $py1 - $d['reach'] / $yMax * ($py1 - $py0)];
}
$line = ''; $prev = null;
foreach ($pts as [$x, $y]) {
    if (!$prev) { $line = sprintf('M%.1f %.1f', $x, $y); }
    else { $dx = ($x - $prev[0]) / 2.4; $line .= sprintf(' C%.1f %.1f %.1f %.1f %.1f %.1f', $prev[0] + $dx, $prev[1], $x - $dx, $y, $x, $y); }
    $prev = [$x, $y];
}
$firstPt = reset($pts); $lastPt = end($pts);
$area = $line . sprintf(' L%.1f %d L%.1f %d Z', $lastPt[0], $py1, $firstPt[0], $py1);

// Four things we track after training, each answered with a documented project result (see pp_projects()).
// Shown as photo bars: each result fills its own bar, out of 100 per cent. [question, result, what, context, project slug, project, photo]
$questions = [
    ['Are people using what they learnt?', '72%', 'adopted sack or box gardening', 'Women in the programme, reported after training', 'womens-climate-resilience-yumbe', 'Women’s Climate Resilience in Yumbe',
        ['assets/img/impact/seedlings-woman.jpg', 'A woman in a programme T-shirt holding seedlings ready to plant in Yumbe', '50% 35%']],
    ['Are families growing more food?', '35%', 'average reduction in household spending on vegetables', 'Reported by participating households as home production improved', 'womens-climate-resilience-yumbe', 'Women’s Climate Resilience in Yumbe',
        ['assets/img/impact/cabbages-mulched.jpg', 'Cabbages growing under straw mulch in Yumbe', '45% 55%']],
    ['Are incomes becoming more stable?', '78%', 'moved into sustainable income pathways', 'Reported across SMILES target groups', 'smiles', 'SMILES',
        ['assets/img/programmes/yumbe-poultry-care.jpg', 'A young man refilling a drinker in a poultry house in Yumbe', '50% 30%']],
    ['Are communities better placed to face the next shock?', '40%', 'fall in food insecurity', 'Reported across SMILES target groups', 'smiles', 'SMILES',
        ['assets/img/programmes/yumbe-mother-baby.jpg', 'A mother laughing with her baby at a programme session in Yumbe', '48% 30%']],
];

// What change looks like, each line with a photograph
$changes = [
    ['Impact is a woman harvesting vegetables from a sack garden beside her home instead of buying everything at the market.', 'assets/img/programmes/yumbe-sack-garden.jpg', 'Cabbages growing in a tiered sack garden in Yumbe', '55% 50%'],
    ['It is a young refugee starting a poultry business and trading with the host community.', 'assets/img/programmes/yumbe-poultry-house.jpg', 'A woman standing in the doorway of her poultry house in Yumbe', '50% 56%'],
    ['It is a farmer checking market information before deciding where to sell.', 'assets/img/programmes/yumbe-phone-session.jpg', 'Women looking at a phone together during a session in Yumbe', '42% 45%'],
    ['It is a child eating a meal at school instead of learning on an empty stomach.', 'assets/img/impact/school-cup.jpg', 'A pupil holding his cup at mealtime in a classroom in Rukungiri', '50% 35%'],
    ['It is a household cooking with biogas instead of spending hours looking for firewood.', 'assets/img/impact/biogas-mixing.jpg', 'A young man mixing slurry at the inlet of a biogas digester beside a cattle shed in Rukungiri', '50% 40%'],
];

// Headline figures: an icon for each, and small dots for counts small enough to show one by one
$figIcon = function (string $label): string {
    foreach (['farmer' => 'basket', 'refugee' => 'globe', 'student' => 'users', 'librar' => 'book', 'biogas' => 'sun', 'borehole' => 'droplet', 'tree' => 'leaf'] as $k => $ico) {
        if (stripos($label, $k) !== false) return $ico;
    }
    return 'trending-up';
};
$figCount = fn(string $v): int => (int) preg_replace('/\D/', '', $v);
$tones = ['t-white', 't-green', 't-sand', 't-white', 't-blue', 't-sand', 't-deep'];

// Report files: local copies show their size; a year without a cover image gets a typographic one
$reportFile = function (array $r): array {
    $url = $r['file_url'];
    $size = '';
    if ($url && !preg_match('~^https?://~', $url) && is_file(__DIR__ . '/' . ltrim($url, '/'))) {
        $size = number_format(filesize(__DIR__ . '/' . ltrim($url, '/')) / 1048576, 1) . ' MB';
    }
    $cover = 'assets/img/reports/annual-report-' . preg_replace('/\D/', '', $r['year']) . '.jpg';
    return [asset_url($url), $size, is_file(__DIR__ . '/' . $cover) ? $cover : null];
};
$latest = $reports ? $reports[count($reports) - 1] : null;
$latestFile = $latest ? $reportFile($latest) : null;

$closeBg = 'assets/img/impact/cabbage-rows.jpg';
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css', 'assets/css/impact.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/impact.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg im" id="top">
  <?= ab_brush_defs() ?>

  <!-- 1. Opening: the question we ask, and four years of reach drawn as a curve over a field -->
  <section class="im-hero" aria-labelledby="imTitle">
    <div class="container">
      <div class="im-hero-grid">
        <div class="im-hero-copy ab-reveal">
          <nav class="im-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/programs.php">Our work</a><span aria-hidden="true">/</span><span aria-current="page">Impact and reports</span></nav>
          <span class="ab-eyebrow">Impact and reports</span>
          <h1 id="imTitle">We ask what <?= ab_mark('changed', 31) ?>, not only what was delivered.</h1>
        </div>
        <div class="im-hero-side ab-reveal">
          <p class="ab-lead">BetterLife tracks whether people are using what they learnt, whether families are growing more food, whether incomes are becoming more stable and whether communities are better placed to face the next shock.</p>
          <div class="im-actions">
            <a href="#imYears" class="pg-btn">Explore the reports <?= icon('arrow-right', 16) ?></a>
            <?php if ($latestFile): ?>
              <a href="<?= h($latestFile[0]) ?>" class="im-link" target="_blank" rel="noopener"><?= icon('file-text', 17) ?> Read the <?= h($latest['year']) ?> report<?= $latestFile[1] ? ' <small>(PDF, ' . h($latestFile[1]) . ')</small>' : '' ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <figure class="im-growth ab-reveal" aria-labelledby="imGrowthCap">
        <figcaption id="imGrowthCap"><strong>People reached each year</strong><span>From our annual reports</span></figcaption>
        <div class="im-plot">
          <div class="im-plot-box">
            <svg viewBox="0 0 1000 500" aria-hidden="true" focusable="false">
              <defs>
                <clipPath id="imArea"><path d="<?= $area ?>"/></clipPath>
                <linearGradient id="imAreaTint" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0b3d2e" stop-opacity=".05"/><stop offset="1" stop-color="#0b3d2e" stop-opacity=".4"/></linearGradient>
              </defs>
              <?php foreach ([0, 30000, 60000, 90000, 120000] as $g): $gy = $py1 - $g / $yMax * ($py1 - $py0); ?>
                <line class="im-grid" x1="70" x2="950" y1="<?= $gy ?>" y2="<?= $gy ?>"/>
                <text class="im-axis" x="62" y="<?= $gy + 5 ?>" text-anchor="end"><?= $g ? ($g / 1000) . 'k' : '0' ?></text>
              <?php endforeach; ?>
              <g clip-path="url(#imArea)" class="im-area">
                <image href="<?= h(asset_url('assets/img/about/rukungiri-communal-farm.jpg')) ?>" x="70" y="40" width="880" height="420" preserveAspectRatio="xMidYMid slice"/>
                <rect x="70" y="40" width="880" height="420" fill="url(#imAreaTint)"/>
              </g>
              <path class="im-line" d="<?= $line ?>" pathLength="1"/>
              <?php foreach ($pts as $y => [$x, $yy]): ?>
                <line class="im-drop" x1="<?= $x ?>" x2="<?= $x ?>" y1="<?= $yy ?>" y2="<?= $py1 ?>"/>
                <text class="im-year" x="<?= $x ?>" y="<?= $py1 + 34 ?>" text-anchor="middle"><?= h($y) ?></text>
              <?php endforeach; ?>
            </svg>
            <?php $k = 0; foreach ($pts as $y => [$x, $yy]): $d = $years[$y]; ?>
              <div class="im-pt<?= $y === $last ? ' is-last' : '' ?>" style="left: <?= round($x / 10, 2) ?>%; top: <?= round($yy / 5, 2) ?>%; --k: <?= $k++ ?>">
                <span class="im-pt-photo"><?= ab_img($d['photo'][0], $d['photo'][1], '', true, 'style="object-position: ' . h($d['photo'][2]) . '"', '96px') ?></span>
                <span class="im-pt-label"><strong><span class="ab-count" data-count="<?= $d['reach'] ?>"><?= number_format($d['reach']) ?></span></strong><span><?= h($d['mark']) ?></span></span>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if ($growth >= 2): ?>
            <div class="im-stamp" aria-hidden="true"><strong><?= $growth ?>&times;</strong><span>the reach of <?= h($first) ?></span></div>
          <?php endif; ?>
        </div>
        <ol class="im-plot-key">
          <?php foreach ($years as $y => $d): ?><li><span><?= h($y) ?></span><strong><?= number_format($d['reach']) ?></strong><em><?= h($d['mark']) ?></em></li><?php endforeach; ?>
        </ol>
        <p class="im-growth-note">Each year is counted on its own, so the years are not added together.</p>
      </figure>
    </div>
  </section>

  <!-- 2. Four things we track: each answer fills its own photo bar, out of 100 per cent -->
  <section class="im-questions" aria-labelledby="imQTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">How we measure change</span>
          <h2 id="imQTitle">What happens after the training ends?</h2>
        </div>
        <p class="ab-head-sub">We track four things. Here is what two of our programmes found when they asked.</p>
      </div>
      <ol class="im-qb-grid">
        <?php foreach ($questions as $n => [$ask, $num, $what, $ctx, $slug, $project, $photo]): $v = min(100, (int) $num) / 100; ?>
          <li class="im-qb ab-reveal" style="--v: <?= $v ?>; --i: <?= $n ?>">
            <div class="im-qb-head">
              <span class="im-qb-no"><?= sprintf('%02d', $n + 1) ?></span>
              <h3><?= h($ask) ?></h3>
            </div>
            <div class="im-qb-track">
              <span class="im-qb-fill"><?= ab_img($photo[0], $photo[1], '', true, 'style="object-position: ' . h($photo[2]) . '"', '(max-width: 720px) 50vw, 300px') ?></span>
              <strong class="im-qb-val"><?= h($num) ?></strong>
            </div>
            <p class="im-qb-what"><?= h($what) ?></p>
            <p class="im-qb-src"><?= h($ctx) ?>. <a href="<?= SITE_URL ?>/project.php?slug=<?= h($slug) ?>"><?= h($project) ?> <?= icon('arrow-right', 13) ?></a></p>
          </li>
        <?php endforeach; ?>
      </ol>
      <p class="im-qb-note">Each bar fills to its own result, out of 100 per cent.</p>
    </div>
  </section>

  <!-- 3. Headline figures -->
  <?php if ($stats): $lead = $stats[0]; $rest = array_slice($stats, 1); ?>
  <section class="im-reach" id="imReach" aria-labelledby="imReachTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Our reach</span>
          <h2 id="imReachTitle">The numbers behind the work</h2>
        </div>
        <p class="ab-head-sub">Each figure stands on its own. We show them side by side rather than adding them together.</p>
      </div>
      <ul class="im-figs">
        <li class="im-fig is-lead ab-reveal">
          <div class="im-fig-bg"><?= ab_img('assets/img/impact/crowd-yumbe.jpg', 'A crowd of women gathered outside a building on a programme day in Yumbe', '', true, 'style="object-position: 50% 42%"', '(max-width: 720px) 100vw, 640px') ?></div>
          <div class="im-fig-body">
            <strong><span class="ab-count" data-count="<?= $figCount($lead['value']) ?>"><?= h($lead['value']) ?></span></strong>
            <span class="im-fig-label"><?= h($lead['label']) ?></span>
          </div>
        </li>
        <?php foreach ($rest as $k => $s): $c = $figCount($s['value']); $tone = $tones[$k % count($tones)]; ?>
          <li class="im-fig <?= $tone ?> ab-reveal">
            <span class="im-fig-ico" aria-hidden="true"><?= icon($figIcon($s['label']), 20) ?></span>
            <div>
              <?php if ($c > 0 && $c <= 100): ?>
                <span class="im-dots" aria-hidden="true"><?php for ($d = 0; $d < $c; $d++): ?><i style="--d: <?= $d ?>"></i><?php endfor; ?></span>
              <?php endif; ?>
              <strong><span class="ab-count" data-count="<?= $c ?>"><?= h($s['value']) ?></span></strong>
              <span class="im-fig-label"><?= h($s['label']) ?></span>
            </div>
          </li>
        <?php endforeach; ?>
        <?php if ($latestFile): ?>
          <li class="im-fig is-source ab-reveal">
            <?php if ($latestFile[2]): ?><?= ab_img($latestFile[2], '', 'im-source-cover', true, '', '120px') ?><?php endif; ?>
            <div>
              <span class="im-fig-label">The full picture is in our <?= h($latest['year']) ?> annual report.</span>
              <a href="<?= h($latestFile[0]) ?>" target="_blank" rel="noopener" class="im-source-link">Read the report <?= icon('arrow-right', 14) ?></a>
            </div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </section>
  <?php endif; ?>

  <!-- 4. The annual reports: choose a cover to see that year -->
  <?php if ($reports): ?>
  <section class="im-years" id="imYears" aria-labelledby="imYearsTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Annual reports</span>
          <h2 id="imYearsTitle">Year by year, in our own reports</h2>
        </div>
        <p class="ab-head-sub">Choose a year to see what it held, then read the full report.</p>
      </div>
      <div class="im-years-grid">
        <div class="im-shelf-col ab-reveal">
          <div class="im-year-tabs" role="tablist" aria-label="Annual reports by year">
            <?php foreach ($reports as $r): $on = $r === $latest; ?>
              <button type="button" class="im-year-tab" role="tab" id="imTab<?= h($r['year']) ?>" aria-controls="imPanel<?= h($r['year']) ?>" aria-selected="<?= $on ? 'true' : 'false' ?>" tabindex="<?= $on ? '0' : '-1' ?>" data-key="<?= h($r['year']) ?>"><?= h($r['year']) ?></button>
            <?php endforeach; ?>
          </div>
          <!-- The covers, fanned on a shelf; selecting one is the same as choosing its year above (impact.js) -->
          <div class="im-shelf">
            <?php foreach ($reports as $n => $r): $f = $reportFile($r); $on = $r === $latest; ?>
              <div class="im-book<?= $on ? ' is-on' : '' ?>" data-key="<?= h($r['year']) ?>" style="--n: <?= $n ?>; --of: <?= count($reports) ?>">
                <?php if ($f[2]): ?>
                  <?= ab_img($f[2], 'Cover of the ' . $r['year'] . ' annual report', 'im-book-cover', true, '', '(max-width: 720px) 50vw, 280px') ?>
                <?php else: ?>
                  <span class="im-book-plain"><span>Annual report</span><strong><?= h($r['year']) ?></strong></span>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="im-panels">
          <?php foreach ($reports as $r): $f = $reportFile($r); $d = $years[$r['year']] ?? null; $on = $r === $latest; ?>
            <div class="im-panel<?= $on ? ' is-active' : '' ?>" id="imPanel<?= h($r['year']) ?>" role="tabpanel" aria-labelledby="imTab<?= h($r['year']) ?>" tabindex="0">
              <div class="im-panel-top">
                <span class="im-panel-year"><?= h($r['year']) ?></span>
                <?php if ($d): ?><p class="im-panel-reach"><strong><?= number_format($d['reach']) ?></strong> people reached</p><?php endif; ?>
              </div>
              <?php if ($d): ?>
                <p class="im-panel-line"><?= h($d['line']) ?></p>
                <ul class="im-notes">
                  <?php foreach ($d['notes'] as [$num, $text]): ?><li><strong><?= h($num) ?></strong><span><?= h($text) ?></span></li><?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <?php if ($r['file_url']): ?>
                <a href="<?= h($f[0]) ?>" class="pg-btn im-btn-light" target="_blank" rel="noopener"><?= icon('file-text', 17) ?> Read the <?= h($r['year']) ?> report<?php if ($f[1]): ?><span class="im-btn-meta">PDF, <?= h($f[1]) ?></span><?php endif; ?></a>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="im-all ab-reveal">
        <span class="im-all-label">All reports</span>
        <?php foreach (array_reverse($reports) as $r): $f = $reportFile($r); if (!$r['file_url']) continue; ?>
          <a href="<?= h($f[0]) ?>" target="_blank" rel="noopener" class="im-chip"><?= icon('file-text', 15) ?> <?= h($r['year']) ?><?= $f[1] ? '<small>' . h($f[1]) . '</small>' : '' ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- 5. What change looks like in a home, a market and a classroom -->
  <section class="im-change" aria-labelledby="imChangeTitle">
    <div class="container im-change-grid">
      <div class="im-change-head ab-reveal">
        <span class="ab-eyebrow">In everyday life</span>
        <h2 id="imChangeTitle">What change looks like</h2>
        <p class="im-change-coda">These changes may begin with one activity. Their value lies in what becomes possible afterwards.</p>
      </div>
      <ol class="im-change-list">
        <?php foreach ($changes as $n => [$line, $img, $alt, $pos]): ?>
          <li class="ab-reveal">
            <span class="im-change-num"><?= sprintf('%02d', $n + 1) ?></span>
            <p><?= h($line) ?></p>
            <div class="im-change-photo"><?= ab_img($img, $alt, '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 720px) 100vw, 200px') ?></div>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <!-- 6. Accountability -->
  <section class="pg-close im-close" aria-labelledby="imCloseTitle">
    <div class="pg-close-bg"><?= ab_img($closeBg, '', '', true, 'style="object-position: 50% 45%"', '100vw') ?></div>
    <div class="container">
      <div class="pg-close-inner ab-reveal">
        <span class="ab-eyebrow">Accountability</span>
        <h2 id="imCloseTitle">We publish what we did, what changed and where we still need to improve.</h2>
        <p>Our reports are written for communities, partners and the public alike. If you would like more detail on any result, ask us.</p>
      </div>
      <div class="pg-hero-actions ab-reveal" style="margin-top: 28px;">
        <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Programme enquiry') ?>" class="pg-btn">Ask about our results <?= icon('arrow-right', 16) ?></a>
        <a href="<?= SITE_URL ?>/partners.php" class="pg-btn pg-btn-ghost">Meet our partners</a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
