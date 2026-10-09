<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'Contact Us';
$activePage = 'contact';
$pageDescription = 'Talk to BetterLife International about supporting a programme, working with us, visiting the farm, buying our products or learning more about what we do.';

$flash = flash_get();
$old = $_SESSION['contact_old'] ?? [];
unset($_SESSION['contact_old']);

$topics = require __DIR__ . '/includes/contact-topics.php';

// Which topic to start on: the link that brought the visitor here may name it, sometimes with a project ("Partnership enquiry: RISE")
$asked = trim((string) ($old['subject'] ?? $_GET['subject'] ?? ''));
$about = trim((string) ($old['about'] ?? ''));
$aliases = ['media enquiry' => 'Media and speaking', 'farm product enquiry' => 'Product order'];
$chosen = '';
foreach (array_keys($topics) as $t) if (strcasecmp($t, $asked) === 0) $chosen = $t;
if (!$chosen && isset($aliases[strtolower($asked)])) $chosen = $aliases[strtolower($asked)];
if (!$chosen && preg_match('/^(.+?):\s*(.+)$/u', $asked, $m)) {
    foreach (array_keys($topics) as $t) if (strcasecmp($t, trim($m[1])) === 0) { $chosen = $t; $about = mb_substr(trim($m[2]), 0, 120); }
}
$prompt = $chosen ? $topics[$chosen][3] : 'Tell us a little about what you have in mind.';

$email = setting($pdo, 'email');
$phone = setting($pdo, 'phone');

// Where we are: offices placed on the map with the same projection as the site's other maps (includes/map-paths.php)
$map = require __DIR__ . '/includes/map-paths.php';
$P = $map['proj'];
$at = fn(float $lat, float $lon): array => [deg2rad($lon) * $P['scale'] + $P['ox'], -log(tan(M_PI / 4 + deg2rad($lat) / 2)) * $P['scale'] + $P['oy']];
$vb = [372, 300, 150, 128];   // East Africa, from the Congo basin to the coast
$offices = [
    // place, country, what is there, latitude, longitude, the side its label sits (kept clear of nearby pins)
    ['Rukungiri', 'Uganda', 'Head office', -0.789, 29.925, 'left'],
    ['Yumbe and Bidi Bidi', 'Uganda', 'West Nile programme hub', 3.465, 31.247, 'right'],
    ['Juba', 'South Sudan', 'Country office', 4.859, 31.571, 'right'],
    ['Yambio', 'South Sudan', 'Field operations', 4.570, 28.398, 'left'],
    ['Kayanga Town', 'Tanzania', 'Karagwe District', -1.554, 31.098, 'right'],
];
$pct = function (float $lat, float $lon) use ($at, $vb): string {
    [$x, $y] = $at($lat, $lon);
    return 'left: ' . round(($x - $vb[0]) / $vb[2] * 100, 2) . '%; top: ' . round(($y - $vb[1]) / $vb[3] * 100, 2) . '%';
};

// The people who lead the work in each country (from the team list)
$managers = [];
foreach ($pdo->query("SELECT name, role, photo FROM team_members WHERE status = 1 AND role LIKE 'Country Manager,%' ORDER BY sort_order") as $m) {
    $managers[trim(substr($m['role'], strlen('Country Manager,')))] = $m;
}
$countries = [
    'Uganda'      => ['Head office in Rukungiri, and our West Nile programme hub in Yumbe and Bidi Bidi.', null],
    'South Sudan' => ['Our office in Juba, with field operations in Yambio.', $managers['South Sudan'] ?? null],
    'Tanzania'    => ['Kayanga Town, Karagwe District, where we work with FADECO.', $managers['Tanzania'] ?? null],
];
$elsewhere = array_filter([$managers['Ghana'] ?? null, $managers['DR Congo'] ?? null]);

