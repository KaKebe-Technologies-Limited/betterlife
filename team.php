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

// Country teams on the map: where each country sits, and where each card is pinned (map units, see includes/map-paths.php).
// The Regional Director is pinned too, linked to every country; Uganda, where BetterLife began, is marked last.
$map = require __DIR__ . '/includes/map-paths.php';
$mapKeys = ['Uganda' => 'Uganda', 'South Sudan' => 'South Sudan', 'Tanzania' => 'Tanzania', 'Ghana' => 'Ghana', 'DR Congo' => 'Democratic Republic of the Congo', 'Democratic Republic of Congo' => 'Democratic Republic of the Congo'];
$vb = [20, 45, 790, 640];
// On narrower screens the map is framed on Africa alone and the people sit on it as portraits, close to their countries
$vbNarrow = [30, 43, 572, 640];
$pct = fn(float $x, float $y): string => 'left: ' . round(($x - $vb[0]) / $vb[2] * 100, 2) . '%; top: ' . round(($y - $vb[1]) / $vb[3] * 100, 2) . '%';
$pctNarrow = fn(float $x, float $y): string => 'left: ' . round(($x - $vbNarrow[0]) / $vbNarrow[2] * 100, 2) . '%; top: ' . round(($y - $vbNarrow[1]) / $vbNarrow[3] * 100, 2) . '%';
// [wide: x, y, side the text sits], [narrow: x, y]
$pinPlaces = [
    'regional'                         => [[612, 104, 'right'], [548, 104]],
    'Ghana'                            => [[96, 452, 'right'], [118, 214]],
    'Democratic Republic of the Congo' => [[232, 566, 'left'], [238, 528]],
    'South Sudan'                      => [[612, 214, 'right'], [432, 168]],
    'Uganda'                           => [[640, 334, 'right'], [540, 292]],
    'Tanzania'                         => [[612, 474, 'right'], [528, 500]],
];
$regional = array_values(array_filter($leadership, fn($m) => preg_match('/^Regional\b/i', $m['role'])));
$pins = [];
foreach ($regional as $m) $pins[] = ['key' => 'regional', 'member' => $m, 'label' => $m['role'], 'short' => 'Regional'];
foreach ($managers as $m) {
    $key = $mapKeys[$countryOf($m)] ?? null;
    if ($key && isset($map['countries'][$key])) $pins[] = ['key' => $key, 'member' => $m, 'label' => $countryOf($m), 'short' => $countryOf($m)];
}
$pins[] = ['key' => 'Uganda', 'member' => null, 'label' => 'Where BetterLife began', 'short' => 'Uganda'];
$countryKeys = array_values(array_unique(array_filter(array_column($pins, 'key'), fn($k) => $k !== 'regional')));

// Who the work is for: participants photographed in Yumbe (consent confirmed)
$community = [];
for ($i = 1; $i <= 8; $i++) $community[] = 'assets/img/about/yumbe-portrait-' . $i . '.jpg';

