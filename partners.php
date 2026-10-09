<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/programmes.php';
$pageTitle = 'Our Partners';
$activePage = 'partners';
$pageDescription = 'The organisations that work alongside BetterLife International, and how each partnership supports the work.';

// What each partnership involves, from BetterLife's own programme material (shared with About and Home)
$partners = require __DIR__ . '/includes/partners-list.php';

try {
    $org = $pdo->query("SELECT subtitle, body FROM content_items WHERE page = 'programs' AND section_key = 'our-projects' AND title = 'Dovetail Impact Foundation' AND status = 1 LIMIT 1")->fetch();
} catch (PDOException $e) {
    error_log('partners: ' . $e->getMessage());
    $org = false;
}
$orgParas = $org ? array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/u', (string) $org['body'])))) : [];

$pageStyles  = ['assets/css/about.css', 'assets/css/programmes.css'];
$pageScripts = ['assets/js/about.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab pg" id="top">
  <?= ab_brush_defs() ?>

  <section class="pg-page-head" aria-labelledby="pgTitle">
    <div class="container">
      <nav class="ab-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/about.php">About Us</a><span aria-hidden="true">/</span><span aria-current="page">Partners</span></nav>
      <h1 id="pgTitle">Stronger in <?= ab_mark('partnership', 91) ?></h1>
      <p>We work with organisations that bring resources, knowledge and reach while respecting the experience of the communities at the centre of the work.</p>
    </div>
  </section>

  <section aria-labelledby="pgPartnersTitle" style="padding-top: clamp(36px, 4.5vw, 56px);">
    <div class="container">
      <h2 id="pgPartnersTitle" class="sr-only">Our partners</h2>
      <ul class="pg-partner-list">
        <?php foreach ($partners as [$logo, $name, $what, $link]): ?>
          <li class="pg-partner-card ab-reveal">
            <?php if ($logo && is_file(__DIR__ . '/assets/img/partners/' . $logo)): ?>
              <div class="pg-partner-logo pg-logo-<?= h(pathinfo($logo, PATHINFO_FILENAME)) ?>"><img src="<?= h(asset_url('assets/img/partners/' . $logo)) ?>" alt="<?= h($name) ?> logo" loading="lazy" decoding="async"></div>
            <?php else: ?>
              <div class="pg-partner-logo is-name" aria-hidden="true"><span><?= h($name) ?></span></div>
            <?php endif; ?>
            <div>
              <h3><?= h($name) ?></h3>
              <?php if ($what): ?><p><?= h($what) ?></p><?php endif; ?>
              <?php if ($link && preg_match('#^https?://#', $link)): ?><a href="<?= h($link) ?>" class="pg-link" target="_blank" rel="noopener">Visit their website <?= icon('external-link', 14) ?><span class="sr-only"> (opens in a new tab)</span></a>
              <?php elseif ($link): ?><a href="<?= h(str_starts_with($link, '#') ? $link : SITE_URL . '/' . $link) ?>" class="pg-link"><?= str_starts_with($link, '#') ? 'How this support works' : 'See the work' ?> <?= icon('arrow-right', 15) ?></a><?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <?php if ($orgParas): ?>
    <section class="pg-section-cream" id="organisation" aria-labelledby="pgOrgTitle">
      <div class="container pg-org">
        <div class="ab-reveal">
          <span class="ab-eyebrow">Strengthening the organisation behind the work</span>
          <h2 id="pgOrgTitle"><?= h($org['subtitle'] ?: 'Growing without losing what made the work local') ?></h2>
          <p class="pg-partner" style="margin-top: 18px;"><?= icon('heart', 14) ?> With Dovetail Impact Foundation</p>
        </div>
        <div class="pg-story ab-reveal">
          <?php foreach ($orgParas as $para): ?><p><?= h($para) ?></p><?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="pg-invite" aria-labelledby="pgInviteTitle">
    <div class="container">
      <div class="pg-invite-card ab-reveal">
        <div>
          <h2 id="pgInviteTitle">Become a partner</h2>
          <p>Fund a programme, contribute equipment or expertise, or connect farmers to markets. We would be glad to hear what you have in mind.</p>
        </div>
        <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Partnership enquiry') ?>" class="pg-btn">Start a conversation <?= icon('arrow-right', 16) ?></a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