$newTab = '<span class="sr-only"> (opens in a new tab)</span>';
$pageStyles  = ['assets/css/about.css', 'assets/css/contact.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/contact.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab ct" id="top">
  <?= ab_brush_defs() ?>

  <!-- Opening: a welcome, and the quickest ways to reach us -->
  <section class="ct-hero" aria-labelledby="ctTitle">
    <svg class="ct-hero-strokes" viewBox="0 0 1200 500" preserveAspectRatio="none" aria-hidden="true" focusable="false"><g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(-80, 470, 360, 446, 70, 71) ?>"/><path class="f-blue" d="<?= lp_brush_d(960, 26, 1280, 4, 44, 73) ?>"/></g></svg>
    <div class="container ct-hero-grid">
      <div class="ct-hero-copy">
        <nav class="ct-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">Contact</span></nav>
        <p class="ct-kicker">Contact</p>
        <h1 id="ctTitle">Every partnership starts with a <?= ab_mark('conversation', 77) ?></h1>
        <p class="ct-lead">Whether you want to support a programme, work with BetterLife, visit the farm, buy from it or learn more about what we do, we would be glad to hear from you.</p>
        <ul class="ct-reach">
          <?php if ($email): ?>
            <li>
              <a href="mailto:<?= h($email) ?>"><span class="ct-reach-icon"><?= icon('mail', 20) ?></span><span><small>Email us</small><?= h($email) ?></span></a>
              <button type="button" class="ct-copy" data-copy="<?= h($email) ?>" hidden><svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg><span class="ct-copy-text">Copy</span></button>
              <span class="ct-copy-done" role="status"></span>
            </li>
          <?php endif; ?>
          <?php if ($phone): ?>
            <li><a href="tel:<?= h(preg_replace('/\s+/', '', $phone)) ?>"><span class="ct-reach-icon"><?= icon('phone', 20) ?></span><span><small>Call us</small><?= h($phone) ?></span></a></li>
          <?php endif; ?>
        </ul>
        <a class="ct-write-link" href="#write">Or send us a message <?= icon('arrow-right', 15) ?></a>
      </div>
      <div class="ct-hero-art">
        <div class="ct-hero-main"><?= ab_img('assets/img/team/edwin-welcome.jpg', 'Edwin Namakanga, our Outreach Coordinator, greeting with open arms beside thatched homes', '', false, 'style="object-position: 50% 35%"', '(max-width: 900px) 90vw, 520px') ?></div>
        <div class="ct-hero-side"><?= ab_img('assets/img/team/team-waving.jpg', 'BetterLife team members in programme vests, smiling and waving in a garden', '', true, 'style="object-position: 50% 40%"', '(max-width: 900px) 45vw, 240px') ?></div>
        <p class="ct-bubble" aria-hidden="true">We would love to hear from you</p>
      </div>
    </div>
  </section>

  <!-- The message: first what it is about, then who you are, then what you would like to say -->
  <section class="ct-write" id="write" aria-labelledby="ctWriteTitle">
    <div class="container">
      <div class="ct-head ab-reveal">
        <span class="ab-eyebrow">Send a message</span>
        <h2 id="ctWriteTitle">What would you like to talk about?</h2>
      </div>

      <?php if ($flash && $flash['type'] === 'success'): ?>
        <div class="ct-sent" role="status">
          <span class="ct-sent-icon" aria-hidden="true"><?= icon('check', 30) ?></span>
          <div>
            <h3>Message sent</h3>
            <p><?= h($flash['message']) ?></p>
            <a href="<?= SITE_URL ?>/contact.php#write">Send another message <?= icon('arrow-right', 15) ?></a>
          </div>
        </div>
      <?php else: ?>
        <form class="ct-form" method="post" action="<?= SITE_URL ?>/contact-submit.php" novalidate>
          <?= csrf_field() ?>
          <?php if ($flash): ?><p class="ct-error" role="alert"><?= icon('x', 16) ?> <?= h($flash['message']) ?></p><?php endif; ?>

          <fieldset class="ct-step">
            <legend><span class="ct-step-n">1</span> Choose a topic</legend>
            <div class="ct-topics">
              <?php foreach ($topics as $value => [$label, $ico, $line, $ask]): $on = $chosen === $value || (!$chosen && $value === 'General enquiry' && $asked !== ''); ?>
                <label class="ct-topic">
                  <input type="radio" name="subject" value="<?= h($value) ?>" data-ask="<?= h($ask) ?>"<?= $on ? ' checked' : '' ?>>
                  <span class="ct-topic-card">
                    <span class="ct-topic-icon" aria-hidden="true"><?= icon($ico, 20) ?></span>
                    <strong><?= h($label) ?></strong>
                    <small><?= h($line) ?></small>
                  </span>
                </label>
              <?php endforeach; ?>
            </div>
          </fieldset>

          <fieldset class="ct-step">
            <legend><span class="ct-step-n">2</span> About you</legend>
            <div class="ct-fields">
              <div class="ct-field">
                <label for="ctName">Your name <span aria-hidden="true">*</span></label>
                <input id="ctName" type="text" name="name" required maxlength="150" autocomplete="name" value="<?= h($old['name'] ?? '') ?>">
              </div>
              <div class="ct-field">
                <label for="ctEmail">Email address <span aria-hidden="true">*</span></label>
                <input id="ctEmail" type="email" name="email" required maxlength="150" autocomplete="email" value="<?= h($old['email'] ?? '') ?>">
              </div>
              <div class="ct-field">
                <label for="ctPhone">Phone number <small>(optional)</small></label>
                <input id="ctPhone" type="tel" name="phone" maxlength="50" autocomplete="tel" value="<?= h($old['phone'] ?? '') ?>">
              </div>
            </div>
          </fieldset>

          <fieldset class="ct-step">
            <legend><span class="ct-step-n">3</span> Your message</legend>
            <?php if ($about !== ''): ?>
              <p class="ct-about"><?= icon('tag', 14) ?> About: <strong><?= h($about) ?></strong></p>
              <input type="hidden" name="about" value="<?= h($about) ?>">
            <?php endif; ?>
            <div class="ct-field">
              <label for="ctMessage" class="ct-ask"><?= h($prompt) ?> <span aria-hidden="true">*</span></label>
              <textarea id="ctMessage" name="message" required maxlength="5000" rows="6"><?= h($old['message'] ?? '') ?></textarea>
            </div>
            <!-- Left empty by people; automated form-fillers complete it -->
            <div class="ct-trap" aria-hidden="true"><label for="ctWebsite">Website</label><input id="ctWebsite" type="text" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="ct-send">
              <button type="submit"><?= icon('send', 17) ?> Send message</button>
              <p>Your message goes straight to our team’s inbox. We use your details only to reply.</p>
            </div>
          </fieldset>
        </form>
      <?php endif; ?>
    </div>
  </section>

  <!-- Where to find us -->
  <section class="ct-where" aria-labelledby="ctWhereTitle">
    <div class="container ct-where-grid">
      <div class="ct-where-copy">
        <div class="ct-head ab-reveal">
          <span class="ab-eyebrow">Where to find us</span>
          <h2 id="ctWhereTitle">Offices close to the work</h2>
        </div>
        <ul class="ct-countries">
          <?php foreach ($countries as $cname => [$where, $lead]): ?>
            <li class="ab-reveal">
              <h3><?= h($cname) ?></h3>
              <p><?= h($where) ?></p>
              <?php if ($cname === 'Uganda' && ($postal = setting($pdo, 'postal_address'))): ?><p class="ct-postal"><?= icon('mail', 14) ?> Write to us: <?= h($postal) ?></p><?php endif; ?>
              <?php if ($lead): ?>
                <p class="ct-lead-person"><?= ab_img($lead['photo'], '', '', true, '', '44px') ?><span><strong><?= h($lead['name']) ?></strong> <?= h($lead['role']) ?></span></p>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if ($elsewhere): ?>
          <p class="ct-elsewhere ab-reveal">
            <span class="ct-faces"><?php foreach ($elsewhere as $m): ?><?= ab_img($m['photo'], '', '', true, '', '40px') ?><?php endforeach; ?></span>
            <span>Our work in <?= h(implode(' and ', array_map(fn($m) => trim(substr($m['role'], strlen('Country Manager,'))), $elsewhere))) ?> is led by <?= h(implode(' and ', array_map(fn($m) => $m['name'], $elsewhere))) ?>. <a href="<?= SITE_URL ?>/team.php">Meet the whole team</a>.</span>
          </p>
        <?php endif; ?>
      </div>

      <figure class="ct-map ab-reveal" aria-labelledby="ctMapCaption">
        <div class="ct-map-art">
          <svg viewBox="<?= implode(' ', $vb) ?>" aria-hidden="true" focusable="false">
            <path class="ct-land" d="<?= $map['africa'] ?>"/>
            <?php foreach (['Democratic Republic of the Congo', 'South Sudan', 'Uganda', 'Tanzania'] as $k): if (empty($map['countries'][$k])): continue; endif; ?>
              <path class="ct-country<?= in_array($k, ['Uganda', 'South Sudan', 'Tanzania'], true) ? ' is-office' : '' ?>" d="<?= $map['countries'][$k]['d'] ?>"/>
            <?php endforeach; ?>
          </svg>
          <?php foreach (['Uganda' => [1.9, 32.9], 'South Sudan' => [6.9, 30.1], 'Tanzania' => [-4.9, 34.2], 'DR Congo' => [0.6, 26.0]] as $lbl => [$la, $lo]): ?>
            <span class="ct-map-country" style="<?= $pct($la, $lo) ?>" aria-hidden="true"><?= h($lbl) ?></span>
          <?php endforeach; ?>
          <?php foreach ($offices as $i => [$place, $cn, $what, $la, $lo, $side]): ?>
            <span class="ct-pin is-<?= $side ?><?= $i === 0 ? ' is-home' : '' ?>" style="<?= $pct($la, $lo) ?>" aria-hidden="true"><span class="ct-pin-dot"><?= $i + 1 ?></span><span class="ct-pin-label"><?= h($place) ?></span></span>
          <?php endforeach; ?>
        </div>
        <figcaption id="ctMapCaption">
          <ol class="ct-legend">
            <?php foreach ($offices as $i => [$place, $cn, $what]): ?>
              <li><span class="ct-legend-n" aria-hidden="true"><?= $i + 1 ?></span><span><strong><?= h($place) ?></strong>, <?= h($cn) ?><small><?= h($what) ?></small></span></li>
            <?php endforeach; ?>
          </ol>
        </figcaption>
      </figure>
    </div>
  </section>

  <!-- Visit the farm -->
  <section class="ct-farm" aria-labelledby="ctFarmTitle">
    <div class="container ct-farm-grid">
      <figure class="ct-farm-photo ab-reveal">
        <?= ab_img('assets/img/farm/rukungiri-farm-sign.jpg', 'The roadside sign for BetterLife Agro-Tourism Farm and BetterLife International Organisation in Rukungiri', '', true, '', '(max-width: 900px) 80vw, 380px') ?>
        <figcaption>Look for this sign on the road in Rukungiri</figcaption>
      </figure>
      <div class="ct-farm-copy ab-reveal">
        <span class="ab-eyebrow">Come and see it</span>
        <h2 id="ctFarmTitle">Visit BetterLife Agro Tourism Farm</h2>
        <p>Schools, farmers, community groups, development partners and visitors can see how solar energy, irrigation, livestock, beekeeping and food processing work together on our farm in Rukungiri.</p>
        <div class="ct-farm-actions">
          <a class="ct-btn" href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Farm visit') ?>#write" data-topic="Farm visit">Plan a visit <?= icon('arrow-right', 16) ?></a>
          <a class="ct-btn is-quiet" href="<?= SITE_URL ?>/farm.php">About the farm</a>
          <a class="ct-btn is-quiet" href="<?= SITE_URL ?>/products.php"><?= icon('shopping-bag', 16) ?> Shop the farm</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Looking for something else -->
  <section class="ct-more" aria-labelledby="ctMoreTitle">
    <div class="container">
      <div class="ct-head ab-reveal">
        <span class="ab-eyebrow">Looking for something else?</span>
        <h2 id="ctMoreTitle">You may find it here</h2>
      </div>
      <ul class="ct-links">
        <li class="ab-reveal"><a href="<?= SITE_URL ?>/team.php"><?= icon('users', 22) ?><strong>Meet the team</strong><span>The people behind BetterLife, country by country</span></a></li>
        <li class="ab-reveal"><a href="<?= SITE_URL ?>/projects.php"><?= icon('grid', 22) ?><strong>Our projects</strong><span>Where the work takes place, and with whom</span></a></li>
        <li class="ab-reveal"><a href="<?= SITE_URL ?>/impact-reports.php"><?= icon('trending-up', 22) ?><strong>Impact reports</strong><span>What changed, and how we know</span></a></li>
        <li class="ab-reveal"><a href="<?= SITE_URL ?>/blog.php"><?= icon('newspaper', 22) ?><strong>Stories and press</strong><span>What others are writing about us</span></a></li>
      </ul>
      <div class="ct-social ab-reveal">
        <span>Follow BetterLife</span>
        <?php foreach (social_links($pdo) as [$ico, $network, $handle, $url]): ?>
          <a href="<?= h($url) ?>" target="_blank" rel="noopener"><?= icon($ico, 18) ?> <?= h($handle) ?><?= $newTab ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
