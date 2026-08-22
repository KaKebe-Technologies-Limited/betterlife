<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
$activePage = 'home';

$heroImages = [
    setting($pdo, 'hero_image_1', 'assets/img/hero-real-1.jpg'),
    setting($pdo, 'hero_image_2', 'assets/img/farm-field-1.jpg'),
    setting($pdo, 'hero_image_3', 'assets/img/product-honey.jpg'),
    setting($pdo, 'hero_image_4', 'assets/img/about-real-1.jpg'),
    setting($pdo, 'hero_image_5', 'assets/img/farm-field-2.jpg'),
    setting($pdo, 'hero_image_6', 'assets/img/program-trees.jpg'),
    setting($pdo, 'hero_image_7', 'assets/img/product-ghee.jpg'),
    setting($pdo, 'hero_image_8', 'assets/img/product-yogurt.jpg'),
];
$stats = $pdo->query("SELECT * FROM stats WHERE status = 1 ORDER BY sort_order")->fetchAll();
$programs = $pdo->query("SELECT * FROM programs WHERE status = 1 ORDER BY sort_order LIMIT 4")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY featured DESC, sort_order LIMIT 4")->fetchAll();
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE status = 1 ORDER BY sort_order LIMIT 3")->fetchAll();
$posts = $pdo->query("SELECT bp.*, bc.name AS cat_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE bp.status = 'published' ORDER BY bp.published_at DESC LIMIT 3")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero-new" id="top">
  <div class="container hero-grid">
    <div class="hero-text fade-up">
      <span class="hero-badge"><span class="dot"></span> Youth-led · Refugee-inspired · Since <?= h(setting($pdo,'founded_year','2021')) ?></span>
      <h1><?= h(setting($pdo, 'hero_title')) ?></h1>
      <p class="lead"><?= h(excerpt(setting($pdo, 'hero_subtitle'), 150)) ?></p>
      <div class="feature-pills">
        <span class="pill pill-active"><?= icon('leaf', 15) ?> Green Skills</span>
        <span class="pill"><?= icon('globe', 15) ?> Climate Education</span>
        <span class="pill"><?= icon('shopping-bag', 15) ?> Farm to Market</span>
        <span class="pill"><?= icon('users', 15) ?> Youth-Led</span>
      </div>
      <div class="hero-actions">
        <a href="<?= SITE_URL ?>/about.php" class="btn btn-hero-cta">Discover Our Story <span class="cta-dot"><?= icon('arrow-right', 15) ?></span></a>
        <a href="<?= SITE_URL ?>/products.php" class="btn btn-outline-dark"><?= icon('shopping-bag', 16) ?> Shop BetterLife Farm</a>
      </div>
    </div>

    <div class="hero-scroll-panel fade-up">
      <span class="hero-dot dot-green"></span>
      <span class="hero-dot dot-blue"></span>
      <span class="hero-dot dot-outline"></span>
      <div class="scroll-track">
        <?php foreach (array_chunk($heroImages, 3) as $ri => $rowImages): ?>
          <div class="scroll-row <?= $ri % 2 === 0 ? 'dir-left' : 'dir-right' ?>">
            <?php foreach (array_merge($rowImages, $rowImages) as $ii => $img): ?>
              <div class="scroll-tile <?= $ii % 2 === 0 ? 'tint-green' : 'tint-blue' ?>">
                <img src="<?= asset_url($img) ?>" alt="BetterLife International" loading="lazy">
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="impact-section section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Local Impact</span>
      <h2>Change You Can See On The Ground</h2>
      <p class="muted">Real numbers from real communities — and a look at the work behind them.</p>
    </div>
    <div class="impact-stats">
      <?php foreach ($stats as $s): ?>
        <div class="stat-item fade-up">
          <strong data-count="<?= h($s['value']) ?>">0</strong>
          <span><?= h($s['label']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="impact-photos">
      <div class="impact-photo fade-up"><img src="<?= asset_url('assets/img/farm-field-2.jpg') ?>" alt="Organic dairy farming"><span class="cap">Organic dairy farming</span></div>
      <div class="impact-photo fade-up"><img src="<?= asset_url('assets/img/program-trees.jpg') ?>" alt="Tree planting"><span class="cap">Nature-based restoration</span></div>
      <div class="impact-photo fade-up"><img src="<?= asset_url('assets/img/product-honey.jpg') ?>" alt="Beekeeping"><span class="cap">Community beekeeping</span></div>
    </div>
  </div>
</section>

<section id="about-preview">
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame">
        <img src="<?= asset_url(setting($pdo, 'about_image')) ?>" alt="BetterLife International community work">
        <div class="float-card">
          <strong><?= h(setting($pdo,'founded_year','2021')) ?></strong>
          <span>Founded by youth &amp; refugee leaders</span>
        </div>
      </div>
      <div class="fade-up">
        <span class="eyebrow">Who We Are</span>
        <h2>Building a better life, one community at a time</h2>
        <p class="muted"><?= nl2br(h(mb_substr(setting($pdo, 'about_who_text'), 0, 420))) ?>&hellip;</p>
        <ul class="check-list">
          <li><span class="tick"><?= icon('check', 13) ?></span> Working across Uganda, South Sudan, Tanzania, Ghana &amp; DR Congo</li>
          <li><span class="tick"><?= icon('check', 13) ?></span> Sustainable agriculture, green skills &amp; climate education</li>
          <li><span class="tick"><?= icon('check', 13) ?></span> A working model farm producing honey, ghee &amp; yoghurt</li>
        </ul>
        <a href="<?= SITE_URL ?>/about.php" class="btn btn-outline-dark" style="margin-top:16px;">Read Our Full Story →</a>
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">What We Do</span>
      <h2>Programs Rooted in Sustainability</h2>
      <p class="muted">From climate-smart farms to green libraries, every program is designed with — and by — the communities we serve.</p>
    </div>
    <div class="grid grid-4">
      <?php foreach ($programs as $p): ?>
        <div class="card program-card fade-up">
          <div class="thumb"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['title']) ?>"></div>
          <div class="body">
            <div class="icon-badge"><?= icon('leaf', 20) ?></div>
            <span class="tagline"><?= h($p['tagline']) ?></span>
            <h3><?= h($p['title']) ?></h3>
            <p class="muted" style="font-size:14px;"><?= h(excerpt($p['summary'], 90)) ?></p>
            <a href="<?= SITE_URL ?>/programs.php#<?= h($p['slug']) ?>" class="more">Learn more →</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow">From Our Farm</span>
        <h2><?= h(setting($pdo, 'farm_title')) ?></h2>
        <p class="muted"><?= h(excerpt(setting($pdo, 'farm_text'), 380)) ?></p>
        <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary" style="margin-top:10px;">Browse Farm Products →</a>
      </div>
      <div class="fade-up grid" style="grid-template-columns:1fr 1fr; gap:20px;">
        <?php foreach ($products as $prod): ?>
          <div class="card product-card">
            <div class="thumb">
              <img src="<?= asset_url($prod['image']) ?>" alt="<?= h($prod['name']) ?>">
              <span class="badge-cat"><?= h($prod['category']) ?></span>
            </div>
            <div class="body" style="padding:16px;">
              <h3 style="font-size:15px;"><?= h($prod['name']) ?></h3>
              <div class="price" style="font-size:14px;"><?= format_price($prod['price']) ?> <small>/ <?= h($prod['unit']) ?></small></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($testimonials): ?>
