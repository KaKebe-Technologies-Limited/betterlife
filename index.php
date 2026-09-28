<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
$activePage = 'home';
$pageDescription = 'BetterLife International works with women, young people, refugees, displaced families and farming communities across Uganda, South Sudan, Tanzania, Ghana and the DRC to turn climate pressure into practical action.';

$programs = $pdo->query("SELECT * FROM programs WHERE status = 1 ORDER BY sort_order LIMIT 5")->fetchAll();
$posts = $pdo->query("SELECT bp.*, bc.name AS cat_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bc.id = bp.category_id WHERE bp.status = 'published' ORDER BY bp.published_at DESC LIMIT 3")->fetchAll();
$impactStories = $pdo->query("SELECT * FROM impact_stories WHERE status = 1 ORDER BY sort_order LIMIT 3")->fetchAll();

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

<section class="hero-split" id="top">
  <div class="container hero-split-inner">

    <div class="hero-copy">
      <span class="hero-kicker"><span class="dot"></span> <?= h(setting($pdo, 'hero_kicker', 'Founded in Uganda. Working across five African countries.')) ?></span>
      <h1><?= nl2br(preg_replace('/\b(begin again)\b/i', '<span class="highlight">$1</span>', h(setting($pdo, 'hero_title')))) ?></h1>
      <p class="lead"><?= h(setting($pdo, 'hero_subtitle')) ?></p>
      <div class="hero-actions">
        <a href="<?= SITE_URL ?>/programs.php" class="btn btn-hero-cta">Explore Our Work <span class="cta-dot"><?= icon('arrow-right', 15) ?></span></a>
        <a href="<?= SITE_URL ?>/contact.php" class="btn-ghost-dark"><span class="play-ico"><?= icon('arrow-right', 12) ?></span> Partner With Us</a>
      </div>
      <div class="hero-features">
        <div class="hero-feature"><span class="icon-badge"><?= icon('leaf', 18) ?></span><span>Climate-Resilient<br>Agriculture</span></div>
        <div class="hero-feature"><span class="icon-badge"><?= icon('book', 18) ?></span><span>Climate<br>Education</span></div>
        <div class="hero-feature"><span class="icon-badge"><?= icon('droplet', 18) ?></span><span>Clean Water<br>&amp; Energy</span></div>
        <div class="hero-feature"><span class="icon-badge"><?= icon('users', 18) ?></span><span>Green Skills &amp;<br>Livelihoods</span></div>
      </div>
    </div>

    <div class="hero-art">
      <div class="hero-art-dots" aria-hidden="true">
        <span class="accent-dot d-orange" style="width:18px;height:18px;top:2%;left:8%;animation-delay:0s;"></span>
        <span class="accent-dot d-red"    style="width:13px;height:13px;top:10%;right:4%;animation-delay:.6s;"></span>
        <span class="accent-dot d-green"  style="width:11px;height:11px;top:46%;left:-2%;animation-delay:1.2s;"></span>
        <span class="accent-dot d-blue"   style="width:14px;height:14px;bottom:18%;left:4%;animation-delay:1.8s;"></span>
        <span class="accent-dot d-blue"   style="width:11px;height:11px;bottom:-2%;left:38%;animation-delay:2.4s;"></span>
      </div>

      <svg class="africa-art" viewBox="0 0 600 760" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" role="img" aria-label="The shape of the African continent, representing the five countries BetterLife works in">
        <defs>
          <path id="africaShape" d="M150,40 C250,20 350,15 420,35 C460,50 470,75 450,95 C480,100 540,110 580,160 C560,190 530,200 510,220 C530,260 540,300 525,340 C515,380 495,420 470,460 C450,500 430,540 410,580 C395,620 385,670 375,750 C350,730 320,700 300,660 C280,620 265,580 255,540 C245,500 230,470 210,450 C185,440 160,445 140,460 C125,440 115,415 110,390 C100,370 90,355 75,345 C60,335 50,315 55,295 C60,270 75,250 90,225 C100,200 95,175 105,150 C115,120 130,90 150,65 C152,55 150,48 150,40 Z"/>
        </defs>
        <use href="#africaShape" class="africa-fill"/>
        <use href="#africaShape" class="africa-dash" transform="translate(-14,-14) scale(0.98)"/>
      </svg>

      <div class="hero-photo-overlap">
        <img id="heroOverlapPhoto" src="<?= asset_url('assets/img/village-girl-portrait.webp') ?>" alt="A girl from a BetterLife community in Uganda">
      </div>

      <div class="hero-stat-card">
        <strong>112,430</strong>
        <span>People reached across<br>5 African countries in 2025</span>
      </div>
    </div>

  </div>
</section>

