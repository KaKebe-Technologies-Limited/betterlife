<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
$activePage = 'home';
$pageDescription = 'BetterLife International works with women, young people, refugees, displaced families and farming communities across Uganda, South Sudan, Tanzania, Ghana and the DRC to turn climate pressure into practical action.';

$heroImages = [
    setting($pdo, 'hero_image_1', 'assets/img/hero-real-1.jpg'),
    setting($pdo, 'hero_image_2', 'assets/img/farm-field-1.jpg'),
    setting($pdo, 'hero_image_3', 'assets/img/product-honey.jpg'),
    setting($pdo, 'hero_image_4', 'assets/img/about-real-1.jpg'),
    setting($pdo, 'hero_image_5', 'assets/img/farm-field-2.jpg'),
    setting($pdo, 'hero_image_6', 'assets/img/program-trees.jpg'),
    setting($pdo, 'hero_image_7', 'assets/img/product-ghee.jpg'),
    setting($pdo, 'hero_image_8', 'assets/img/product-yogurt.jpg'),
    setting($pdo, 'hero_image_9', 'assets/img/betterlifeint-source/programs/program-photo-1.jpg'),
    setting($pdo, 'hero_image_10', 'assets/img/betterlifeint-source/programs/program-photo-3.jpg'),
    setting($pdo, 'hero_image_11', 'assets/img/betterlifeint-source/projects/project-agro-tourism-alt.jpeg'),
    setting($pdo, 'hero_image_12', 'assets/img/betterlifeint-source/impact-reports/impact-photo-1.jpeg'),
];
$programs = $pdo->query("SELECT * FROM programs WHERE status = 1 ORDER BY sort_order LIMIT 5")->fetchAll();
$posts = $pdo->query("SELECT bp.*, bc.name AS cat_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE bp.status = 'published' ORDER BY bp.published_at DESC LIMIT 3")->fetchAll();

// Button label for each programme area on the "What We Do" grid.
$programCta = [
    'climate-resilient-agriculture'      => 'Explore Food and Agriculture',
    'green-skills-livelihoods'            => 'Explore Livelihoods',
    'climate-education-youth-leadership'  => 'Explore Education and Youth Leadership',
    'clean-energy-water-restoration'      => 'Explore Energy and Restoration',
    'digital-innovation'                  => 'Explore Digital Innovation',
];

require __DIR__ . '/includes/header.php';
?>

<section class="hero-full" id="top">
  <div class="hero-scroll-panel">
    <div class="scroll-track">
      <?php foreach (array_chunk($heroImages, 4) as $ri => $rowImages): ?>
        <div class="scroll-row <?= $ri % 2 === 0 ? 'dir-left' : 'dir-right' ?>">
          <?php foreach (array_merge($rowImages, $rowImages) as $ii => $img): ?>
            <div class="scroll-tile <?= $ii % 2 === 0 ? 'tint-green' : 'tint-blue' ?>">
              <img src="<?= asset_url($img) ?>" alt="BetterLife International" loading="lazy">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="hero-scrim"></div>
  </div>

  <div class="container hero-full-copy">
    <div class="hero-text fade-up">
      <span class="hero-badge"><span class="dot"></span> <?= h(setting($pdo, 'hero_kicker', 'Founded in Uganda. Working across five African countries.')) ?></span>
      <h1><?= h(setting($pdo, 'hero_title')) ?></h1>
      <p class="lead"><?= h(setting($pdo, 'hero_subtitle')) ?></p>
      <div class="hero-actions">
        <a href="<?= SITE_URL ?>/programs.php" class="btn btn-hero-cta">See Our Work <span class="cta-dot"><?= icon('arrow-right', 15) ?></span></a>
        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline">Partner With Us</a>
      </div>
    </div>
  </div>
</section>

<section class="impact-section section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Impact at a Glance</span>
      <h2>The Numbers Matter. What Happens Afterwards Matters More.</h2>
      <p class="muted">In 2025, BetterLife reached 112,430 people. Behind that number are farmers who changed how they grow food, young people who found a way into work, women who began earning and saving, and families that became less exposed to the next failed season.</p>
    </div>
    <div class="impact-stats">
      <div class="stat-item fade-up"><strong data-count="112,430">0</strong><span>People reached in 2025</span></div>
      <div class="stat-item fade-up"><strong data-count="18,900">0</strong><span>Farmers supported</span></div>
      <div class="stat-item fade-up"><strong data-count="41,200">0</strong><span>Refugees and host-community members reached</span></div>
      <div class="stat-item fade-up"><strong data-count="5">0</strong><span>African countries</span></div>
    </div>
    <div style="margin-top:28px;">
      <a href="<?= SITE_URL ?>/impact-reports.php" class="btn btn-outline-dark">See Our Impact</a>
    </div>
  </div>
</section>

