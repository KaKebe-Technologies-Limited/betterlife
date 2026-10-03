<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'BetterLife Farm';
$activePage = 'farm';
$pageDescription = 'BetterLife Agro Tourism Farm brings together practical learning, clean energy, food production and market access, giving farmers a route from training to a real market.';

// Managed from Admin → Page Content (page "farm", section "on_the_farm").
$onTheFarm = content_items($pdo, 'farm', 'on_the_farm');
// Pulls straight from the real product catalogue (Admin → Products) so this
// preview always matches what is actually for sale, with no separate copy
// to keep in sync.
$products = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY featured DESC, sort_order LIMIT 3")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>BetterLife Farm</div>
    <h1>Food today. A route to independence tomorrow.</h1>
    <p style="max-width:640px;color:#e2f0e9;">The Farm Is a Stepping-Stone, Not a Destination.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow">BetterLife Agro Tourism Farm</span>
        <h2>How the Model Works</h2>
        <p class="muted">BetterLife Agro Tourism Farm brings together practical learning, clean energy, food production and market access.</p>
        <p class="muted">BetterLife International trains and supports farmers, refugees, women and vulnerable households. BetterLife Agro Tourism Farm Ltd handles production, processing, packaging and sales.</p>
        <p class="muted">Participants who are rebuilding their livelihoods can choose to spend up to two flexible hours at the farm. They receive practical agricultural training, food support, free seedlings and starter inputs to begin producing at home.</p>
        <p class="muted">The arrangement leaves time for family care, study, job-seeking and other income activities. As people become more stable, they move into independent production and can become suppliers to BetterLife Agro Tourism Farm Ltd.</p>
        <p class="muted">The point is not to keep people working at the farm. It is to help them reach a place where they no longer need to.</p>
      </div>
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/farm-field-1.jpg') ?>" alt="BetterLife Agro Tourism Farm">
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/farm-field-2.jpg') ?>" alt="Solar-powered irrigation on BetterLife Agro Tourism Farm">
      </div>
      <div class="fade-up">
        <span class="eyebrow">Powered by Clean Energy</span>
        <p class="muted">Solar energy powers irrigation, water pumping and key activities on the farm. This makes production more reliable through dry periods and shows farmers how clean energy can reduce both climate risk and operating costs.</p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">On the Farm</span>
    </div>
    <div class="detail-grid fade-up">
      <?php foreach ($onTheFarm as $b): ?>
        <div class="detail-block"><h4><?= h($b['title']) ?></h4><p><?= h($b['body']) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow">From Farmer to Customer</span>
        <p class="muted">Training has limited value if a farmer produces and cannot sell. BetterLife Agro Tourism Farm Ltd buys, processes and markets produce from the farm and participating farmers.</p>
        <p class="muted">This turns BetterLife&rsquo;s products into more than items on a shelf. They are the final link in a chain that begins with skills and ends with income.</p>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/product-honey-2.jpg') ?>" alt="Jars of BetterLife Honey">
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Our Products</span>
    </div>
    <div class="grid grid-3">
      <?php foreach ($products as $p): ?>
        <div class="card product-card fade-up">
          <div class="thumb"><img src="<?= asset_url($p['image']) ?>" alt="<?= h($p['name']) ?>"></div>
          <div class="body" style="padding:22px;">
            <h3 style="font-size:17px;"><?= h($p['name']) ?></h3>
            <p class="muted" style="font-size:14px;"><?= h($p['short_desc']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:28px;">
      <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary">Shop BetterLife Products</a>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/farm/rukungiri-farm-sign.jpg') ?>" alt="The roadside sign for BetterLife Agro-Tourism Farm and BetterLife International Organisation in Rukungiri" loading="lazy">
      </div>
      <div class="fade-up">
        <span class="eyebrow">Visit the Farm</span>
        <p class="muted">Schools, farmers, community groups, development partners and visitors can experience how solar energy, irrigation, livestock, beekeeping and food processing work together.</p>
        <div class="hero-actions" style="justify-content:flex-start;margin-top:18px;">
          <a href="<?= SITE_URL ?>/contact.php?subject=Farm visit" class="btn btn-primary">Book a Farm Visit</a>
          <a href="<?= SITE_URL ?>/contact.php?subject=Partner With the Farm" class="btn btn-outline-dark">Partner With the Farm</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
