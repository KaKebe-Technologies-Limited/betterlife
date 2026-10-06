<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/media.php';
$pageTitle = 'Stories';
$activePage = 'blog';
$pageDescription = 'Stories from BetterLife International: voices from the communities we work with, what we are learning, and what others are writing about our work.';

$categorySlug = (string) ($_GET['category'] ?? '');
$search = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 7;

$where = ["bp.status = 'published'"];
$params = [];
if ($categorySlug !== '') {
    $where[] = 'bc.slug = ?';
    $params[] = $categorySlug;
}
if ($search !== '') {
    $where[] = '(bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
$whereSql = implode(' AND ', $where);

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE $whereSql");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pg = paginate($total, $page, $perPage);

$stmt = $pdo->prepare("SELECT bp.*, bc.name AS cat_name, bc.slug AS cat_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE $whereSql ORDER BY bp.published_at DESC LIMIT $perPage OFFSET {$pg['offset']}");
$stmt->execute($params);
$posts = $stmt->fetchAll();

// The newest story leads, on the first page of the full list
$filtered = $categorySlug !== '' || $search !== '';
$lead = (!$filtered && $page === 1 && $posts) ? array_shift($posts) : null;

// Only the categories that have stories become filters
$categories = $pdo->query("SELECT bc.*, COUNT(bp.id) AS cnt FROM blog_categories bc LEFT JOIN blog_posts bp ON bp.category_id = bc.id AND bp.status = 'published' GROUP BY bc.id HAVING cnt > 0 ORDER BY bc.id")->fetchAll();
$catName = '';
foreach ($categories as $c) if ($c['slug'] === $categorySlug) $catName = $c['name'];

$minutes = fn(array $p): int => max(1, (int) round(str_word_count(strip_tags((string) $p['content'])) / 200));
$storyUrl = fn(array $p): string => SITE_URL . '/blog-single.php?slug=' . rawurlencode($p['slug']);
$listUrl = function (array $set = []) use ($categorySlug, $search): string {
    $q = array_filter(array_merge(['category' => $categorySlug, 'q' => $search], $set), fn($v) => $v !== '' && $v !== null && $v !== 1);
    return SITE_URL . '/blog.php' . ($q ? '?' . http_build_query($q) : '') . '#blog';
};

$coverage = require __DIR__ . '/includes/coverage.php';
$press = $coverage['press'];
$pressByYear = [];
foreach ($press as $item) $pressByYear[substr($item[0], 0, 4)][] = $item;
$outlets = array_values(array_unique(array_map(fn($i) => $i[1], $press)));
// The opening's clippings: an international, a national and a local title
$clipUrls = [
    'https://african.business/2026/08/trade-investment/africas-young-disruptors-part-two',
    'https://www.newvision.co.ug/category/agriculture/forbes-honours-21-year-old-ugandan-for-climat-NV_235908_062026',
    'https://factsmediauganda.com/2025/04/14/betterlife-international-at-five-years-transforming-90000-lives-across-east-africa/',
];
$clips = array_values(array_filter(array_map(fn($u) => current(array_filter($press, fn($p) => $p[3] === $u)) ?: null, $clipUrls)));
// Counts for the news section: every article, including those that ran in a second outlet
$articleCount = count($press) + array_sum(array_map(fn($p) => count($p[5] ?? []), $press));
$allOutlets = array_unique(array_merge($outlets, ...array_map(fn($p) => array_column($p[5] ?? [], 0), $press)));
$firstYear = min(array_keys($pressByYear));
$fmt = fn(string $d): string => $d === '' ? '' : date('j F Y', strtotime($d));
$newTab = '<span class="sr-only"> (opens in a new tab)</span>';

$flash = flash_get();   // after subscribing, the newsletter form returns here

$pageStyles  = ['assets/css/about.css', 'assets/css/stories.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/stories.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab st" id="top">
  <?= ab_brush_defs() ?>

  <!-- Opening: what the page holds, with clippings from the coverage -->
  <section class="st-hero" aria-labelledby="stTitle">
    <svg class="st-hero-strokes" viewBox="0 0 1200 500" preserveAspectRatio="none" aria-hidden="true" focusable="false"><g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(-80, 470, 380, 444, 70, 61) ?>"/><path class="f-blue" d="<?= lp_brush_d(940, 26, 1280, 4, 44, 63) ?>"/></g></svg>
    <div class="container st-hero-grid">
      <div class="st-hero-copy">
        <nav class="st-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">Stories</span></nav>
        <p class="st-kicker">Stories</p>
        <h1 id="stTitle">Told from the <?= ab_mark('ground up', 57) ?></h1>
        <p class="st-lead">What others are writing about BetterLife International, the recognition along the way, and stories from our own team.</p>
        <nav class="st-jump" aria-label="On this page">
          <a href="#news"><?= icon('newspaper', 15) ?> In the news</a>
          <a href="#recognition"><?= icon('award', 15) ?> Recognition</a>
          <a href="#web"><?= icon('globe', 15) ?> Around the web</a>
          <a href="#blog"><?= icon('book', 15) ?> Our blog</a>
        </nav>
      </div>
      <div class="st-clips" aria-label="From the coverage">
        <?php foreach ($clips as $i => [$d, $outlet, $headline, $url]): ?>
          <a class="st-clip st-clip-<?= $i + 1 ?>" href="<?= h($url) ?>" target="_blank" rel="noopener">
            <span class="st-clip-outlet"><?= h($outlet) ?></span>
            <span class="st-clip-head"><?= h($headline) ?></span>
            <span class="st-clip-date"><?= h($fmt($d)) ?> <?= icon('external-link', 13) ?></span>
            <?= $newTab ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Where BetterLife has appeared, as a moving line of names -->
  <section class="st-outlets" aria-label="BetterLife has appeared in">
    <p class="st-outlets-label">As featured in</p>
    <div class="st-ticker">
      <?php for ($copy = 0; $copy < 2; $copy++): ?>
        <ul class="st-ticker-row"<?= $copy ? ' aria-hidden="true"' : '' ?>>
          <?php foreach ($allOutlets as $o): ?><li><?= h($o) ?></li><?php endforeach; ?>
          <li>Moonshot</li><li>Forbes Africa</li>
        </ul>
      <?php endfor; ?>
    </div>
  </section>

  <!-- In the news -->
  <section class="st-news" id="news" aria-labelledby="stNewsTitle">
    <div class="container">
      <div class="st-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">In the news</span>
          <h2 id="stNewsTitle">What others are writing</h2>
        </div>
        <p class="st-head-note">Coverage of BetterLife International and its founder, Denise Ayebare. Each link opens the original article.</p>
      </div>
      <ul class="st-news-stats ab-reveal" aria-label="The coverage in numbers">
        <li><strong><?= $articleCount ?></strong> articles</li>
        <li><strong><?= count($allOutlets) ?></strong> news outlets</li>
        <li><strong><?= h($firstYear) ?></strong> the earliest</li>
      </ul>
      <?php foreach ($pressByYear as $year => $items): ?>
        <div class="st-year ab-reveal">
          <h3 class="st-year-n"><?= h($year) ?></h3>
          <ul class="st-press">
            <?php foreach ($items as $item): [$d, $outlet, $headline, $url, $about] = $item; ?>
              <li>
                <a href="<?= h($url) ?>" target="_blank" rel="noopener">
                  <span class="st-press-top"><span class="st-press-outlet"><?= h($outlet) ?></span><span class="st-press-date"><?= h($fmt($d)) ?></span></span>
                  <span class="st-press-head"><?= h($headline) ?> <?= icon('external-link', 15) ?></span>
                  <span class="st-press-about"><?= h($about) ?></span>
                  <?= $newTab ?>
                </a>
                <?php if (!empty($item[5])): ?><p class="st-press-also">Also published by <?php foreach ($item[5] as $k => [$ao, $au]): ?><?= $k ? ', ' : '' ?><a href="<?= h($au) ?>" target="_blank" rel="noopener"><?= h($ao) ?><?= $newTab ?></a><?php endforeach; ?></p><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Recognition -->
  <section class="st-recog" id="recognition" aria-labelledby="stRecogTitle">
    <div class="container st-recog-grid">
      <figure class="st-recog-photo ab-reveal">
        <?= ab_img('assets/img/programmes/academy-denise-moonshot-award.jpg', 'Denise Ayebare on stage, speaking into a microphone and holding the Moonshot Borderless Award', '', true, '', '(max-width: 900px) 80vw, 420px') ?>
        <figcaption>Denise Ayebare receiving the Moonshot Borderless Award</figcaption>
      </figure>
      <div class="st-recog-copy ab-reveal">
        <span class="ab-eyebrow">Recognition</span>
        <h2 id="stRecogTitle">Recognised along the way</h2>
        <p>Awards and lists that have recognised BetterLife’s founder, Denise Ayebare, and the work she leads with the BetterLife team.</p>
        <ol class="st-timeline">
          <?php foreach ($coverage['recognition'] as [$year, $name, $detail, $url]): ?>
            <li>
              <span class="st-timeline-year"><?= h($year) ?></span>
              <a href="<?= h($url) ?>" target="_blank" rel="noopener"><strong><?= h($name) ?></strong> <span><?= h($detail) ?></span><?= $newTab ?></a>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <!-- Around the web -->
  <section class="st-web" id="web" aria-labelledby="stWebTitle">
    <div class="container">
      <div class="st-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">Around the web</span>
          <h2 id="stWebTitle">Where else to find us</h2>
        </div>
        <p class="st-head-note">Partner pages, project pages and profiles that feature BetterLife International.</p>
      </div>
      <ul class="st-links">
        <?php foreach ($coverage['web'] as [$type, $title, $about, $url, $domain]): ?>
          <li class="ab-reveal">
            <a href="<?= h($url) ?>" target="_blank" rel="noopener">
              <span class="st-link-type"><?= h($type) ?></span>
              <strong><?= h($title) ?></strong>
              <span class="st-link-about"><?= h($about) ?></span>
              <span class="st-link-domain"><?= h($domain) ?> <?= icon('external-link', 13) ?></span>
              <?= $newTab ?>
            </a>
          </li>
        <?php endforeach; ?>
        <li class="st-link-press ab-reveal">
          <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Media enquiry') ?>">
            <span class="st-link-type">For journalists</span>
            <strong>Writing about BetterLife?</strong>
            <span class="st-link-about">For interviews and information, contact our team.</span>
            <span class="st-link-domain">Media enquiries <?= icon('arrow-right', 14) ?></span>
          </a>
        </li>
      </ul>
    </div>
  </section>

  <!-- Our own writing comes last: first what others say about BetterLife -->
  <section class="st-stories" id="blog" aria-labelledby="stStoriesTitle">
    <div class="container">
      <div class="st-head-row ab-reveal">
        <div class="ab-head">
          <span class="ab-eyebrow">In our own words</span>
          <h2 id="stStoriesTitle">From our blog</h2>
        </div>
        <form class="st-search" method="get" action="<?= SITE_URL ?>/blog.php#blog" role="search">
          <?php if ($categorySlug !== ''): ?><input type="hidden" name="category" value="<?= h($categorySlug) ?>"><?php endif; ?>
          <label class="sr-only" for="stSearch">Search stories</label>
          <input id="stSearch" type="search" name="q" value="<?= h($search) ?>" placeholder="Search stories">
          <button type="submit" aria-label="Search"><?= icon('search', 17) ?></button>
        </form>
      </div>

      <?php if ($categories): ?>
        <nav class="st-cats" aria-label="Story topics">
          <a href="<?= h($listUrl(['category' => '', 'page' => 1])) ?>"<?= $categorySlug === '' ? ' aria-current="true"' : '' ?>>All stories</a>
          <?php foreach ($categories as $c): ?>
            <a href="<?= h($listUrl(['category' => $c['slug'], 'page' => 1])) ?>"<?= $categorySlug === $c['slug'] ? ' aria-current="true"' : '' ?>><?= h($c['name']) ?> <small><?= (int) $c['cnt'] ?></small></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>

      <?php if ($filtered): ?>
        <p class="st-count" role="status"><?= $total ?> <?= $total === 1 ? 'story' : 'stories' ?><?= $catName ? ' in ' . h($catName) : '' ?><?= $search !== '' ? ' matching “' . h($search) . '”' : '' ?> · <a href="<?= SITE_URL ?>/blog.php#blog">Show all stories</a></p>
      <?php endif; ?>

      <?php if ($lead): ?>
        <article class="st-lead-story ab-reveal">
          <div class="st-lead-media"><?= ab_img($lead['featured_image'], '', '', false, '', '(max-width: 900px) 100vw, 640px') ?></div>
          <div class="st-lead-body">
            <span class="st-tag"><?= h($lead['cat_name'] ?? 'Stories') ?></span>
            <h3><a href="<?= h($storyUrl($lead)) ?>"><?= h($lead['title']) ?></a></h3>
            <p><?= h(excerpt($lead['excerpt'] ?: $lead['content'], 220)) ?></p>
            <p class="st-meta"><?= h(format_date($lead['published_at'], 'j F Y')) ?> · <?= $minutes($lead) ?> min read</p>
            <span class="st-more" aria-hidden="true">Read the story <?= icon('arrow-right', 15) ?></span>
          </div>
        </article>
      <?php endif; ?>

      <?php if ($posts): ?>
        <div class="st-grid">
          <?php foreach ($posts as $p): ?>
            <article class="st-card ab-reveal">
              <div class="st-card-media"><?= ab_img($p['featured_image'], '', '', true, '', '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 380px') ?></div>
              <div class="st-card-body">
                <span class="st-tag"><?= h($p['cat_name'] ?? 'Stories') ?></span>
                <h3><a href="<?= h($storyUrl($p)) ?>"><?= h($p['title']) ?></a></h3>
                <p><?= h(excerpt($p['excerpt'] ?: $p['content'], 150)) ?></p>
                <p class="st-meta"><?= h(format_date($p['published_at'], 'j F Y')) ?> · <?= $minutes($p) ?> min read</p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php elseif (!$lead): ?>
        <p class="st-empty">No stories match<?= $search !== '' ? ' “' . h($search) . '”' : '' ?> yet. <a href="<?= SITE_URL ?>/blog.php#blog">Show all stories</a></p>
      <?php endif; ?>

      <?php if ($pg['pages'] > 1): ?>
        <nav class="st-pages" aria-label="More stories">
          <?php for ($i = 1; $i <= $pg['pages']; $i++): ?>
            <?php if ($i === $pg['page']): ?><span aria-current="page"><?= $i ?></span><?php else: ?><a href="<?= h($listUrl(['page' => $i])) ?>"><?= $i ?></a><?php endif; ?>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>
    </div>
  </section>

  <!-- Follow and subscribe -->
  <section class="st-follow" id="follow" aria-labelledby="stFollowTitle">
    <div class="container st-follow-grid">
      <div class="st-follow-copy">
        <span class="ab-eyebrow">Keep up with us</span>
        <h2 id="stFollowTitle">New stories, straight to you</h2>
        <p>Follow BetterLife International for news from the field, or have new stories sent to your inbox.</p>
        <div class="st-social">
          <?php foreach ($coverage['social'] as [$ico, $network, $handle, $url]): ?>
            <a href="<?= h($url) ?>" target="_blank" rel="noopener"><?= icon($ico, 20) ?><span><small><?= h($network) ?></small><?= h($handle) ?></span><?= $newTab ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <form class="st-subscribe" action="<?= SITE_URL ?>/newsletter-submit.php" method="post">
        <label for="stEmail">Email address</label>
        <div class="st-subscribe-row">
          <input id="stEmail" type="email" name="email" required placeholder="you@example.org" autocomplete="email">
          <button type="submit">Subscribe <?= icon('arrow-right', 15) ?></button>
        </div>
        <?php if ($flash): ?><p class="st-flash is-<?= h($flash['type']) ?>" role="status"><?= h($flash['message']) ?></p><?php endif; ?>
        <p class="st-subscribe-note">Occasional updates only. You can unsubscribe at any time.</p>
      </form>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
