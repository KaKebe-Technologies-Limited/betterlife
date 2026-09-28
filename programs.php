<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Work';
$activePage = 'programs';
$pageDescription = 'BetterLife works across climate-resilient agriculture, green skills and livelihoods, climate education and youth leadership, clean energy and restoration, and digital innovation for farmers.';

require __DIR__ . '/includes/header.php';

/**
 * Render one sub-programme block from a content_items row. The list of
 * blocks per area of work is stored in the database and managed from
 * Admin → Page Content (page "programs", section = the area's id below),
 * so staff can edit the wording or add/remove sub-programmes there.
 */
function workblock_row(array $row): void {
    echo '<article class="workblock fade-up">';
    echo '<h3>' . h($row['title']) . '</h3>';
    if ($row['subtitle']) echo '<p class="workblock-note">' . h($row['subtitle']) . '</p>';
    echo nl2p($row['body']);
    if ($row['cta_label'] && $row['cta_href']) echo '<a href="' . h(SITE_URL . '/' . ltrim($row['cta_href'], '/')) . '" class="btn btn-outline-dark btn-sm">' . h($row['cta_label']) . '</a>';
    echo '</article>';
}
/** Render every block in a "programs" section, in order. */
function workblocks(PDO $pdo, string $sectionKey): void {
    foreach (content_items($pdo, 'programs', $sectionKey) as $row) workblock_row($row);
}
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Our Work</div>
    <h1>Food. Income. Knowledge. Energy. Opportunity.</h1>
    <p style="max-width:660px;color:#e2f0e9;">One Life Does Not Fit into One Project Box.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="prose-narrow fade-up">
      <p>A family facing climate pressure may also be dealing with hunger, unemployment, displacement and weak access to markets. Our programmes are organised by area of work, but they are designed to connect around the person.</p>
    </div>
  </div>
</section>

<!-- ===================== Climate-Resilient Agriculture ===================== -->
<section id="climate-resilient-agriculture" class="section-cream">
  <div class="container">
    <div class="split" style="margin-bottom:10px;">
      <div class="fade-up">
        <span class="eyebrow">Area of Work</span>
        <h2>Climate-Resilient Agriculture and Food Security</h2>
        <p class="muted">When the rains become unreliable, the first loss may be a crop. What follows can be a loss of income, fewer meals, unpaid school costs and debt carried into the next season.</p>
        <p class="muted">BetterLife works with farmers, women, refugees and vulnerable households to make food production more reliable. We use demonstration gardens and practical training in composting, mulching, water conservation, drought-tolerant crops, sack and box gardening, agroforestry, greenhouse farming, irrigation, poultry, aquaculture and beekeeping.</p>
        <p class="muted">We also help participants think beyond the harvest. Savings groups, enterprise support, digital information and market connections make it more possible for farming to provide both food and income.</p>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-7.jpg') ?>" alt="Drip-irrigated vegetable rows on a BetterLife demonstration plot">
      </div>
    </div>
    <div class="workblock-list">
      <?php workblocks($pdo, 'climate-resilient-agriculture'); ?>
      <div class="workblock-photo fade-up"><img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-2.jpg') ?>" alt="Greenhouse farming on a BetterLife demonstration plot"><span class="cap">Greenhouse farming in practice</span></div>
    </div>
  </div>
</section>

<!-- ===================== Green Skills & Livelihoods ===================== -->
<section id="green-skills-livelihoods">
  <div class="container">
    <div style="max-width:760px;margin:0 auto 10px;">
      <div class="fade-up">
        <span class="eyebrow">Area of Work</span>
        <h2>Green Skills, Livelihoods and Market Access</h2>
        <p class="muted">Learning a trade is one step. Finding tools, capital and customers is another.</p>
        <p class="muted">BetterLife combines practical skills with enterprise coaching, savings, finance and market connections. Participants train in areas suited to local demand, including agriculture, poultry, carpentry, tailoring, barbering, weaving and solar technology.</p>
        <p class="muted">The work continues beyond the training day. We help people test a business idea, understand costs, join a savings group, approach finance and find a route into the market.</p>
      </div>
    </div>
    <div class="workblock-list">
      <?php workblocks($pdo, 'green-skills-livelihoods'); ?>
      <div class="workblock-photo fade-up"><img src="<?= asset_url('assets/img/impact-story-2.jpg') ?>" alt="SMILES participants at a livelihoods training session"><span class="cap">Refugees and host-community members training together</span></div>
    </div>
  </div>
