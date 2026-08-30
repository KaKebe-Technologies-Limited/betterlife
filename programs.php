<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Work';
$activePage = 'programs';
$pageDescription = 'BetterLife works across climate-resilient agriculture, green skills and livelihoods, climate education and youth leadership, clean energy and restoration, and digital innovation for farmers.';

require __DIR__ . '/includes/header.php';

/** Render one sub-programme block. */
function workblock(string $title, string $note, array $paras, ?string $ctaLabel = null, ?string $ctaHref = null): void {
    echo '<article class="workblock fade-up">';
    echo '<h3>' . $title . '</h3>';
    if ($note !== '') echo '<p class="workblock-note">' . $note . '</p>';
    foreach ($paras as $p) echo '<p>' . $p . '</p>';
    if ($ctaLabel && $ctaHref) echo '<a href="' . $ctaHref . '" class="btn btn-outline-dark btn-sm">' . h($ctaLabel) . '</a>';
    echo '</article>';
}
$stories = SITE_URL . '/blog.php';
$farm = SITE_URL . '/farm.php';
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
      <?php
      workblock('Women&rsquo;s Climate Resilience in Yumbe', 'Implemented with support from Foundation S &ndash; The Sanofi Collective', [
        'Before the work began, we spoke with 100 women in Yumbe about the way climate pressure was changing their lives. Many were spending around four hours a day collecting water. Some travelled as far as 24 kilometres in search of firewood. Eighty-one per cent had experienced crop failure, and 63 per cent reported having withdrawn a child from school at some point because the household could not meet the cost.',
        'Those answers changed the shape of the project. This could not be a farming course alone.',
        'BetterLife worked with refugee, displaced and host-community women through local-language training and gardens where they could see each practice working. The women learnt composting, mulching, sack and box gardening, trellising, drought-tolerant crops, agroforestry and briquette-making. They received seedlings and support to use simple digital tools for soil and market information.',
        'Among the 72 women who completed structured training, knowledge of climate-smart agriculture rose from 22 per cent to 92 per cent. Seventy-two per cent adopted sack or box gardening, 63 per cent took up composting, 54 per cent used mulching and 57 per cent introduced drought-tolerant crops. Participating households reported an average 35 per cent reduction in spending on vegetables as home production improved.',
        'The figures matter, but so did the way the change happened. Women learnt in groups, saw the methods before risking their own resources and could return with questions after trying them at home. Confidence grew alongside knowledge.',
      ], 'Read the Project Story', $stories);

      workblock('Green Leaf Platforms Uganda', 'In partnership with Farm Radio International', [
        'What happens after a radio programme ends?',
        'A woman may hear about a regenerative farming practice and understand it perfectly, yet still be unable to try it. She may not control the land, own a phone or have the materials to risk on an unfamiliar method. She may need to see the practice working nearby before she trusts it with a small harvest.',
        'BetterLife&rsquo;s partnership with Farm Radio International is designed around that gap between receiving information and acting on it.',
        'Under Green Leaf Platforms Uganda, radio and digital agricultural content is connected to Women&rsquo;s Action Circles across 12 districts. These circles build on groups women already trust, including savings groups, farmer organisations, cooperatives and community networks.',
        'Women listen, discuss what the information means in their setting, ask questions, visit demonstration plots and test practices together. Peer champions support those with limited access to phones or extension services. Women&rsquo;s feedback also travels back into the programme, helping shape content around the questions and barriers they are actually facing.',
        'Farm Radio International brings its radio, digital and farmer-learning experience. BetterLife leads the women&rsquo;s inclusion work on the ground, connecting listening to learning, practice and adoption.',
        'The model grows from a lesson we first saw clearly in Yumbe: women adopt what they can see, and groups often carry change farther than individuals working alone.',
      ], 'Learn About Green Leaf Platforms', $stories);

      workblock('BetterLife SPRING', 'Sustainable Powered Resilient Irrigation for Next Generation Farming', [
        'In South Sudan, farming families are expected to plan around rainfall that is becoming harder to predict. When rain stops too early, households lose food and the income they hoped to earn from the season.',
        'BetterLife SPRING combines solar-powered irrigation with practical training in soil health, crop planning and water management. Farmers, refugees and displaced families can produce more reliably through dry periods without depending on fuel-powered pumping.',
        'The project brings together clean energy, food production and livelihoods. The aim is not simply to install irrigation equipment, but to help communities use it well, maintain it and turn more reliable production into stronger household security.',
      ], 'Explore BetterLife SPRING', $stories);

      workblock('BetterLife Agro Tourism Farm', '', [
        'The farm is BetterLife&rsquo;s working demonstration and market link. Farmers learn through solar-powered irrigation, greenhouse farming, beekeeping, dairy production and livestock rearing, then have a route to supply produce through BetterLife Agro Tourism Farm Ltd.',
      ], 'Discover the Farm', $farm);
      ?>
    </div>
  </div>
