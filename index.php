<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
$activePage = 'home';

$heroImage = setting($pdo, 'hero_image', 'assets/img/hero-real-1.jpg');
$stats = $pdo->query("SELECT * FROM stats WHERE status = 1 ORDER BY sort_order")->fetchAll();
$programs = $pdo->query("SELECT * FROM programs WHERE status = 1 ORDER BY sort_order LIMIT 4")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY featured DESC, sort_order LIMIT 3")->fetchAll();
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE status = 1 ORDER BY sort_order LIMIT 3")->fetchAll();
$posts = $pdo->query("SELECT bp.*, bc.name AS cat_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE bp.status = 'published' ORDER BY bp.published_at DESC LIMIT 3")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero" id="top" style="--hero-img:url('<?= asset_url($heroImage) ?>')">
  <div class="container">
    <div class="hero-content">
      <span class="hero-badge"><span class="dot"></span> Youth-led · Refugee-inspired · Since <?= h(setting($pdo,'founded_year','2021')) ?></span>
      <h1><?= h(setting($pdo, 'hero_title')) ?></h1>
      <p class="lead"><?= h(setting($pdo, 'hero_subtitle')) ?></p>
      <div class="hero-actions">
        <a href="<?= SITE_URL ?>/about.php" class="btn btn-primary">Discover Our Story →</a>
        <a href="<?= SITE_URL ?>/products.php" class="btn btn-outline">🍯 Shop BetterLife Farm</a>
      </div>
      <div class="hero-stats">
        <?php foreach (array_slice($stats, 0, 3) as $s): ?>
          <div><strong><?= h($s['value']) ?></strong><span><?= h($s['label']) ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <a href="#about-preview" class="hero-scroll"><span class="line"></span>Scroll</a>
</section>

<section class="stats-strip">
  <div class="container">
    <div class="grid">
      <?php foreach ($stats as $s): ?>
        <div class="stat-item">
          <strong data-count="<?= h($s['value']) ?>">0</strong>
          <span><?= h($s['label']) ?></span>
        </div>
      <?php endforeach; ?>
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
          <li><span class="tick">✓</span> Working across Uganda, South Sudan, Tanzania, Ghana &amp; DR Congo</li>
          <li><span class="tick">✓</span> Sustainable agriculture, green skills &amp; climate education</li>
          <li><span class="tick">✓</span> A working model farm producing honey, ghee &amp; yoghurt</li>
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
            <div class="icon-badge">🌿</div>
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
