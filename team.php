<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'Our Team';
$activePage = 'team';
$pageDescription = 'Meet the leadership, country teams, programme team and board of directors behind BetterLife International, working across five African countries.';

// Everyone comes from Admin → Team; a photo added there appears here automatically
$all = $pdo->query("SELECT * FROM team_members WHERE status = 1 ORDER BY sort_order, id")->fetchAll();
// A placeholder bio counts as no bio, so no empty "Read bio" appears
$bioOf = fn(array $m): string => preg_match('/^\s*bio coming soon/i', (string) $m['bio']) ? '' : trim((string) $m['bio']);
$countryOf = fn(array $m): ?string => preg_match('/^Country Manager,\s*(.+)$/i', $m['role'], $r) ? trim($r[1]) : null;

$leadership = array_values(array_filter($all, fn($m) => $m['category'] === 'leadership'));
$managers   = array_values(array_filter($all, fn($m) => $m['category'] === 'staff' && $countryOf($m)));
$programme  = array_values(array_filter($all, fn($m) => $m['category'] === 'staff' && !$countryOf($m)));
$board      = array_values(array_filter($all, fn($m) => $m['category'] === 'board'));
$volunteers = array_values(array_filter($all, fn($m) => $m['category'] === 'volunteer'));

// Opening sentences of a bio, whole sentences only
$lede = function (string $bio, int $max): string {
    $out = '';
    foreach (preg_split('/(?<=[.!?])\s+/u', $bio) as $s) {
        if ($out !== '' && mb_strlen($out . ' ' . $s) > $max) break;
        $out = trim($out . ' ' . $s);
    }
    return $out;
};

// Portrait: the photo, or until one is added, the person's initials on one of four brand colours
$initials = function (string $name): string {
    $w = preg_split('/\s+/u', trim($name));
    return mb_strtoupper(mb_substr($w[0], 0, 1) . (count($w) > 1 ? mb_substr(end($w), 0, 1) : ''));
};
$hasPhoto = fn(array $m): bool => !empty($m['photo']) && is_file(__DIR__ . '/' . $m['photo']);
$face = function (array $m, string $sizes) use ($initials, $hasPhoto) {
    if ($hasPhoto($m)) return ab_img($m['photo'], $m['name'], 'tm-photo', true, '', $sizes);
    return '<span class="tm-mono tone-' . ((int) $m['id'] % 4) . '" aria-hidden="true"><span>' . h($initials($m['name'])) . '</span></span>';
};

// Bios for the reading panel, in the order they appear on the page
$bios = [];
foreach (array_merge($leadership, $managers, $programme, $board, $volunteers) as $m) {
    if ($bioOf($m) === '') continue;
    $bios[] = ['id' => (int) $m['id'], 'name' => $m['name'], 'role' => $m['role'], 'bio' => $bioOf($m),
        'photo' => $hasPhoto($m) ? asset_url($m['photo']) : null, 'ini' => $initials($m['name']), 'tone' => (int) $m['id'] % 4];
}
$canOpen = array_flip(array_column($bios, 'id'));
$more = fn(array $m, string $label = 'Read bio') => isset($canOpen[(int) $m['id']])
    ? '<button type="button" class="tm-more" data-member="' . (int) $m['id'] . '" aria-haspopup="dialog" aria-label="' . h($label . ': ' . $m['name']) . '">' . h($label) . ' ' . icon('arrow-right', 15) . '</button>'
    : '';

// Country teams on the map: where each country sits, and where its card is pinned (map units, see includes/map-paths.php)
$map = require __DIR__ . '/includes/map-paths.php';
$mapKeys = ['Uganda' => 'Uganda', 'South Sudan' => 'South Sudan', 'Tanzania' => 'Tanzania', 'Ghana' => 'Ghana', 'DR Congo' => 'Democratic Republic of the Congo', 'Democratic Republic of Congo' => 'Democratic Republic of the Congo'];
$pinAt = ['Ghana' => [96, 452, 'right'], 'Democratic Republic of the Congo' => [232, 566, 'left'], 'South Sudan' => [612, 214, 'right'], 'Uganda' => [640, 334, 'right'], 'Tanzania' => [612, 474, 'right']];
$vb = [20, 45, 790, 640];
// On narrower screens the map is framed on Africa alone and the managers sit on it as portraits, close to their countries
$pinAtNarrow = ['Ghana' => [118, 214], 'Democratic Republic of the Congo' => [238, 528], 'South Sudan' => [432, 168], 'Uganda' => [540, 292], 'Tanzania' => [528, 500]];
$vbNarrow = [30, 43, 572, 640];
$pctNarrow = fn(float $x, float $y): string => 'left: ' . round(($x - $vbNarrow[0]) / $vbNarrow[2] * 100, 2) . '%; top: ' . round(($y - $vbNarrow[1]) / $vbNarrow[3] * 100, 2) . '%';
$pct = fn(float $x, float $y): string => 'left: ' . round(($x - $vb[0]) / $vb[2] * 100, 2) . '%; top: ' . round(($y - $vb[1]) / $vb[3] * 100, 2) . '%';
$pins = [['key' => 'Uganda', 'member' => null]];
foreach ($managers as $m) {
    $key = $mapKeys[$countryOf($m)] ?? null;
    if ($key && isset($map['countries'][$key])) $pins[] = ['key' => $key, 'member' => $m];
}