<section class="done-section">
  <div class="container">
    <div class="done-grid">
      <div class="done-collage fade-up">
        <div class="dc-item dc-1"><img src="<?= asset_url('assets/img/yumbe-greenhouse-group.webp') ?>" alt="Building a greenhouse with women in Yumbe"></div>
        <div class="dc-item dc-2"><img src="<?= asset_url('assets/img/classroom-climate-club.webp') ?>" alt="A school climate club in session"></div>
        <div class="dc-item dc-3"><img src="<?= asset_url('assets/img/farmers-planting-together.webp') ?>" alt="Two generations working the same field"></div>
      </div>
      <div class="fade-up">
        <span class="eyebrow">Our Impact</span>
        <h2>What have we done with <em>your help?</em></h2>
        <p class="muted">Every season, BetterLife works alongside farmers, refugees, women and young people across Uganda, South Sudan, Tanzania, Ghana and the DRC &mdash; turning climate pressure into food people can grow, skills they can earn from and routes into a stronger local economy.</p>
        <p class="muted">In 2025 alone, that meant 112,430 people reached, 18,900 farmers supported and 65 community boreholes delivering cleaner water closer to home.</p>
        <a href="<?= SITE_URL ?>/impact-reports.php" class="btn btn-hero-cta">See Our Impact <span class="cta-dot"><?= icon('arrow-right', 15) ?></span></a>
      </div>
    </div>
  </div>
</section>

<?php if ($impactStories): ?>
<section class="section-cream stories-section">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Real Stories</span>
      <h2>Learn the stories of <em>those we've already helped</em></h2>
    </div>
    <div class="stories-row">
      <?php foreach ($impactStories as $story): ?>
        <a href="<?= SITE_URL ?>/impact-reports.php" class="story-card fade-up">
          <div class="story-photo"><img src="<?= asset_url($story['image']) ?>" alt="<?= h($story['title']) ?>"></div>
          <h4><?= h($story['title']) ?></h4>
          <p><?= h($story['caption']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="reach-section">
  <div class="container reach-grid">
    <div class="fade-up">
      <span class="eyebrow">Where We Work</span>
      <h2>We are always where others need help.</h2>
      <div class="reach-stats">
        <div class="reach-stat"><strong>112,430</strong><span>People reached</span></div>
        <div class="reach-stat"><strong>65</strong><span>Community boreholes</span></div>
        <div class="reach-stat"><strong>50,000+</strong><span>Tree seedlings raised</span></div>
      </div>
      <a href="<?= SITE_URL ?>/about.php#where-we-work" class="btn btn-outline-dark">See Where We Work</a>
    </div>
    <div class="reach-map fade-up">
      <svg viewBox="0 0 600 760" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Map of the five African countries BetterLife works in">
        <path class="reach-map-fill" d="M150,40 C250,20 350,15 420,35 C460,50 470,75 450,95 C480,100 540,110 580,160 C560,190 530,200 510,220 C530,260 540,300 525,340 C515,380 495,420 470,460 C450,500 430,540 410,580 C395,620 385,670 375,750 C350,730 320,700 300,660 C280,620 265,580 255,540 C245,500 230,470 210,450 C185,440 160,445 140,460 C125,440 115,415 110,390 C100,370 90,355 75,345 C60,335 50,315 55,295 C60,270 75,250 90,225 C100,200 95,175 105,150 C115,120 130,90 150,65 C152,55 150,48 150,40 Z"/>
        <g class="reach-pin" transform="translate(230,270)"><circle r="10"/><text y="-18">Uganda</text></g>
        <g class="reach-pin" transform="translate(400,330)"><circle r="10"/><text y="-18">South Sudan</text></g>
        <g class="reach-pin" transform="translate(280,180)"><circle r="10"/><text y="-18">Ghana</text></g>
        <g class="reach-pin" transform="translate(270,420)"><circle r="10"/><text y="-18">Tanzania</text></g>
        <g class="reach-pin" transform="translate(180,340)"><circle r="10"/><text y="-18">DR Congo</text></g>
      </svg>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="cta-band-photo"><img src="<?= asset_url('assets/img/field-team-group-under-tree.webp') ?>" alt="The BetterLife field team"></div>
  <div class="container">
    <div class="cta-band-inner fade-up">
      <h2>Ready to grow this work with us? Everyone can help.</h2>
      <div class="hero-actions">
        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-hero-cta">Partner With Us <span class="cta-dot"><?= icon('arrow-right', 15) ?></span></a>
        <a href="<?= SITE_URL ?>/programs.php" class="btn-ghost-light">Explore Our Work</a>
      </div>
    </div>
  </div>
</section>

<section id="why-we-exist">
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow">Why We Exist</span>
        <h2>Climate Change Rarely Arrives Calling Itself Climate Change</h2>
        <p class="muted">It arrives as a harvest that fails twice in one year. It is the extra distance a woman walks when the nearest water source dries up. It is the child who misses school because there is more work to do at home. It is the young person who leaves agriculture because one bad season can erase everything.</p>
        <p class="muted">These problems are connected. Food depends on water. Water collection takes time. Time affects education and income. Income determines whether a family can recover when the next shock comes.</p>
        <p class="muted">BetterLife works across those connections. We bring together agriculture, livelihoods, clean energy, education, technology and market access around the way people actually live.</p>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/program-trees-2.jpg') ?>" alt="A mother and child in a BetterLife community environmental project">
      </div>
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
    <div style="max-width:760px;margin:0 auto;">
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
        <img src="<?= asset_url('assets/img/product-ghee-2.jpg') ?>" alt="Dairy products from BetterLife Agro Tourism Farm">
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