<section class="section-dark">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow">Voices</span>
      <h2>What People Say About Us</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($testimonials as $t): ?>
        <div class="card testimonial-card fade-up" style="background:#fff;">
          <span class="quote-mark">&ldquo;</span>
          <p class="quote">&ldquo;<?= h($t['quote']) ?>&rdquo;</p>
          <div class="who">
            <img src="<?= asset_url($t['photo'] ?: 'assets/img/logo.png') ?>" alt="<?= h($t['author_name']) ?>">
            <div>
              <strong><?= h($t['author_name']) ?></strong>
              <span><?= h($t['author_role']) ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Latest Stories</span>
      <h2>From the BetterLife Blog</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($posts as $post): ?>
        <div class="card post-card fade-up">
          <div class="thumb"><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>"><img src="<?= asset_url($post['featured_image']) ?>" alt="<?= h($post['title']) ?>"></a></div>
          <div class="body">
            <span class="cat-badge"><?= h($post['cat_name'] ?? 'General') ?></span>
            <h3><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>"><?= h($post['title']) ?></a></h3>
            <p class="excerpt"><?= h(excerpt($post['excerpt'] ?: $post['content'], 100)) ?></p>
            <a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>" class="readmore">Read Story →</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="cta-banner fade-up">
      <div>
        <h3>Partner with BetterLife International</h3>
        <p>Whether you want to volunteer, partner on a program, or simply enjoy honey and ghee with purpose — we'd love to hear from you.</p>
      </div>
      <a href="<?= SITE_URL ?>/contact.php" class="btn btn-white">Get In Touch →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