// Who the work is for: participants photographed in Yumbe (consent confirmed)
$community = [];
for ($i = 1; $i <= 8; $i++) $community[] = 'assets/img/about/yumbe-portrait-' . $i . '.jpg';

$teamCount = count($leadership) + count($managers) + count($programme);
$countryCount = count(array_unique(array_column($pins, 'key')));

$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css', 'assets/css/team.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/team.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab tm" id="top">
  <?= ab_brush_defs() ?>

  <!-- 1. Opening -->
  <section class="tm-hero" aria-labelledby="tmTitle">
    <div class="container tm-hero-grid">
      <div class="tm-hero-copy">
        <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">Our Team</span></nav>
        <span class="ab-eyebrow">Our team</span>
        <h1 id="tmTitle">People who know the work and the <?= ab_mark('places', 31) ?> where it happens.</h1>
        <p class="ab-lead">BetterLife is led by an African team working across community development, agriculture, law, climate action, finance, monitoring, communications and youth leadership.</p>
        <p>Our country and programme teams bring professional knowledge together with a close understanding of the communities where we work. Our board provides oversight, experience and accountability as the organisation grows.</p>
        <ul class="tm-facts">
          <li><strong><?= $teamCount ?></strong><span>people on the team</span></li>
          <li><strong><?= $countryCount ?></strong><span>countries</span></li>
          <?php if ($board): ?><li><strong><?= count($board) ?></strong><span>board members</span></li><?php endif; ?>
        </ul>
        <nav class="tm-jump" aria-label="On this page">
          <?php if ($leadership): ?><a href="#leadership">Leadership</a><?php endif; ?>
          <?php if ($managers): ?><a href="#countries">Country teams</a><?php endif; ?>
          <?php if ($programme): ?><a href="#programme">Programme team</a><?php endif; ?>
          <?php if ($board): ?><a href="#board">Board</a><?php endif; ?>
        </nav>
      </div>
      <!-- Three photographs of the team together in the field -->
      <figure class="tm-hero-photo">
        <?= ab_img('assets/img/team/team-waving.jpg', 'Eight BetterLife team members in programme vests, smiling and waving in a garden', 'tm-hero-main', false, '', '(max-width: 900px) 92vw, 560px') ?>
        <?= ab_img('assets/img/team/team-laughing.jpg', 'Four BetterLife team members laughing with their arms around each other', 'tm-hero-a', true, '', '(max-width: 900px) 52vw, 320px') ?>
        <?= ab_img('assets/img/team/edwin-welcome.jpg', 'Edwin Namakanga, our Outreach Coordinator, greeting with open arms beside thatched homes', 'tm-hero-b', true, '', '(max-width: 900px) 34vw, 200px') ?>
        <figcaption>The BetterLife team in the field</figcaption>
      </figure>
    </div>
  </section>

  <?php if ($leadership): ?>
  <!-- 2. Leadership: the founder first, then the rest of the executive team -->
  <section class="tm-lead" id="leadership" aria-labelledby="tmLeadTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">Who leads</span>
        <h2 id="tmLeadTitle">Executive leadership</h2>
      </div>
      <div class="tm-lead-grid">
        <?php foreach ($leadership as $i => $m): $bio = $bioOf($m); ?>
          <article class="tm-lead-card<?= $i === 0 ? ' is-first' : '' ?> ab-reveal" style="--i: <?= $i ?>">
            <div class="tm-lead-photo"><?= $face($m, $i === 0 ? '(max-width: 900px) 90vw, 520px' : '(max-width: 900px) 40vw, 240px') ?></div>
            <div class="tm-lead-body">
              <span class="tm-role"><?= h($m['role']) ?></span>
              <h3><?= h($m['name']) ?></h3>
              <?php if ($bio): ?><p><?= h($i === 0 ? $lede($bio, 330) : $lede($bio, 170)) ?></p><?php endif; ?>
              <?= mb_strlen($bio) > 170 || ($i === 0 && $bio) ? $more($m, 'Read full bio') : '' ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($managers): ?>
  <!-- 3. Country teams: each country manager pinned to the country they lead -->
  <section class="tm-where" id="countries" aria-labelledby="tmWhereTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Country teams</span>
          <h2 id="tmWhereTitle">Led locally, in <?= $countryCount === 5 ? 'five' : $countryCount ?> countries</h2>
        </div>
        <p class="ab-head-sub">Country managers lead programmes, partnerships and community relationships in their countries, so solutions are shaped by the people closest to the challenges.</p>
      </div>
      <div class="tm-map ab-reveal">
        <p class="tm-map-hint" aria-hidden="true">Tap a portrait to read their bio</p>
        <div class="tm-map-art">
        <svg class="tm-map-svg" viewBox="<?= implode(' ', $vb) ?>" aria-hidden="true" focusable="false">
          <path class="tm-land" d="<?= $map['africa'] ?>"/>
          <?php foreach ($pins as $p): ?>
            <path class="tm-country" d="<?= $map['countries'][$p['key']]['d'] ?>" data-key="<?= h($p['key']) ?>"/>
          <?php endforeach; ?>
          <?php foreach ($pins as $p): [$cx, $cy] = $map['countries'][$p['key']]['c']; [$ax, $ay] = $pinAt[$p['key']]; ?>
            <g class="tm-leader" data-key="<?= h($p['key']) ?>">
              <path d="M<?= $cx ?>,<?= $cy ?> L<?= $ax ?>,<?= $ay ?>"/>
              <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="4.5"/>
            </g>
          <?php endforeach; ?>
          <?php foreach ($pins as $p): [$cx, $cy] = $map['countries'][$p['key']]['c']; [$ax, $ay] = $pinAtNarrow[$p['key']]; ?>
            <g class="tm-leader is-narrow" data-key="<?= h($p['key']) ?>">
              <path d="M<?= $cx ?>,<?= $cy ?> L<?= $ax ?>,<?= $ay ?>"/>
              <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="5"/>
            </g>
          <?php endforeach; ?>
        </svg>
        <!-- Narrower screens: the same people as portraits on the map (the full cards are listed beneath it) -->
        <div class="tm-map-faces" aria-hidden="true">
          <?php foreach ($pins as $p): [$ax, $ay] = $pinAtNarrow[$p['key']]; $m = $p['member']; ?>
            <span class="tm-mface<?= $m ? '' : ' is-home' ?>" data-key="<?= h($p['key']) ?>"<?= $m && isset($canOpen[(int) $m['id']]) ? ' data-member="' . (int) $m['id'] . '"' : '' ?> style="<?= $pctNarrow($ax, $ay) ?>">
              <span class="tm-mface-img"><?= $m ? $face($m, '64px') : '<span class="tm-pin-mark">' . icon('leaf', 20) . '</span>' ?></span>
              <span class="tm-mface-label"><?= h($m ? $countryOf($m) : 'Uganda') ?></span>
            </span>
          <?php endforeach; ?>
        </div>
        </div>
        <ul class="tm-pins" aria-label="Country managers">
          <?php foreach ($pins as $p): [$ax, $ay, $side] = $pinAt[$p['key']]; $m = $p['member']; ?>
            <li class="tm-pin is-<?= $side ?><?= $m ? '' : ' is-home' ?>" data-key="<?= h($p['key']) ?>" style="<?= $pct($ax, $ay) ?>">
              <?php if ($m): ?>
                <span class="tm-pin-face"<?= $m && isset($canOpen[(int) $m['id']]) ? ' data-member="' . (int) $m['id'] . '"' : '' ?>><?= $face($m, '96px') ?></span>
                <span class="tm-pin-text">
                  <strong><?= h($m['name']) ?></strong>
                  <small><?= h($countryOf($m)) ?></small>
                  <?= $more($m) ?>
                </span>
              <?php else: ?>
                <span class="tm-pin-face"><span class="tm-pin-mark"><?= icon('leaf', 26) ?></span></span>
                <span class="tm-pin-text"><strong>Uganda</strong><small>Where BetterLife began</small></span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($programme): ?>
  <!-- 4. Programme and operations team -->
  <section class="tm-people" id="programme" aria-labelledby="tmProgTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Programme team</span>
          <h2 id="tmProgTitle">Programmes, operations and communications</h2>
        </div>
        <p class="ab-head-sub">The people who coordinate the work, track what changes, keep the organisation running and carry community voices to a wider audience.</p>
      </div>
      <ul class="tm-grid">
        <?php foreach ($programme as $i => $m): ?>
          <li class="tm-card ab-reveal" style="--i: <?= $i ?>">
            <div class="tm-card-photo"><?= $face($m, '(max-width: 720px) 45vw, 240px') ?></div>
            <h3><?= h($m['name']) ?></h3>
            <span class="tm-role"><?= h($m['role']) ?></span>
            <?= $more($m) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($board): ?>
  <!-- 5. Board of directors -->
  <section class="tm-board" id="board" aria-labelledby="tmBoardTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Oversight</span>
          <h2 id="tmBoardTitle">Board of directors</h2>
        </div>
        <p class="ab-head-sub">Our board provides oversight, experience and accountability as the organisation grows.</p>
      </div>
      <ul class="tm-roll">
        <?php foreach ($board as $i => $m): $bio = $bioOf($m); ?>
          <li class="tm-roll-row ab-reveal" style="--i: <?= $i ?>">
            <span class="tm-roll-face"><?= $face($m, '120px') ?></span>
            <div class="tm-roll-name">
              <h3><?= h($m['name']) ?></h3>
              <span class="tm-role"><?= h($m['role']) ?></span>
            </div>
            <?php if ($bio): ?>
              <p class="tm-roll-bio"><?= h($lede($bio, 190)) ?></p>
              <?php if (mb_strlen($bio) > mb_strlen($lede($bio, 190))): ?><div class="tm-roll-more"><?= $more($m) ?></div><?php endif; ?>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($volunteers): ?>
  <!-- Volunteers and community champions (shown once any are added in Admin → Team) -->
  <section class="tm-people tm-volunteers" aria-labelledby="tmVolTitle">
    <div class="container">
      <div class="ab-head ab-reveal">
        <span class="ab-eyebrow">In the community</span>
        <h2 id="tmVolTitle">Volunteers and community champions</h2>
      </div>
      <ul class="tm-grid">
        <?php foreach ($volunteers as $i => $m): ?>
          <li class="tm-card ab-reveal" style="--i: <?= $i ?>">
            <div class="tm-card-photo"><?= $face($m, '(max-width: 720px) 45vw, 240px') ?></div>
            <h3><?= h($m['name']) ?></h3>
            <span class="tm-role"><?= h($m['role']) ?></span>
            <?= $more($m) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
  <?php endif; ?>

  <!-- 6. Who the work is for, and an invitation to join -->
  <section class="tm-close" aria-labelledby="tmCloseTitle">
    <div class="ab-glide tm-faces" role="group" aria-label="Women taking part in our programme in Yumbe">
      <div class="ab-glide-row" data-glide="1">
        <?php foreach ($community as $p): ?>
          <span class="ab-glide-item tm-face" style="--ar: .75"><?= ab_img($p, 'A woman taking part in our sustainable agriculture programme in Yumbe', '', true, '', '(max-width: 720px) 40vw, 260px') ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="container tm-close-inner ab-reveal">
      <span class="ab-eyebrow">Who the work is for</span>
      <h2 id="tmCloseTitle">Behind every role here are the women, refugees, young people and farmers we work alongside.</h2>
      <p>Want to join the BetterLife team? We are always looking for passionate people to work with us.</p>
      <div class="tm-close-actions">
        <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('General enquiry') ?>" class="pg-btn">Get in touch <?= icon('arrow-right', 16) ?></a>
        <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Volunteer enquiry') ?>" class="pg-btn pg-btn-ghost">Volunteer with us</a>
      </div>
    </div>
  </section>
</main>

<!-- Reading panel for full bios (team.js); moves between people with the arrows or the keyboard -->
<dialog class="ab tm-dialog" id="tmDialog" aria-labelledby="tmDialogName">
  <div class="tm-dialog-card">
    <button type="button" class="tm-dialog-close" aria-label="Close"><?= icon('x', 20) ?></button>
    <div class="tm-dialog-photo" aria-hidden="true"></div>
    <div class="tm-dialog-body">
      <span class="tm-role" id="tmDialogRole"></span>
      <h2 id="tmDialogName"></h2>
      <p id="tmDialogBio"></p>
      <div class="tm-dialog-nav">
        <button type="button" data-step="-1"><?= icon('arrow-right', 15) ?> <span>Previous</span></button>
        <span class="tm-dialog-count" aria-live="polite"></span>
        <button type="button" data-step="1"><span>Next</span> <?= icon('arrow-right', 15) ?></button>
      </div>
    </div>
  </div>
</dialog>
<script>window.__teamBios = <?= json_encode($bios, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
