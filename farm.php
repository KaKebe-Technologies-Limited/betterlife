<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'BetterLife Farm';
$activePage = 'farm';
$pageDescription = 'BetterLife Agro Tourism Farm brings together practical learning, clean energy, food production and market access, giving farmers a route from training to a real market.';

$onTheFarm = [
  ['Solar-Powered Irrigation', 'Water is pumped and distributed using solar energy, helping crops survive when rainfall is unreliable.'],
  ['Greenhouse and Crop Farming', 'Farmers learn water-efficient production, soil management, crop care and methods suited to limited land.'],
  ['Dairy and Livestock', 'Livestock supports food, manure, household income and the farm&rsquo;s dairy value chain.'],
  ['Beekeeping', 'Community beekeeping creates income while encouraging the protection of trees and flowering plants.'],
  ['Poultry and Aquaculture', 'Diversified production reduces the risk of depending on one crop or one season.'],
  ['Seedlings', 'Participants receive free seedlings to establish gardens of their own and begin moving towards independent production.'],
];
$products = [
  ['BetterLife Honey', 'Produced through community beekeeping and local value chains that support both livelihoods and environmental care.'],
  ['BetterLife Ghee', 'Made through local dairy production, creating a market for milk while adding value closer to the farmer.'],
  ['BetterLife Vanilla Yoghurt', 'Fresh vanilla yoghurt made from locally sourced milk and processed through the BetterLife dairy value chain.'],
];

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
    <div class="section-head fade-up">
      <span class="eyebrow">Powered by Clean Energy</span>
    </div>
    <div class="prose-narrow fade-up">
      <p>Solar energy powers irrigation, water pumping and key activities on the farm. This makes production more reliable through dry periods and shows farmers how clean energy can reduce both climate risk and operating costs.</p>
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
        <div class="detail-block"><h4><?= $b[0] ?></h4><p><?= $b[1] ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">From Farmer to Customer</span>
    </div>
    <div class="prose-narrow fade-up">
      <p>Training has limited value if a farmer produces and cannot sell. BetterLife Agro Tourism Farm Ltd buys, processes and markets produce from the farm and participating farmers.</p>
      <p>This turns BetterLife&rsquo;s products into more than items on a shelf. They are the final link in a chain that begins with skills and ends with income.</p>
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
        <div class="card value-card fade-up">
          <h4><?= h($p[0]) ?></h4>
          <p><?= h($p[1]) ?></p>
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
    <div class="section-head fade-up">
      <span class="eyebrow">Visit the Farm</span>
    </div>
    <div class="prose-narrow fade-up">
      <p>Schools, farmers, community groups, development partners and visitors can experience how solar energy, irrigation, livestock, beekeeping and food processing work together.</p>
      <div class="hero-actions" style="justify-content:flex-start;margin-top:18px;">
        <a href="<?= SITE_URL ?>/contact.php?subject=Farm visit" class="btn btn-primary">Book a Farm Visit</a>
        <a href="<?= SITE_URL ?>/contact.php?subject=Partner With the Farm" class="btn btn-outline-dark">Partner With the Farm</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