</section>

<!-- ===================== Green Skills & Livelihoods ===================== -->
<section id="green-skills-livelihoods">
  <div class="container">
    <div class="split" style="margin-bottom:10px;">
      <div class="fade-up img-frame bg-blue">
        <img src="<?= asset_url('assets/img/project-smiles.jpg') ?>" alt="Participants at a BetterLife livelihoods training">
      </div>
      <div class="fade-up">
        <span class="eyebrow">Area of Work</span>
        <h2>Green Skills, Livelihoods and Market Access</h2>
        <p class="muted">Learning a trade is one step. Finding tools, capital and customers is another.</p>
        <p class="muted">BetterLife combines practical skills with enterprise coaching, savings, finance and market connections. Participants train in areas suited to local demand, including agriculture, poultry, carpentry, tailoring, barbering, weaving and solar technology.</p>
        <p class="muted">The work continues beyond the training day. We help people test a business idea, understand costs, join a savings group, approach finance and find a route into the market.</p>
      </div>
    </div>
    <div class="workblock-list">
      <?php
      workblock('SMILES', 'Market Inclusive Livelihood Pathways to Self-Reliance', [
        'SMILES brings refugees and host-community members into the same training groups and local economy.',
        'Participants learn practical skills such as poultry, farming, carpentry, tailoring, barbering and solar technology. They then receive mentorship, business support, market connections and access to small loans. Where appropriate, BetterLife helps participants who lack conventional collateral by acting as a guarantor.',
        'The economic work also creates room for social cohesion. Refugees and host-community members build relationships as they train, save, trade and solve business problems together. Peacebuilding becomes part of ordinary economic life rather than a separate conversation in a workshop.',
        'Across target groups, 78 per cent of participants moved into sustainable income pathways, 85 per cent reported improved refugee-host relations and food insecurity fell by 40 per cent.',
      ], 'Explore SMILES', $stories);

      workblock('Agribusiness Connekt', '', [
        'Farmers are often trained to produce and then left alone at the point where business begins.',
        'Agribusiness Connekt links farmers and small agricultural enterprises to buyers, finance, services and market information. It helps producers understand what customers need, find opportunities beyond their immediate location and make better decisions about when and where to sell.',
        'The platform also supports farmers trained through BetterLife programmes, giving them a route from production into longer-term enterprise.',
      ], 'Explore Agribusiness Connekt', $stories);
      ?>
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
      <?php
      workblock('Green Libraries, Eco Labs and School Climate Clubs', '', [
        'Green Libraries and Eco Labs give learners access to books, digital resources and practical environmental activities. Students take part in school gardens, tree planting, waste separation, plastic banks, debates and public speaking.',
        'More than 4,500 students have been engaged through school climate education, and BetterLife has supported over 20 Green Libraries and Eco Labs. Our school work has included Lake Victoria School Entebbe and Buddo Junior School.',
        'The aim is not to turn every child into a climate expert. It is to make the subject understandable enough for them to connect it to their home, school and community, and confident enough for them to act.',
      ], 'Explore Green Libraries', $stories);

      workblock('Apala One Stop Youth Centre', '', [
        'Talent exists in rural communities. Access often does not.',
        'The Apala One Stop Youth Centre in Alebtong gives young people a place to read, use computers, learn digital skills and develop ideas. Launched in December 2023, the centre opened with ten computers, more than 3,000 books and space to serve around 400 young people.',
        'Apala is both a learning centre and a statement about opportunity: where a young person is born should not decide how far their curiosity can take them.',
      ], 'Visit the Apala Story', $stories);

      workblock('Climate Education and Youth Enterprise in Tanzania', '', [
        'Together with FADECO, BetterLife uses schools, community radio and practical training to reach young people and communities in Tanzania.',
        'Eco Clubs involve students in tree planting, waste management and environmental leadership. FADECO Radio carries information on climate, agriculture, health and livelihoods into communities that formal training may not reach.',
        'Young people also learn skills such as reusable sanitary-pad production and liquid soap-making. The activities respond to health and dignity while opening small routes into enterprise.',
      ], 'Explore Our Work in Tanzania', $stories);

      workblock('BetterLife Pre-COP Climate Academy', '', [
        'Climate negotiations can feel distant from the places already living with the consequences. The BetterLife Pre-COP Climate Academy helps young Africans understand those negotiations and connect them to what is happening in their own communities.',
        'Participants learn about adaptation, climate finance, loss and damage, negotiations and advocacy. The academy also asks a more grounded question: what do these decisions mean for a farmer facing drought, a family displaced by floods or a young person trying to build a future?',
        'Supported by Moonshot, the virtual academy brings together young people from Uganda and across Africa to learn from experts and one another.',
      ], 'Explore the Climate Academy', $stories);

      workblock('Climate Innovation Hackathons', '', [
        'BetterLife&rsquo;s hackathons give young people a real problem, a team and room to build. Participants use technology, entrepreneurship and local knowledge to develop practical responses to climate and community challenges.',
        'Our youth climate-innovation work has included support from ICPAC&rsquo;s innovation ecosystem, connecting young people to regional climate knowledge and new opportunities to test their ideas.',
      ], 'Explore Youth Innovation', $stories);
      ?>
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
      <?php
      workblock('Community Tree Nurseries and Agroforestry', '', [
        'BetterLife-supported nurseries have raised more than 50,000 indigenous and fruit-tree seedlings, with over 20,000 distributed to schools, farmers and communities.',
        'Fruit trees can contribute to nutrition and income. Indigenous trees protect soil, provide shade and support biodiversity. Through agroforestry, trees become part of the farm rather than competing with it.',
        'We focus on what happens after distribution, including care, monitoring and survival. A seedling in the ground is a beginning, not a result by itself.',
      ], 'Explore Nature Restoration', $stories);

      workblock('Community Biogas', '', [
        'BetterLife has supported more than 48 household biogas systems. Families turn organic waste into cleaner cooking energy, reducing smoke inside the home and dependence on firewood and charcoal.',
        'The process also produces an organic by-product that can be returned to the soil. One household system therefore connects waste, energy, health and farming.',
      ], 'Explore Clean Energy', $stories);

      workblock('Water Access', '', [
        'BetterLife has supported 65 community boreholes. Reliable water reduces the time and distance people travel, improves household health and makes small-scale food production more possible.',
        'For us, water is not a side issue. It sits at the centre of health, food, education and climate resilience.',
      ]);

      workblock('BetterLife Renewable Pathways', '', [
        'BetterLife Renewable Pathways works with schools and communities on waste separation, plastic banks, recycling and the responsible reuse of materials.',
        'Four school plastic banks give learners a practical way to understand waste and participate in keeping plastics out of the environment. The programme also explores safe, locally appropriate ways of recovering value from materials that would otherwise be dumped or burnt.',
      ], 'Explore Renewable Pathways', $stories);

      workblock('Briquette-Making', '', [
        'Women and vulnerable households learn to make briquettes from suitable agricultural and organic waste. Briquettes can reduce dependence on firewood and charcoal while creating a product for household use or small-scale sale.',
        'For a woman who spends hours collecting fuel, an alternative made closer to home can mean saved time as well as income.',
      ]);
      ?>
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
      <?php
      workblock('Soilla', '', [
        'Soilla is BetterLife&rsquo;s digital agricultural advisory platform. It helps farmers access soil and crop guidance, climate information, market prices and agricultural services.',
        'Farmers can use the platform to better understand what may grow in their soil, monitor farm conditions, locate suppliers and experts and connect with other producers. The aim is to make information that is often expensive or distant more accessible to smallholder farmers.',
        'Soilla is paired with field training because owning a phone or receiving data does not automatically make a tool useful. Farmers need confidence, local support and information that fits their crops and circumstances.',
        'Our engagement with the World Food Programme has contributed to work around farmer information, verification and the responsible use of agricultural and climate data.',
      ], 'Explore Soilla', $stories);

      workblock('Agribusiness Connekt', '', [
        'Where Soilla supports production decisions, Agribusiness Connekt focuses on the business around the farm. It links producers to buyers, finance, services and market opportunities.',
        'Together, the two platforms respond to a gap we see repeatedly: farmers learn how to produce, but remain disconnected from the systems that determine whether production becomes income.',
      ], 'Explore Agribusiness Connekt', $stories);
      ?>
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
      <?php
      workblock('Dovetail Impact Foundation', 'Growing without losing what made the work local', [
        'As BetterLife expanded, we had to answer a difficult question: how do we reach more people without becoming distant from the communities that shaped us?',
        'Dovetail Impact Foundation has supported BetterLife through strategic acceleration and institutional strengthening. The relationship has helped us clarify our theory of change, strengthen how we measure and communicate impact and build more deliberate systems for growth and resource mobilisation.',
        'This support sits behind all our programmes rather than inside one field project. It helps us examine the full path from training to application, from application to income and from individual progress to stronger community systems.',
        'Growth, for BetterLife, is not simply a larger number. It is the ability to reach more people while protecting the dignity, usefulness and local ownership of the work.',
      ]);
      ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
