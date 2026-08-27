<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us';
$activePage = 'about';
$pageDescription = 'BetterLife International was founded in Uganda in 2021 with USD 200. It now works across Uganda, South Sudan, Tanzania, Ghana and the Democratic Republic of Congo on climate-resilient agriculture, livelihoods, clean energy, education and market access.';

$howWeWork = [
  ['We Start by Listening', 'We work through women&rsquo;s groups, farmer groups, refugee and host-community structures, schools, community organisations and local facilitators. These groups help us understand what is changing, what has already been tried and what people can realistically sustain.'],
  ['People Learn by Seeing and Doing', 'A technique explained in a workshop can remain abstract. A garden that is producing through a dry spell is harder to dismiss. We use demonstration sites, local-language facilitation, peer learning and community champions so that people can see, question and test new practices.'],
  ['Training Must Lead Somewhere', 'Knowledge matters, but so do tools, money and customers. We connect learning to starter inputs, savings, finance, business support, services and markets wherever possible.'],
  ['We Build on What Already Exists', 'We strengthen existing groups and local institutions instead of creating temporary committees that disappear when a project closes. Local ownership is not an exit strategy added at the end. It is part of the design from the beginning.'],
  ['We Pay Attention to What Changes', 'We use monitoring data, participant feedback and field observation to understand whether people are applying what they learnt and whether that application is improving food, income, confidence or resilience.'],
];
$whoWeWorkWith = [
  ['Women and Girls', 'Women hold much of the responsibility for food, water and household care, yet often have less access to land, money, technology and decision-making. Our programmes pay attention to the barriers that shape participation, including time, phone access, mobility, confidence and social norms.'],
  ['Children and Young People', 'We work with children and young people through schools, youth centres, climate education, digital learning, livelihoods, leadership and innovation programmes. They are not only preparing for the future; they already have ideas and decisions to contribute today.'],
  ['Refugees and Displaced Families', 'Our programmes support people affected by displacement to rebuild their ability to produce food, earn, save and take part in the local economy. We work with refugees and host communities together whenever possible.'],
  ['Smallholder Farmers', 'We help farmers improve soil and water management, access climate and market information, diversify production and connect to services, finance and buyers.'],
];
$whereWeWork = [
  ['Uganda', 'Uganda is where BetterLife began and where much of our work is based. Programmes include climate-resilient agriculture, refugee and host-community livelihoods, women&rsquo;s economic empowerment, Green Libraries, tree nurseries, clean energy, water access, digital agriculture and BetterLife Agro Tourism Farm. Our West Nile work is coordinated through Yumbe and Bidi Bidi.'],
  ['South Sudan', 'Through our Juba office and field presence in Yambio, we work on food security, solar-powered irrigation, climate-resilient agriculture and practical livelihoods for young people, women and communities affected by conflict and displacement.'],
  ['Tanzania', 'Our Tanzania work is based in Kayanga Town, Karagwe District. Together with FADECO, we use schools, community radio and practical training to support climate education, health, green skills and youth enterprise.'],
  ['Ghana', 'In Ghana, our work focuses on youth leadership, climate action, sustainable livelihoods and community-led environmental solutions.'],
  ['Democratic Republic of Congo', 'Our work in the Democratic Republic of Congo supports young people and communities affected by poverty, conflict and displacement through livelihoods, environmental action and local participation.'],
];
$journey = [
  ['2021', 'BetterLife International was founded in Uganda with USD 200 and a small team of young people.'],
  ['2022', 'The organisation expanded its community programmes and began establishing work in South Sudan and Tanzania.'],
  ['2023', 'BetterLife launched Soilla, expanded its school and environmental work and opened the Apala One Stop Youth Centre in Alebtong.'],
  ['2024', 'Green Libraries, community restoration, clean energy, water access and livelihoods programmes continued to grow.'],
  ['2025', 'BetterLife deepened its work with women in Yumbe through the Foundation S-supported climate-resilience programme and strengthened BetterLife Agro Tourism Farm as a bridge from training to markets.'],
  ['2026', 'The Women&rsquo;s Action Circle approach began expanding through Farm Radio International&rsquo;s Green Leaf Platforms Uganda. Work also continued on Soilla, Agribusiness Connekt, the BetterLife Climate Academy and regional youth innovation.'],
];
$guides = [
  ['Dignity', 'People are partners with knowledge and ability, not passive recipients of help.'],
  ['Community Leadership', 'Those living with a problem should have real influence over the response.'],
  ['Usefulness', 'An idea matters when people can put it to work in their own circumstances.'],
  ['Accountability', 'We are responsible to the communities, partners and institutions that place their trust in us.'],
  ['Inclusion', 'Participation must be designed around the barriers people face, not simply offered in theory.'],
  ['Learning', 'We change course when evidence and experience show us a better way.'],
];

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>About Us</div>
    <h1>It Began with the Lives We Knew</h1>
    <p style="max-width:640px;color:#e2f0e9;">This work did not begin in a conference room.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <p class="muted">BetterLife International was founded by young people who understood that poverty, displacement and climate change do not happen one at a time. Our work grew from the need for solutions that make sense in the whole of a person&rsquo;s life.</p>
      </div>
      <div class="fade-up img-frame">
        <img src="<?= asset_url(setting($pdo, 'about_image')) ?>" alt="BetterLife International team">
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Our Story</span>
      <h2>From USD 200 to Work Across Five Countries</h2>
    </div>
    <div class="prose-narrow fade-up"><?= nl2p(setting($pdo, 'about_who_text')) ?></div>
  </div>
