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
        'bar' => ['assets/img/programmes/rukungiri-pupils-writing.jpg', '50% 30%'],
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
        'bar' => ['assets/img/home/harvest-tomatoes.jpg', '50% 55%'],
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
        'bar' => ['assets/img/programmes/yumbe-poultry-house.jpg', '50% 35%'],
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
        'bar' => ['assets/img/programmes/yumbe-session-laughter.jpg', '55% 40%'],
    ],
];
$first = array_key_first($years); $last = array_key_last($years);
$peak = max(array_column($years, 'reach'));
$growth = (int) floor($years[$last]['reach'] / $years[$first]['reach']);

// Four things we track after training, each answered with a documented project result (see pp_projects())
$questions = [
    ['Are people using what they learnt?', '72%', 'adopted sack or box gardening', 'Women in the programme, reported after training', 'womens-climate-resilience-yumbe', 'Women’s Climate Resilience in Yumbe'],
    ['Are families growing more food?', '35%', 'average reduction in household spending on vegetables', 'Reported by participating households as home production improved', 'womens-climate-resilience-yumbe', 'Women’s Climate Resilience in Yumbe'],
    ['Are incomes becoming more stable?', '78%', 'moved into sustainable income pathways', 'Reported across SMILES target groups', 'smiles', 'SMILES'],
    ['Are communities better placed to face the next shock?', '40%', 'fall in food insecurity', 'Reported across SMILES target groups', 'smiles', 'SMILES'],
];

// What change looks like, each line with a photograph
$changes = [
    ['Impact is a woman harvesting vegetables from a sack garden beside her home instead of buying everything at the market.', 'assets/img/programmes/yumbe-sack-garden.jpg', 'A tiered sack garden planted with seedlings in Yumbe', '50% 50%'],
    ['It is a young refugee starting a poultry business and trading with the host community.', 'assets/img/programmes/yumbe-poultry-care.jpg', 'A young man refilling a drinker in a poultry house in Yumbe', '45% 20%'],
    ['It is a farmer checking market information before deciding where to sell.', 'assets/img/programmes/rukungiri-soilla-phone.jpg', 'A young farmer smiling as he shows the Soilla app on his phone in Rukungiri', '50% 30%'],
    ['It is a child using a computer or opening a climate book for the first time.', 'assets/img/programmes/rukungiri-boy-writing.jpg', 'A pupil writing at his desk in Rukungiri', '50% 40%'],
    ['It is a household cooking with biogas instead of spending hours looking for firewood.', 'assets/img/programmes/clean-cooking-cookoff.jpg', 'BetterLife team members with students at a clean cooking cook-off', '55% 40%'],
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

$closeBg = 'assets/img/about/rukungiri-community-field.jpg';
$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css', 'assets/css/impact.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/impact.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg im" id="top">
  <?= ab_brush_defs() ?>

  <!-- 1. Opening: the question we ask, and four years of reach drawn as photographs -->
  <section class="im-hero" aria-labelledby="imTitle">
    <div class="container im-hero-grid">
      <div class="im-hero-copy ab-reveal">
        <nav class="im-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/programs.php">Our work</a><span aria-hidden="true">/</span><span aria-current="page">Impact and reports</span></nav>
        <span class="ab-eyebrow">Impact and reports</span>
        <h1 id="imTitle">We ask what <?= ab_mark('changed', 31) ?>, not only what was delivered.</h1>
        <p class="ab-lead">BetterLife tracks whether people are using what they learnt, whether families are growing more food, whether incomes are becoming more stable and whether communities are better placed to face the next shock.</p>
        <div class="im-actions">
          <a href="#imYears" class="pg-btn">Explore the reports <?= icon('arrow-right', 16) ?></a>
          <?php if ($latestFile): ?>
            <a href="<?= h($latestFile[0]) ?>" class="im-link" target="_blank" rel="noopener"><?= icon('file-text', 17) ?> Read the <?= h($latest['year']) ?> report<?= $latestFile[1] ? ' <small>(PDF, ' . h($latestFile[1]) . ')</small>' : '' ?></a>
          <?php endif; ?>
        </div>
      </div>

      <figure class="im-growth ab-reveal" aria-labelledby="imGrowthCap">
        <figcaption id="imGrowthCap"><strong>People reached each year</strong><span>From our annual reports</span></figcaption>
        <div class="im-plot">
        <ol class="im-bars">
          <?php $i = 0; foreach ($years as $y => $d): ?>
            <li class="<?= $y === $last ? 'is-now' : '' ?>" style="--k: <?= round($d['reach'] / $peak, 3) ?>; --i: <?= $i++ ?>">
              <span class="im-bar-num"><span class="ab-count" data-count="<?= $d['reach'] ?>"><?= number_format($d['reach']) ?></span></span>
              <span class="im-bar"><?= ab_img($d['bar'][0], '', '', true, 'style="object-position: ' . h($d['bar'][1]) . '"', '(max-width: 720px) 25vw, 140px') ?></span>
              <span class="im-bar-year"><?= h($y) ?></span>
            </li>
          <?php endforeach; ?>
        </ol>
        <?php if ($growth >= 2): ?>
          <div class="im-stamp" aria-hidden="true"><strong><?= $growth ?>&times;</strong><span>the reach of <?= h($first) ?></span></div>
        <?php endif; ?>
        </div>
        <p class="im-growth-note">Each year is counted on its own, so the years are not added together.</p>
      </figure>
    </div>
  </section>

  <!-- 2. Four things we track, each answered with a documented result -->
  <section class="im-questions" aria-labelledby="imQTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">How we measure change</span>
          <h2 id="imQTitle">What happens after the training ends?</h2>
        </div>
        <p class="ab-head-sub">We track four things. Here is what two of our programmes found when they asked.</p>
      </div>
      <ol class="im-q-grid">
        <?php foreach ($questions as $n => [$ask, $num, $what, $ctx, $slug, $project]): ?>
          <li class="im-q ab-reveal">
            <div class="im-q-ask">
              <span class="im-q-num"><?= sprintf('%02d', $n + 1) ?></span>
              <h3><?= h($ask) ?></h3>
            </div>
            <div class="im-q-found">
              <span class="im-q-label">What we found</span>
              <strong><?= h($num) ?></strong>
              <p><?= h($what) ?></p>
              <p class="im-q-ctx"><?= h($ctx) ?></p>
              <a href="<?= SITE_URL ?>/project.php?slug=<?= h($slug) ?>"><?= h($project) ?> <?= icon('arrow-right', 14) ?></a>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
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
          <div class="im-fig-bg"><?= ab_img('assets/img/about/yumbe-women-celebrating.jpg', 'Programme participants and BetterLife staff celebrating together under a tree in Yumbe', '', true, 'style="object-position: 50% 40%"', '(max-width: 720px) 100vw, 640px') ?></div>
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