$teamCount = count($leadership) + count($managers) + count($programme);
$countryCount = count($countryKeys);

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
  <!-- 3. Country teams: the Regional Director, linked to every country, and each country manager pinned to their country -->
  <section class="tm-where" id="countries" aria-labelledby="tmWhereTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Country teams</span>
          <h2 id="tmWhereTitle">Led locally, in <?= $countryCount === 5 ? 'five' : $countryCount ?> countries</h2>
        </div>
        <p class="ab-head-sub">Our Regional Director works across all five countries, and country managers lead programmes, partnerships and community relationships in their own, so solutions are shaped by the people closest to the challenges.</p>
      </div>
      <div class="tm-map ab-reveal">
        <p class="tm-map-hint" aria-hidden="true">Tap a portrait to read their bio</p>
        <div class="tm-map-art">
        <svg class="tm-map-svg" viewBox="<?= implode(' ', $vb) ?>" aria-hidden="true" focusable="false">
          <path class="tm-land" d="<?= $map['africa'] ?>"/>
          <?php foreach ($countryKeys as $key): ?>
            <path class="tm-country" d="<?= $map['countries'][$key]['d'] ?>" data-key="<?= h($key) ?>"/>
          <?php endforeach; ?>
          <?php foreach ([0, 1] as $at): ?>
            <?php foreach ($pins as $p): [$ax, $ay] = $pinPlaces[$p['key']][$at]; ?>
              <g class="tm-leader<?= $p['key'] === 'regional' ? ' is-regional' : '' ?><?= $at ? ' is-narrow' : '' ?>" data-key="<?= h($p['key']) ?>">
                <?php foreach ($p['key'] === 'regional' ? $countryKeys : [$p['key']] as $key): [$cx, $cy] = $map['countries'][$key]['c']; ?>
                  <path d="M<?= $cx ?>,<?= $cy ?> L<?= $ax ?>,<?= $ay ?>"/>
                  <?php if ($p['key'] !== 'regional'): ?><circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $at ? 5 : 4.5 ?>"/><?php endif; ?>
                <?php endforeach; ?>
              </g>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </svg>
        <!-- Narrower screens: the same people as portraits on the map (the full cards are listed beneath it) -->
        <div class="tm-map-faces" aria-hidden="true">
          <?php foreach ($pins as $p): [$ax, $ay] = $pinPlaces[$p['key']][1]; $m = $p['member']; ?>
            <span class="tm-mface<?= $m ? '' : ' is-home' ?><?= $p['key'] === 'regional' ? ' is-regional' : '' ?>" data-key="<?= h($p['key']) ?>"<?= $m && isset($canOpen[(int) $m['id']]) ? ' data-member="' . (int) $m['id'] . '"' : '' ?> style="<?= $pctNarrow($ax, $ay) ?>">
              <span class="tm-mface-img"><?= $m ? $face($m, '64px') : '<span class="tm-pin-mark">' . icon('leaf', 20) . '</span>' ?></span>
              <span class="tm-mface-label"><?= h($p['short']) ?></span>
            </span>
          <?php endforeach; ?>
        </div>
        </div>
        <ul class="tm-pins" aria-label="Country teams">
          <?php foreach ($pins as $p): [$ax, $ay, $side] = $pinPlaces[$p['key']][0]; $m = $p['member']; ?>
            <li class="tm-pin is-<?= $side ?><?= $m ? '' : ' is-home' ?><?= $p['key'] === 'regional' ? ' is-regional' : '' ?>" data-key="<?= h($p['key']) ?>" style="<?= $pct($ax, $ay) ?>">
              <?php if ($m): ?>
                <span class="tm-pin-face"<?= isset($canOpen[(int) $m['id']]) ? ' data-member="' . (int) $m['id'] . '"' : '' ?>><?= $face($m, '96px') ?></span>
                <span class="tm-pin-text">
                  <strong><?= h($m['name']) ?></strong>
                  <small><?= h($p['label']) ?></small>
                  <?= $more($m) ?>
                </span>
              <?php else: ?>
                <span class="tm-pin-face"><span class="tm-pin-mark"><?= icon('leaf', 26) ?></span></span>
                <span class="tm-pin-text"><strong>Uganda</strong><small><?= h($p['label']) ?></small></span>
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

  <?php if ($board): $seats = count($board); ?>
  <!-- 5. Board of directors: seated around a table; choose a seat (or watch them turn) to read about each member -->
  <section class="tm-board" id="board" aria-labelledby="tmBoardTitle">
    <div class="container">
      <div class="ab-head ab-head-split ab-reveal">
        <div>
          <span class="ab-eyebrow">Oversight</span>
          <h2 id="tmBoardTitle">Board of directors</h2>
        </div>
        <p class="ab-head-sub">Our board provides oversight, experience and accountability as the organisation grows.</p>
      </div>
      <div class="tm-council ab-reveal">
        <div class="tm-council-table" aria-hidden="true"></div>
        <div class="tm-seats" role="tablist" aria-label="Board members">
          <?php foreach ($board as $k => $m):
            // Seats on an arc over the table: 41% of the width out from its centre, which sits at the foot of a 2:1.1 stage
            $a = M_PI + M_PI * ($k + 0.5) / $seats; $x = round(50 + 41 * cos($a), 2); $y = round(96 + 41 / 0.55 * sin($a), 2); ?>
            <button type="button" role="tab" class="tm-seat" id="tmSeat-<?= (int) $m['id'] ?>" aria-controls="tmSeatPanel-<?= (int) $m['id'] ?>" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>" tabindex="<?= $k === 0 ? '0' : '-1' ?>" data-key="<?= (int) $m['id'] ?>" style="--x: <?= $x ?>%; --y: <?= $y ?>%">
              <span class="tm-seat-face"><?= $face($m, '120px') ?></span>
              <span class="tm-seat-name"><?= h($m['name']) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <div class="tm-seat-panels">
          <?php foreach ($board as $k => $m): $bio = $bioOf($m); ?>
            <div class="tm-seat-panel<?= $k === 0 ? ' is-active' : '' ?>" role="tabpanel" id="tmSeatPanel-<?= (int) $m['id'] ?>" aria-labelledby="tmSeat-<?= (int) $m['id'] ?>" tabindex="0">
              <span class="tm-role"><?= h($m['role']) ?></span>
              <h3><?= h($m['name']) ?></h3>
              <?php if ($bio): ?><p><?= h($lede($bio, 200)) ?></p><?php endif; ?>
              <?= $bio && mb_strlen($bio) > mb_strlen($lede($bio, 200)) ? $more($m, 'Read full bio') : '' ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
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
