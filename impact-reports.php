<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'Impact & Reports';
$activePage = 'impact';
$pageDescription = 'What changed after the training ended: BetterLife’s reach year by year, the results behind it and our annual reports from 2022 to 2025.';

// Headline figures (Admin → Stats) and the annual reports (Admin → Impact & Reports)
$stats = $pdo->query("SELECT * FROM stats WHERE status = 1 ORDER BY sort_order")->fetchAll();
$reports = $pdo->query("SELECT * FROM reports WHERE status = 1 ORDER BY year, sort_order")->fetchAll();

// This page is laid out like a printed field report and uses photographs that appear nowhere else on the site (assets/img/impact/).

// Each year as its annual report tells it. Reach is counted per year: the years sit side by side and are never added together.
$years = [
    '2022' => [
        'reach' => 27400, 'pages' => 11,
        'mark'  => 'Green Libraries, Eco Labs and 20 Plastic Banks',
        'photo' => ['assets/img/impact/bottle-cap-wall.jpg', 'A young woman beside a wall decorated with recycled bottle caps', '50% 40%'],
        'line'  => 'Climate education, green learning spaces and recycling in schools and refugee-hosting communities.',
        'notes' => [
            ['7,000+', 'students in climate education across more than 20 rural primary schools'],
            ['5', 'new BetterLife Green Libraries, including in refugee settlements'],
            ['3', 'Green Eco Labs in South Sudan, where more than 3,000 people learnt practical skills'],
            ['20', 'Plastic Banks, with 25 local cooperatives formed around them'],
        ],
    ],
    '2023' => [
        'reach' => 37550, 'pages' => 11,
        'mark'  => 'BetterLife Agro Tourism Farm founded',
        'photo' => ['assets/img/impact/dairy-farm.jpg', 'A farm worker in overalls beside dairy cows in their shed', '55% 45%'],
        'line'  => 'A turn towards smart and sustainable agriculture, including the founding of the BetterLife Agro Tourism Farm.',
        'notes' => [
            ['48', 'biogas plants, bringing clean energy to more than 1,200 households'],
            ['10', 'boreholes, giving 3,500 people clean water in refugee-hosting communities in South Sudan'],
            ['1,500+', 'refugees trained in sustainable farming'],
            ['30', 'schools holding environmental debates, with more than 2,500 students'],
        ],
    ],
    '2024' => [
        'reach' => 68500, 'pages' => 11,
        'mark'  => 'Climate education in 120+ schools',
        'photo' => ['assets/img/impact/school-garden.jpg', 'Pupils harvesting greens in a school garden', '55% 40%'],
        'line'  => 'A year of consolidation and scale-up across Uganda, South Sudan, Tanzania and Ghana.',
        'notes' => [
            ['120+', 'schools reached with climate education'],
            ['9,600+', 'farmers and young people trained at climate-smart demonstration farms'],
            ['5,400+', 'young people and women in SMILES skills training and mentoring'],
            ['2,300+', 'young people in climate leadership and community action'],
        ],
    ],
    '2025' => [
        'reach' => 112430, 'pages' => 12,
        'mark'  => '18,900 farmers on Soilla',
        'photo' => ['assets/img/impact/greenhouse-tomatoes.jpg', 'A woman smiling among tomato plants in a greenhouse', '60% 35%'],
        'line'  => 'The year our work moved from programme expansion to regional scale, with women, young people and displaced families at the centre.',
        'notes' => [
            ['18,900', 'farmers using the Soilla platform, with more than 420,000 tailored advisory messages sent'],
            ['37%', 'higher crop yields and 28% lower input costs among farmers using Soilla'],
            ['41,200', 'refugees and host-community members reached'],
            ['60+', 'youth dialogues on climate action'],
        ],
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

// Four things we track after training, each answered with a documented project result (see pp_projects())
$questions = [
    ['Are people using what they learnt?', '72%', 'adopted sack or box gardening', 'Women in the programme, reported after training', 'womens-climate-resilience-yumbe', 'Women’s Climate Resilience in Yumbe'],
    ['Are families growing more food?', '35%', 'average reduction in household spending on vegetables', 'Reported by participating households as home production improved', 'womens-climate-resilience-yumbe', 'Women’s Climate Resilience in Yumbe'],
    ['Are incomes becoming more stable?', '78%', 'moved into sustainable income pathways', 'Reported across SMILES target groups', 'smiles', 'SMILES'],
    ['Are communities better placed to face the next shock?', '40%', 'fall in food insecurity', 'Reported across SMILES target groups', 'smiles', 'SMILES'],
];

// Field notes: what change looks like, one photograph to each line
$changes = [
    ['Impact is a woman harvesting vegetables from a sack garden beside her home instead of buying everything at the market.', 'assets/img/impact/sack-gardens.jpg', 'Women and a BetterLife trainer among tall sack gardens planted with greens', '50% 45%'],
    ['It is a young refugee starting a poultry business and trading with the host community.', 'assets/img/impact/poultry-drinker.jpg', 'A woman smiling as she holds up a poultry drinker in a wooden poultry house', '62% 40%'],
    ['It is a farmer checking market information before deciding where to sell.', 'assets/img/impact/hanging-scale.jpg', 'A hanging scale weighing a sack of produce', '50% 30%'],
    ['It is a child using a computer or opening a climate book for the first time.', 'assets/img/impact/boy-session-yumbe.jpg', 'A young boy in a green jumper at a community session in Yumbe', '42% 35%'],
    ['It is a household cooking with biogas instead of spending hours looking for firewood.', 'assets/img/impact/kitchen-stove.jpg', 'A woman cooking at a built stove in a kitchen', '55% 50%'],
];

// The numbers: dots for counts small enough to show one by one; two photographs set among the figures
$figCount = fn(string $v): int => (int) preg_replace('/\D/', '', $v);
$ledgerPhotos = [
    ['assets/img/impact/mushrooms.jpg', 'A young woman harvesting oyster mushrooms from their growing bags', '55% 40%'],
    ['assets/img/impact/women-session-yumbe.jpg', 'Women listening at a programme session in Yumbe', '50% 30%'],
];

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

// The crowd that fills the large figure on the cover (a lighter sized copy when one exists)
$crowd = 'assets/img/impact/crowd-yumbe.jpg';
$crowdV = ab_variants($crowd);
$crowdUrl = asset_url($crowdV[1600] ?? $crowdV[960] ?? $crowd);

$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css', 'assets/css/impact.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/impact.js'];
$pageHead = '<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,800;0,9..144,900;1,9..144,400;1,9..144,500;1,9..144,600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">'
    . '<link rel="preload" as="image" href="' . h($crowdUrl) . '">'
    . '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab im" id="top">

  <!-- Cover: the year's reach, filled with the people behind it -->
  <section class="im-cover" aria-labelledby="imTitle">
    <div class="container">
      <div class="im-masthead">
        <span>BetterLife International</span>
        <span>Impact and reports</span>
        <span>Four annual reports, <?= h($first) ?>–<?= h($last) ?></span>
      </div>
      <p class="im-big">
        <span class="im-big-n" style="background-image: url('<?= h($crowdUrl) ?>')"><?= number_format($years[$last]['reach']) ?></span>
        <span class="im-big-cap"><em>people reached in <?= h($last) ?></em><span>From our <?= h($last) ?> annual report</span></span>
      </p>
      <div class="im-cover-grid">
        <h1 id="imTitle">We ask what <em>changed</em>, not only what was delivered.</h1>
        <div class="im-cover-copy">
          <p>BetterLife tracks whether people are using what they learnt, whether families are growing more food, whether incomes are becoming more stable and whether communities are better placed to face the next shock.</p>
          <?php if ($latestFile): ?>
            <a href="<?= h($latestFile[0]) ?>" class="im-pill" target="_blank" rel="noopener"><?= icon('file-text', 17) ?> Read the <?= h($latest['year']) ?> report<?= $latestFile[1] ? ' <small>PDF, ' . h($latestFile[1]) . '</small>' : '' ?></a>
          <?php endif; ?>
        </div>
        <nav class="im-toc" aria-label="On this page">
          <span class="im-toc-title">In this report</span>
          <ol>
            <li><a href="#imCurve"><span>01</span> Four years of reach</a></li>
            <li><a href="#imAsk"><span>02</span> After the training</a></li>
            <li><a href="#imLedger"><span>03</span> The numbers</a></li>
            <li><a href="#imArchive"><span>04</span> The annual reports</a></li>
            <li><a href="#imNotes"><span>05</span> Field notes</a></li>
          </ol>
        </nav>
      </div>
    </div>
  </section>

  <!-- 01. The curve: people reached each year; the area under the line is a field being worked -->
  <section class="im-curve" id="imCurve" aria-labelledby="imCurveTitle">
    <div class="container">
      <header class="im-sechead ab-reveal">
        <span class="im-secno">01</span>
        <div>
          <h2 id="imCurveTitle">Four years, counted one year at a time</h2>
          <p>People reached each year, as our annual reports record them. In <?= h($last) ?> we reached <?= $growth ?> times as many people as in <?= h($first) ?>.</p>
        </div>
      </header>
      <figure class="im-plot ab-reveal">
        <div class="im-plot-box">
          <svg viewBox="0 0 1000 500" aria-hidden="true" focusable="false">
            <defs>
              <clipPath id="imArea"><path d="<?= $area ?>"/></clipPath>
              <linearGradient id="imAreaTint" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0b3d2e" stop-opacity=".05"/><stop offset="1" stop-color="#0b3d2e" stop-opacity=".45"/></linearGradient>
            </defs>
            <?php foreach ([0, 30000, 60000, 90000, 120000] as $g): $gy = $py1 - $g / $yMax * ($py1 - $py0); ?>
              <line class="im-grid" x1="70" x2="950" y1="<?= $gy ?>" y2="<?= $gy ?>"/>
              <text class="im-axis" x="62" y="<?= $gy + 5 ?>" text-anchor="end"><?= $g ? ($g / 1000) . 'k' : '0' ?></text>
            <?php endforeach; ?>
            <g clip-path="url(#imArea)" class="im-area">
              <image href="<?= h(asset_url('assets/img/impact/cabbage-field.jpg')) ?>" x="70" y="40" width="880" height="420" preserveAspectRatio="xMidYMid slice"/>
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
        <figcaption><span>Fig. 1</span> People reached each year, <?= h($first) ?> to <?= h($last) ?>. Each year is counted on its own, so the years are not added together.</figcaption>
      </figure>
      <ol class="im-plot-key">
        <?php foreach ($years as $y => $d): ?><li><span><?= h($y) ?></span><strong><?= number_format($d['reach']) ?></strong><em><?= h($d['mark']) ?></em></li><?php endforeach; ?>
      </ol>
    </div>
  </section>

  <!-- 02. After the training: four questions, four answers -->
  <section class="im-ask" id="imAsk" aria-labelledby="imAskTitle">
    <div class="container im-ask-grid">
      <figure class="im-ask-photo ab-reveal">
        <?= ab_img('assets/img/impact/mother-baby-yumbe.jpg', 'A mother holding her smiling baby at a programme session in Yumbe', '', true, 'style="object-position: 52% 45%"', '(max-width: 900px) 100vw, 460px') ?>
        <figcaption><span>Fig. 2</span> At a programme session in Yumbe</figcaption>
      </figure>
      <div>
        <header class="im-sechead ab-reveal">
          <span class="im-secno">02</span>
          <div>
            <h2 id="imAskTitle">What happens after the training ends?</h2>
            <p>We track four things. Here is what two of our programmes found when they asked.</p>
          </div>
        </header>
        <ol class="im-qa">
          <?php foreach ($questions as $q => [$ask, $num, $what, $ctx, $slug, $project]): ?>
            <li class="ab-reveal">
              <span class="im-qa-no">Q<?= $q + 1 ?></span>
              <h3><?= h($ask) ?></h3>
              <p class="im-qa-answer"><strong><?= h($num) ?></strong> <?= h($what) ?></p>
              <p class="im-qa-src"><?= h($ctx) ?> · <a href="<?= SITE_URL ?>/project.php?slug=<?= h($slug) ?>"><?= h($project) ?></a></p>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <!-- 03. The numbers, as a ledger -->
  <?php if ($stats): $lead = $stats[0]; $rest = array_slice($stats, 1);
    // Lead figure, a photograph, three figures, a photograph, the rest, then where the figures come from
    $cells = array_merge([['lead', $lead], ['photo', $ledgerPhotos[0]]], array_map(fn($s) => ['fig', $s], array_slice($rest, 0, 3)), [['photo', $ledgerPhotos[1]]], array_map(fn($s) => ['fig', $s], array_slice($rest, 3)), [['source', null]]);
    $no = 1; ?>
  <section class="im-ledger" id="imLedger" aria-labelledby="imLedgerTitle">
    <div class="container">
      <header class="im-sechead is-dark ab-reveal">
        <span class="im-secno">03</span>
        <div>
          <h2 id="imLedgerTitle">The numbers, each standing on its own</h2>
          <p>We show these figures side by side rather than adding them together.</p>
        </div>
      </header>
      <ul class="im-ledger-grid">
        <?php foreach ($cells as [$kind, $c]): ?>
          <?php if ($kind === 'photo'): ?>
            <li class="im-cell is-photo ab-reveal"><?= ab_img($c[0], $c[1], '', true, 'style="object-position: ' . h($c[2]) . '"', '(max-width: 720px) 50vw, 300px') ?></li>
          <?php elseif ($kind === 'source'): ?>
            <?php if ($latestFile): ?>
              <li class="im-cell is-source ab-reveal">
                <span class="im-cell-no">Source</span>
                <p>Figures from our <?= h($latest['year']) ?> annual report and programme records.</p>
                <a href="<?= h($latestFile[0]) ?>" target="_blank" rel="noopener">Read the <?= h($latest['year']) ?> report <?= icon('arrow-right', 14) ?></a>
              </li>
            <?php endif; ?>
          <?php else: $cnt = $figCount($c['value']); ?>
            <li class="im-cell<?= $kind === 'lead' ? ' is-lead' : '' ?> ab-reveal">
              <span class="im-cell-no"><?= sprintf('3.%d', $no++) ?></span>
              <strong><span class="ab-count" data-count="<?= $cnt ?>"><?= h($c['value']) ?></span></strong>
              <span class="im-cell-label"><?= h($c['label']) ?></span>
              <?php if ($kind !== 'lead' && $cnt > 0 && $cnt <= 100): ?>
                <span class="im-dots" aria-hidden="true"><?php for ($d = 0; $d < $cnt; $d++): ?><i style="--d: <?= $d ?>"></i><?php endfor; ?></span>
              <?php endif; ?>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
  <?php endif; ?>

  <!-- 04. The annual reports: choose a year, or a cover -->
  <?php if ($reports): ?>
  <section class="im-archive" id="imArchive" aria-labelledby="imArchiveTitle">
    <div class="container">
      <header class="im-sechead ab-reveal">
        <span class="im-secno">04</span>
        <div>
          <h2 id="imArchiveTitle">The annual reports</h2>
          <p>Choose a year to see what it held, then read the full report.</p>
        </div>
      </header>
      <div class="im-archive-grid">
        <div class="im-shelf-col ab-reveal">
          <div class="im-year-tabs" role="tablist" aria-label="Annual reports by year">
            <?php foreach ($reports as $r): $on = $r === $latest; ?>
              <button type="button" class="im-year-tab" role="tab" id="imTab<?= h($r['year']) ?>" aria-controls="imPanel<?= h($r['year']) ?>" aria-selected="<?= $on ? 'true' : 'false' ?>" tabindex="<?= $on ? '0' : '-1' ?>" data-key="<?= h($r['year']) ?>"><?= h($r['year']) ?></button>
            <?php endforeach; ?>
          </div>
          <!-- The covers, fanned on a shelf; choosing one is the same as choosing its year above (impact.js) -->
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
              <div class="im-panel-head">
                <span>Annual report</span>
                <span><?= $d ? (int) $d['pages'] . ' pages' : '' ?><?= $f[1] ? ' · PDF ' . h($f[1]) : '' ?></span>
              </div>
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
                <a href="<?= h($f[0]) ?>" class="im-pill" target="_blank" rel="noopener"><?= icon('file-text', 17) ?> Read the <?= h($r['year']) ?> report<?= $f[1] ? ' <small>PDF, ' . h($f[1]) . '</small>' : '' ?></a>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- 05. Field notes: the photograph follows the line being read -->
  <section class="im-essay" id="imNotes" aria-labelledby="imNotesTitle">
    <div class="container">
      <header class="im-sechead ab-reveal">
        <span class="im-secno">05</span>
        <div>
          <h2 id="imNotesTitle">Field notes: what change looks like</h2>
          <p>These changes may begin with one activity. Their value lies in what becomes possible afterwards.</p>
        </div>
      </header>
      <div class="im-essay-grid">
        <div class="im-frame" aria-hidden="true">
          <?php foreach ($changes as $c => [$line, $p, $alt, $pos]): ?>
            <div class="im-frame-img<?= $c === 0 ? ' is-on' : '' ?>"><?= ab_img($p, '', '', true, 'style="object-position: ' . h($pos) . '"', '(max-width: 900px) 100vw, 560px') ?></div>
          <?php endforeach; ?>
          <span class="im-frame-count"><span>01</span> / <?= sprintf('%02d', count($changes)) ?></span>
        </div>
        <ol class="im-steps">
          <?php foreach ($changes as $c => [$line, $p, $alt, $pos]): ?>
            <li class="im-step<?= $c === 0 ? ' is-on' : '' ?>">
              <figure class="im-step-photo"><?= ab_img($p, $alt, '', true, 'style="object-position: ' . h($pos) . '"', '100vw') ?></figure>
              <span class="im-step-no">Field note <?= sprintf('%02d', $c + 1) ?></span>
              <p><?= h($line) ?></p>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <!-- The end of the report -->
  <section class="im-end" aria-labelledby="imEndTitle">
    <div class="im-end-bg"><?= ab_img('assets/img/impact/terraced-fields.jpg', '', '', true, 'style="object-position: 50% 55%"', '100vw') ?></div>
    <div class="container">
      <div class="im-end-inner ab-reveal">
        <span class="im-end-kicker">Accountability</span>
        <h2 id="imEndTitle">We publish what we did, what changed and where we still need to improve.</h2>
        <p>Our reports are written for communities, partners and the public alike. If you would like more detail on any result, ask us.</p>
        <div class="im-end-actions">
          <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Programme enquiry') ?>" class="im-pill is-light">Ask about our results <?= icon('arrow-right', 16) ?></a>
          <a href="<?= SITE_URL ?>/partners.php" class="im-pill is-ghost">Meet our partners</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