</section>

<!-- ===================== Climate Education & Youth Leadership ===================== -->
<section id="climate-education-youth-leadership" class="section-cream">
  <div class="container">
    <div class="split" style="margin-bottom:10px;">
      <div class="fade-up">
        <span class="eyebrow">Area of Work</span>
        <h2>Climate Education, Youth Leadership and Innovation</h2>
        <p class="muted">Young people will live longest with today&rsquo;s climate decisions. They should be doing more than listening to adults explain the future to them.</p>
        <p class="muted">BetterLife creates spaces where children and young people can learn, question, debate, build and take part in decisions. The work moves between classrooms, youth centres, digital spaces, policy conversations and practical community action.</p>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-5.jpg') ?>" alt="Students with school environment club banners">
      </div>
    </div>
    <div class="workblock-list">
      <?php workblocks($pdo, 'climate-education-youth-leadership'); ?>
      <div class="workblock-photo fade-up"><img src="<?= asset_url('assets/img/betterlifeint-source/programs/program-photo-4.jpg') ?>" alt="Students taking part in a BetterLife school climate club"><span class="cap">School climate clubs in action</span></div>
    </div>
  </div>
</section>

<!-- ===================== Clean Energy, Water & Restoration ===================== -->
<section id="clean-energy-water-restoration">
  <div class="container">
    <div class="split" style="margin-bottom:10px;">
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/project-spring.jpg') ?>" alt="Women drawing water at a BetterLife-supported community borehole">
      </div>
      <div class="fade-up">
        <span class="eyebrow">Area of Work</span>
        <h2>Clean Energy, Water and Environmental Restoration</h2>
        <p class="muted">Energy poverty, water insecurity and environmental loss often sit inside the same household.</p>
        <p class="muted">When firewood is scarce, women and girls walk farther. When a water source dries up, food production and school attendance suffer. When land is degraded, a farmer&rsquo;s options narrow with every season.</p>
        <p class="muted">BetterLife works on practical solutions that reduce those pressures while restoring the environment.</p>
      </div>
    </div>
    <div class="workblock-list">
      <?php workblocks($pdo, 'clean-energy-water-restoration'); ?>
      <div class="workblock-photo fade-up"><img src="<?= asset_url('assets/img/betterlifeint-source/projects/project-renewable-pathways-alt.jpg') ?>" alt="BetterLife Renewable Pathways plastic recycling work"><span class="cap">Renewable Pathways: turning waste into value</span></div>
    </div>
  </div>
</section>

<!-- ===================== Digital Innovation ===================== -->
<section id="digital-innovation" class="section-cream">
  <div class="container">
    <div class="split" style="margin-bottom:10px;">
      <div class="fade-up">
        <span class="eyebrow">Area of Work</span>
        <h2>Digital Innovation for Agriculture</h2>
        <p class="muted">Technology is useful when it shortens the distance between a farmer and a good decision.</p>
        <p class="muted">BetterLife develops digital tools around practical gaps: understanding soil, preparing for weather, finding a service, accessing finance and reaching a buyer.</p>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/project-soilla-app.jpg') ?>" alt="The Soilla mobile app for soil and crop guidance">
      </div>
    </div>
    <div class="workblock-list">
      <?php workblocks($pdo, 'digital-innovation'); ?>
    </div>
  </div>
</section>

<!-- ===================== Strengthening the Organisation ===================== -->
<section id="our-projects">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Strengthening the Organisation Behind the Work</span>
    </div>
    <div class="workblock-list">
      <?php workblocks($pdo, 'our-projects'); ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