<section id="why-we-exist">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Why We Exist</span>
      <h2>Climate Change Rarely Arrives Calling Itself Climate Change</h2>
    </div>
    <div class="container-narrow" style="padding:0;max-width:760px;">
      <p class="muted">It arrives as a harvest that fails twice in one year. It is the extra distance a woman walks when the nearest water source dries up. It is the child who misses school because there is more work to do at home. It is the young person who leaves agriculture because one bad season can erase everything.</p>
      <p class="muted">These problems are connected. Food depends on water. Water collection takes time. Time affects education and income. Income determines whether a family can recover when the next shock comes.</p>
      <p class="muted">BetterLife works across those connections. We bring together agriculture, livelihoods, clean energy, education, technology and market access around the way people actually live.</p>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">What We Do</span>
      <h2>Practical Work, Built Around Real Lives</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($programs as $p): ?>
        <div class="card program-card fade-up">
          <div class="thumb"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['title']) ?>"></div>
          <div class="body">
            <div class="icon-badge"><?= icon($p['icon'] ?: 'leaf', 20) ?></div>
            <h3><?= h($p['title']) ?></h3>
            <p class="muted" style="font-size:14px;"><?= h($p['summary']) ?></p>
            <a href="<?= SITE_URL ?>/programs.php#<?= h($p['slug']) ?>" class="more"><?= h($programCta[$p['slug']] ?? 'Learn more') ?></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="our-model">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Our Model</span>
      <h2>Our Work Begins Where the Handout Ends</h2>
    </div>
    <div class="split" style="align-items:start;">
      <div class="fade-up">
        <p class="muted">Emergency support can help a family through today. Rebuilding a life takes more.</p>
        <p class="muted">We begin by listening to what is making it difficult for people to grow food, earn, save or plan ahead. From there, we combine practical training with demonstration, coaching, starter inputs, savings, finance, information and markets.</p>
        <p class="muted">People do not simply attend a workshop and leave. They test what they have learnt, adapt it to their circumstances and receive support as they put it to use. Existing women&rsquo;s groups, farmer groups, schools, local facilitators and public institutions are involved so that the work is not held together by BetterLife alone.</p>
      </div>
      <div class="fade-up">
        <div class="journey-list">
          <div class="journey-row"><div class="year">Listen</div><p>Understand the problem as the community experiences it.</p></div>
          <div class="journey-row"><div class="year">Demonstrate</div><p>Show what works in a form people can see and test.</p></div>
          <div class="journey-row"><div class="year">Apply</div><p>Support people as they use new skills at home, on the farm or in business.</p></div>
          <div class="journey-row"><div class="year">Connect</div><p>Open routes to inputs, savings, finance, information and markets.</p></div>
          <div class="journey-row"><div class="year">Carry Forward</div><p>Build local groups and leadership that can continue the work.</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-2.jpg') ?>" alt="Women in Yumbe learning climate-resilient farming">
      </div>
      <div class="fade-up">
        <span class="eyebrow">Featured Work</span>
        <h2>What Women in Yumbe Taught Us About Climate Resilience</h2>
        <p class="muted">Before we introduced a single farming technique, we asked women how the changing climate was affecting their day. Their answers went far beyond crops.</p>
        <p class="muted">They spoke about the hours spent looking for water, the distance travelled for firewood, the cost of buying vegetables and the choices families made when a harvest failed. With support from Foundation S &ndash; The Sanofi Collective, BetterLife worked with refugee, displaced and host-community women in Yumbe to respond to those realities together.</p>
        <p class="muted">Women learnt through gardens they could see and practices they could try: composting, mulching, sack and box gardening, drought-tolerant crops, agroforestry, briquette-making and simple digital tools for soil and market information.</p>
        <p class="muted">The lesson was straightforward. Women adopt what they can see. Groups learn faster than individuals working alone. And information becomes useful when people have the confidence and support to act on it.</p>
        <a href="<?= SITE_URL ?>/programs.php#climate-resilient-agriculture" class="btn btn-outline-dark" style="margin-top:8px;">Read the Yumbe Story</a>
      </div>
    </div>
  </div>
</section>

<section id="betterlife-farm">
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow">BetterLife Farm</span>
        <h2>From Training to a Real Market</h2>
        <p class="muted">BetterLife Agro Tourism Farm is where our work in agriculture, clean energy and livelihoods meets production and sales.</p>
        <p class="muted">The farm demonstrates solar-powered irrigation, greenhouse farming, dairy production, beekeeping and livestock rearing. It also creates a route for farmers trained by BetterLife International to supply produce for processing and sale through BetterLife Agro Tourism Farm Ltd.</p>
        <p class="muted">Our products include BetterLife Honey, Ghee and Vanilla Yoghurt. Each one is part of a wider value chain connecting knowledge, production and household income.</p>
        <div class="hero-actions" style="justify-content:flex-start;">
          <a href="<?= SITE_URL ?>/farm.php" class="btn btn-primary">Discover the Farm</a>
          <a href="<?= SITE_URL ?>/products.php" class="btn btn-outline-dark">Shop Our Products</a>
        </div>
      </div>
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/farm-field-2.jpg') ?>" alt="BetterLife Agro Tourism Farm">
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Partners</span>
      <h2>The Work Is Stronger in Partnership</h2>
      <p class="muted">BetterLife works with organisations that bring resources, knowledge and reach while respecting the experience of the communities at the centre of the work.</p>
    </div>
    <ul class="partner-names fade-up">
      <li>Foundation S &ndash; The Sanofi Collective</li>
      <li>Farm Radio International</li>
      <li>Dovetail Impact Foundation</li>
      <li>World Food Programme</li>
      <li>HBCU Green Fund</li>
      <li>FADECO</li>
      <li>ICPAC</li>
      <li>Moonshot</li>
    </ul>
    <div style="text-align:center;margin-top:32px;">
      <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-dark">Work With Us</a>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">Latest Stories</span>
      <h2>From the Field</h2>
      <p class="muted">Read what communities are teaching us, how the work is changing and what we are learning as we grow.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($posts as $post): ?>
        <div class="card post-card fade-up">
          <div class="thumb"><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>"><img src="<?= asset_url($post['featured_image']) ?>" alt="<?= h($post['title']) ?>"></a></div>
          <div class="body">
            <span class="cat-badge"><?= h($post['cat_name'] ?? 'From the Field') ?></span>
            <h3><a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>"><?= h($post['title']) ?></a></h3>
            <p class="excerpt"><?= h(excerpt($post['excerpt'] ?: $post['content'], 100)) ?></p>
            <a href="<?= SITE_URL ?>/blog-single.php?slug=<?= h($post['slug']) ?>" class="readmore">Read Story</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