</section>

<section>
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/farm-field-3.jpg') ?>" alt="BetterLife International in the field">
      </div>
      <div class="fade-up" style="display:flex;flex-direction:column;gap:18px;">
        <span class="eyebrow">Purpose</span>
        <div class="card" style="padding:24px;">
          <div class="icon-badge" style="width:38px;height:38px;margin-bottom:10px;"><?= icon('target', 20) ?></div>
          <h3>Mission</h3>
          <p class="muted" style="font-size:14px;margin:0;"><?= h(setting($pdo, 'mission_text')) ?></p>
        </div>
        <div class="card" style="padding:24px;">
          <div class="icon-badge" style="width:38px;height:38px;margin-bottom:10px;"><?= icon('eye', 20) ?></div>
          <h3>Vision</h3>
          <p class="muted" style="font-size:14px;margin:0;"><?= h(setting($pdo, 'vision_text')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">How We See the Work</span>
      <h2>A Failed Harvest Is Never Just a Farming Problem</h2>
    </div>
    <div class="prose-narrow fade-up">
      <p>When a woman tells us her harvest failed, seeds may appear to be the answer. But listen longer and the picture changes. She may have no water nearby. She may spend much of the day collecting firewood. She may lack money for inputs, access to a phone or a buyer for what she grows.</p>
      <p>Giving her seeds alone leaves most of the problem untouched.</p>
      <p>BetterLife takes a systems approach because people live in systems. We connect food to water, time, energy, income, finance, information and markets. One programme may therefore include a demonstration garden, a savings group, a digital tool and a buyer connection. The combination is shaped by the barriers people are actually facing.</p>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">How We Work</span>
    </div>
    <div class="detail-grid fade-up">
      <?php foreach ($howWeWork as $b): ?>
        <div class="detail-block"><h4><?= $b[0] ?></h4><p><?= $b[1] ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Who We Work With</span>
    </div>
    <div class="detail-grid fade-up">
      <?php foreach ($whoWeWorkWith as $b): ?>
        <div class="detail-block"><h4><?= $b[0] ?></h4><p><?= $b[1] ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Where We Work</span>
    </div>
    <div class="detail-grid fade-up">
      <?php foreach ($whereWeWork as $b): ?>
        <div class="detail-block"><h4><?= $b[0] ?></h4><p><?= $b[1] ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section-cream">
  <div class="container">
    <div class="split">
      <div class="fade-up img-frame">
        <img src="<?= asset_url('assets/img/hero-farm-1.jpg') ?>" alt="BetterLife International journey">
      </div>
      <div class="fade-up">
        <span class="eyebrow">Our Journey</span>
        <h2>From a Local Idea to Work Across Five Countries</h2>
        <div class="journey-list">
          <?php foreach ($journey as $j): ?>
            <div class="journey-row">
              <div class="year"><?= h($j[0]) ?></div>
              <p><?= $j[1] ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head center fade-up">
      <span class="eyebrow" style="justify-content:center;">What Guides Us</span>
    </div>
    <div class="grid grid-3">
      <?php foreach ($guides as $i => $g): ?>
        <div class="card value-card fade-up">
          <div class="num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
          <h4><?= h($g[0]) ?></h4>
          <p><?= h($g[1]) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
